<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Instructor;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Notification sent when an instruction request is assigned to a librarian
 *
 * This notification informs both the assigned librarian and the instructor
 * about the assignment. Uses request ID and status information to ensure
 * proper serialization when the notification is queued. Data is loaded
 * from the repository at notification time.
 *
 * Verifies the librarian assignment by checking data loaded from
 * the repository instead of direct model relationships.
 *
 * @implements ShouldQueue via parent class
 */
class RequestAssignedNotification extends BaseInstructionRequestNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * Determines the appropriate email template based on the recipient type
     * and generates a MailMessage with request-specific information. Uses
     * base class methods to load consistent template data.
     *
     * @param object $notifiable The recipient of the notification (User or Instructor)
     * @return MailMessage
     * @throws InvalidArgumentException|Exception When notifiable type is not supported
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieve template data from package
        $templateData = $this->getTemplateData();

        // Determine template based on recipient type and assigned librarian
        $templateName = match(true) {
            $notifiable instanceof User &&
            $notifiable->id === ($templateData['detail']['assigned_librarian_id'] ?? null) => 'emails.librarian.assigned',
            $notifiable instanceof Instructor => 'emails.instructor.assigned',
            default => throw new InvalidArgumentException(
                'Unsupported notifiable type: ' . get_class($notifiable)
            )
        };

        try {
            return (new MailMessage)
                ->subject($this->package->librarianSubject)
                ->view($templateName, [
                    'request' => $templateData,
                    'dashboardUrl' => $this->getDashboardUrl(),
                    'emailSubject' => $this->package->librarianSubject,
                    'headerColor' => $this->package->headerColor
                ]);
        } catch (Exception $e) {
            Log::error('Failed to create assigned notification email', [
                'request_id' => $templateData['request_id'] ?? 'unknown',
                'recipient_id' => $notifiable->id,
                'recipient_type' => get_class($notifiable),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
