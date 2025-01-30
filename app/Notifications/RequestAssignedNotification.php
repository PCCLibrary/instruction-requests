<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Instructor;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

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
 * @implements \Illuminate\Contracts\Queue\ShouldQueue via parent class
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     * @throws \InvalidArgumentException When notifiable type is not supported
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Retrieve template data using base class method
        $templateData = $this->getTemplateData();

        // Determine template based on recipient type and assigned librarian
        $templateName = match(true) {
            $notifiable instanceof User &&
            $notifiable->id === ($templateData['detail']['assigned_librarian_id'] ?? null),
                $notifiable instanceof Instructor => 'emails.librarian.assigned',
            default => throw new \InvalidArgumentException(
                'Unsupported notifiable type: ' . get_class($notifiable)
            )
        };

        // Log the email preparation details
        Log::info('Preparing assigned status email', [
            'request_id' => $templateData['id'],
            'recipient_id' => $notifiable->id,
            'recipient_type' => get_class($notifiable),
            'is_assigned_librarian' => $notifiable instanceof User &&
                $notifiable->id === ($templateData['detail']['assigned_librarian_id'] ?? null),
            'template' => $templateName
        ]);

        try {
            return (new MailMessage)
                ->subject($this->buildSubjectLine())
                ->view($templateName, [
                    'request' => $templateData,
                    'dashboardUrl' => $this->generateDashboardEditUrl(),
                    'emailSubject' => $this->buildSubjectLine()
                ]);
        } catch (\Exception $e) {
            // Log any errors in email creation
            Log::error('Failed to create assigned notification email', [
                'request_id' => $templateData['id'],
                'recipient_id' => $notifiable->id,
                'recipient_type' => get_class($notifiable),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
