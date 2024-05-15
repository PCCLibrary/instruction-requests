<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InstructionRequest;

class InstructorNotification extends Notification
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
        return (new MailMessage)
            ->subject('Your Instruction Request Has Been Submitted')
            ->view('emails.instructors', ['request' => $this->instructionRequest]);
    }

}
