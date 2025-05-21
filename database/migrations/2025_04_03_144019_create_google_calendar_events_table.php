<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->id();

            // Change from foreignId to unsignedInteger to match the referenced table's type
            $table->unsignedInteger('instruction_request_id')
                ->comment('Related instruction request');

            $table->string('google_event_id')
                ->nullable()
                ->index()
                ->comment('Unique identifier for the event in Google Calendar');

            $table->string('google_calendar_id')
                ->comment('Identifier of the calendar where the event was created');

            // Keep as foreignId since users table uses id() (bigIncrements)
            $table->unsignedBigInteger('librarian_id')
                ->nullable()
                ->comment('Librarian who created the event');

            // Change to unsignedInteger to match campuses table
            $table->unsignedInteger('campus_id')
                ->nullable()
                ->comment('Campus associated with the event');

            $table->string('event_title')
                ->comment('Title of the calendar event');

            $table->dateTime('start_time')
                ->comment('Start date and time of the event');

            $table->dateTime('end_time')
                ->comment('End date and time of the event');

            $table->text('description')
                ->nullable()
                ->comment('Detailed description of the event');

            $table->string('location')
                ->nullable()
                ->comment('Location of the event');

            $table->json('attendees')
                ->nullable()
                ->comment('JSON array of event attendees');

            $table->json('raw_event_data')
                ->nullable()
                ->comment('Full original event data from Google Calendar API');

            $table->timestamps();

            // Add foreign key constraints separately with explicit references
            $table->foreign('instruction_request_id')
                ->references('id')
                ->on('instruction_requests')
                ->onDelete('cascade');

            $table->foreign('librarian_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('campus_id')
                ->references('id')
                ->on('campuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_calendar_events');
    }
};
