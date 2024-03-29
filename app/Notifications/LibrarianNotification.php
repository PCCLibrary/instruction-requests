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
    public $viewUrl;

    /**
     * Create a new notification instance.
     *
     * @param $instructionRequest
     * @param string $viewUrl
     * @return void
     */
    public function __construct($instructionRequest, string $viewUrl)
    {
        $this->instructionRequest = $instructionRequest;
        $this->viewUrl = $viewUrl;
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
            ->line('A new instruction request has been received.')
            ->action('View Request', $this->viewUrl)
            ->line('Testing test');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
