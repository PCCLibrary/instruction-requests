<?php

namespace App\Notifications;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

/**
 * Notification sent when an instruction request is accepted by a librarian
 *
 * This notification informs campus librarians that the request has been accepted.
 * Uses request ID and status information to ensure proper serialization
 * when the notification is queued. Data is loaded from the repository at notification time.
 *
 * Provides email notifications with request-specific details using
 * consistent template data loaded from the repository.
 *
 * @implements \Illuminate\Contracts\Queue\ShouldQueue via parent class
 */
class RequestAcceptedNotification extends BaseInstructionRequestNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * Determines the appropriate email template based on the recipient type
     * and generates a MailMessage with request-specific information. Uses
     * base class methods to load consistent template data.
     *
     * @param object $notifiable The recipient of the notification (User/Librarian)
     * @return \Illuminate\Notifications\Messages\MailMessage
     * @throws \InvalidArgumentException When notifiable type is not supported
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieve template data from package
        $templateData = $this->getTemplateData();

        // Determine template based on recipient type
        $templateName = match(true) {
            $notifiable instanceof Instructor => 'emails.instructor.accepted',
            $notifiable instanceof User => 'emails.librarian.accepted',
            default => throw new \InvalidArgumentException(
                'Unsupported notifiable type: ' . get_class($notifiable)
            )
        };

        try {
            return (new MailMessage)
                ->subject($this->getSubjectForNotifiable($notifiable))
                ->view($templateName, [
                    'request' => $templateData,
                    'dashboardUrl' => $this->getDashboardUrl(),
                    'emailSubject' => $this->getSubjectForNotifiable($notifiable)
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to create accepted notification email', [
                'request_id' => $templateData['request_id'] ?? 'unknown',
                'recipient_id' => $notifiable->id,
                'recipient_type' => get_class($notifiable),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
