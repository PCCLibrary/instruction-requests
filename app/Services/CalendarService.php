<?php

namespace App\Services;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Exceptions\GoogleCalendarApiException;
use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling Google Calendar integration
 */
class CalendarService
{
    /**
     * Get pre-populated event form data for an instruction request
     */
    public function getEventFormData(InstructionRequests $request): array
    {
        return $this->formatEventData($request);
    }

    /**
     * Create a Google Calendar event for an instruction request
     *
     * This method handles the complete lifecycle of creating a Google Calendar event,
     * including database transaction management, event creation, and status tracking.
     *
     * @param InstructionRequests $request The instruction request to create an event for
     * @param array $customData Optional override data for the event
     * @return GoogleCalendarEvent The created calendar event record
     * @throws InvalidCalendarConfigurationException If calendar configuration is invalid
     * @throws GoogleCalendarApiException If Google Calendar API returns an error
     * @throws \Exception For unexpected errors during event creation
     */
    public function createEvent(InstructionRequests $request, array $customData = []): GoogleCalendarEvent
    {
        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') ||
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian']);
        }

        // Retrieve calendar ID from campus
        $calendarId = null;
        if ($request->campus) {
            $calendarId = $request->campus->getCalendarId();
        }

        // Strict validation - throw exception if no valid calendar ID
        if (!$calendarId) {
            throw new InvalidCalendarConfigurationException(
                "No valid Google Calendar ID found for campus",
                [
                    'campus_id' => $request->campus_id,
                    'campus_name' => $request->campus->name ?? 'Unknown Campus',
                    'gcal_field' => $request->campus->gcal ?? 'Not Set'
                ]
            );
        }

        // Check if event already exists for this request
        if ($request->googleCalendarEvent) {
            // Update the status to scheduled even for existing events
            if ($request->status !== 'scheduled') {
                // Use the InstructionRequestService to update the status
                $instructionRequestService = app(InstructionRequestService::class);
                $updatedRequest = $instructionRequestService->updateInstructionRequest([
                    'status' => 'scheduled'
                ], $request->id);

                // Update the existing event with the updated instruction request
                $request->googleCalendarEvent->instructionRequest = $updatedRequest;

                // Ensure we have the latest version of the request
                $request = $updatedRequest;
            }

            // Continue with the event creation process to update the existing event
        }

        try {
            // Format event data
            $eventData = $this->formatEventData($request);

            // Override with any custom data provided
            $eventData = array_merge($eventData, $customData);

            return DB::transaction(function() use ($request, $eventData, $calendarId) {
                try {
                    // Create a native Google Calendar Event object for more direct control
                    $googleEvent = new Google_Service_Calendar_Event();
                    $googleEvent->setSummary($eventData['event_title']);

                    // Set start time
                    $start = new Google_Service_Calendar_EventDateTime();
                    $start->setDateTime($eventData['start_time_obj']->format(DateTime::RFC3339));
                    $start->setTimeZone($eventData['start_time_obj']->getTimezone()->getName());
                    $googleEvent->setStart($start);

                    // Set end time
                    $end = new Google_Service_Calendar_EventDateTime();
                    $end->setDateTime($eventData['end_time_obj']->format(DateTime::RFC3339));
                    $end->setTimeZone($eventData['end_time_obj']->getTimezone()->getName());
                    $googleEvent->setEnd($end);

                    // Set description and location
                    if (isset($eventData['description'])) {
                        $googleEvent->setDescription($eventData['description']);
                    }

                    if (isset($eventData['location'])) {
                        $googleEvent->setLocation($eventData['location']);
                    }

                    // Add attendees
                    $attendees = [];

                    // Add instructor as attendee if email is available
                    if ($request->instructor && $request->instructor->email) {
                        $instructorAttendee = new Google_Service_Calendar_EventAttendee();
                        $instructorAttendee->setEmail($request->instructor->email);
                        $instructorAttendee->setDisplayName($request->instructor->display_name ?? $request->instructor->name);
                        $attendees[] = $instructorAttendee;
                    }

                    // Add assigned librarian as attendee if available
                    $assignedLibrarian = null;
                    if ($request->detail && $request->detail->assigned_librarian_id) {
                        $assignedLibrarian = User::find($request->detail->assigned_librarian_id);

                        if ($assignedLibrarian && $assignedLibrarian->email) {
                            $librarianAttendee = new Google_Service_Calendar_EventAttendee();
                            $librarianAttendee->setEmail($assignedLibrarian->email);
                            $librarianAttendee->setDisplayName($assignedLibrarian->display_name ?? $assignedLibrarian->name);
                            $attendees[] = $librarianAttendee;
                        }
                    }

                    // Validate that we have an assigned librarian with email for impersonation
                    if (!$assignedLibrarian || !$assignedLibrarian->email) {
                        throw new InvalidCalendarConfigurationException(
                            "Cannot create calendar event: No assigned librarian with valid email found",
                            [
                                'request_id' => $request->id,
                                'assigned_librarian_id' => $request->detail->assigned_librarian_id ?? 'not_set',
                                'assigned_librarian_email' => $assignedLibrarian?->email ?? 'not_found'
                            ]
                        );
                    }

                    // Set the attendees array if we have any
                    if (!empty($attendees)) {
                        $googleEvent->setAttendees($attendees);
                    }

                    // Initialize the Google Calendar Service using assigned librarian's email
                    $calendarService = $this->initializeGoogleCalendarService($assignedLibrarian->email);

                    // If we have an existing event, delete it first
                    if ($request->googleCalendarEvent) {
                        try {
                            // Delete from Google Calendar
                            $calendarService->events->delete(
                                $request->googleCalendarEvent->google_calendar_id,
                                $request->googleCalendarEvent->google_event_id
                            );

                            // Delete local record
                            $request->googleCalendarEvent()->delete();

                        } catch (Google_Service_Exception $deleteException) {
                            Log::warning('CalendarService: Failed to delete existing event', [
                                'error' => $deleteException->getMessage()
                            ]);
                            // Continue with creating new event even if delete fails
                        }
                    }

                    // Insert the event directly using the Google API Client
                    $createdEvent = $calendarService->events->insert(
                        $calendarId,
                        $googleEvent,
                        ['sendUpdates' => 'all', 'conferenceDataVersion' => 0]
                    );

                    // Create a local record for the event using the relationship method
                    $googleCalendarEvent = $request->googleCalendarEvent()->create([
                        'google_event_id' => $createdEvent->getId(),
                        'google_calendar_id' => $calendarId,
                        'librarian_id' => $request->detail->assigned_librarian_id,
                        'campus_id' => $request->campus_id,
                        'event_title' => $eventData['event_title'],
                        'start_time' => $eventData['start_time'],
                        'end_time' => $eventData['end_time'],
                        'description' => $eventData['description'] ?? null,
                        'location' => $eventData['location'] ?? null,
                        'attendees' => json_encode($attendees),
                        'raw_event_data' => json_encode($createdEvent),
                        'html_link' => $createdEvent->getHtmlLink()
                    ]);

                    // Use the injected InstructionRequestService to update the status
                    $instructionRequestService = app(InstructionRequestService::class);
                    $updatedRequest = $instructionRequestService->updateInstructionRequest([
                        'status' => 'scheduled'
                    ], $request->id);

                    // Return the event model and reference the updated request
                    $googleCalendarEvent->instructionRequest = $updatedRequest;
                    return $googleCalendarEvent;

                } catch (Google_Service_Exception $googleException) {
                    // Convert Google API exception to our custom exception
                    throw GoogleCalendarApiException::fromGoogleServiceException($googleException);
                } catch (\Exception $e) {
                    // Log unexpected errors but re-throw them
                    Log::error('CalendarService: Unexpected error creating calendar event', [
                        'request_id' => $request->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });

        } catch (GoogleCalendarApiException $e) {
            // Re-throw Google API exceptions as-is
            throw $e;
        } catch (InvalidCalendarConfigurationException $e) {
            // Re-throw configuration exceptions as-is
            throw $e;
        } catch (\Exception $e) {
            // Log and re-throw unexpected errors
            Log::error('CalendarService: Failed to create Google Calendar event', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a Google Calendar event
     *
     * @param GoogleCalendarEvent $calendarEvent The event record to delete
     * @return array Result of the operation with success status and messages
     */
    public function deleteEvent(GoogleCalendarEvent $calendarEvent): array
    {
        $result = [
            'success' => false,
            'messages' => [],
            'instruction_request_id' => $calendarEvent->instruction_request_id
        ];

        try {
            DB::beginTransaction();

            // Eager load the instruction request to ensure it exists
            $request = $calendarEvent->instructionRequest;

            if (!$request) {
                throw new \Exception('No associated instruction request found');
            }

            // Get the calendar ID from the campus
            $calendarId = null;
            if ($request->campus) {
                $calendarId = $request->campus->getCalendarId();
            }

            if (!$calendarId) {
                Log::warning('CalendarService: No valid calendar ID found');
                // Continue anyway to delete local record
            }

            // Try to delete from Google Calendar
            try {
                // Get the librarian who created the event for impersonation
                $eventLibrarian = null;
                if ($calendarEvent->librarian_id) {
                    $eventLibrarian = User::find($calendarEvent->librarian_id);
                }

                // Validate that we have the event librarian with email for impersonation
                if (!$eventLibrarian || !$eventLibrarian->email) {
                    throw new InvalidCalendarConfigurationException(
                        "Cannot delete calendar event: No event librarian with valid email found",
                        [
                            'event_id' => $calendarEvent->id,
                            'librarian_id' => $calendarEvent->librarian_id ?? 'not_set',
                            'librarian_email' => $eventLibrarian?->email ?? 'not_found'
                        ]
                    );
                }

                // Initialize the Google Calendar service using event librarian's email
                $calendarService = $this->initializeGoogleCalendarService($eventLibrarian->email);

                // Use the event ID and calendar ID to delete the event
                $calendarId = $calendarId ?? $calendarEvent->google_calendar_id; // Fallback to stored ID

                $calendarService->events->delete(
                    $calendarId,
                    $calendarEvent->google_event_id
                );

                $result['messages'][] = 'Google Calendar event deleted successfully.';

                Log::info('CalendarService: Google Calendar event deleted successfully');
            } catch (\Exception $apiException) {
                Log::warning('CalendarService: Error deleting event from Google Calendar', [
                    'error' => $apiException->getMessage()
                ]);
                $result['messages'][] = 'Unable to delete event from Google Calendar.';
                // Continue anyway to delete local record
            }

            // Delete local event record using the relationship method
            try {
                // Log before deletion
                Log::info('CalendarService: Starting deletion process for event', [
                    'event_id' => $calendarEvent->id,
                    'google_event_id' => $calendarEvent->google_event_id
                ]);

                // Use relationship method for deletion - this is the only correct approach
                $request->googleCalendarEvent()->delete();

                // Verify deletion was successful
                $stillExists = GoogleCalendarEvent::where('id', $calendarEvent->id)->exists();

                if (!$stillExists) {
                    $result['messages'][] = 'Local calendar event record deleted successfully.';
                    Log::info('CalendarService: Local calendar event record deleted through relationship');
                } else {
                    throw new \Exception('Record deletion verification failed - record still exists after deletion attempt');
                }
            } catch (\Exception $localDeletionException) {
                Log::warning('CalendarService: Error deleting local calendar event', [
                    'error' => $localDeletionException->getMessage()
                ]);
                $result['messages'][] = 'Unable to delete local calendar event record.';
                throw $localDeletionException; // This will trigger rollback
            }

            // Update request status to 'accepted' using service
            $instructionRequestService = app(InstructionRequestService::class);
            $instructionRequestService->updateInstructionRequest([
                'status' => 'accepted'
            ], $request->id);

            Log::info('CalendarService: Instruction request status updated to accepted');

            DB::commit();

            Log::info('CalendarService: Deletion completed successfully');

            $result['success'] = true;
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('CalendarService: Calendar event deletion failed', [
                'error' => $e->getMessage()
            ]);

            $result['messages'][] = 'Failed to complete calendar event deletion.';
            return $result;
        }
    }

    /**
     * Initialize the Google API Client with correct scopes and authentication
     *
     * @param string $impersonateEmail Email to impersonate (required)
     * @return Google_Service_Calendar The initialized Google Calendar service
     * @throws InvalidCalendarConfigurationException If configuration is invalid
     */
    private function initializeGoogleCalendarService(string $impersonateEmail): Google_Service_Calendar
    {
        // Get credentials path from environment
        $credentialsPath = base_path(env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_JSON_LOCATION'));

        // Ensure credentials file exists
        if (!file_exists($credentialsPath)) {
            throw new InvalidCalendarConfigurationException(
                "Google Calendar service account credentials file not found",
                [
                    'credentials_path' => $credentialsPath,
                    'environment' => app()->environment()
                ]
            );
        }

        // Validate impersonation email is provided
        if (empty($impersonateEmail)) {
            throw new InvalidCalendarConfigurationException(
                "Impersonation email is required for calendar operations",
                ['provided_email' => $impersonateEmail]
            );
        }

        // Initialize Google API Client
        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);

        // Use the specific scope that is authorized in Google Workspace
        $client->setScopes(['https://www.googleapis.com/auth/calendar.events']);

        // Use the provided email for impersonation
        $client->setSubject($impersonateEmail);

        // Create and return the Google Calendar service
        return new Google_Service_Calendar($client);
    }

    /**
     * Test method to verify the service is working correctly
     *
     * @param InstructionRequests $request The instruction request to test with
     * @return array Debug information
     */
    public function testCalendarService(InstructionRequests $request): array
    {
        Log::info('CalendarService: testCalendarService called');

        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') ||
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian']);
        }

        try {
            // Get calendar ID from campus
            $calendarId = null;
            if ($request->campus) {
                $calendarId = $request->campus->getCalendarId();
            }

            if (!$calendarId) {
                return [
                    'success' => false,
                    'timestamp' => now()->toDateTimeString(),
                    'request_id' => $request->id,
                    'error' => 'No valid calendar ID found for campus',
                    'campus_id' => $request->campus_id,
                    'campus_name' => $request->campus->name ?? 'Unknown Campus'
                ];
            }

            // Get assigned librarian for testing
            $assignedLibrarian = null;
            if ($request->detail && $request->detail->assigned_librarian_id) {
                $assignedLibrarian = User::find($request->detail->assigned_librarian_id);
            }

            if (!$assignedLibrarian || !$assignedLibrarian->email) {
                return [
                    'success' => false,
                    'timestamp' => now()->toDateTimeString(),
                    'request_id' => $request->id,
                    'error' => 'No assigned librarian with valid email found for testing',
                    'assigned_librarian_id' => $request->detail->assigned_librarian_id ?? 'not_set',
                    'assigned_librarian_email' => $assignedLibrarian?->email ?? 'not_found'
                ];
            }

            // Attempt to initialize Google Calendar API Client using assigned librarian
            $calendarService = $this->initializeGoogleCalendarService($assignedLibrarian->email);

            $result = [
                'success' => true,
                'timestamp' => now()->toDateTimeString(),
                'request_id' => $request->id,
                'request_status' => $request->status,
                'calendar_id' => $calendarId,
                'detail_present' => $request->detail ? true : false,
                'detail_datetime' => $request->detail?->instruction_datetime,
                'detail_duration' => $request->detail?->instruction_duration,
                'instructor_present' => $request->instructor ? true : false,
                'campus_present' => $request->campus ? true : false,
                'campus_name' => $request->campus?->name,
                'googleCalendarService_class' => get_class($calendarService),
                'assigned_librarian_id' => $assignedLibrarian->id,
                'assigned_librarian_email' => $assignedLibrarian->email,
                'assigned_librarian_name' => $assignedLibrarian->display_name ?? $assignedLibrarian->name,
                'current_user_email' => Auth::user()?->email ?? 'Not authenticated'
            ];

            return $result;

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'timestamp' => now()->toDateTimeString(),
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e)
            ];

            // Log error
            Log::error('CalendarService: testCalendarService failed', $error);

            return $error;
        }
    }

    /**
     * Format event data from an instruction request
     *
     * @param InstructionRequests $request The instruction request
     * @return array Formatted event data for Google Calendar
     */
    protected function formatEventData(InstructionRequests $request): array
    {
        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') ||
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian']);
        }

        // Get class name from department and course number
        $className = trim("{$request->department} {$request->course_number}");

        // Get instructor name
        $instructorName = $request->instructor ? $request->instructor->name : 'Unknown Instructor';

        // Get librarian name - use assigned librarian from detail, not the originally requested librarian
        $assignedLibrarian = null;
        if ($request->detail && $request->detail->assigned_librarian_id) {
            // Load the assigned librarian from the detail relationship
            $assignedLibrarian = User::find($request->detail->assigned_librarian_id);
        }
        $librarianName = $assignedLibrarian
            ? ($assignedLibrarian->display_name ?? $assignedLibrarian->name)
            : 'Unknown Librarian';

        // Format title as requested: [class name] [instructor name] - [librarian name]
        $title = "{$className} {$instructorName} - {$librarianName}";

        // Get start time from instruction_datetime in the details
        $startTime = $request->detail->instruction_datetime ?
            Carbon::parse($request->detail->instruction_datetime) :
            Carbon::now()->addDays(1)->setTime(9, 0);

        // Calculate end time based on instruction_duration from details
        $duration = $request->detail->instruction_duration ?? $request->duration ?? 60; // Default to 60 minutes if not set
        $endTime = (clone $startTime)->addMinutes(intval($duration));

        // Format location: [campus] - [room]
        $campusName = $request->campus ? $request->campus->name : 'Unknown Campus';
        $room = $request->detail && $request->detail->room ? $request->detail->room : '';
        $location = $room ? "{$campusName} - {$room}" : $campusName;

        // Create a concise description
        $studentCount = $request->number_of_students ?? 'Unknown number of';
        $instructionDescription = $request->library_instruction_description ?? '';

        // Truncate instruction description to first sentence if it's too long
        if (strlen($instructionDescription) > 100) {
            $firstSentence = preg_match('/^(.*?[.!?])\s/s', $instructionDescription, $matches) ?
                $matches[1] : substr($instructionDescription, 0, 100) . '...';
            $instructionDescription = $firstSentence;
        }

        $description = "Library instruction session for {$className} with {$studentCount} students.";
        if ($instructionDescription) {
            $description .= " Topic: {$instructionDescription}";
        }

        // Store both the date string and Carbon object
        // Format for HTML datetime-local input must be: YYYY-MM-DDTHH:MM
        $startTimeFormatted = $startTime->format('Y-m-d\TH:i');
        $endTimeFormatted = $endTime->format('Y-m-d\TH:i');

        return [
            'event_title' => $title,
            'start_time' => $startTimeFormatted,
            'end_time' => $endTimeFormatted,
            'start_time_obj' => $startTime,
            'end_time_obj' => $endTime,
            'location' => $location,
            'description' => $description
        ];
    }
}
