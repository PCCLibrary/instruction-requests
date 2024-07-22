<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class LibrarianNotification extends Notification
{
    use Queueable;

    public $instructionRequest;

    /**
     * Create a new notification instance.
     *
     * @param $instructionRequest
     * @return void
     */
    public function __construct($instructionRequest)
    {
        $this->instructionRequest = $instructionRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable) : MailMessage
    {
        $instructorName = $this->instructionRequest->instructor_name ?? 'Unknown Instructor';
        $campusName = $this->instructionRequest->campus_name ?? 'Unknown Campus';
        $currentDate = now()->format('m/d/Y');

        $subject = "Instruction Request Received: $instructorName - $campusName - $currentDate";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.librarians', ['request' => $this->instructionRequest]);
    }

}
