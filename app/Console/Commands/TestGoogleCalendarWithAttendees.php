<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Classes;
use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequestDetails;
use App\Models\InstructionRequests;
use App\Models\Instructor;
use App\Models\User;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Test Google Calendar integration by creating an event with attendees';

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
    public function handle(CalendarService $calendarService)
    {
        $this->info('Starting Google Calendar attendee test...');

        // Create a random future date (7-10 days from now) at 10:00 AM
        $daysInFuture = rand(7, 10);
        $startTime = Carbon::now()
            ->addDays($daysInFuture)
            ->setHour(10)
            ->setMinute(0)
            ->setSecond(0);

        $endTime = (clone $startTime)->addMinutes($this->config['duration']);

        $this->info("Test will create an event on: " . $startTime->format('l, F j, Y \a\t g:i A'));

        // Create a minimal instruction request and details
        $this->info('Creating test instruction request...');

        try {
            $instructor = $this->createOrFindInstructor();
            $class = $this->createOrFindClass();
            $campus = $this->createOrFindCampus();

            // Create the instruction request
            $instructionRequest = $this->createTestInstructionRequest(
                $instructor->id,
                $class->id,
                $campus->id,
                $startTime
            );

            // Enable detailed logging for debugging
            Log::channel('daily')->info('TestGoogleCalendarWithAttendees: Starting test', [
                'config' => $this->config,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'request_id' => $instructionRequest->id,
                'environment' => app()->environment(),
                'user_to_impersonate' => config('google-calendar.user_to_impersonate')
            ]);

            // Prepare data for the event
            $eventData = [
                'event_title' => $this->config['event_title'],
                'start_time' => $startTime->format('Y-m-d\TH:i'),
                'end_time' => $endTime->format('Y-m-d\TH:i'),
                'start_time_obj' => $startTime,
                'end_time_obj' => $endTime,
                'description' => $this->config['description'],
                'location' => $this->config['location']
            ];

            $this->info('Creating Google Calendar event...');
            $this->newLine();

            // Create the event
            try {
                $createdEvent = $calendarService->createEvent($instructionRequest, $eventData);

                $this->info('✅ Success! Google Calendar event created:');
                $this->newLine();
                $this->info("📅 Event Title: {$createdEvent->event_title}");
                $this->info("🔗 Google Event ID: {$createdEvent->google_event_id}");
                $this->info("🕒 Start Time: {$createdEvent->start_time}");
                $this->info("🕓 End Time: {$createdEvent->end_time}");
                $this->info("📍 Location: {$createdEvent->location}");
                $this->info("👤 Attendees: {$this->config['librarian_email']}, {$this->config['instructor_email']}");

                Log::channel('daily')->info('TestGoogleCalendarWithAttendees: Calendar event created successfully', [
                    'google_event_id' => $createdEvent->google_event_id,
                    'request_id' => $instructionRequest->id
                ]);

                // Check for cleaning up the test data
                if ($this->confirm('Do you want to delete the test event and data?', true)) {
                    $this->info('Deleting test data...');

                    // Delete the Google Calendar event
                    $calendarService->deleteEvent($createdEvent);

                    // Delete the instruction request (will cascade to details)
                    $instructionRequest->delete();

                    $this->info('Test data deleted.');
                } else {
                    $this->warn('Test data was not deleted. Remember to clean up manually later.');
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

        } catch (\Exception $e) {
            $this->error('❌ Error during test setup:');
            $this->error($e->getMessage());

            Log::channel('daily')->error('TestGoogleCalendarWithAttendees: Error during test setup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Create or find a test instructor
     */
    private function createOrFindInstructor(): Instructor
    {
        return Instructor::firstOrCreate(
            ['email' => $this->config['instructor_email']],
            [
                'name' => 'Test Instructor',
                'display_name' => 'Test Instructor',
                'pronouns' => 'they/them',
                'phone' => '555-123-4567'
            ]
        );
    }

    /**
     * Create or find a test class
     */
    private function createOrFindClass(): Classes
    {
        return Classes::firstOrCreate(
            [
                'department_code' => 'TEST',
                'course_number' => '101',
                'course_crn' => 'TEST-CRN'
            ],
            [
                'course_name' => 'Test Course'
            ]
        );
    }

    /**
     * Create or find a test campus
     */
    private function createOrFindCampus(): Campus
    {
        return Campus::firstOrCreate(
            ['code' => 'TEST'],
            [
                'name' => 'Test Campus',
                'gcal' => $this->config['calendar_id']
            ]
        );
    }

    /**
     * Create a minimal test instruction request
     */
    private function createTestInstructionRequest(int $instructorId, int $classId, int $campusId, Carbon $startTime): InstructionRequests
    {
        // Create the instruction request
        $instructionRequest = InstructionRequests::create([
            'instruction_type' => 'on-campus',
            'instructor_id' => $instructorId,
            'class_id' => $classId,
            'campus_id' => $campusId,
            'department' => 'TEST',
            'course_number' => '101',
            'course_crn' => 'TEST-CRN',
            'number_of_students' => 25,
            'status' => 'accepted', // Required for calendar events
            'duration' => $this->config['duration'],
            'preferred_datetime' => $startTime
        ]);

        // Create instruction request details with assigned librarian
        $librarian = User::where('email', $this->config['librarian_email'])->first();

        if (!$librarian) {
            $this->warn("Librarian with email {$this->config['librarian_email']} not found. Creating a detail record without assigned librarian.");
        }

        InstructionRequestDetails::create([
            'instruction_requests_id' => $instructionRequest->id,
            'assigned_librarian_id' => $librarian->id ?? null,
            'instruction_datetime' => $startTime,
            'instruction_duration' => $this->config['duration'],
            'created_by' => 'Test Command',
            'last_updated_by' => 'Test Command',
            'room' => 'Room 220'
        ]);

        // Reload the model with relationships
        return InstructionRequests::with(['detail', 'instructor', 'campus'])->find($instructionRequest->id);
    }
}
