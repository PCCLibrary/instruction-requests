<?php

namespace App\ValueObjects;

/**
 * NotificationPackage
 *
 * Value object that contains all data needed for sending instruction request notifications.
 * This eliminates redundant data loading and provides clean separation between
 * data preparation and notification sending.
 */
class NotificationPackage
{
    public function __construct(
        public readonly array $templateData,
        public readonly string $dashboardUrl,
        public readonly string $instructorSubject,
        public readonly string $librarianSubject
    ) {}

    /**
     * Get the appropriate subject line for the given notifiable
     */
    public function getSubjectFor(object $notifiable): string
    {
        // If it's an instructor (has email but no display_name), use instructor subject
        if (isset($notifiable->email) && !isset($notifiable->display_name)) {
            return $this->instructorSubject;
        }

        // Otherwise use librarian subject
        return $this->librarianSubject;
    }
}
