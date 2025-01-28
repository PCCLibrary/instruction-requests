<?php
// InstructorNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification for instructors about their instruction requests
 *
 * Sends email notifications to instructors about the status
 * of their library instruction requests.
 */
class InstructorNotification extends BaseEmailNotification
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
            ->view('emails.instructor.request_received', $this->prepareMailData());
    }
}
