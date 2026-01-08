<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'instruction_request_id',
        'notification_type',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'sent_at',
        'sent_by_user_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function instructionRequest(): BelongsTo
    {
        return $this->belongsTo(InstructionRequests::class, 'instruction_request_id');
    }

    public function sentByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
