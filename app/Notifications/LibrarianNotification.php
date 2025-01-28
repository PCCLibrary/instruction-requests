<?php

// LibrarianNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification for librarians about instruction requests
 *
 * Sends email notifications to librarians about new and updated
 * library instruction requests that require their attention.
 */
class LibrarianNotification extends BaseEmailNotification
{
    /**
     * Get the mail representation of the notification
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->view('emails.librarian.new_request', $this->prepareMailData());
    }
}
