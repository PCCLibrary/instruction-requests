<?php

// app/Console/Commands/TestGoogleCalendar.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TestGoogleCalendar extends Command
{
    protected $signature = 'calendar:test';
    protected $description = 'Test Google Calendar integration';

    public function handle()
    {
        $this->info('Testing Google Calendar integration...');

        // Define calendar ID from config
        $calendarId = config('google-calendar.calendar_id');
        $this->info('Using calendar ID: ' . $calendarId);

        try {
            // Test 1: List events to verify basic access
            $this->info('Attempting to list events...');
            $events = Event::get(null, null, [], $calendarId); // Use explicit calendar ID
            $this->info('Successfully retrieved ' . count($events) . ' events.');

            // Test 2: Create a simple event
            $this->info('Attempting to create a test event...');
            $event = new Event;
            $event->name = 'Test Event ' . date('Y-m-d H:i:s');
            $event->startDateTime = Carbon::now();
            $event->endDateTime = Carbon::now()->addHour();

            // Save the event, no need to pass the calendar ID again if the default is set
            $createdEvent = $event->save();
            $this->info('Successfully created event with ID: ' . $createdEvent->id);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Google Calendar test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
