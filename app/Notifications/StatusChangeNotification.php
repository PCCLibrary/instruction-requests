<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Instructor;
use App\Models\Campus;
use App\Models\InstructionRequest;

class StatusChangeNotification extends Notification
{
    use Queueable;

    public $instructionRequest;
    public $subject;
    public $instructor_name;
    public $date_requested;
    public $campus_name;

    /**
     * Create a new notification instance.
     *
     * @param InstructionRequest $instructionRequest
     * @param string $subject
     * @return void
     */
    public function __construct(InstructionRequest $instructionRequest, string $subject)
    {
        $this->instructionRequest = $instructionRequest;
        $this->subject = $subject;

        $instructor = Instructor::find($instructionRequest->instructor_id);
        $this->instructor_name = $instructor ? $instructor->display_name : 'Unknown Instructor';

        $this->date_requested = $instructionRequest->date_requested ? $instructionRequest->date_requested->format('m/d/Y') : 'N/A';

        $campus = Campus::find($instructionRequest->campus_id);
        $this->campus_name = $campus ? $campus->name : 'Unknown Campus';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.status_change', [
                'request' => $this->instructionRequest,
                'instructor_name' => $this->instructor_name,
                'date_requested' => $this->date_requested,
                'campus_name' => $this->campus_name,
            ]);
    }
}
