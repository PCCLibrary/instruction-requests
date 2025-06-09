<?php

namespace App\Exceptions;

use Exception;
use Google_Service_Exception;
use Throwable;

class GoogleCalendarApiException extends Exception
{
    /**
     * The original Google API error data
     *
     * @var array
     */
    protected array $googleErrorData;

    /**
     * The HTTP status code from Google API
     *
     * @var int
     */
    protected int $httpStatusCode;

    /**
     * Create a new GoogleCalendarApiException
     *
     * @param string $message The user-friendly error message
     * @param array $googleErrorData The original Google API error data
     * @param int $httpStatusCode The HTTP status code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        array $googleErrorData = [],
        int $httpStatusCode = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $httpStatusCode, $previous);
        $this->googleErrorData = $googleErrorData;
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * Create an exception from a Google Service Exception
     *
     * @param Google_Service_Exception $googleException
     * @return self
     */
    public static function fromGoogleServiceException(Google_Service_Exception $googleException): self
    {
        // Parse the Google API error response
        $errorData = [];
        $userMessage = 'An error occurred with Google Calendar.';
        $httpCode = $googleException->getCode();

        try {
            // Decode the JSON error response
            $responseBody = $googleException->getMessage();
            $decoded = json_decode($responseBody, true);

            if (isset($decoded['error'])) {
                $errorData = $decoded['error'];

                // Extract the main error message from Google's response
                if (isset($errorData['message'])) {
                    $userMessage = $errorData['message'];
                } elseif (isset($errorData['errors'][0]['message'])) {
                    $userMessage = $errorData['errors'][0]['message'];
                }
            }
        } catch (\Exception $parseException) {
            // If we can't parse the JSON, fall back to the original message
            $userMessage = $googleException->getMessage();
        }

        return new self(
            $userMessage,
            $errorData,
            $httpCode,
            $googleException
        );
    }

    /**
     * Get the original Google API error data
     *
     * @return array
     */
    public function getGoogleErrorData(): array
    {
        return $this->googleErrorData;
    }

    /**
     * Get the HTTP status code
     *
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Get the error reason from Google API (if available)
     *
     * @return string|null
     */
    public function getGoogleErrorReason(): ?string
    {
        if (isset($this->googleErrorData['errors'][0]['reason'])) {
            return $this->googleErrorData['errors'][0]['reason'];
        }

        return null;
    }

    /**
     * Get the error domain from Google API (if available)
     *
     * @return string|null
     */
    public function getGoogleErrorDomain(): ?string
    {
        if (isset($this->googleErrorData['errors'][0]['domain'])) {
            return $this->googleErrorData['errors'][0]['domain'];
        }

        return null;
    }

    /**
     * Check if this is a permission-related error
     *
     * @return bool
     */
    public function isPermissionError(): bool
    {
        $reason = $this->getGoogleErrorReason();
        return in_array($reason, ['requiredAccessLevel', 'forbidden', 'insufficientPermissions']);
    }

    /**
     * Check if this is a configuration-related error
     *
     * @return bool
     */
    public function isConfigurationError(): bool
    {
        $reason = $this->getGoogleErrorReason();
        return in_array($reason, ['notFound', 'invalid']);
    }
}
