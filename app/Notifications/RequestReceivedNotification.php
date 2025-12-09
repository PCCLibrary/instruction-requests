<?php

namespace App\Notifications;

use App\Models\Instructor;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Notification sent when an instruction request is first received
 *
 * This notification is sent to both the instructor who submitted the request
 * and any librarians who need to be notified of new requests. Uses request ID
 * and status information to ensure proper serialization when the notification
 * is queued. Data is loaded from the repository at notification time.
 *
 * Provides different templates based on recipient type using data loaded
 * from the repository to ensure consistency.
 *
 * @implements ShouldQueue via parent class
 */
class RequestReceivedNotification extends BaseInstructionRequestNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * Determines the appropriate email template based on the recipient type
     * and generates a MailMessage with request-specific information. Uses
     * notification package for consistent data delivery.
     *
     * @param object $notifiable The recipient of the notification (Instructor or User/Librarian)
     * @return MailMessage
     * @throws InvalidArgumentException|Exception When notifiable type is not supported
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieve template data from package
        $templateData = $this->getTemplateData();

        // Determine template based on recipient type
        $templateName = match(true) {
            $notifiable instanceof Instructor => 'emails.instructor.request_received',
            $notifiable instanceof User => 'emails.librarian.new_request',
            default => throw new InvalidArgumentException(
                'Unsupported notifiable type: ' . get_class($notifiable)
            )
        };

        // Determine subject based on recipient type
        $subject = ($notifiable instanceof Instructor)
            ? $this->package->instructorSubject
            : $this->package->librarianSubject;

        try {
            return (new MailMessage)
                ->subject($subject)
                ->view($templateName, [
                    'request' => $templateData,
                    'dashboardUrl' => $this->getDashboardUrl(),
                    'emailSubject' => $subject,
                    'headerColor' => $this->package->headerColor
                ]);
        } catch (Exception $e) {
            Log::error('Failed to create received request email notification', [
                'request_id' => $templateData['request_id'] ?? 'unknown',
                'recipient_id' => $notifiable->id,
                'recipient_type' => get_class($notifiable),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
