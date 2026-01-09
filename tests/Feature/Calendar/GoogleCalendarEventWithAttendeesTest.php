<?php

namespace Tests\Feature\Calendar;

use App\Models\Campus;
use App\Models\GoogleCalendarEvent;
use App\Models\InstructionRequests;
use App\Models\InstructionRequestDetails;
use App\Models\Instructor;
use App\Models\User;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Spatie\GoogleCalendar\GoogleCalendar;
use Google_Service_Calendar_Event;

class GoogleCalendarEventWithAttendeesTest extends TestCase
{
    use RefreshDatabase;

    protected $mock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the GoogleCalendar class to avoid actual API calls
        $this->mock = Mockery::mock('Spatie\GoogleCalendar\GoogleCalendar');
        $this->app->instance(GoogleCalendar::class, $this->mock);

        // Set up test environment variables with a placeholder value
        config(['google-calendar.user_to_impersonate' => 'test-impersonation-user@example.com']);
    }

    /** @test */
    public function it_can_create_calendar_event_with_attendees()
    {
        // Create test data
        $librarian = User::factory()->create([
            'email' => 'gustavo.lanzas@pcc.edu',
            'display_name' => 'Gustavo Lanzas'
        ]);

        $instructor = Instructor::factory()->create([
            'email' => 'gustavo.lanzas@gmail.com',
            'display_name' => 'Gustavo Instructor'
        ]);

        $campus = Campus::factory()->create([
            'gcal' => 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com'
        ]);

        $request = InstructionRequests::factory()->create([
            'instructor_id' => $instructor->id,
            'campus_id' => $campus->id,
            'status' => 'accepted',
            'instruction_type' => 'on-campus'
        ]);

        $detail = InstructionRequestDetails::factory()->create([
            'instruction_requests_id' => $request->id,
            'assigned_librarian_id' => $librarian->id,
            'instruction_datetime' => Carbon::now()->addDays(7),
            'instruction_duration' => 60
        ]);

        // Mock the Google API response
        $mockEvent = new Google_Service_Calendar_Event();
        $mockEvent->setId('test_event_id_123');

        $this->mock->shouldReceive('insertEvent')
            ->once()
            ->andReturn($mockEvent);

        // Create the service and test event creation
        $calendarService = app(CalendarService::class);

        $customData = [
            'event_title' => 'Test Calendar Event',
            'description' => 'This is a test event with attendees'
        ];

        $googleCalendarEvent = $calendarService->createEvent($request, $customData);

        // Assert that the event was created successfully
        $this->assertInstanceOf(GoogleCalendarEvent::class, $googleCalendarEvent);
        $this->assertEquals('test_event_id_123', $googleCalendarEvent->google_event_id);
        $this->assertEquals($request->id, $googleCalendarEvent->instruction_request_id);

        // The status should be updated to 'in_progress'
        $this->assertEquals('in_progress', $request->fresh()->status);
    }

    /** @test */
    public function it_throws_exception_when_impersonation_not_configured()
    {
        // Clear the impersonation user
        config(['google-calendar.user_to_impersonate' => null]);

        // Create test data
        $librarian = User::factory()->create([
            'email' => 'gustavo.lanzas@pcc.edu',
            'display_name' => 'Gustavo Lanzas'
        ]);

        $instructor = Instructor::factory()->create([
            'email' => 'gustavo.lanzas@gmail.com',
            'display_name' => 'Gustavo Instructor'
        ]);

        $campus = Campus::factory()->create([
            'gcal' => 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com'
        ]);

        $request = InstructionRequests::factory()->create([
            'instructor_id' => $instructor->id,
            'campus_id' => $campus->id,
            'status' => 'accepted',
            'instruction_type' => 'on-campus'
        ]);

        $detail = InstructionRequestDetails::factory()->create([
            'instruction_requests_id' => $request->id,
            'assigned_librarian_id' => $librarian->id,
            'instruction_datetime' => Carbon::now()->addDays(7),
            'instruction_duration' => 60
        ]);

        // Set up expectation for exception
        $this->expectException(\App\Exceptions\InvalidCalendarConfigurationException::class);
        $this->expectExceptionMessage('Cannot add attendees without proper impersonation configuration');

        // Create the service and attempt event creation
        $calendarService = app(CalendarService::class);

        $customData = [
            'event_title' => 'Test Calendar Event',
            'description' => 'This is a test event with attendees'
        ];

        // This should throw the exception
        $calendarService->createEvent($request, $customData);
    }

    /** @test */
    public function it_reports_correct_impersonation_configuration_status()
    {
        // Set up the impersonation user with a placeholder value
        config(['google-calendar.user_to_impersonate' => 'test-impersonation-user@example.com']);

        // Create test data
        $librarian = User::factory()->create([
            'email' => 'gustavo.lanzas@pcc.edu',
            'display_name' => 'Gustavo Lanzas'
        ]);

        $instructor = Instructor::factory()->create([
            'email' => 'gustavo.lanzas@gmail.com',
            'display_name' => 'Gustavo Instructor'
        ]);

        $campus = Campus::factory()->create([
            'gcal' => 'c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com'
        ]);

        $request = InstructionRequests::factory()->create([
            'instructor_id' => $instructor->id,
            'campus_id' => $campus->id,
            'status' => 'received',
            'instruction_type' => 'on-campus'
        ]);

        $detail = InstructionRequestDetails::factory()->create([
            'instruction_requests_id' => $request->id,
            'assigned_librarian_id' => $librarian->id,
            'instruction_datetime' => Carbon::now()->addDays(7),
            'instruction_duration' => 60
        ]);

        // Mock the Google API response
        $mockEvent = new Google_Service_Calendar_Event();

        $this->mock->shouldReceive('insertEvent')
            ->never(); // We're not creating an event in this test

        // Create the service and test configuration check
        $calendarService = app(CalendarService::class);

        // Call the test method which includes the impersonation check
        $result = $calendarService->testCalendarService($request);

        // Assert that impersonation is correctly reported as configured
        $this->assertTrue($result['success']);
        $this->assertTrue($result['impersonation_configured']);
        $this->assertEquals('test-impersonation-user@example.com', $result['impersonation_user']);

        // Now clear the impersonation user and test again
        config(['google-calendar.user_to_impersonate' => null]);

        $result = $calendarService->testCalendarService($request);

        // Should still be successful overall but impersonation should be reported as not configured
        $this->assertTrue($result['success']);
        $this->assertFalse($result['impersonation_configured']);
        $this->assertNull($result['impersonation_user']);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
