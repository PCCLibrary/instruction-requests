<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;

class TestGoogleCalendarWithAttendees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:google-calendar-attendees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Google Calendar integration by creating an event with attendees directly using Spatie';

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
        'event_title' => 'TEST - Library Instruction Session',
        'description' => 'This is a test event created by the command line tool. Please ignore or delete.',
        'location' => 'Sylvania Campus - Room 220',

        // Event duration in minutes
        'duration' => 30
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Calendar attendee test using Spatie directly...');

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
        }

        $this->info("Calendar ID: " . $this->config['calendar_id']);
        $impersonationEmail = config('google-calendar.user_to_impersonate');
        $this->info("Impersonation email: " . ($impersonationEmail ?: 'Not configured'));
        $this->info("Auth profile: " . config('google-calendar.default_auth_profile'));
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
        Log::channel('daily')->info('TestGoogleCalendarWithAttendees: Starting direct Spatie test', [
            'config' => $this->config,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'environment' => app()->environment(),
            'calendar_id' => $this->config['calendar_id'],
            'user_to_impersonate' => config('google-calendar.user_to_impersonate'),
            'credentials_path' => $credentialsPath,
            'credentials_exists' => file_exists($credentialsPath)
        ]);

        try {
            // Create the event using Spatie directly
            $this->info('Creating Google Calendar event directly with Spatie...');
            $this->newLine();

            // TESTING: Direct token test using Google API Client
            $this->info("Attempting direct Google API Client test...");

            // Create a client instance directly
            $client = new \Google_Client();
            $client->setAuthConfig($credentialsPath);

            // Configure the client with the exact scope from Admin Console
            $client->setScopes(['https://www.googleapis.com/auth/calendar.events']);

            // Set subject for impersonation
            $client->setSubject($impersonationEmail);

            // Show what we're using
            $this->info("Using custom client with scopes: " . json_encode($client->getScopes()));
            $this->info("Impersonating: " . $impersonationEmail);

            try {
                // Try to fetch a token directly to see if it works
                $token = $client->fetchAccessTokenWithAssertion();
                $this->info("✅ Successfully obtained access token directly");
                $this->info("Token type: " . ($token['token_type'] ?? 'unknown'));
                $this->info("Expires in: " . ($token['expires_in'] ?? 'unknown'));

                // Now try using the Spatie approach
                $this->info("Proceeding with Spatie Event creation after successful token acquisition");

                // Create a new event
                $event = new Event;

                // Set calendar ID
                $calendarId = $this->config['calendar_id'];

                // Set event properties
                $event->name = $this->config['event_title'];
                $event->startDateTime = $startTime;
                $event->endDateTime = $endTime;
                $event->description = $this->config['description'];
                $event->location = $this->config['location'];

                // Add attendees
                $event->addAttendee(['email' => $this->config['librarian_email']]);
                $event->addAttendee(['email' => $this->config['instructor_email']]);

                // Save event to Google Calendar
                $googleEvent = $event->save(null, ['conferenceDataVersion' => 0], $calendarId);

                // Display success information
                $this->info('✅ Success! Google Calendar event created:');
                $this->newLine();
                $this->info("📅 Event Title: {$event->name}");
                $this->info("🔗 Google Event ID: {$googleEvent->id}");
                $this->info("🕒 Start Time: {$startTime->format('Y-m-d H:i:s')}");
                $this->info("🕓 End Time: {$endTime->format('Y-m-d H:i:s')}");
                $this->info("📍 Location: {$event->location}");
                $this->info("👤 Attendees: {$this->config['librarian_email']}, {$this->config['instructor_email']}");

                Log::channel('daily')->info('TestGoogleCalendarWithAttendees: Calendar event created successfully', [
                    'google_event_id' => $googleEvent->id,
                    'calendar_id' => $calendarId
                ]);

                // Check for cleaning up the test data
                if ($this->confirm('Do you want to delete the test event?', true)) {
                    $this->info('Deleting test event...');

                    // Delete the event directly from Google Calendar
                    $eventToDelete = Event::find($googleEvent->id, $calendarId);
                    $eventToDelete->delete();

                    $this->info('Test event deleted.');
                } else {
                    $this->warn('Test event was not deleted. Remember to clean up manually later.');
                }

            } catch (\Exception $e) {
                $this->error("❌ Failed to get token directly: " . $e->getMessage());
                throw $e; // Re-throw to stop execution
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to create Google Calendar event:');
            $this->error($e->getMessage());

            Log::channel('daily')->error('TestGoogleCalendarWithAttendees: Failed to create calendar event', [
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
            $this->info('User to Impersonate: ' . config('google-calendar.user_to_impersonate'));
            $this->info('Application Environment: ' . app()->environment());

            return Command::FAILURE;
        }
    }
}
