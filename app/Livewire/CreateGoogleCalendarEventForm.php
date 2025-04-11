<?php

namespace App\Livewire;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Models\InstructionRequests;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

/**
 * Livewire Component for Creating Google Calendar Events
 *
 * Handles the creation of Google Calendar events directly from an instruction request.
 */
class CreateGoogleCalendarEventForm extends Component
{
    /**
     * The instruction request model instance
     *
     * @var InstructionRequests
     */
    public $instructionRequest;

    /**
     * Event creation form fields
     *
     * @var string
     */
    public $eventName;
    public $startTime;
    public $endTime;
    public $description;
    public $location;
    public $instructorEmail;
    public $librarianEmail;

    /**
     * Rules for event creation validation
     *
     * @var array
     */
    protected $rules = [
        'eventName' => 'required|string|max:255',
        'startTime' => 'required|date',
        'endTime' => 'required|date|after:startTime',
        'description' => 'nullable|string',
        'location' => 'nullable|string|max:255',
    ];

    /**
     * Mount the component with a specific instruction request
     *
     * @param int $requestId The ID of the instruction request
     */
    public function mount(int $requestId)
    {
        // Log that the component is being mounted
        Log::info('CreateGoogleCalendarEventForm: Component mounting', [
            'request_id' => $requestId
        ]);
        
        // Fetch the instruction request with all necessary relationships
        $this->instructionRequest = InstructionRequests::with([
            'instructor',
            'detail',
            'campus',
            'librarian',
            'classes'
        ])->findOrFail($requestId);

        // Populate form with pre-filled data
        $this->populateFormData();
    }

    /**
     * Populate form fields with pre-existing instruction request data
     *
     * Uses database records to set initial values, ensuring consistency
     * and preventing DOM-based data manipulation.
     */
    protected function populateFormData()
    {
        // Log that we're starting to populate form data
        Log::debug('CreateGoogleCalendarEventForm: Starting populateFormData', [
            'request_id' => $this->instructionRequest->id
        ]);
        
        try {
            // Use CalendarService to get pre-formatted event data
            $calendarService = app(CalendarService::class);
            $eventData = $calendarService->getEventFormData($this->instructionRequest);

            // Ensure we're using database record for datetime
            $detail = $this->instructionRequest->detail;

            // Log the source of our datetime for debugging
            Log::info('Populating Calendar Event Form Data', [
                'instruction_datetime' => $detail->instruction_datetime,
                'instruction_duration' => $detail->instruction_duration,
                'event_data' => $eventData
            ]);

            $this->eventName = $eventData['event_title'];

            // Use database datetime directly, avoiding DOM conversion
            $startTime = Carbon::parse($detail->instruction_datetime);
            $duration = $detail->instruction_duration ?? 60; // Default to 60 minutes if not set
            $endTime = (clone $startTime)->addMinutes((int) $duration);

            $this->startTime = $startTime->format('Y-m-d\TH:i');
            $this->endTime = $endTime->format('Y-m-d\TH:i');

            $this->description = $eventData['description'] ?? '';
            $this->location = $eventData['location'] ?? '';

            // Set attendee emails
            $this->instructorEmail = $this->instructionRequest->instructor?->email;
            $this->librarianEmail = $this->instructionRequest->librarian?->email;
            
            // Log successful form population
            Log::debug('CreateGoogleCalendarEventForm: Form data populated successfully', [
                'eventName' => $this->eventName,
                'startTime' => $this->startTime,
                'endTime' => $this->endTime
            ]);
        } catch (\Exception $e) {
            // Log any errors that occurred during form population
            Log::error('CreateGoogleCalendarEventForm: Error populating form data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw the exception to be handled by the caller
            throw $e;
        }
    }

    /**
     * Create a Google Calendar event from the Livewire component
     *
     * Handles the creation of a Google Calendar event by:
     * - Validating form inputs
     * - Preparing custom event data from database records
     * - Calling the CalendarService to create the event
     * - Managing success and error states
     *
     * @param CalendarService $calendarService Injected calendar service for event creation
     * @throws \Exception For unexpected errors during event creation
     */
    public function createEvent(CalendarService $calendarService)
    {
        // First thing - log that we're in the method - this helps debug if the method is being called
        Log::critical('CreateGoogleCalendarEventForm: createEvent method is definitely called', [
            'timestamp' => now()->toDateTimeString(),
            'component_id' => $this->getId()
        ]);
        
        // Log the initial event creation attempt using database records
        Log::debug('CreateGoogleCalendarEventForm: createEvent() method called', [
            'component_id' => $this->getId(),
            'form_data' => [
                'eventName' => $this->eventName,
                'startTime' => $this->startTime,
                'endTime' => $this->endTime
            ]
        ]);
        
        Log::info('CreateGoogleCalendarEventForm: Preparing to create event', [
            'request_id' => $this->instructionRequest->id,
            'database_datetime' => $this->instructionRequest->detail->instruction_datetime,
            'database_duration' => $this->instructionRequest->detail->instruction_duration
        ]);

        // Validate form inputs
        $this->validate();

        try {
            // Prepare custom event data exclusively from database records
            $detail = $this->instructionRequest->detail;
            $startTime = Carbon::parse($detail->instruction_datetime);
            $duration = (int)$detail->instruction_duration ?? 60; // Default to 60 if not set
            $endTime = (clone $startTime)->addMinutes($duration);

            $customData = [
                'event_title' => $this->eventName, // Allow custom title
                'start_time' => $startTime->format('Y-m-d\TH:i'),
                'end_time' => $endTime->format('Y-m-d\TH:i'),
                'description' => $this->description,
                'location' => $this->location,
                'start_time_obj' => $startTime,
                'end_time_obj' => $endTime
            ];

            // Log detailed event creation attempt
            Log::info('CreateGoogleCalendarEventForm: Creating event with database-sourced data', [
                'request_id' => $this->instructionRequest->id,
                'start_time' => $customData['start_time'],
                'end_time' => $customData['end_time'],
                'duration' => $duration
            ]);

            // Just before calling CalendarService
            Log::critical('CreateGoogleCalendarEventForm: About to call CalendarService->createEvent', [
                'timestamp' => now()->toDateTimeString(),
                'request_id' => $this->instructionRequest->id,
                'calendar_service_class' => get_class($calendarService)
            ]);
            
            // Verify the service instance is valid
            if (!method_exists($calendarService, 'createEvent')) {
                Log::critical('CreateGoogleCalendarEventForm: CalendarService missing createEvent method', [
                    'service_type' => gettype($calendarService),
                    'service_class' => get_class($calendarService)
                ]);
                throw new \RuntimeException('Calendar service does not have createEvent method');
            }
            
            // First do a test call to verify the CalendarService is working correctly
            Log::critical('CreateGoogleCalendarEventForm: Testing CalendarService with test method', [
                'request_id' => $this->instructionRequest->id
            ]);
            
            try {
                $testResult = $calendarService->testCalendarService($this->instructionRequest);
                Log::critical('CreateGoogleCalendarEventForm: CalendarService test succeeded', $testResult);
            } catch (\Exception $testException) {
                Log::critical('CreateGoogleCalendarEventForm: CalendarService test failed', [
                    'error' => $testException->getMessage(),
                    'trace' => $testException->getTraceAsString()
                ]);
            }
            
            // Attempt to create the event using CalendarService
            try {
                // Refresh the instruction request to ensure we have the latest data
                $this->instructionRequest->refresh();
                
                // Log the data we're sending
                Log::critical('CreateGoogleCalendarEventForm: Calling CalendarService->createEvent with data', [
                    'custom_data_keys' => array_keys($customData),
                    'start_time' => $customData['start_time'],
                    'end_time' => $customData['end_time']
                ]);
                
                $googleCalendarEvent = $calendarService->createEvent(
                    $this->instructionRequest,
                    $customData
                );
                
                // If we get here, the event was created successfully
                Log::critical('CreateGoogleCalendarEventForm: CalendarService call succeeded', [
                    'event_id' => $googleCalendarEvent->id ?? 'unknown',
                    'google_event_id' => $googleCalendarEvent->google_event_id ?? 'unknown'
                ]);
            } catch (\Exception $serviceException) {
                // Log error from CalendarService call
                Log::critical('CreateGoogleCalendarEventForm: CalendarService->createEvent threw exception', [
                    'error' => $serviceException->getMessage(),
                    'class' => get_class($serviceException),
                    'trace' => $serviceException->getTraceAsString()
                ]);
                
                throw $serviceException;
            }

            // Log successful event creation
            Log::info('CreateGoogleCalendarEventForm: Event created successfully', [
                'request_id' => $this->instructionRequest->id,
                'event_id' => $googleCalendarEvent->id,
                'google_event_id' => $googleCalendarEvent->google_event_id
            ]);

            // Log the successful event dispatch
            Log::info('CreateGoogleCalendarEventForm: Dispatching googleCalendarEventCreated event', [
                'request_id' => $this->instructionRequest->id,
                'event_id' => $googleCalendarEvent->id
            ]);
            
            // Dispatch event to close modal and refresh page
            $this->dispatch('googleCalendarEventCreated', ['requestId' => $this->instructionRequest->id]);
            
            // Flash message for page refresh
            session()->flash('success', 'Google Calendar event created successfully.');

        } catch (InvalidCalendarConfigurationException $e) {
            // Log the configuration error
            Log::warning('CreateGoogleCalendarEventForm: Invalid calendar configuration', [
                'request_id' => $this->instructionRequest->id,
                'context' => $e->getContext()
            ]);

            // Add error with link to edit campus
            $campusId = $e->getContextValue('campus_id');
            $campusName = $e->getContextValue('campus_name', 'this campus');

            $this->addError('calendar',
                "Invalid calendar configuration for {$campusName}. Please " .
                "<a href='" . route('campuses.edit', $campusId) . "' class='underline'>update the calendar</a>."
            );
        } catch (\Exception $e) {
            // Log any unexpected errors
            Log::error('CreateGoogleCalendarEventForm: Unexpected error creating event', [
                'request_id' => $this->instructionRequest->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            // Add a generic error message
            $this->addError('calendar', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Render the Livewire component
     *
     * @return View
     */
    public function render()
    {
        return view('livewire.create-google-calendar-event-form');
    }
    
    /**
     * Method that can be called directly via JavaScript as a backup
     * This provides an alternative way to trigger the event creation
     */
    public function createCalendarEventDirectly()
    {
        Log::critical('CreateGoogleCalendarEventForm: createCalendarEventDirectly method called', [
            'timestamp' => now()->toDateTimeString(),
            'component_id' => $this->getId()
        ]);
        
        // Call the main createEvent method with the CalendarService injected
        $this->createEvent(app(CalendarService::class));
    }
}
