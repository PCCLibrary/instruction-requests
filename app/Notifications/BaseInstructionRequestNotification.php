<?php

namespace App\Notifications;

use App\ValueObjects\NotificationPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Base class for instruction request notifications
 *
 * This class provides common functionality for all instruction request notifications,
 * including email channel configuration and package consumption.
 * Uses NotificationPackage for data delivery to prevent redundant database queries.
 */
abstract class BaseInstructionRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Notification package containing all required data
     *
     * @var NotificationPackage
     */
    protected NotificationPackage $package;

    /**
     * Create a new notification instance.
     *
     * @param NotificationPackage $package Pre-loaded data package
     */
    public function __construct(NotificationPackage $package)
    {
        $this->package = $package;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get template data from the notification package.
     *
     * @return array
     */
    protected function getTemplateData(): array
    {
        return $this->package->templateData;
    }

    /**
     * Get dashboard URL from the notification package.
     *
     * @return string
     */
    protected function getDashboardUrl(): string
    {
        return $this->package->dashboardUrl;
    }

    /**
     * Get appropriate subject line for a notifiable entity.
     *
     * @param object $notifiable
     * @return string
     */
    protected function getSubjectForNotifiable(object $notifiable): string
    {
        return $this->package->getSubjectFor($notifiable);
    }

    /**
     * Handle a failed notification delivery.
     *
     * @param mixed $notifiable
     * @param \Exception $exception
     * @return void
     */
    public function failed($notifiable, $exception): void
    {
        Log::error('Notification delivery failed', [
            'notification_type' => get_class($this),
            'request_id' => $this->package->templateData['request_id'] ?? 'unknown',
            'recipient_id' => $notifiable->id,
            'recipient_type' => get_class($notifiable),
            'error' => $exception->getMessage(),
        ]);
    }
}
