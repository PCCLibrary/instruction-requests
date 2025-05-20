<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class GoogleCalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'google_calendar_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instruction_request_id',
        'google_event_id',
        'google_calendar_id',
        'librarian_id',
        'campus_id',
        'event_title',
        'start_time',
        'end_time',
        'description',
        'location',
        'attendees',
        'raw_event_data',
        'html_link',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'attendees' => 'json',
        'raw_event_data' => 'json',
    ];

    /**
     * Get the instruction request that owns this calendar event.
     */
    public function instructionRequest(): BelongsTo
    {
        return $this->belongsTo(InstructionRequests::class, 'instruction_request_id');
    }

    /**
     * Get the librarian (user) associated with this calendar event.
     */
    public function librarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }

    /**
     * Get the campus associated with this calendar event.
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
}
