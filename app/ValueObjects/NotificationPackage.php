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
        public readonly string $librarianSubject,
        public readonly string $headerColor
    ) {}
}
