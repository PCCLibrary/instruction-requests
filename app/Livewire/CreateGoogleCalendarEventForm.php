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
     */
    protected function populateFormData()
    {
        // Use CalendarService to get pre-formatted event data
        $calendarService = app(CalendarService::class);
        $eventData = $calendarService->getEventFormData($this->instructionRequest);

        $this->eventName = $eventData['event_title'];
        $this->startTime = $eventData['start_time'];
        $this->endTime = $eventData['end_time'];
        $this->description = $eventData['description'] ?? '';
        $this->location = $eventData['location'] ?? '';

        // Set attendee emails
        $this->instructorEmail = $this->instructionRequest->instructor?->email;
        $this->librarianEmail = $this->instructionRequest->librarian?->email;
    }

    /**
     * Create the Google Calendar event
     *
     * Validates form data, creates the event, and handles potential errors
     */
    public function createEvent(CalendarService $calendarService)
    {
        // Validate form inputs
        $this->validate();

        try {
            // Prepare custom event data from form
            // Format date strings from the form (already in Y-m-d\TH:i format from the input)
            $startTime = $this->startTime; 
            $endTime = $this->endTime;
            
            // Parse them to Carbon objects for Google Calendar API
            try {
                $startTimeObj = Carbon::parse($startTime);
                $endTimeObj = Carbon::parse($endTime);
                
                // Log the values for debugging
                Log::info('Form data before API call', [
                    'event_title' => $this->eventName,
                    'start_time' => $startTime,
                    'start_time_parsed' => $startTimeObj->toIso8601String(),
                    'end_time' => $endTime,
                    'end_time_parsed' => $endTimeObj->toIso8601String(),
                    'description' => $this->description,
                    'location' => $this->location,
                ]);
                
                $customData = [
                    'event_title' => $this->eventName,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'start_time_obj' => $startTimeObj,
                    'end_time_obj' => $endTimeObj,
                    'description' => $this->description,
                    'location' => $this->location,
                ];
            } catch (\Exception $e) {
                Log::error('Error parsing form dates', [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'error' => $e->getMessage()
                ]);
                
                $this->addError('datetime', 'Invalid date format. Please check the date fields.');
                throw $e;
            }

            // Attempt to create the event
            $googleCalendarEvent = $calendarService->createEvent(
                $this->instructionRequest,
                $customData
            );

            // Log successful event creation
            Log::info('Google Calendar event created via Livewire', [
                'request_id' => $this->instructionRequest->id,
                'event_id' => $googleCalendarEvent->id
            ]);

            // Dispatch event to close modal and potentially refresh page
            $this->dispatch('googleCalendarEventCreated');

            // Optional: Add a success flash message
            session()->flash('success', 'Google Calendar event created successfully.');

        } catch (InvalidCalendarConfigurationException $e) {
            // Log the configuration error
            Log::warning('Invalid calendar configuration', [
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
            Log::error('Unexpected error creating Google Calendar event', [
                'request_id' => $this->instructionRequest->id,
                'error' => $e->getMessage()
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
}
