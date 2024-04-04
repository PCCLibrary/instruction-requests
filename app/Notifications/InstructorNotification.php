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

    protected $instructionRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InstructionRequest $instructionRequest)
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
            ->greeting('Hello!')
            ->line('Your instruction request has been submitted successfully.')
            ->line('Thank you for using our application!');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) : array
    {
        return [
            //
        ];
    }
}
