<?php

namespace App\ValueObjects;

/**
 * Value object containing all data needed for an instruction request notification.
 *
 * This package eliminates the need for notifications to load data themselves,
 * improving performance by ensuring single data loads per notification type.
 */
class NotificationPackage
{
    /**
     * Create a new notification package.
     *
     * @param array $templateData Formatted data for email templates
     * @param string $dashboardUrl Direct link to request in dashboard
     * @param string $instructorSubject Subject line for instructor emails
     * @param string $librarianSubject Subject line for librarian emails
     */
    public function __construct(
        public readonly array $templateData,
        public readonly string $dashboardUrl,
        public readonly string $instructorSubject,
        public readonly string $librarianSubject
    ) {}

    /**
     * Get the appropriate subject line for a notifiable entity.
     *
     * @param object $notifiable The User or Instructor being notified
     * @return string The subject line to use
     */
    public function getSubjectFor(object $notifiable): string
    {
        // Determine recipient type and return appropriate subject
        if (get_class($notifiable) === 'App\Models\Instructor') {
            return $this->instructorSubject;
        }

        return $this->librarianSubject;
    }
}
