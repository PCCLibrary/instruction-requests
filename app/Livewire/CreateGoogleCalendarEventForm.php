<?php

namespace App\Livewire;

use App\Exceptions\InvalidCalendarConfigurationException;
use App\Exceptions\GoogleCalendarApiException;
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
        try {
            // Use CalendarService to get pre-formatted event data
            $calendarService = app(CalendarService::class);
            $eventData = $calendarService->getEventFormData($this->instructionRequest);

            // Ensure we're using database record for datetime
            $detail = $this->instructionRequest->detail;

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

        } catch (\Exception $e) {
            // Log any errors that occurred during form population
            Log::error('Error populating calendar form data', [
                'request_id' => $this->instructionRequest->id,
                'error' => $e->getMessage()
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
            // Refresh the instruction request to ensure we have the latest data
            $this->instructionRequest->refresh();

            $googleCalendarEvent = $calendarService->createEvent(
                $this->instructionRequest,
                $customData
            );

            // If we get here, the event was created successfully
            // Refresh the request to get the latest status
            $this->instructionRequest->refresh();

            // Use Toaster facade for success notification
            app(Toaster::class)->success('Google Calendar event created successfully.');

            // Dispatch event to close parent modal
            $this->dispatch('close-schedule-modal');

            // Dispatch event for client-side reload
            $this->dispatch('googleCalendarEventCreated', [
                'requestId' => $this->instructionRequest->id,
                'status' => 'scheduled',
                'timestamp' => now()->toDateTimeString()
            ]);

            // Clear the form
            $this->reset(['description', 'location']);

        } catch (GoogleCalendarApiException $e) {
            // Handle Google Calendar API errors with specific messages
            $googleErrorMessage = $e->getMessage();

            // Add contextual help based on error type
            if ($e->isPermissionError()) {
                $helpText = "💡 Contact DST for help with calendar permissions.";
            } elseif ($e->isConfigurationError()) {
                $helpText = "💡 Check the calendar configuration for this campus.";
            } else {
                $helpText = "💡 Please try again in a few minutes.";
            }

            $fullErrorMessage = $googleErrorMessage . " " . $helpText;

            // Log the error for debugging but with less detail
            Log::error('Google Calendar API error', [
                'request_id' => $this->instructionRequest->id,
                'error' => $googleErrorMessage,
                'error_reason' => $e->getGoogleErrorReason(),
                'http_code' => $e->getHttpStatusCode()
            ]);

            // Use Toaster facade for error
            app(Toaster::class)->error($fullErrorMessage);
            $this->addError('calendar', $fullErrorMessage);

        } catch (InvalidCalendarConfigurationException $e) {
            // Handle configuration errors
            // Special handling for impersonation issues
            if (strpos($e->getMessage(), 'impersonation') !== false) {
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
            // Log unexpected errors with minimal detail
            Log::error('Unexpected error creating calendar event', [
                'request_id' => $this->instructionRequest->id,
                'error_message' => $e->getMessage()
            ]);

            // Add a generic error message
            $errorMessage = 'An unexpected error occurred. Please try again.';

            // Use Toaster facade for error
            app(Toaster::class)->error($errorMessage);
            $this->addError('calendar', $errorMessage);
        }
    }

    /**
     * Cancel and close the modal
     */
    public function cancel()
    {
        $this->dispatch('close-modal');
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
