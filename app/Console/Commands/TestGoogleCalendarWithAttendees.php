<?php

// app/Console/Commands/TestGoogleCalendarWithAttendees.php
namespace App\Console\Commands;

use App\Models\InstructionRequests;
use App\Services\CalendarService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestGoogleCalendarWithAttendees extends Command
{
    protected $signature = 'calendar:test-attendees {request_id? : ID of an instruction request to use for testing}';
    protected $description = 'Test Google Calendar integration with attendees using impersonation';

    /**
     * @var CalendarService
     */
    protected $calendarService;

    /**
     * Create a new command instance.
     *
     * @param CalendarService $calendarService
     */
    public function __construct(CalendarService $calendarService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
    }

    public function handle()
    {
        $this->info('Testing Google Calendar integration with attendees...');

        // Check if impersonation email is configured
        $impersonationEmail = config('google-calendar.user_to_impersonate');

        if (empty($impersonationEmail)) {
            $this->error('Error: Impersonation email is not configured.');
            $this->line('Please set the GOOGLE_CALENDAR_IMPERSONATE_EMAIL environment variable.');
            return Command::FAILURE;
        }

        $this->info('Using impersonation email: ' . $impersonationEmail);

        try {
            // Get instruction request ID from argument or use latest one
            $requestId = $this->argument('request_id');

            if (!$requestId) {
                $this->info('No request ID provided, using the latest instruction request...');
                $latestRequest = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian'])
                    ->whereHas('detail', function ($query) {
                        $query->whereNotNull('instruction_datetime');
                    })
                    ->latest()
                    ->first();

                if (!$latestRequest) {
                    $this->error('No suitable instruction requests found. Please create one first or specify an ID.');
                    return Command::FAILURE;
                }

                $requestId = $latestRequest->id;
            }

            $request = InstructionRequests::with(['instructor', 'detail', 'campus', 'librarian'])
                ->findOrFail($requestId);

            $this->info("Using instruction request #{$request->id} for testing.");

            // Display request details for verification
            $this->line("Instructor: {$request->instructor->display_name} ({$request->instructor->email})");
            $this->line("Librarian: {$request->detail->assignedLibrarian->display_name ?? 'None'} " .
                         "({$request->detail->assignedLibrarian->email ?? 'None'})");
            $this->line("Campus: {$request->campus->name}");
            $this->line("Calendar ID: {$request->campus->getCalendarId() ?? 'Not set'}");

            if (!$request->campus->getCalendarId()) {
                $this->error("Error: Calendar ID not configured for campus {$request->campus->name}");
                return Command::FAILURE;
            }

            // Test 1: Test calendar service configuration
            $this->info('Testing CalendarService configuration...');
            $result = $this->calendarService->testCalendarService($request);

            if ($result['success']) {
                $this->info('✅ CalendarService test successful');
                $this->line("Impersonation configured: " . ($result['impersonation_configured'] ? 'Yes' : 'No'));
                $this->line("Impersonation user: " . ($result['impersonation_user'] ?? 'Not set'));
            } else {
                $this->error('❌ CalendarService test failed: ' . ($result['error'] ?? 'Unknown error'));
                $this->table(['Key', 'Value'], collect($result)->map(function ($value, $key) {
                    return [$key, is_scalar($value) ? $value : json_encode($value)];
                })->toArray());
                return Command::FAILURE;
            }

            // Only proceed with event creation if user confirms
            if (!$this->confirm('Would you like to try creating a calendar event with attendees?', true)) {
                $this->info('Cancelled event creation.');
                return Command::SUCCESS;
            }

            // Test 2: Create an event with attendees using the CalendarService
            $this->info('Attempting to create a calendar event with attendees...');

            $customData = [
                'event_title' => 'Test Event with Attendees - ' . now()->format('Y-m-d H:i:s')
            ];

            $googleCalendarEvent = $this->calendarService->createEvent($request, $customData);

            $this->info('✅ Successfully created event with ID: ' . $googleCalendarEvent->google_event_id);
            $this->line("Event title: {$googleCalendarEvent->event_title}");
            $this->line("Start time: {$googleCalendarEvent->start_time->format('Y-m-d H:i:s')}");
            $this->line("End time: {$googleCalendarEvent->end_time->format('Y-m-d H:i:s')}");

            // Display attendees
            $attendees = json_decode($googleCalendarEvent->attendees, true);
            if (!empty($attendees)) {
                $this->info('Attendees:');
                foreach ($attendees as $attendee) {
                    $this->line("- {$attendee['email']}");
                }
            } else {
                $this->warn('No attendees were added to the event.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Google Calendar attendees test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // For debugging purposes, show more details in verbose mode
            if ($this->getOutput()->isVerbose()) {
                $this->line('Stack trace:');
                $this->line($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
