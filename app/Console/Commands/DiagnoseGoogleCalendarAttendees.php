<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\GoogleCalendarEvent;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\GoogleCalendar;

class DiagnoseGoogleCalendarAttendees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:google-calendar-attendees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostic command to test Google Calendar attendee functionality in isolation';

    /**
     * Hardcoded test configuration
     */
    private array $config = [
        // Calendar ID - directly used in test
        'calendar_id' => 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com',

        // Attendee emails - update these as needed
        'librarian_email' => 'gustavo.lanzas@pcc.edu',
        'instructor_email' => 'lisa.morrow@pcc.edu',

        // Event details - update these as needed
        'event_title' => 'DIAGNOSTIC - Google Calendar Attendee Test',
        'description' => 'This is a diagnostic test event that directly tests the attendee functionality. Please ignore or delete.',
        'location' => 'Google Calendar Integration Testing',

        // Event duration in minutes
        'duration' => 30
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Calendar attendee diagnostic test...');
        $this->info('This test directly calls the Google Calendar API to diagnose attendee functionality.');
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
        $this->newLine();

        // Check for impersonation configuration
        $impersonationUser = config('google-calendar.user_to_impersonate');

        if (empty($impersonationUser)) {
            $this->error('❌ No impersonation user configured. This will likely cause the attendee functionality to fail.');
            $this->warn('Set GOOGLE_CALENDAR_IMPERSONATE_EMAIL in your .env file to test attendee functionality.');

            if (!$this->confirm('Continue without impersonation configuration?', false)) {
                return Command::FAILURE;
            }
        } else {
            $this->info("✅ Impersonation configured with user: {$impersonationUser}");
        }

        // Display diagnostic information
        $this->info('--- Configuration Diagnostics ---');
        $this->info('Calendar ID: ' . $this->config['calendar_id']);
        $this->info('Environment: ' . app()->environment());
        $this->info('User to Impersonate: ' . ($impersonationUser ?? 'Not configured'));
        $this->info('Google Calendar Package: spatie/laravel-google-calendar');
        $this->newLine();

        // Enable detailed logging
        Log::channel('daily')->info('DiagnoseGoogleCalendarAttendees: Starting diagnostic test', [
            'config' => $this->config,
            'start_time' => $startTime->toDateTimeString(),
            'impersonation_user' => $impersonationUser,
            'environment' => app()->environment()
        ]);

        try {
            // Create direct Google Calendar event using Spatie package
            $this->info('Creating Google Calendar event directly (bypassing CalendarService)...');

            // Create a Google Calendar instance
            $googleCalendar = App::make(GoogleCalendar::class, [
                'calendarId' => $this->config['calendar_id']
            ]);

            // Create a native Google Calendar Event object
            $googleEvent = new Google_Service_Calendar_Event();
            $googleEvent->setSummary($this->config['event_title']);

            // Set start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startTime->format(\DateTime::RFC3339));
            $start->setTimeZone($startTime->getTimezone()->getName());
            $googleEvent->setStart($start);

            // Set end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endTime->format(\DateTime::RFC3339));
            $end->setTimeZone($endTime->getTimezone()->getName());
            $googleEvent->setEnd($end);

            // Set description and location
            $googleEvent->setDescription($this->config['description']);
            $googleEvent->setLocation($this->config['location']);

            // Add attendees
            $attendees = [];

            // Add librarian as attendee
            $librarianAttendee = new Google_Service_Calendar_EventAttendee();
            $librarianAttendee->setEmail($this->config['librarian_email']);
            $librarianAttendee->setDisplayName('Gustavo Lanzas (Test Librarian)');
            $attendees[] = $librarianAttendee;

            // Add instructor as attendee
            $instructorAttendee = new Google_Service_Calendar_EventAttendee();
            $instructorAttendee->setEmail($this->config['instructor_email']);
            $instructorAttendee->setDisplayName('Lisa Morrow (Test Instructor)');
            $attendees[] = $instructorAttendee;

            // Set attendees
            $googleEvent->setAttendees($attendees);

            // Insert the event
            $createdEvent = $googleCalendar->insertEvent($googleEvent);

            Log::channel('daily')->info('DiagnoseGoogleCalendarAttendees: Calendar event created successfully', [
                'google_event_id' => $createdEvent->id,
                'html_link' => $createdEvent->htmlLink ?? 'Not available'
            ]);

            $this->info('✅ Success! Google Calendar event created with attendees:');
            $this->newLine();
            $this->info("📅 Event Title: {$this->config['event_title']}");
            $this->info("🔗 Google Event ID: {$createdEvent->id}");
            $this->info("🕒 Start Time: {$startTime->format('Y-m-d H:i:s')}");
            $this->info("🕓 End Time: {$endTime->format('Y-m-d H:i:s')}");
            $this->info("📍 Location: {$this->config['location']}");
            $this->info("👤 Attendees: {$this->config['librarian_email']}, {$this->config['instructor_email']}");
            $this->info("🌐 Event URL: " . ($createdEvent->htmlLink ?? 'Not available'));

            // Check for cleaning up the test data
            if ($this->confirm('Do you want to delete the test event?', true)) {
                $this->info('Deleting test event...');

                // Delete the Google Calendar event
                $googleCalendar->deleteEvent($createdEvent->id);

                $this->info('Test event deleted.');
            } else {
                $this->warn('Test event was not deleted. Remember to clean up manually later.');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Diagnostic test failed:');
            $this->error($e->getMessage());

            Log::channel('daily')->error('DiagnoseGoogleCalendarAttendees: Diagnostic test failed', [
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

            $this->newLine();
            $this->info('Potential Solutions:');
            $this->info('1. Check that GOOGLE_CALENDAR_IMPERSONATE_EMAIL is set in your .env file');
            $this->info('2. Verify the service account has domain-wide delegation enabled in Google Workspace');
            $this->info('3. Ensure the correct OAuth scopes are configured (https://www.googleapis.com/auth/calendar)');
            $this->info('4. Check that the impersonation user has access to create events in the calendar');

            return Command::FAILURE;
        }
    }
}
