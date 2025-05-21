<?php

namespace App\Livewire;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Models\InstructionRequests;
use App\Models\User;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

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

            // Use assigned librarian from detail instead of originally requested librarian
            $assignedLibrarian = null;
            if ($this->instructionRequest->detail && $this->instructionRequest->detail->assigned_librarian_id) {
                $assignedLibrarian = User::find($this->instructionRequest->detail->assigned_librarian_id);
                $this->librarianEmail = $assignedLibrarian?->email;
            } else {
                $this->librarianEmail = $this->instructionRequest->librarian?->email;
            }

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
        // First thing - log that we're in the method
        Log::info('CreateGoogleCalendarEventForm: Creating calendar event', [
            'request_id' => $this->instructionRequest->id
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

            // Attempt to create the event using CalendarService
            try {
                // Refresh the instruction request to ensure we have the latest data
                $this->instructionRequest->refresh();

                $googleCalendarEvent = $calendarService->createEvent(
                    $this->instructionRequest,
                    $customData
                );

                // If we get here, the event was created successfully
                Log::info('CreateGoogleCalendarEventForm: Event created successfully', [
                    'request_id' => $this->instructionRequest->id,
                    'event_id' => $googleCalendarEvent->id
                ]);

                // Refresh the request to get the latest status
                $this->instructionRequest->refresh();

                // Log the status after refresh for debugging
                Log::info('CreateGoogleCalendarEventForm: Status after refresh', [
                    'request_id' => $this->instructionRequest->id,
                    'status' => $this->instructionRequest->status
                ]);

                // Use Toaster facade for success notification
                app(Toaster::class)->success('Google Calendar event created successfully.');

                // Dispatch event for client-side reload
                $this->dispatch('googleCalendarEventCreated', [
                    'requestId' => $this->instructionRequest->id,
                    'status' => 'scheduled',
                    'timestamp' => now()->toDateTimeString()
                ]);

                // Clear the form and close modal (if needed)
                $this->reset(['description', 'location']);
                $this->dispatch('close-modal');
            } catch (\Exception $serviceException) {
                // Log error from CalendarService call
                Log::error('CreateGoogleCalendarEventForm: Error creating calendar event', [
                    'error' => $serviceException->getMessage(),
                    'request_id' => $this->instructionRequest->id
                ]);

                throw $serviceException;
            }

        } catch (InvalidCalendarConfigurationException $e) {
            // Log the configuration error
            Log::warning('CreateGoogleCalendarEventForm: Invalid calendar configuration', [
                'request_id' => $this->instructionRequest->id,
                'context' => $e->getContext()
            ]);

            // Special handling for impersonation issues
            if (strpos($e->getMessage(), 'impersonation') !== false) {
                Log::warning('Calendar impersonation configuration issue', [
                    'context' => $e->getContext()
                ]);

                $errorMessage = "Calendar configuration issue: Impersonation user not properly configured. " .
                    "Please contact the system administrator to set the GOOGLE_CALENDAR_IMPERSONATE_EMAIL environment variable.";

                // Use Toaster facade for error
                app(Toaster::class)->error($errorMessage);

                $this->addError('calendar', $errorMessage);
            } else {
                // Add error with link to edit campus for other issues
                $campusId = $e->getContextValue('campus_id');
                $campusName = $e->getContextValue('campus_name', 'this campus');

                $errorMessage = "Invalid calendar configuration for {$campusName}. Please " .
                    "update the calendar in the campus settings.";

                // Use Toaster facade for error
                app(Toaster::class)->error($errorMessage);

                $this->addError('calendar',
                    "Invalid calendar configuration for {$campusName}. Please " .
                    "<a href='" . route('campuses.edit', $campusId) . "' class='underline'>update the calendar</a>."
                );
            }
        } catch (\Exception $e) {
            // Log any unexpected errors
            Log::error('CreateGoogleCalendarEventForm: Unexpected error creating event', [
                'request_id' => $this->instructionRequest->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            // Add a generic error message
            $errorMessage = 'An unexpected error occurred. Please try again.';

            // Use Toaster facade for error
            app(Toaster::class)->error($errorMessage);

            $this->addError('calendar', $errorMessage);
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
