<?php

namespace App\Notifications;

use App\Models\InstructionRequests;
use App\Models\User;
use App\Repositories\InstructionRequestRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Base class for instruction request notifications
 *
 * This class provides common functionality for all instruction request notifications,
 * including email channel configuration, data loading, and dashboard URL generation.
 * Uses request ID instead of full model or DTO to prevent serialization issues when queued.
 * Data is loaded from the repository at notification time using efficient relationship loading.
 */
abstract class BaseInstructionRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ID of the instruction request
     *
     * @var int
     */
    protected int $requestId;

    /**
     * Previous status of the request, if applicable
     *
     * @var string
     */
    protected string $oldStatus;

    /**
     * New status of the request
     *
     * @var string
     */
    protected string $newStatus;

    /**
     * Cached template data to avoid multiple database queries
     *
     * @var array|null
     */
    private ?array $cachedTemplateData = null;

    /**
     * Create a new notification instance.
     *
     * @param int $requestId The ID of the instruction request
     * @param string $oldStatus Previous status (if applicable)
     * @param string $newStatus New status
     */
    public function __construct(int $requestId, string $oldStatus = '', string $newStatus = '')
    {
        $this->requestId = $requestId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;

        Log::info('Creating ' . class_basename($this), [
            'request_id' => $requestId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        Log::debug('Setting up notification channels', [
            'notification_type' => class_basename($this),
            'recipient_id' => $notifiable->id,
            'recipient_type' => get_class($notifiable),
            'request_id' => $this->requestId
        ]);
        return ['mail'];
    }

    /**
     * Load and return instruction request with all required relationships.
     *
     * @return InstructionRequests
     * @throws \RuntimeException|\Exception If request not found
     */
    protected function loadInstructionRequest(): InstructionRequests
    {
        try {
            /** @var InstructionRequestRepository $repository */
            $repository = app(InstructionRequestRepository::class);

            /** @var InstructionRequests|null $request */
            $request = $repository->find($this->requestId);

            if (!$request) {
                throw new \RuntimeException("Instruction request {$this->requestId} not found");
            }

            // Load all required relationships after retrieving from repository
            $request->load(['detail', 'instructor', 'classes', 'campus']);

            return $request;
        } catch (\Exception $e) {
            Log::error('Failed to load instruction request', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get instruction request data formatted for email templates.
     *
     * @return array<string, mixed>
     *  @throws \RuntimeException|\Exception If request not found
     */
    protected function getTemplateData(): array
    {
        // Return cached data if already loaded
        if ($this->cachedTemplateData !== null) {
            return $this->cachedTemplateData;
        }

        Log::info('Loading template data', [
            'class' => get_class($this),
            'request_id' => $this->requestId
        ]);

        try {
            $request = $this->loadInstructionRequest();

            /**
             * Get assigned librarian if exists
             * @todo add requested librarian for instructor requests
             * @var User|null $assignedLibrarian
             */
            $assignedLibrarian = null;
            if ($request->detail?->assigned_librarian_id) {
                $assignedLibrarian = User::find($request->detail->assigned_librarian_id);
            }

            // Cache and return template data
            $this->cachedTemplateData = [
                'id' => $request->id,
                'instruction_type' => $request->instruction_type,
                'status' => $request->status,
                'course_department' => $request->department,
                'course_number' => $request->course_number,
                'course_crn' => $request->course_crn,
                'course_name' => $request->classes->course_name,
                'number_of_students' => $request->number_of_students,
                'class_description' => $request->class_description,
                'assignment_description' => $request->assignment_description,
                'preferred_datetime' => $request->preferred_datetime,
                'alternate_datetime' => $request->alternate_datetime,
                'asynchronous_instruction_ready_date' => $request->asynchronous_instruction_ready_date,
                'duration' => $request->duration,
                'campus_name' => $request->campus->name,
                'instructor_name' => $request->instructor->name,
                'instructor_email' => $request->instructor->email,
                'librarian_name' => $assignedLibrarian?->display_name,
                'ada_provisions_needed' => $request->ada_provisions_needed,
                'ada_provisions_description' => $request->ada_provisions_description,
                'detail' => $request->detail?->toArray() ?? []
            ];

            return $this->cachedTemplateData;
        } catch (\Exception $e) {
            Log::error('Failed to load template data', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Build formatted subject line for email.
     *
     * @return string
     * @throws \Exception
     */
    protected function buildSubjectLine(): string
    {
        $data = $this->getTemplateData();

        // Format datetime
        $formattedDateTime = $this->formatDateTime($data['preferred_datetime']);

        // Prepare parts of the subject line
        $parts = [
            "{$formattedDateTime}",
            "{$data['campus_name']}",
            "{$data['course_department']}-{$data['course_number']}",
        ];

        // Add librarian name if available
        if (!empty($data['librarian_name'])) {
            $parts[] = "{$data['librarian_name']}";
        }

        return "Library Instruction Request: " . implode(', ', $parts);
    }

// Helper method to format datetime
    protected function formatDateTime(string $dateTime): string
    {
        try {
            $carbonDate = Carbon::parse($dateTime);
            return $carbonDate->format('Y-m-d h:ia');
        } catch (\Exception $e) {
            // Fallback to original format if parsing fails
            return $dateTime;
        }
    }

    /**
     * Generate a dashboard edit URL for an instruction request.
     *
     * @return string
     */
    protected function generateDashboardEditUrl(): string
    {

        $dashboardUrl = sprintf(
            '%s/dashboard/instructionRequests/%d/edit',
            config('app.url'),
            $this->requestId
        );

        Log::debug('Dashboard URL Generation', [
            'request_id' => $this->requestId,
            'generated_url' => $dashboardUrl,
            'app_url' => config('app.url')
        ]);

        return $dashboardUrl;
    }

    /**
     * Get campus librarian users for notifications.
     *
     * @param string $librarian_ids JSON array of librarian IDs
     * @return \Illuminate\Database\Eloquent\Collection<User>
     */
    protected function getCampusLibrarians(string $librarian_ids): \Illuminate\Database\Eloquent\Collection
    {
        $ids = json_decode($librarian_ids, true);
        return User::whereIn('id', $ids)->get();
    }

    /**
     * Handle a failed notification.
     *
     * @param mixed $notifiable
     * @param \Throwable $e
     * @return void
     */
    public function failed($notifiable, \Throwable $e): void
    {
        Log::error('Failed to send notification', [
            'notification_type' => class_basename($this),
            'request_id' => $this->requestId,
            'recipient_id' => $notifiable->id,
            'recipient_type' => get_class($notifiable),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
