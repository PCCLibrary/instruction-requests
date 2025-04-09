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

        // Always load a fresh copy of the instruction request from the database
        $request = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian', 'classes'])
            ->findOrFail($requestId);

        // Log database values for debugging
        Log::info('Database values for calendar event', [
            'request_id' => $requestId,
            'has_detail' => $request->detail ? 'yes' : 'no',
            'instruction_datetime' => $request->detail->instruction_datetime ?? 'not set',
            'instruction_duration' => $request->detail->instruction_duration ?? 'not set'
        ]);

        // Start with datetime from detail record
        $startTime = null;
        $duration = 60; // Default duration in minutes if none is specified

        // Use instruction_datetime from detail if available
        if ($request->detail && $request->detail->instruction_datetime) {
            $startTime = Carbon::parse($request->detail->instruction_datetime);
            // Also use duration from detail if available
            if ($request->detail->instruction_duration) {
                $duration = intval($request->detail->instruction_duration);
            }
        } else {
            // Fall back to preferred_datetime if instruction_datetime is not set
            if ($request->preferred_datetime) {
                $startTime = Carbon::parse($request->preferred_datetime);
                if ($request->duration) {
                    $duration = intval($request->duration);
                }
            } else {
                // Default to tomorrow at 9 AM if no times are set
                $startTime = Carbon::now()->addDays(1)->setTime(9, 0);
            }
        }

        // Get pre-populated event data from the CalendarService
        // This will handle formatting event title, description, etc.
        $calendarService = app(CalendarService::class);
        $eventData = $calendarService->getEventFormData($request);

        // Pre-populate form fields from the event data
        $this->eventName = $eventData['event_title'];
        $this->location = $eventData['location'];
        $this->description = $eventData['description'];

        // Override the start and end times with our directly chosen values
        // Format the start time for datetime-local input
        $this->startTime = $startTime->format('Y-m-d\TH:i');
        
        // Calculate end time based on the duration
        $this->endTime = (clone $startTime)->addMinutes($duration)->format('Y-m-d\TH:i');

        // Store emails for attendees
        $this->instructorEmail = $request->instructor->email ?? '';
        $this->librarianEmail = $request->librarian->email ?? '';

        Log::info('Loaded form values for calendar event', [
            'request_id' => $requestId,
            'event_name' => $this->eventName,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'duration_used' => $duration
        ]);
    }
    
    public function createEvent()
    {
        // Validate form data
        $this->validate();
        
        // Get the request with campus - load a fresh copy
        $request = InstructionRequests::with(['campus', 'instructor', 'librarian', 'detail'])
            ->findOrFail($this->requestId);
        
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