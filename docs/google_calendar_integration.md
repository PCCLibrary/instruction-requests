# Google Calendar Integration Implementation Plan

## Overview

This implementation plan outlines the steps to integrate Google Calendar with the Library Instruction Request System. The integration will allow librarians to manually create calendar events after accepting instruction requests, marking them as "scheduled" in the system.

## Implementation Goals

- Allow librarians to create Google Calendar events from accepted instruction requests
- Add "scheduled" and "rejected" status options to the request workflow
- Track created events and handle deletions within the application 
- Provide integration with the existing notification system

## Prerequisites

- ✅ Google API credentials for service account
- ✅ Spatie Laravel Google Calendar package installed
- ✅ Package configuration published

## 1. Database Modifications

### 1.1 New Request Statuses

Add two new status values to the existing status field in the `instruction_requests` table:
- `rejected`: When a librarian rejects an assigned request (currently reverts to received)
- `scheduled`: When a Google Calendar event has been created

### 1.2 Google Calendar Events Table

Create a new migration for the `google_calendar_events` table:

```php
Schema::create('google_calendar_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('instruction_request_id')->constrained()->onDelete('cascade');
    $table->string('google_event_id')->nullable()->index();
    $table->string('google_calendar_id');
    $table->timestamps();
    $table->softDeletes();
});
```

## 2. Model Implementation

### 2.1 Campus Model Enhancement

Add a method to extract the calendar ID from the campus's Google Calendar share URL:

```php
/**
 * Extract Google Calendar ID from share URL
 *
 * @return string|null
 */
public function extractCalendarId(): ?string
{
    if (empty($this->gcal)) {
        return null;
    }
    
    try {
        // Extract calendar ID using regex patterns for different URL formats
        if (preg_match('/calendar\/([a-zA-Z0-9]+(@group\.calendar\.google\.com)?)/', $this->gcal, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/calendars\/([^\/]+)/', $this->gcal, $matches)) {
            return urldecode($matches[1]);
        }
        
        return null;
    } catch (\Exception $e) {
        Log::error('Failed to extract Calendar ID', [
            'campus_id' => $this->id,
            'url' => $this->gcal,
            'error' => $e->getMessage()
        ]);
        return null;
    }
}
```

### 2.2 Google Calendar Event Model

Create a new model for tracking calendar events:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleCalendarEvent extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instruction_request_id',
        'google_event_id',
        'google_calendar_id',
    ];
    
    /**
     * Get the instruction request that owns this event.
     */
    public function instructionRequest()
    {
        return $this->belongsTo(InstructionRequests::class, 'instruction_request_id');
    }
}
```

### 2.3 InstructionRequests Model Updates

Add the relationship to Google Calendar events:

```php
/**
 * Get the Google Calendar event associated with this request.
 */
public function googleCalendarEvent()
{
    return $this->hasOne(GoogleCalendarEvent::class, 'instruction_request_id');
}
```

## 3. Calendar Service Implementation

Create a service class to handle calendar interactions:

```php
namespace App\Services;

use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class CalendarService
{
    /**
     * Create a Google Calendar event for an instruction request.
     *
     * @param InstructionRequests $request The instruction request
     * @param array $formData Form data for the event
     * @param string $calendarId Google Calendar ID
     * @return GoogleCalendarEvent|null
     */
    public function createEventFromData(InstructionRequests $request, array $formData, string $calendarId): ?GoogleCalendarEvent
    {
        // Check if event already exists for this request
        if ($request->googleCalendarEvent) {
            Log::debug('Event already exists for request', [
                'request_id' => $request->id,
                'event_id' => $request->googleCalendarEvent->google_event_id
            ]);
            return $request->googleCalendarEvent;
        }
        
        try {
            // Create a new Event
            $event = new Event;
            
            // Set event properties
            $event->name = $formData['eventName'];
            $event->startDateTime = Carbon::parse($formData['startTime'], 'America/Los_Angeles');
            $event->endDateTime = Carbon::parse($formData['endTime'], 'America/Los_Angeles');
            $event->description = $formData['description'];
            
            // Add location if provided
            if (!empty($formData['location'])) {
                $event->location = $formData['location'];
            }
            
            // Add attendees
            if (!empty($formData['instructorEmail'])) {
                $event->addAttendee(['email' => $formData['instructorEmail']]);
            }
            
            if (!empty($formData['librarianEmail'])) {
                $event->addAttendee(['email' => $formData['librarianEmail']]);
            }
            
            // Disable Google Meet
            $event->guestsCanInviteOthers = false;
            $event->conferenceData = ['createRequest' => ['requestId' => null]];
            
            // Enable notifications
            $event->sendNotifications = true;
            
            // Create the event on Google Calendar
            $createdEvent = $event->save(null, $calendarId);
            
            // Get the Google event ID
            $googleEventId = $createdEvent->id;
            
            // Create and return the local mapping record
            $googleCalendarEvent = GoogleCalendarEvent::create([
                'instruction_request_id' => $request->id,
                'google_event_id' => $googleEventId,
                'google_calendar_id' => $calendarId,
            ]);
            
            Log::info('Created Google Calendar event', [
                'request_id' => $request->id,
                'google_event_id' => $googleEventId,
                'calendar_id' => $calendarId
            ]);
            
            return $googleCalendarEvent;
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event', [
                'request_id' => $request->id,
                'calendar_id' => $calendarId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Delete a Google Calendar event for an instruction request.
     *
     * @param InstructionRequests $request
     * @return bool
     */
    public function deleteEvent(InstructionRequests $request): bool
    {
        // Check if there's an event to delete
        if (!$request->googleCalendarEvent) {
            Log::warning('No Google Calendar event found for request', [
                'request_id' => $request->id
            ]);
            return false;
        }
        
        try {
            $googleEventId = $request->googleCalendarEvent->google_event_id;
            $calendarId = $request->googleCalendarEvent->google_calendar_id;
            
            // Get the event from Google Calendar
            $event = Event::find($googleEventId, $calendarId);
            
            if (!$event) {
                Log::warning('Google Calendar event not found', [
                    'request_id' => $request->id,
                    'google_event_id' => $googleEventId,
                    'calendar_id' => $calendarId
                ]);
                
                // Delete the local record even if the Google event is not found
                $request->googleCalendarEvent->delete();
                return false;
            }
            
            // Delete the event
            $event->delete();
            
            // Delete the local record
            $request->googleCalendarEvent->delete();
            
            Log::info('Deleted Google Calendar event', [
                'request_id' => $request->id,
                'google_event_id' => $googleEventId,
                'calendar_id' => $calendarId
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete Google Calendar event', [
                'request_id' => $request->id,
                'google_event_id' => $request->googleCalendarEvent->google_event_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
}
```

## 4. Livewire Component for Event Creation Modal

Create a Livewire component for the event creation modal:

```php
namespace App\Http\Livewire;

use App\Models\InstructionRequests;
use App\Services\CalendarService;
use Carbon\Carbon;
use Livewire\Component;

class CreateGoogleCalendarEventForm extends Component
{
    public $requestId;
    public $eventName;
    public $startTime;
    public $endTime;
    public $description;
    public $location;
    public $instructorEmail;
    public $librarianEmail;
    
    protected $listeners = ['showEventForm'];
    
    protected $rules = [
        'eventName' => 'required|string|max:255',
        'startTime' => 'required|date',
        'endTime' => 'required|date|after:startTime',
        'description' => 'nullable|string',
        'location' => 'nullable|string|max:255',
    ];
    
    public function mount()
    {
        // Initialize empty form
    }
    
    public function showEventForm($requestId)
    {
        $this->requestId = $requestId;
        $request = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian', 'classes'])
            ->findOrFail($requestId);
        
        // Pre-populate form fields from the instruction request
        $this->eventName = "{$request->department} {$request->course_number} Library Instruction";
        
        // Format the start time
        if ($request->preferred_datetime) {
            $this->startTime = Carbon::parse($request->preferred_datetime)->format('Y-m-d\TH:i');
            
            // Calculate end time based on duration
            $durationMinutes = intval($request->duration);
            if ($durationMinutes > 0) {
                $this->endTime = Carbon::parse($request->preferred_datetime)
                    ->addMinutes($durationMinutes)
                    ->format('Y-m-d\TH:i');
            } else {
                // Default to 1 hour if duration not set
                $this->endTime = Carbon::parse($request->preferred_datetime)
                    ->addHour()
                    ->format('Y-m-d\TH:i');
            }
        }
        
        // Set location based on instruction type
        if ($request->instruction_type == 'on-campus') {
            $room = $request->detail->room ?? '';
            $this->location = $request->campus->name . ($room ? ", Room $room" : '');
        } else if ($request->instruction_type == 'remote') {
            $this->location = 'Remote session';
        } else {
            $this->location = 'Asynchronous instruction';
        }
        
        // Set description
        $this->description = "Library instruction session for {$request->department} {$request->course_number} class.\n\n";
        if ($request->class_description) {
            $this->description .= "Class description: " . $request->class_description . "\n\n";
        }
        if ($request->assignment_description) {
            $this->description .= "Assignment: " . $request->assignment_description;
        }
        
        // Store emails for attendees
        $this->instructorEmail = $request->instructor->email;
        $this->librarianEmail = $request->librarian->email;
    }
    
    public function createEvent()
    {
        $this->validate();
        
        // Get the request
        $request = InstructionRequests::with('campus')->findOrFail($this->requestId);
        
        // Get the calendar ID from the campus
        $calendarId = $request->campus->extractCalendarId();
        if (!$calendarId) {
            session()->flash('error', 'Unable to determine Google Calendar ID for this campus.');
            return;
        }
        
        // Create form data array
        $formData = [
            'eventName' => $this->eventName,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'description' => $this->description,
            'location' => $this->location,
            'instructorEmail' => $this->instructorEmail,
            'librarianEmail' => $this->librarianEmail,
        ];
        
        // Use the calendar service to create the event
        $calendarService = app(CalendarService::class);
        $googleEventRecord = $calendarService->createEventFromData($request, $formData, $calendarId);
        
        if ($googleEventRecord) {
            // Update the request status to scheduled
            $request->status = 'scheduled';
            $request->save();
            
            // Show success message
            session()->flash('success', 'Calendar event created successfully.');
            
            // Dispatch event for the parent component
            $this->emit('googleCalendarEventCreated', $this->requestId);
        } else {
            // Show error message
            session()->flash('error', 'Failed to create calendar event. Please try again or contact support.');
        }
    }
    
    public function render()
    {
        return view('livewire.create-google-calendar-event-form');
    }
}
```

## 5. Livewire Component Template

Create the view for the Livewire component:

```blade
<div>
    <div class="modal-header">
        <h5 class="modal-title">Schedule Library Instruction</h5>
        <button type="button" class="btn-close" wire:click="$emit('closeModal')" aria-label="Close"></button>
    </div>
    
    <form wire:submit.prevent="createEvent">
        <div class="modal-body">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="mb-3">
                <label for="eventName" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="eventName" wire:model="eventName">
                @error('eventName') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="startTime" class="form-label">Start Time</label>
                <input type="datetime-local" class="form-control" id="startTime" wire:model="startTime">
                @error('startTime') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="endTime" class="form-label">End Time</label>
                <input type="datetime-local" class="form-control" id="endTime" wire:model="endTime">
                @error('endTime') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" wire:model="location">
                @error('location') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="3" wire:model="description"></textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <p class="form-text">
                    <strong>Attendees:</strong><br>
                    Instructor: {{ $instructorEmail }}<br>
                    Librarian: {{ $librarianEmail }}
                </p>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="$emit('closeModal')">Cancel</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Create Calendar Event</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>
```

## 6. UI Integration - Instruction Request Edit View

Add the "Schedule" button and modal to the instruction request edit view:

```blade
{{-- In instruction-request edit view --}}
@if($instructionRequest->status === 'accepted')
    <button
        type="button"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        @click="$dispatch('open-modal', 'schedule-event')"
    >
        Schedule on Calendar
    </button>
    
    <x-modal name="schedule-event" :show="false" focusable>
        @livewire('create-google-calendar-event-form')
    </x-modal>
@endif

@if($instructionRequest->status === 'scheduled')
    <button
        type="button"
        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        @click="$dispatch('open-modal', 'delete-event')"
    >
        Delete Calendar Event
    </button>
    
    <x-modal name="delete-event" :show="false" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium">Delete Calendar Event</h2>
            <p class="mt-2">Are you sure you want to delete this calendar event? This will set the request status back to "accepted".</p>
            <div class="mt-6 flex justify-end">
                <button
                    type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded mr-2"
                    @click="$dispatch('close')"
                >
                    Cancel
                </button>
                <form method="POST" action="{{ route('instructionRequests.deleteCalendarEvent', $instructionRequest->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                        Delete Event
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
@endif
```

## 7. Controller Updates for Event Deletion

Add a method to the InstructionRequestController for deleting events:

```php
/**
 * Delete Google Calendar event for instruction request.
 *
 * @param int $id
 * @return RedirectResponse
 */
public function deleteCalendarEvent(int $id): RedirectResponse
{
    $instructionRequest = $this->instructionRequestService->findInstructionRequestById($id);
    
    if (empty($instructionRequest)) {
        session()->flash('error', 'Instruction Request not found.');
        return redirect(route('instructionRequests.index'));
    }
    
    if ($instructionRequest->status !== 'scheduled') {
        session()->flash('error', 'Instruction Request is not scheduled.');
        return redirect(route('instructionRequests.edit', $id));
    }
    
    // Use calendar service to delete the event
    $calendarService = app(CalendarService::class);
    $result = $calendarService->deleteEvent($instructionRequest);
    
    if ($result) {
        // Update status back to accepted
        $instructionRequest->status = 'accepted';
        $instructionRequest->save();
        
        session()->flash('success', 'Calendar event deleted successfully.');
    } else {
        // Calendar event not found or deletion failed
        if ($instructionRequest->googleCalendarEvent) {
            // The record was found locally but not in Google Calendar
            $instructionRequest->googleCalendarEvent->delete();
            $instructionRequest->status = 'accepted';
            $instructionRequest->save();
            
            session()->flash('warning', 'Calendar event not found in Google Calendar, but status has been updated.');
        } else {
            session()->flash('error', 'Failed to delete calendar event.');
        }
    }
    
    return redirect(route('instructionRequests.edit', $id));
}
```

## 8. Add Routes for Calendar Events

Add routes for calendar event functionality:

```php
// In routes/web.php, inside the authenticated group
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Existing routes...
    
    // Calendar event routes
    Route::delete('instructionRequests/{id}/calendar-event', [InstructionRequestController::class, 'deleteCalendarEvent'])
        ->name('instructionRequests.deleteCalendarEvent');
});
```

## 9. Implementation Steps

1. **Setup Phase**
   - Create the migration for adding the google_calendar_events table
   - Run the migration to add the table
   - Create the GoogleCalendarEvent model
   - Add the relationship to the InstructionRequests model
   - Implement the extractCalendarId method in the Campus model

2. **Service Implementation**
   - Create the CalendarService class with event creation and deletion methods
   - Register the service in the service container if needed

3. **UI Implementation**
   - Create the Livewire component for event creation
   - Create the Blade template for the component
   - Update the instruction request edit view to include the scheduling button and modal
   - Add the event deletion button and modal

4. **Controller Integration**
   - Add the controller method for handling event deletion
   - Add the required routes
   - Update the InstructionRequestService to handle the new status changes

5. **Testing**
   - Test event creation with a test calendar
   - Test event deletion
   - Verify status changes are correctly handled
   - Test error handling paths

## 10. Future Enhancements

- **Calendar View**: Add a calendar view in the dashboard to display all scheduled instruction sessions
- **Bulk Operations**: Allow scheduling multiple sessions at once
- **Calendar Filtering**: Enable filtering by librarian, campus, or date range
- **Event Updates**: Add functionality to update existing calendar events if request details change

## 11. Progress Update

### Completed Items
- ✅ Created database migration for google_calendar_events table
- ✅ Created GoogleCalendarEvent model with relationships
- ✅ Added relationship to InstructionRequests model
- ✅ Implemented CalendarService with core methods
- ✅ Modified InstructionRequestController to handle event deletion
- ✅ Added new status options (`scheduled`, `rejected`)
- ✅ Added routes for calendar event functionality
- ✅ Implemented calendar ID extraction from campus URLs

### Remaining Tasks
- ❌ Complete the Livewire component for event creation
- ❌ Finalize the Blade template for the component
- ❌ Fully integrate UI elements in the instruction request edit view
- ❌ Implement comprehensive testing
- ❌ Add event update functionality for when request details change
