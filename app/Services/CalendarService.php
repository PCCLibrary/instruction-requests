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
     */
    public function createEvent(InstructionRequests $request, array $customData = [], ?string $calendarId = null): GoogleCalendarEvent
    {
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

            // Create the event in Google Calendar
            $event = new Event;

            // Set the event properties
            $event->name = $eventData['event_title'];
            $event->startDateTime = Carbon::parse($eventData['start_time'], 'America/Los_Angeles');
            $event->endDateTime = Carbon::parse($eventData['end_time'], 'America/Los_Angeles');

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

            // Add librarian as attendee if available
            if ($request->librarian && $request->librarian->email) {
                $event->addAttendee(['email' => $request->librarian->email]);
            }

            // Save the event to Google Calendar with the specified calendar ID
            DB::beginTransaction();

            try {
                $createdEvent = $event->save(null, $calendarId);

                // Create a local record for the event
                $googleCalendarEvent = GoogleCalendarEvent::create([
                    'instruction_request_id' => $request->id,
                    'google_event_id' => $createdEvent->id,
                    'google_calendar_id' => $calendarId,
                    'librarian_id' => $request->librarian_id,
                    'campus_id' => $request->campus_id,
                    'event_title' => $eventData['event_title'],
                    'start_time' => $eventData['start_time'],
                    'end_time' => $eventData['end_time'],
                    'description' => $eventData['description'] ?? null,
                    'location' => $eventData['location'] ?? null,
                    'attendees' => json_encode($event->attendees ?? []),
                    'raw_event_data' => json_encode($createdEvent->toArray())
                ]);

                // Update request status to 'scheduled'
                $request->status = 'scheduled';
                $request->save();

                DB::commit();

                Log::info('Google Calendar event created successfully', [
                    'request_id' => $request->id,
                    'google_event_id' => $createdEvent->id,
                    'calendar_id' => $calendarId
                ]);

                return $googleCalendarEvent;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e; // Re-throw for outer catch block
            }
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Propagate the exception
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

        // Get librarian name
        $librarianName = $request->librarian ? ($request->librarian->display_name ?? $request->librarian->name) : 'Unknown Librarian';

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

        return [
            'event_title' => $title,
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'location' => $location,
            'description' => $description
        ];
    }
}
