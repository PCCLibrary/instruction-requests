<?php

// BaseEmailNotification.php
namespace App\Notifications;

use App\Enums\InstructionRequestStatus;
use App\Models\InstructionRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Base email notification class for instruction requests
 *
 * Handles common notification functionality and data preparation
 * for instruction request related notifications.
 */
class BaseEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Number of times the job may be attempted
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying
     *
     * @var array<int>
     */
    public array $backoff = [60, 300, 900];

    /**
     * Required by Laravel's MailChannel
     * Protected with getter to ensure proper initialization
     *
     * @var string
     */
    protected string $subject = '';

    /**
     * Stores notification data for persistence
     *
     * @var array{requestId: int, subject: string}
     */
    protected array $data;

    /**
     * Create a new notification instance
     *
     * @param int $requestId The ID of the instruction request
     * @param string $subject The email subject line
     */
    public function __construct(int $requestId, string $subject)
    {
        $this->subject = $subject;
        $this->data = [
            'requestId' => $requestId,
            'subject' => $subject,
        ];
    }

    /**
     * Get the notification subject
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Create a new notification instance from an InstructionRequests model
     *
     * @param InstructionRequests $request The instruction request model
     * @param string $subject The email subject line
     * @return static
     */
    public static function fromRequest(InstructionRequests $request, string $subject): self
    {
        return new static($request->id, $subject);
    }

    /**
     * Get the notification's delivery channels
     *
     * @param mixed $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get formatted request data for email templates
     *
     * @return array<string, mixed>
     * @throws \Exception If the request cannot be found or data cannot be formatted
     */
    public function getData(): array
    {
        try {
            $request = InstructionRequests::with(['instructor', 'campus', 'detail'])->find($this->data['requestId']);

            if (!$request) {
                throw new \Exception("Instruction request {$this->data['requestId']} not found");
            }

            $data = [
                'id' => $request->id,
                'instructor_name' => $request->instructor?->display_name,
                'instructor_email' => $request->instructor?->email,
                'instructor_phone' => $request->instructor?->phone,
                'course_department' => $request->department,
                'course_number' => $request->course_number,
                'course_crn' => $request->course_crn,
                'campus_name' => $request->campus?->name,
                'status' => InstructionRequestStatus::fromString($request->status),
                'date_requested' => $request->created_at?->format('m/d/Y'),
                'preferred_datetime' => $request->preferred_datetime?->format('m/d/Y H:i'),
                'alternate_datetime' => $request->alternate_datetime?->format('m/d/Y H:i'),
                'dashboard_url' => route('instructionRequests.edit', ['id' => $request->id]),
                'instruction_type' => $request->instruction_type
            ];

            if ($request->detail) {
                $data['duration'] = $request->detail->duration;
                $data['number_of_students'] = $request->detail->number_of_students;
                if ($request->detail->asynchronous_instruction_ready_date) {
                    $data['asynchronous_instruction_ready_date'] = $request->detail->asynchronous_instruction_ready_date->format('m/d/Y');
                }
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Failed to format notification data', [
                'request_id' => $this->data['requestId'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return array<string, mixed>
     */
    protected function prepareMailData(): array
    {
        $requestData = $this->getData();

        return [
            'request' => $requestData,
            'subject' => $this->getSubject(),
            'heading' => $this->getSubject()
        ];
    }

    /**
     * Prepare the notification for serialization
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'data' => $this->data,
            'subject' => $this->subject
        ];
    }

    /**
     * Restore the notification from serialization
     *
     * @param array<string, mixed> $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data['data'];
        $this->subject = $data['subject'];
    }
}
