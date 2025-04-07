<?php

namespace App\Livewire;

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
        $this->requestId = $requestId;
        
        // Get current form values from the parent form
        $this->updateFormValuesFromParent();
        
        // Find the instruction request with its related models
        $request = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian', 'classes'])
            ->findOrFail($requestId);
        
        // Get pre-populated event data from the CalendarService
        $calendarService = app(CalendarService::class);
        $eventData = $calendarService->getEventFormData($request);
        
        // Pre-populate form fields from the event data
        $this->eventName = $eventData['event_title'];
        
        // Format the start and end times for datetime-local inputs
        $this->startTime = Carbon::parse($eventData['start_time'])
            ->format('Y-m-d\TH:i');
        
        $this->endTime = Carbon::parse($eventData['end_time'])
            ->format('Y-m-d\TH:i');
        
        $this->location = $eventData['location'];
        $this->description = $eventData['description'];
        
        // Store emails for attendees
        $this->instructorEmail = $request->instructor->email ?? '';
        $this->librarianEmail = $request->librarian->email ?? '';
        
        Log::info('Loaded form values for calendar event', [
            'request_id' => $requestId,
            'event_name' => $this->eventName,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime
        ]);
    }
    
    /**
     * Update form values from parent form if values have changed
     */
    private function updateFormValuesFromParent()
    {
        // We would implement this if needed later
        // This would get the current instruction datetime and duration from the parent form
    }
    
    public function createEvent()
    {
        // Validate form data
        $this->validate();
        
        // Get the request with campus
        $request = InstructionRequests::with('campus')->findOrFail($this->requestId);
        
        // Get the calendar ID from the campus
        $calendarId = null;
        if ($request->campus && $request->campus->gcal) {
            $calendarService = app(CalendarService::class);
            $calendarId = $calendarService->extractCalendarId($request->campus->gcal);
        }
        
        // Check if we have a calendar ID
        if (!$calendarId) {
            session()->flash('error', 'Unable to determine Google Calendar ID for this campus.');
            Log::error('Google Calendar ID not found for campus', [
                'campus_id' => $request->campus_id,
                'gcal_url' => $request->campus->gcal ?? 'not set'
            ]);
            return;
        }
        
        // Create form data array
        $formData = [
            'event_title' => $this->eventName,
            'start_time' => Carbon::parse($this->startTime)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::parse($this->endTime)->format('Y-m-d H:i:s'),
            'description' => $this->description,
            'location' => $this->location,
        ];
        
        Log::info('Creating calendar event', [
            'request_id' => $this->requestId,
            'calendar_id' => $calendarId,
            'form_data' => $formData
        ]);
        
        // Use the calendar service to create the event
        $calendarService = app(CalendarService::class);
        $googleEventRecord = $calendarService->createEvent($request, $formData, $calendarId);
        
        if ($googleEventRecord) {
            // Show success message
            session()->flash('success', 'Calendar event created successfully.');
            Log::info('Calendar event created successfully', [
                'request_id' => $this->requestId,
                'event_id' => $googleEventRecord->google_event_id
            ]);
            
            // Emit event to close modal and refresh the page
            $this->dispatch('googleCalendarEventCreated', $this->requestId);
            $this->dispatch('closeModal');
        } else {
            // Show error message
            session()->flash('error', 'Failed to create calendar event. Please try again or contact support.');
            Log::error('Failed to create calendar event', [
                'request_id' => $this->requestId
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.create-google-calendar-event-form');
    }
}