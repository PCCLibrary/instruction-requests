<?php

namespace App\Services;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;
use Spatie\GoogleCalendar\GoogleCalendar;

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
     * @throws \Exception For unexpected errors during event creation
     */
    public function createEvent(InstructionRequests $request, array $customData = []): GoogleCalendarEvent
    {
        // Critical logging to ensure we can see this method is being called
        Log::critical('CalendarService: createEvent method CALLED', [
            'timestamp' => now()->toDateTimeString(),
            'request_id' => $request->id,
            'user_id' => auth()->id() ?? 'not-authenticated'
        ]);

        Log::info('CalendarService: Starting createEvent method', [
            'request_id' => $request->id,
            'custom_data_keys' => array_keys($customData)
        ]);

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
            Log::info('Event already exists for request', [
                'request_id' => $request->id,
                'event_id' => $request->googleCalendarEvent->google_event_id
            ]);
            return $request->googleCalendarEvent;
        }

        try {
            // Format event data
            $eventData = $this->formatEventData($request);

            // Override with any custom data provided
            $eventData = array_merge($eventData, $customData);

            return DB::transaction(function() use ($request, $eventData, $calendarId) {
                // Log transaction start
                Log::info('CalendarService: Starting transaction to create event', [
                    'request_id' => $request->id,
                    'transaction_level' => DB::transactionLevel()
                ]);

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

                    // Set the attendees array if we have any
                    if (!empty($attendees)) {
                        $googleEvent->setAttendees($attendees);
                    }

                    // Get a GoogleCalendar instance with the correct calendar ID
                    $googleCalendar = App::make(GoogleCalendar::class, [
                        'calendarId' => $calendarId
                    ]);

                    // Log that we're about to call the Google Calendar API
                    Log::debug('CalendarService: Calling Google Calendar API to create event', [
                        'calendar_id' => $calendarId,
                        'event_name' => $eventData['event_title'],
                        'start_time' => $eventData['start_time_obj']->format('Y-m-d H:i:s'),
                        'end_time' => $eventData['end_time_obj']->format('Y-m-d H:i:s'),
                        'attendees_count' => count($attendees)
                    ]);

                    // Insert the event directly using the insertEvent method
                    $createdEvent = $googleCalendar->insertEvent($googleEvent);

                    Log::debug('CalendarService: Successfully created event in Google Calendar', [
                        'google_event_id' => $createdEvent->id,
                        'event_url' => $createdEvent->htmlLink ?? 'Not available'
                    ]);

                    // Create a local record for the event
                    $googleCalendarEvent = GoogleCalendarEvent::create([
                        'instruction_request_id' => $request->id,
                        'google_event_id' => $createdEvent->id,
                        'google_calendar_id' => $calendarId,
                        'librarian_id' => $request->detail->assigned_librarian_id,
                        'campus_id' => $request->campus_id,
                        'event_title' => $eventData['event_title'],
                        'start_time' => $eventData['start_time'],
                        'end_time' => $eventData['end_time'],
                        'description' => $eventData['description'] ?? null,
                        'location' => $eventData['location'] ?? null,
                        'attendees' => json_encode($attendees),
                        'raw_event_data' => json_encode($createdEvent)
                    ]);

                    Log::info('CalendarService: Local GoogleCalendarEvent record created successfully', [
                        'event_id' => $googleCalendarEvent->id,
                        'google_event_id' => $googleCalendarEvent->google_event_id
                    ]);

                    // Use the injected InstructionRequestService to update the status
                    $instructionRequestService = app(InstructionRequestService::class);
                    $instructionRequestService->updateInstructionRequest([
                        'status' => 'scheduled'
                    ], $request->id);

                    Log::info('CalendarService: Instruction request status updated successfully', [
                        'request_id' => $request->id,
                        'status' => 'scheduled'
                    ]);

                    return $googleCalendarEvent;

                } catch (\Exception $e) {
                    Log::error('CalendarService: Failed to create Google Calendar event', [
                        'request_id' => $request->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

        } catch (\Exception $e) {
            Log::error('CalendarService: Failed to create Google Calendar event (outer try/catch)', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

        Log::info('CalendarService: Starting deleteEvent method', [
            'event_id' => $calendarEvent->id,
            'google_event_id' => $calendarEvent->google_event_id,
            'instruction_request_id' => $calendarEvent->instruction_request_id
        ]);

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
                Log::warning('CalendarService: No valid calendar ID found for campus', [
                    'campus_id' => $request->campus_id,
                    'campus_name' => $request->campus->name ?? 'Unknown Campus'
                ]);
                // Continue anyway to delete local record
            }

            // Try to delete from Google Calendar
            try {
                // Get a GoogleCalendar instance with the correct calendar ID
                $googleCalendar = App::make(GoogleCalendar::class, [
                    'calendarId' => $calendarId ?? $calendarEvent->google_calendar_id // Fallback to stored ID
                ]);

                $googleCalendar->deleteEvent($calendarEvent->google_event_id);
                $result['messages'][] = 'Google Calendar event deleted successfully.';

                Log::info('CalendarService: Google Calendar event deleted successfully', [
                    'google_event_id' => $calendarEvent->google_event_id,
                    'calendar_id' => $calendarId
                ]);
            } catch (\Exception $apiException) {
                Log::warning('CalendarService: Error deleting event from Google Calendar', [
                    'event_id' => $calendarEvent->google_event_id,
                    'error' => $apiException->getMessage()
                ]);
                $result['messages'][] = 'Unable to delete event from Google Calendar.';
                // Continue anyway to delete local record
            }

            // Delete local event record
            try {
                $calendarEvent->delete();
                $result['messages'][] = 'Local calendar event record deleted.';

                Log::info('CalendarService: Local calendar event record deleted', [
                    'event_id' => $calendarEvent->id
                ]);
            } catch (\Exception $localDeletionException) {
                Log::warning('CalendarService: Error deleting local calendar event', [
                    'event_id' => $calendarEvent->id,
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

            Log::info('CalendarService: Instruction request status updated to accepted', [
                'request_id' => $request->id
            ]);

            DB::commit();

            $result['success'] = true;
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('CalendarService: Comprehensive calendar event deletion failed', [
                'calendar_event_id' => $calendarEvent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $result['messages'][] = 'Failed to complete calendar event deletion.';
            return $result;
        }
    }

    /**
     * Test method to verify the service is working correctly
     *
     * @param InstructionRequests $request The instruction request to test with
     * @return array Debug information
     */
    public function testCalendarService(InstructionRequests $request): array
    {
        Log::critical('CalendarService: testCalendarService called', [
            'timestamp' => now()->toDateTimeString(),
            'request_id' => $request->id
        ]);

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

            // Attempt to initialize Spatie Google Calendar client
            $googleCalendar = App::make(GoogleCalendar::class, [
                'calendarId' => $calendarId
            ]);

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
                'googleCalendar_class' => get_class($googleCalendar)
            ];

            // Log result
            Log::critical('CalendarService: testCalendarService succeeded', $result);

            return $result;

        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'timestamp' => now()->toDateTimeString(),
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ];

            // Log error
            Log::critical('CalendarService: testCalendarService failed', $error);

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
