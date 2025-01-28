<?php

namespace App\Enums;

/**
 * Enum for instruction request status types
 * Does not replace existing string status in model, provides type safety for notification system
 */
enum InstructionRequestStatus: string
{
    case RECEIVED = 'received';
    case ASSIGNED = 'assigned';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    /**
     * Get all valid status values
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Convert string status to enum
     *
     * @param string $status
     * @return self
     */
    public static function fromString(string $status): self
    {
        return match($status) {
            'received' => self::RECEIVED,
            'assigned' => self::ASSIGNED,
            'accepted' => self::ACCEPTED,
            'rejected' => self::REJECTED,
            default => throw new \InvalidArgumentException("Invalid status: {$status}")
        };
    }
}
