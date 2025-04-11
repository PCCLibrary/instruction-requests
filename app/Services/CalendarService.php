<?php

namespace App\Services;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

/**
 * Service for handling Google Calendar integration
 */
class CalendarService
{
    /**
     * Extract Google Calendar ID from a share URL
     */
    public function extractCalendarId(string $url): ?string
    {
        // First, check if the input is already a valid calendar ID
        if (preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $url) ||
            preg_match('/^c_[a-zA-Z0-9]+(@group\.calendar\.google\.com)?$/', $url)) {
            return $url;
        }

        // Try to extract from standard URL patterns
        $patterns = [
            '/[?&](?:src|cid)=([^&]+)/',
            '/calendar\/u\/\d+\/r\/[^\/]+\/\d+\/\d+\/\d+\?cid=([^&]+)/',
            '/calendar\/embed\?src=([^&]+)/',
            '/[?&]cid=c_([a-zA-Z0-9]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $extracted = urldecode(end($matches));

                // Validate the extracted ID
                if (preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $extracted) ||
                    preg_match('/^c_[a-zA-Z0-9]+(@group\.calendar\.google\.com)?$/', $extracted)) {
                    return $extracted;
                }
            }
        }

        // Log failure and return null
        Log::warning('Failed to extract valid calendar ID from URL', ['url' => $url]);
        return null;
    }

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
     * @param string|null $calendarId Specific calendar ID to use (optional)
     * @return GoogleCalendarEvent The created calendar event record
     * @throws InvalidCalendarConfigurationException If calendar configuration is invalid
     * @throws \Exception For unexpected errors during event creation
     */
    public function createEvent(InstructionRequests $request, array $customData = [], ?string $calendarId = null): GoogleCalendarEvent
    {
        // Critical logging to ensure we can see this method is being called
        Log::critical('CalendarService: createEvent method CALLED', [
            'timestamp' => now()->toDateTimeString(),
            'request_id' => $request->id,
            'user_id' => auth()->id() ?? 'not-authenticated'
        ]);
        
        Log::info('CalendarService: Starting createEvent method', [
            'request_id' => $request->id,
            'custom_data_keys' => array_keys($customData),
            'calendar_id' => $calendarId
        ]);
        
        Log::debug('CalendarService: Extended debug information', [
            'request_status' => $request->status,
            'detail_present' => $request->detail ? 'Yes' : 'No',
            'instructor_present' => $request->instructor ? 'Yes' : 'No',
            'campus_present' => $request->campus ? 'Yes' : 'No',
            'detail_datetime' => $request->detail?->instruction_datetime,
            'detail_duration' => $request->detail?->instruction_duration,
            'assigned_librarian_id' => $request->detail?->assigned_librarian_id,
            'transaction_active' => DB::transactionLevel() > 0 ? 'Yes' : 'No'
        ]);

        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') ||
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian') ||
            !$request->relationLoaded('classes')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian', 'classes']);
        }

        // Retrieve calendar ID from campus if not provided
        if (!$calendarId && $request->campus) {
            $calendarId = $request->campus->getCalendarId();
        }

        // Strict validation - throw exception if no valid calendar ID
        if (!$calendarId) {
            throw new InvalidCalendarConfigurationException(
                "No valid Google Calendar ID found for campus",
                [
                    'campus_id' => $request->campus_id,
                    'campus_name' => $request->campus->name,
                    'gcal_field' => $request->campus->gcal
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
                
                // Create the event in Google Calendar
                $event = new Event;

                // Set the event properties
                $event->name = $eventData['event_title'];
                $event->startDateTime = $eventData['start_time_obj'];
                $event->endDateTime = $eventData['end_time_obj'];

                if (isset($eventData['description'])) {
                    $event->description = $eventData['description'];
                }

                if (isset($eventData['location'])) {
                    $event->location = $eventData['location'];
                }

                // Add instructor as attendee if email is available
                if ($request->instructor && $request->instructor->email) {
                    $event->addAttendee(['email' => $request->instructor->email]);
                }

                // Add assigned librarian as attendee if available
                $assignedLibrarian = null;
                if ($request->detail && $request->detail->assigned_librarian_id) {
                    $assignedLibrarian = User::find($request->detail->assigned_librarian_id);
                }

                if ($assignedLibrarian && $assignedLibrarian->email) {
                    $event->addAttendee(['email' => $assignedLibrarian->email]);
                }

                // Log that we're about to call the Google Calendar API
                Log::debug('CalendarService: Calling Google Calendar API to create event', [
                    'calendar_id' => $calendarId,
                    'event_name' => $event->name,
                    'start_time' => $event->startDateTime->format('Y-m-d H:i:s'),
                    'end_time' => $event->endDateTime->format('Y-m-d H:i:s'),
                    'attendees' => $event->attendees ?? []
                ]);
                
                // Save the event to Google Calendar
                try {
                    // Check that the calendar ID is valid before trying to save
                    if (empty($calendarId)) {
                        throw new InvalidCalendarConfigurationException(
                            "Empty calendar ID when trying to create event",
                            [
                                'calendar_id' => $calendarId,
                                'request_id' => $request->id,
                                'campus_id' => $request->campus_id,
                                'campus_name' => $request->campus?->name
                            ]
                        );
                    }
                    
                    // Log the attempt with detailed parameters
                    Log::info('CalendarService: About to call Google Calendar API', [
                        'calendar_id' => $calendarId,
                        'event_title' => $event->name,
                        'start_time' => $event->startDateTime->format('Y-m-d H:i:s'),
                        'end_time' => $event->endDateTime->format('Y-m-d H:i:s'),
                        'attendee_count' => count($event->attendees ?? [])
                    ]);
                    
                    // Save the event to Google Calendar
                    $createdEvent = $event->save(null, $calendarId);
                    
                    Log::debug('CalendarService: Successfully created event in Google Calendar', [
                        'google_event_id' => $createdEvent->id,
                        'event_url' => $createdEvent->htmlLink ?? 'Not available'
                    ]);
                } catch (InvalidCalendarConfigurationException $configException) {
                    // Re-throw configuration exceptions to be handled specifically
                    Log::error('CalendarService: Invalid calendar configuration', [
                        'error' => $configException->getMessage(),
                        'context' => $configException->getContext()
                    ]);
                    throw $configException;
                } catch (\Exception $apiException) {
                    Log::error('CalendarService: Failed to create event in Google Calendar', [
                        'error' => $apiException->getMessage(),
                        'code' => $apiException->getCode(),
                        'calendar_id' => $calendarId,
                        'class' => get_class($apiException),
                        'trace' => $apiException->getTraceAsString()
                    ]);
                    throw $apiException;
                }

                // Log about creating local record
                Log::info('CalendarService: Creating local GoogleCalendarEvent record', [
                    'request_id' => $request->id,
                    'google_event_id' => $createdEvent->id,
                    'transaction_level' => DB::transactionLevel()
                ]);
                
                try {
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
                        'attendees' => json_encode($event->attendees ?? []),
                        'raw_event_data' => json_encode($createdEvent->toArray())
                    ]);
                    
                    Log::info('CalendarService: Local GoogleCalendarEvent record created successfully', [
                        'event_id' => $googleCalendarEvent->id,
                        'google_event_id' => $googleCalendarEvent->google_event_id
                    ]);
                } catch (\Exception $dbException) {
                    Log::error('CalendarService: Error creating local GoogleCalendarEvent record', [
                        'error' => $dbException->getMessage(),
                        'trace' => $dbException->getTraceAsString()
                    ]);
                    throw $dbException;
                }

                // Log about updating request status
                Log::info('CalendarService: Updating instruction request status to scheduled', [
                    'request_id' => $request->id,
                    'old_status' => $request->status,
                    'new_status' => 'scheduled'
                ]);
                
                try {
                    // Update request status to 'scheduled'
                    $freshRequest = InstructionRequests::find($request->id);
                    $freshRequest->status = 'scheduled';
                    $freshRequest->save();
                    
                    Log::info('CalendarService: Instruction request status updated successfully', [
                        'request_id' => $request->id,
                        'status' => $freshRequest->status
                    ]);
                } catch (\Exception $statusException) {
                    Log::error('CalendarService: Error updating instruction request status', [
                        'error' => $statusException->getMessage(),
                        'trace' => $statusException->getTraceAsString()
                    ]);
                    throw $statusException;
                }

                Log::info('CalendarService: Event created successfully', [
                    'request_id' => $request->id,
                    'event_id' => $googleCalendarEvent->id,
                    'google_event_id' => $createdEvent->id
                ]);

                return $googleCalendarEvent;
            });

        } catch (\Exception $e) {
            Log::error('CalendarService: Failed to create Google Calendar event', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Delete a Google Calendar event
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

            // Try to delete from Google Calendar
            try {
                $googleEvent = Event::find($calendarEvent->google_event_id, $calendarEvent->google_calendar_id);

                if ($googleEvent) {
                    $googleEvent->delete();
                    $result['messages'][] = 'Google Calendar event deleted successfully.';
                } else {
                    $result['messages'][] = 'Google Calendar event not found in calendar.';
                }
            } catch (\Exception $apiException) {
                Log::warning('Error deleting event from Google Calendar', [
                    'event_id' => $calendarEvent->google_event_id,
                    'error' => $apiException->getMessage()
                ]);
                $result['messages'][] = 'Unable to delete event from Google Calendar.';
            }

            // Delete local event record
            try {
                $calendarEvent->delete();
                $result['messages'][] = 'Local calendar event record deleted.';
            } catch (\Exception $localDeletionException) {
                Log::warning('Error deleting local calendar event', [
                    'event_id' => $calendarEvent->id,
                    'error' => $localDeletionException->getMessage()
                ]);
                $result['messages'][] = 'Unable to delete local calendar event record.';
            }

            // Update request status to 'accepted' using service
            $instructionRequestService = app(InstructionRequestService::class);
            $instructionRequestService->updateInstructionRequest([
                'status' => 'accepted'
            ], $request->id);

            DB::commit();

            $result['success'] = true;
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Comprehensive calendar event deletion failed', [
                'calendar_event_id' => $calendarEvent->id,
                'error' => $e->getMessage()
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
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian') ||
            !$request->relationLoaded('classes')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian', 'classes']);
        }
        
        try {
            // Get calendar ID from campus
            $calendarId = null;
            if ($request->campus) {
                $calendarId = $request->campus->getCalendarId();
            }
            
            // Attempt to initialize Spatie Google Calendar client
            $event = new Event;
            
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
                'event_class' => get_class($event)
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
     */
    protected function formatEventData(InstructionRequests $request): array
    {
        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') ||
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian') ||
            !$request->relationLoaded('classes')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian', 'classes']);
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
            Carbon::parse($request->detail->instruction_datetime, 'America/Los_Angeles') :
            Carbon::now('America/Los_Angeles')->addDays(1)->setTime(9, 0);

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

        // Store both the date string (for HTML input) and Carbon object (for API)
        // Format for HTML datetime-local input must be: YYYY-MM-DDTHH:MM
        $startTimeFormatted = $startTime->format('Y-m-d\TH:i');
        $endTimeFormatted = $endTime->format('Y-m-d\TH:i');

        // Log the date/time values for debugging
        Log::info('Formatted event data', [
            'title' => $title,
            'start_time_html' => $startTimeFormatted,
            'end_time_html' => $endTimeFormatted,
            'location' => $location
        ]);

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
