# Google Calendar Integration Implementation Status

## Overview

This document outlines the implementation status of Google Calendar integration with the Library Instruction Request System. The integration allows librarians to manually create calendar events after accepting instruction requests, marking them as "scheduled" in the system, with full status tracking through the workflow.

## Implementation Goals

- Allow librarians to create Google Calendar events from accepted instruction requests
- Add "scheduled" and "rejected" status options to the request workflow
- Track created events and handle deletions within the application
- Provide integration with the existing notification system
- Ensure proper form validation and change detection before calendar events can be created

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
        'librarian_id',
        'campus_id',
        'event_title',
        'start_time',
        'end_time',
        'description',
        'location',
        'attendees',
        'raw_event_data'
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

The CalendarService class has been enhanced to provide more robust functionality for calendar operations:

```php
namespace App\Services;

use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

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
        // Implementation details...
    }
    
    /**
     * Delete a Google Calendar event.
     *
     * @param GoogleCalendarEvent $calendarEvent The event to delete
     * @return array Deletion result with success status and message
     */
    public function deleteEvent(GoogleCalendarEvent $calendarEvent): array
    {
        // Implementation details...
    }
    
    /**
     * Extract Google Calendar ID from a Google Calendar share URL.
     *
     * @param string $url The Google Calendar share URL
     * @return string|null The extracted calendar ID or null if extraction fails
     */
    public function extractCalendarId(string $url): ?string
    {
        // Implementation details...
    }
    
    /**
     * Get event data from an instruction request that can be used to pre-populate a form.
     *
     * @param InstructionRequests $request The instruction request
     * @return array The formatted event data
     */
    public function getEventFormData(InstructionRequests $request): array
    {
        // Implementation details...
    }
    
    /**
     * Format event data from an instruction request.
     *
     * @param InstructionRequests $request The instruction request
     * @return array The formatted event data
     */
    protected function formatEventData(InstructionRequests $request): array
    {
        // Implementation details...
    }
}
```

## 4. Livewire Component for Event Creation Modal

The CreateGoogleCalendarEventForm component has been implemented and enhanced:

```php
namespace App\Http\Livewire;

use App\Models\InstructionRequests;
use App\Services\CalendarService;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

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
    
    public function mount($requestId = null)
    {
        // If request ID is provided during mount, load the data immediately
        if ($requestId) {
            $this->showEventForm($requestId);
        }
    }
    
    public function showEventForm($requestId)
    {
        // Always load a fresh copy of the instruction request from the database
        $request = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian', 'classes'])
            ->findOrFail($requestId);
            
        // Implementation details...
    }
    
    public function createEvent()
    {
        // Implementation details...
    }
    
    public function render()
    {
        return view('livewire.create-google-calendar-event-form');
    }
}
```

## 5. Delete Google Calendar Event Component

A complementary Livewire component for deleting calendar events has been implemented:

```php
namespace App\Http\Livewire;

use App\Models\InstructionRequests;
use App\Services\CalendarService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class DeleteGoogleCalendarEvent extends Component
{
    public int $requestId;

    public function mount(int $requestId)
    {
        $this->requestId = $requestId;
    }

    public function deleteEvent(CalendarService $calendarService)
    {
        // Implementation details...
    }

    public function render()
    {
        $instructionRequest = InstructionRequests::findOrFail($this->requestId);
        return view('livewire.delete-google-calendar-event', ['instructionRequest' => $instructionRequest]);
    }
}
```

## 6. Form Change Detection Implementation

The edit.blade.php template has been enhanced with Alpine.js to detect form changes and validate critical fields:

```js
// Alpine.js store enhancement
Alpine.store('formState', {
    // Core state tracking
    isEditing: this.isEditing,
    hasUnsavedChanges: false,

    // Define which sections should be affected by edit toggle
    // Left column sections (status, file uploads, etc.) are deliberately excluded
    editableSections: [
        'instructorInfo',
        'requestInfo',
        'dateTime',
        'adaProvisions',
        'learningOutcomes',
        'instructionGoals'
    ],

    // Store initial values for critical fields
    initialValues: {
        instructionDatetime: document.getElementById('instruction_datetime')?.value || null,
        instructionDuration: document.getElementById('instruction_duration')?.value || null
    },
    
    // Check if critical scheduling fields have changed
    checkForChanges() {
        const currentDatetime = document.getElementById('instruction_datetime')?.value || null;
        const currentDuration = document.getElementById('instruction_duration')?.value || null;
        
        this.hasUnsavedChanges = 
            (currentDatetime !== this.initialValues.instructionDatetime) || 
            (currentDuration !== this.initialValues.instructionDuration);
        
        console.log('Form changes detected:', {
            hasChanges: this.hasUnsavedChanges,
            original: this.initialValues,
            current: {
                instructionDatetime: currentDatetime,
                instructionDuration: currentDuration
            }
        });
        
        return this.hasUnsavedChanges;
    },
    
    // Reset change tracking after successful form submission
    resetChangeTracking() {
        this.hasUnsavedChanges = false;
        
        // Update stored initial values to match current values
        this.initialValues = {
            instructionDatetime: document.getElementById('instruction_datetime')?.value || null,
            instructionDuration: document.getElementById('instruction_duration')?.value || null
        };
        
        console.log('Change tracking reset, new initial values:', this.initialValues);
    },
    
    // Check if duration is valid for scheduling
    hasDuration() {
        const durationInput = document.getElementById('instruction_duration');
        const duration = durationInput?.value || null;
        return duration && parseInt(duration) > 0;
    },
    
    // Determine if a specific section should be editable
    isSectionEditable(sectionName) {
        return this.isEditing && this.editableSections.includes(sectionName);
    }
});
```

## 7. Schedule Button Implementation

The Schedule button has been enhanced with conditional disabling logic:

```html
<div class="my-4" x-data="{ 
    isOpen: false,
    
    // Method to check if button should be disabled
    checkDisabled() {
        // Check for unsaved changes
        const hasChanges = Alpine.store('formState')?.hasUnsavedChanges || false;
        
        // Check for valid duration
        const hasDuration = Alpine.store('formState')?.hasDuration() || false;
        
        return hasChanges || !hasDuration;
    }
}">
    <button 
        type="button" 
        @click="!checkDisabled() && (isOpen = true)"
        x-bind:class="{
            'opacity-50 cursor-not-allowed': checkDisabled(),
            'hover:bg-indigo-700': !checkDisabled()
        }"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
        <x-heroicon-o-calendar-date-range class="h-4 w-4 text-white mr-2" />
        Create Google Calendar Event
    </button>
    
    <!-- Warning messages for disabled state -->
    <div x-show="checkDisabled()" class="mt-2 text-sm text-amber-600" x-cloak>
        <div x-show="Alpine.store('formState')?.hasUnsavedChanges">
            Please save changes before scheduling.
        </div>
        <div x-show="!Alpine.store('formState')?.hasDuration()">
            Please enter a valid duration before scheduling.
        </div>
    </div>

    <!-- Modal implementation -->
</div>
```

## 8. Controller Updates for Event Handling

Add the controller methods for handling calendar events:

```php
/**
 * Delete a Google Calendar event for an instruction request.
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
    
    // Implementation details...
}
```

## 9. Routes for Calendar Events

Routes have been added for calendar event functionality:

```php
// In routes/web.php, inside the authenticated group
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Existing routes...
    
    // Calendar event routes
    Route::delete('instructionRequests/{id}/calendar-event', [InstructionRequestController::class, 'deleteCalendarEvent'])
        ->name('instructionRequests.deleteCalendarEvent');
});
```

## 10. Implementation Status

### Completed Items

- ✅ Database structure (Google Calendar events table)
- ✅ Model implementation with relationships
- ✅ Calendar service with core methods
- ✅ Livewire components for event creation and deletion
- ✅ UI templates and modals
- ✅ Controller methods for event handling
- ✅ Routes for calendar functionality
- ✅ Form change detection with Alpine.js

### Recent Enhancements

#### Section-Based Edit Toggle System
- ✅ Implemented granular section-specific editing for right column components
- ✅ Created centralized editableSections array to manage toggleable sections
- ✅ Added isSectionEditable() method for easy section-based state checking
- ✅ Made left column components consistently editable regardless of toggle state

#### Form Change Detection
- ✅ Added Alpine.js store to track changes to critical scheduling fields
- ✅ Implemented methods to check if duration is valid (must be present and > 0)
- ✅ Added reset functionality after successful form submission
- ✅ Maintained compatibility with the existing toggle edit functionality
- ✅ Fixed all Alpine.js binding syntax to use proper x-bind:class and x-bind:readonly

#### Schedule Button Improvements
- ✅ Added disabled state logic based on form changes and duration validity
- ✅ Implemented visual feedback with warning messages
- ✅ Used correct Alpine.js binding syntax for consistent browser support
- ✅ Enhanced feedback with specific error messages by condition

#### Livewire Component Updates
- ✅ Modified the CreateGoogleCalendarEventForm component to always fetch fresh data
- ✅ Added better logic for selecting start time and duration
- ✅ Improved logging for debugging
- ✅ Enhanced handling of edge cases with fallback values

#### Form Submission Improvements
- ✅ Ensured form fields are always submittable regardless of visual state
- ✅ Added code to temporarily enable disabled fields during submission
- ✅ Added script to detect successful form submissions and reset change tracking

### Remaining Tasks
- ❌ Implement automated testing for calendar integration
- ❌ Add event update functionality for when request details change
- ❌ Create dashboard calendar view for scheduled sessions
- ❌ Implement bulk scheduling operations
- ❌ Add calendar filtering capabilities
- ❌ Enhance error handling and recovery

## 11. Alpine.js Implementation Details

### 11.1 Binding Syntax

For proper browser compatibility and consistent behavior, these specific Alpine.js syntax patterns are used:

#### Using x-bind: Instead of Shorthand

```html
<!-- INCORRECT: Using Vue-style shorthand syntax -->
:class="{'bg-gray-100': isDisabled}"
:readonly="isDisabled"

<!-- CORRECT: Using full Alpine.js x-bind: prefix -->
x-bind:class="isDisabled ? 'bg-gray-100' : ''"
x-bind:readonly="isDisabled"
```

#### Ternary Expressions for Class Binding

```html
<!-- Recommended Pattern -->
x-bind:class="isDisabled ? 'bg-gray-100 cursor-not-allowed' : ''"
```

### 11.2 Section-Based Editability

The system uses a centralized method to manage which sections should be editable:

```js
// Define all editable sections in one place
editableSections: [
    'instructorInfo',
    'requestInfo',
    'dateTime',
    'adaProvisions',
    'learningOutcomes',
    'instructionGoals'
],

// Method to check if a specific section should be editable
isSectionEditable(sectionName) {
    return this.isEditing && this.editableSections.includes(sectionName);
}
```

Each section then uses this pattern to determine its edit state:

```html
<div x-data="{
    get isDisabled() {
        return !$store.formState.isSectionEditable('sectionName');
    }
}">
    <!-- Section-specific form fields -->
</div>
```

### 11.3 Form Submission Handling

A key improvement ensures that all form fields are submitted regardless of their visual state:

```js
// Handle form submission - CRITICAL
const form = document.getElementById('updateInstructionRequestForm');
form.addEventListener('submit', (event) => {
    console.log('Form submit event triggered, edit state:', this.isEditing);
    
    // Temporarily enable all form fields to ensure they can be submitted
    const formFields = form.querySelectorAll('input, select, textarea');
    formFields.forEach(field => {
        if (field.disabled) {
            // Mark fields that were disabled so we can restore them later if needed
            field.setAttribute('data-was-disabled', 'true');
            field.disabled = false;
        }
    });
    
    // Allow form submission regardless of edit state or unsaved changes
    return true;
});
```

This approach maintains the visual restrictions of the edit toggle while ensuring all field data is properly included in form submissions.

## 12. Future Enhancements

- **Calendar View**: Add a calendar view in the dashboard to display all scheduled instruction sessions
- **Bulk Operations**: Allow scheduling multiple sessions at once
- **Calendar Filtering**: Enable filtering by librarian, campus, or date range
- **Event Updates**: Add functionality to update existing calendar events if request details change
- **Notifications**: Integrate with existing notification system for calendar events