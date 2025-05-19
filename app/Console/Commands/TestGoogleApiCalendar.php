<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;

class TestGoogleApiCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:google-api-calendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Google Calendar integration by creating an event with attendees using Google API Client directly';

    /**
     * Hardcoded test configuration
     */
    private array $config = [
        // Calendar ID - directly used in the test
        'calendar_id' => 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com',

        // Attendee emails - update these as needed
        'librarian_email' => 'gustavo.lanzas@pcc.edu',
        'instructor_email' => 'lisa.morrow@pcc.edu',

        // Event details - update these as needed
        'event_title' => 'TEST - API Client Library Instruction Session',
        'description' => 'This is a test event created by the command line tool using the Google API Client directly. Please ignore or delete.',
        'location' => 'Sylvania Campus - Room 220',

        // Event duration in minutes
        'duration' => 30
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Calendar attendee test using Google API Client directly...');

        // Show all configuration values first for diagnostics
        $this->info('--- Configuration Diagnostics ---');
        $credentialsPath = base_path(env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_JSON_LOCATION'));
        $this->info("Service account credentials path: $credentialsPath");

        if (file_exists($credentialsPath)) {
            $this->info("✅ Credentials file exists");
            $credentialsJson = json_decode(file_get_contents($credentialsPath), true);
            $this->info("Service account email: " . ($credentialsJson['client_email'] ?? 'Not found'));
            $this->info("Service account client ID: " . ($credentialsJson['client_id'] ?? 'Not found'));
        } else {
            $this->error("❌ Credentials file not found at: $credentialsPath");
            return Command::FAILURE;
        }

        $this->info("Calendar ID: " . $this->config['calendar_id']);
        $impersonationEmail = config('google-calendar.user_to_impersonate');
        $this->info("Impersonation email: " . ($impersonationEmail ?: 'Not configured'));
        $this->info("Environment: " . app()->environment());
        $this->newLine();

        // Create a random future date (7-10 days from now) at 10:00 AM
        $daysInFuture = rand(7, 10);
        $startTime = Carbon::now()
            ->addDays($daysInFuture)
            ->setHour(10)
            ->setMinute(0)
            ->setSecond(0);

        $endTime = (clone $startTime)->addMinutes($this->config['duration']);

        $this->info("Test will create an event on: " . $startTime->format('l, F j, Y \a\t g:i A'));

        // Log configuration information
        Log::channel('daily')->info('TestGoogleApiCalendar: Starting direct Google API Client test', [
            'config' => $this->config,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'environment' => app()->environment(),
            'calendar_id' => $this->config['calendar_id'],
            'user_to_impersonate' => $impersonationEmail,
            'credentials_path' => $credentialsPath,
            'credentials_exists' => file_exists($credentialsPath)
        ]);

        try {
            $this->info('Creating Google API Client...');

            // Initialize the Google API Client
            $client = new Google_Client();
            $client->setAuthConfig($credentialsPath);

            // Configure the client with the exact scope from Admin Console
            // Using the specific scope that worked in the token test
            $client->setScopes(['https://www.googleapis.com/auth/calendar.events']);

            // Log all configured scopes
            $this->info("Using scopes: " . json_encode($client->getScopes()));
            Log::channel('daily')->info('TestGoogleApiCalendar: Configured scopes', [
                'scopes' => $client->getScopes()
            ]);

            // Set subject for impersonation if configured
            if ($impersonationEmail) {
                $client->setSubject($impersonationEmail);
                $this->info("Impersonating: " . $impersonationEmail);
                Log::channel('daily')->info('TestGoogleApiCalendar: Using impersonation', [
                    'email' => $impersonationEmail
                ]);
            } else {
                $this->warn("No impersonation email configured, this may cause issues with attendees");
                Log::channel('daily')->warning('TestGoogleApiCalendar: No impersonation email configured');
            }

            try {
                // Try to fetch a token directly to see if it works
                $this->info("Attempting to acquire access token...");
                $token = $client->fetchAccessTokenWithAssertion();
                $this->info("✅ Successfully obtained access token directly");
                $this->info("Token type: " . ($token['token_type'] ?? 'unknown'));
                $this->info("Expires in: " . ($token['expires_in'] ?? 'unknown'));

                Log::channel('daily')->info('TestGoogleApiCalendar: Successfully acquired access token', [
                    'token_type' => $token['token_type'] ?? 'unknown',
                    'expires_in' => $token['expires_in'] ?? 'unknown'
                ]);

                // Create Calendar Service
                $this->info("Creating Google_Service_Calendar instance...");
                $service = new Google_Service_Calendar($client);

                // Create event object
                $this->info("Creating event object...");
                $event = new Google_Service_Calendar_Event();

                // Set basic event properties
                $event->setSummary($this->config['event_title']);
                $event->setDescription($this->config['description']);
                $event->setLocation($this->config['location']);

                // Set start time
                $startDateTime = new Google_Service_Calendar_EventDateTime();
                $startDateTime->setDateTime($startTime->format(DateTime::RFC3339));
                $startDateTime->setTimeZone('America/Los_Angeles');
                $event->setStart($startDateTime);

                // Set end time
                $endDateTime = new Google_Service_Calendar_EventDateTime();
                $endDateTime->setDateTime($endTime->format(DateTime::RFC3339));
                $endDateTime->setTimeZone('America/Los_Angeles');
                $event->setEnd($endDateTime);

                // Add attendees
                $attendees = [];

                // Add librarian attendee
                $this->info("Adding librarian attendee: " . $this->config['librarian_email']);
                $librarianAttendee = new Google_Service_Calendar_EventAttendee();
                $librarianAttendee->setEmail($this->config['librarian_email']);
                $attendees[] = $librarianAttendee;

                // Add instructor attendee
                $this->info("Adding instructor attendee: " . $this->config['instructor_email']);
                $instructorAttendee = new Google_Service_Calendar_EventAttendee();
                $instructorAttendee->setEmail($this->config['instructor_email']);
                $attendees[] = $instructorAttendee;

                // Set attendees on the event
                $event->setAttendees($attendees);

                // Log event data before submission
                Log::channel('daily')->info('TestGoogleApiCalendar: Submitting event data', [
                    'summary' => $event->getSummary(),
                    'start' => $startTime->format(DateTime::RFC3339),
                    'end' => $endTime->format(DateTime::RFC3339),
                    'location' => $event->getLocation(),
                    'description' => $event->getDescription(),
                    'attendees' => [
                        $this->config['librarian_email'],
                        $this->config['instructor_email']
                    ],
                    'calendar_id' => $this->config['calendar_id']
                ]);

                // Insert the event
                $this->info("Inserting event into Google Calendar...");
                $googleEvent = $service->events->insert(
                    $this->config['calendar_id'],
                    $event,
                    ['sendUpdates' => 'all', 'conferenceDataVersion' => 0]
                );

                // Display success information
                $this->info('✅ Success! Google Calendar event created:');
                $this->newLine();
                $this->info("📅 Event Title: {$event->getSummary()}");
                $this->info("🔗 Google Event ID: {$googleEvent->getId()}");
                $this->info("🕒 Start Time: {$startTime->format('Y-m-d H:i:s')}");
                $this->info("🕓 End Time: {$endTime->format('Y-m-d H:i:s')}");
                $this->info("📍 Location: {$event->getLocation()}");
                $this->info("👤 Attendees: {$this->config['librarian_email']}, {$this->config['instructor_email']}");
                $this->info("📎 Event Link: {$googleEvent->getHtmlLink()}");

                Log::channel('daily')->info('TestGoogleApiCalendar: Calendar event created successfully', [
                    'google_event_id' => $googleEvent->getId(),
                    'calendar_id' => $this->config['calendar_id'],
                    'event_link' => $googleEvent->getHtmlLink()
                ]);

                // Check for cleaning up the test data
                if ($this->confirm('Do you want to delete the test event?', true)) {
                    $this->info('Deleting test event...');

                    // Delete the event directly using the Google API Client
                    $service->events->delete($this->config['calendar_id'], $googleEvent->getId());

                    $this->info('Test event deleted.');
                    Log::channel('daily')->info('TestGoogleApiCalendar: Test event deleted', [
                        'google_event_id' => $googleEvent->getId()
                    ]);
                } else {
                    $this->warn('Test event was not deleted. Remember to clean up manually later.');
                    $this->info("Event link for manual cleanup: " . $googleEvent->getHtmlLink());
                }

            } catch (\Exception $e) {
                $this->error("❌ Failed during API operation: " . $e->getMessage());
                Log::channel('daily')->error('TestGoogleApiCalendar: API operation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to stop execution
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to create Google Calendar event:');
            $this->error($e->getMessage());

            Log::channel('daily')->error('TestGoogleApiCalendar: Failed to create calendar event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Show detailed error information
            $this->newLine();
            $this->info('--- Detailed Error Information ---');
            $this->info('Error Type: ' . get_class($e));
            $this->info('Error Code: ' . $e->getCode());
            $this->info('Error File: ' . $e->getFile() . ' (line ' . $e->getLine() . ')');
            $this->info('Error Message: ' . $e->getMessage());

            // Show configuration
            $this->newLine();
            $this->info('--- Configuration ---');
            $this->info('Calendar ID: ' . $this->config['calendar_id']);
            $this->info('User to Impersonate: ' . $impersonationEmail);
            $this->info('Application Environment: ' . app()->environment());

            return Command::FAILURE;
        }
    }
}
