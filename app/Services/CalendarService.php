<?php

namespace App\Services;

use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

/**
 * Service for handling Google Calendar integration
 * 
 * This service handles all interactions with the Google Calendar API
 * including creation and deletion of events, and extracting calendar IDs
 * from Google Calendar URLs.
 */
class CalendarService
{
    /**
     * Create a Google Calendar event for an instruction request.
     *
     * @param InstructionRequests $request The instruction request
     * @param array $customData Optional override data for the event
     * @param string|null $calendarId Google Calendar ID, if null tries to extract from campus
     * @return GoogleCalendarEvent|null The created event or null on failure
     */
    public function createEvent(InstructionRequests $request, array $customData = [], ?string $calendarId = null): ?GoogleCalendarEvent
    {
        // Load relationships if not already loaded
        if (!$request->relationLoaded('instructor') || !$request->relationLoaded('detail') || 
            !$request->relationLoaded('campus') || !$request->relationLoaded('librarian') || 
            !$request->relationLoaded('classes')) {
            $request->load(['instructor', 'detail', 'campus', 'librarian', 'classes']);
        }
        
        // Check if calendar ID is provided, otherwise try to extract from campus
        if (!$calendarId && $request->campus && $request->campus->gcal) {
            $calendarId = $this->extractCalendarId($request->campus->gcal);
        }
        
        // If no calendar ID could be determined, log error and return null
        if (!$calendarId) {
            Log::error('No calendar ID provided or could be extracted from campus', [
                'request_id' => $request->id,
                'campus_id' => $request->campus_id,
                'campus_url' => $request->campus->gcal ?? 'not set'
            ]);
            return null;
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
            
            return null;
        }
    }
    
    /**
     * Delete a Google Calendar event.
     *
     * @param GoogleCalendarEvent $calendarEvent The event to delete
     * @return bool True if successfully deleted, false otherwise
     */
    public function deleteEvent(GoogleCalendarEvent $calendarEvent): bool
    {
        if (!$calendarEvent->google_event_id || !$calendarEvent->google_calendar_id) {
            Log::warning('Incomplete Google Calendar event data for deletion', [
                'id' => $calendarEvent->id,
                'google_event_id' => $calendarEvent->google_event_id,
                'google_calendar_id' => $calendarEvent->google_calendar_id
            ]);
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            // Get the instruction request
            $request = $calendarEvent->instructionRequest;
            
            // Find the event in Google Calendar
            $event = Event::find($calendarEvent->google_event_id, $calendarEvent->google_calendar_id);
            
            // Delete from Google Calendar if found
            if ($event) {
                $event->delete();
                Log::info('Deleted event from Google Calendar', [
                    'google_event_id' => $calendarEvent->google_event_id,
                    'calendar_id' => $calendarEvent->google_calendar_id
                ]);
            } else {
                Log::warning('Event not found in Google Calendar, continuing with local deletion', [
                    'google_event_id' => $calendarEvent->google_event_id,
                    'calendar_id' => $calendarEvent->google_calendar_id
                ]);
            }
            
            // Delete the local record
            $calendarEvent->delete();
            
            // Update request status to 'accepted' if it was 'scheduled'
            if ($request && $request->status === 'scheduled') {
                $request->status = 'accepted';
                $request->save();
            }
            
            DB::commit();
            
            Log::info('Google Calendar event deleted successfully', [
                'calendar_event_id' => $calendarEvent->id,
                'request_id' => $request ? $request->id : null
            ]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete Google Calendar event', [
                'calendar_event_id' => $calendarEvent->id,
                'google_event_id' => $calendarEvent->google_event_id,
                'google_calendar_id' => $calendarEvent->google_calendar_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Extract Google Calendar ID from a Google Calendar share URL.
     *
     * @param string $url The Google Calendar share URL
     * @return string|null The extracted calendar ID or null if extraction fails
     */
    public function extractCalendarId(string $url): ?string
    {
        try {
            // Pattern for standard calendar URLs
            // Examples:
            // - https://calendar.google.com/calendar/u/0/embed?src=example@gmail.com
            // - https://calendar.google.com/calendar/u/0/r?cid=example@group.calendar.google.com
            if (preg_match('/[?&](?:src|cid)=([^&]+)/', $url, $matches)) {
                return urldecode($matches[1]);
            }
            
            // Pattern for newer calendar URLs
            // Example: https://calendar.google.com/calendar/u/0/r/week/2023/1/1?cid=example@group.calendar.google.com
            if (preg_match('/calendar\/u\/\d+\/r\/[^\/]+\/\d+\/\d+\/\d+\?cid=([^&]+)/', $url, $matches)) {
                return urldecode($matches[1]);
            }
            
            // Pattern for calendar IDs in path
            // Example: https://calendar.google.com/calendar/embed?src=example@group.calendar.google.com
            if (preg_match('/calendar\/embed\?src=([^&]+)/', $url, $matches)) {
                return urldecode($matches[1]);
            }
            
            // Direct calendar ID format (for cases where ID is pasted directly)
            if (preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $url)) {
                return $url;
            }
            
            // Calendar ID in alphanumeric format
            // Example: https://calendar.google.com/calendar/u/0/r?cid=c_1a2b3c4d5e6f7g8h
            if (preg_match('/[?&]cid=c_([a-zA-Z0-9]+)/', $url, $matches)) {
                return 'c_' . $matches[1];
            }
            
            Log::warning('Failed to extract calendar ID from URL', ['url' => $url]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error extracting calendar ID', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get event data from an instruction request that can be used to pre-populate a form.
     *
     * @param InstructionRequests $request The instruction request
     * @return array The formatted event data
     */
    public function getEventFormData(InstructionRequests $request): array
    {
        return $this->formatEventData($request);
    }
    
    /**
     * Format event data from an instruction request.
     *
     * @param InstructionRequests $request The instruction request
     * @return array The formatted event data
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
        
        // Get start time from preferred_datetime
        $startTime = $request->preferred_datetime ? 
            Carbon::parse($request->preferred_datetime, 'America/Los_Angeles') : 
            Carbon::now('America/Los_Angeles')->addDays(1)->setTime(9, 0);
        
        // Calculate end time based on duration
        $duration = $request->duration ?? 60; // Default to 60 minutes if not set
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
