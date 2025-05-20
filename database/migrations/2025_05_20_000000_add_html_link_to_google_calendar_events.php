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
        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->string('html_link')
                ->nullable()
                ->after('raw_event_data')
                ->comment('HTML link to the Google Calendar event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->dropColumn('html_link');
        });
    }
};
