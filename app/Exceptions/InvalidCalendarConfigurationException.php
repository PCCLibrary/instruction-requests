<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidCalendarConfigurationException extends Exception
{
    /**
     * Additional context about the configuration error
     *
     * @var array
     */
    protected $context;

    /**
     * Create a new InvalidCalendarConfigurationException
     *
     * @param string $message Human-readable error message
     * @param array $context Detailed context about the configuration issue
     * @param int $code Exception code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "Invalid Google Calendar Configuration",
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get the additional context for the exception
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get a specific context value
     *
     * @param string $key Context key to retrieve
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function getContextValue(string $key, $default = null)
    {
        return $this->context[$key] ?? $default;
    }

    /**
     * Generate a detailed error report
     *
     * @return string
     */
    public function getDetailedReport(): string
    {
        $report = [
            "Error: " . $this->getMessage(),
            "Context Details:",
        ];

        foreach ($this->context as $key => $value) {
            // Safely convert value to string
            $stringValue = is_array($value) ? json_encode($value) : (string)$value;
            $report[] = "  - {$key}: {$stringValue}";
        }

        return implode("\n", $report);
    }
}
