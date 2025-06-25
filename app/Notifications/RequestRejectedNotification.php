<?php

namespace App\Notifications;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

/**
 * Notification sent when an instruction request is rejected
 *
 * This notification informs relevant parties when a previously assigned
 * request has been rejected. Uses request ID and status information
 * to ensure proper serialization when the notification is queued.
 * Data is loaded from the repository at notification time.
 *
 * Provides appropriate template data based on the rejection context,
 * using data loaded from the repository.
 *
 * @implements \Illuminate\Contracts\Queue\ShouldQueue via parent class
 */
class RequestRejectedNotification extends BaseInstructionRequestNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * Determines the appropriate email template based on the recipient type
     * and generates a MailMessage with request-specific information. Uses
     * base class methods to load consistent template data.
     *
     * @param object $notifiable The recipient of the notification (Instructor or User/Librarian)
     * @return \Illuminate\Notifications\Messages\MailMessage
     * @throws \InvalidArgumentException|\Exception When notifiable type is not supported
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieve template data from package
        $templateData = $this->getTemplateData();

        // Determine template based on recipient type
        $templateName = match(true) {
            $notifiable instanceof Instructor => 'emails.instructor.rejected',
            $notifiable instanceof User => 'emails.librarian.rejected',
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
            Log::error('Failed to create rejected notification email', [
                'request_id' => $templateData['request_id'] ?? 'unknown',
                'recipient_id' => $notifiable->id,
                'recipient_type' => get_class($notifiable),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
