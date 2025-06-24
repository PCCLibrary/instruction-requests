<?php

namespace App\Services;

use App\Models\InstructionRequests;
use App\Models\User;
use App\Notifications\RequestReceivedNotification;
use App\Notifications\RequestAssignedNotification;
use App\Notifications\RequestAcceptedNotification;
use App\Notifications\RequestRejectedNotification;
use App\ValueObjects\NotificationPackage;
use App\Repositories\InstructionRequestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service for managing instruction request notifications.
 *
 * Handles all notification orchestration, data preparation, and subject generation.
 * Provides clean separation from CRUD operations and ensures single data loads.
 */
class NotificationService
{
    public function __construct(
        private InstructionRequestRepository $repository
    ) {}

    /**
     * Send notifications for instruction request status changes.
     *
     * @param InstructionRequests $request The request with status change
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @return void
     */
    public function sendStatusChangeNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        $logContext = [
            'request_id' => $request->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => auth()->check() ? auth()->user()->id : 'system',
        ];

        try {
            // Use DB::afterCommit to ensure notifications are sent after transaction completes
            DB::afterCommit(function () use ($request, $oldStatus, $newStatus) {
                $this->sendReceivedNotifications($request, $oldStatus, $newStatus);
                $this->sendAssignedNotifications($request, $oldStatus, $newStatus);
                $this->sendAcceptedNotifications($request, $oldStatus, $newStatus);
                $this->sendRejectedNotifications($request, $oldStatus, $newStatus);
            });

        } catch (\Exception $e) {
            Log::error('Failed to send status change notifications', array_merge($logContext, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));
            throw $e;
        }
    }

    /**
     * Prepare notification package for received status.
     *
     * @param int $requestId
     * @return NotificationPackage
     */
    public function prepareReceivedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $dateFormatted = $this->selectAndFormatDate($request);
        $class = strtoupper($request->department) . $request->course_number;

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for assigned status.
     *
     * @param int $requestId
     * @return NotificationPackage
     */
    public function prepareAssignedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $dateFormatted = $this->selectAndFormatDate($request);
        $class = strtoupper($request->department) . $request->course_number;

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for accepted status.
     *
     * @param int $requestId
     * @return NotificationPackage
     */
    public function prepareAcceptedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $dateFormatted = $this->selectAndFormatDate($request);
        $class = strtoupper($request->department) . $request->course_number;

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for rejected status.
     *
     * @param int $requestId
     * @return NotificationPackage
     */
    public function prepareRejectedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $dateFormatted = $this->selectAndFormatDate($request);
        $class = strtoupper($request->department) . $request->course_number;

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Send notifications for new received requests.
     */
    private function sendReceivedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        if ($newStatus === 'received' && $oldStatus === '') {
            $package = $this->prepareReceivedNotificationPackage($request->id);

            // Notify instructor
            if ($request->instructor) {
                $request->instructor->notify(new RequestReceivedNotification(
                    $request->id, $oldStatus, $newStatus, $package
                ));
            }

            // Notify campus librarians
            if ($request->campus && !empty($request->campus->librarian_ids)) {
                $this->getCampusLibrarians($request->campus->librarian_ids)
                    ->each(function($librarian) use ($request, $oldStatus, $newStatus, $package) {
                        $librarian->notify(new RequestReceivedNotification(
                            $request->id, $oldStatus, $newStatus, $package
                        ));
                    });
            }
        }
    }

    /**
     * Send notifications for assigned requests.
     */
    private function sendAssignedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        if ($newStatus === 'assigned' && $request->detail?->assigned_librarian_id) {
            $package = $this->prepareAssignedNotificationPackage($request->id);

            $librarian = User::find($request->detail->assigned_librarian_id);
            if ($librarian) {
                $librarian->notify(new RequestAssignedNotification(
                    $request->id, $oldStatus, $newStatus, $package
                ));
            }
        }
    }

    /**
     * Send notifications for accepted requests.
     */
    private function sendAcceptedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        if ($newStatus === 'accepted' && $request->campus) {
            $package = $this->prepareAcceptedNotificationPackage($request->id);

            if (!empty($request->campus->librarian_ids)) {
                $this->getCampusLibrarians($request->campus->librarian_ids)
                    ->each(function($librarian) use ($request, $oldStatus, $newStatus, $package) {
                        $librarian->notify(new RequestAcceptedNotification(
                            $request->id, $oldStatus, $newStatus, $package
                        ));
                    });
            }
        }
    }

    /**
     * Send notifications for rejected requests.
     */
    private function sendRejectedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        if ($oldStatus === 'assigned' && $newStatus === 'rejected' && $request->campus) {
            $package = $this->prepareRejectedNotificationPackage($request->id);

            if (!empty($request->campus->librarian_ids)) {
                $this->getCampusLibrarians($request->campus->librarian_ids)
                    ->each(function($librarian) use ($request, $oldStatus, $newStatus, $package) {
                        $librarian->notify(new RequestRejectedNotification(
                            $request->id, $oldStatus, $newStatus, $package
                        ));
                    });
            }
        }
    }

    /**
     * Load instruction request with all required relationships.
     *
     * @param int $requestId
     * @return InstructionRequests
     * @throws \RuntimeException
     */
    private function loadRequestWithRelationships(int $requestId): InstructionRequests
    {
        $request = $this->repository->find($requestId);

        if (!$request) {
            throw new \RuntimeException("Instruction request {$requestId} not found");
        }

        $request->load(['detail', 'instructor', 'classes', 'campus']);

        return $request;
    }

    /**
     * Build template data array from request.
     *
     * @param InstructionRequests $request
     * @return array
     */
    private function buildTemplateData(InstructionRequests $request): array
    {
        $assignedLibrarian = null;
        if ($request->detail?->assigned_librarian_id) {
            $assignedLibrarian = User::find($request->detail->assigned_librarian_id);
        }

        return [
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
    }

    /**
     * Build dashboard URL for request.
     *
     * @param int $requestId
     * @return string
     */
    private function buildDashboardUrl(int $requestId): string
    {
        return sprintf(
            '%s/dashboard/instructionRequests/%d/edit',
            config('app.url'),
            $requestId
        );
    }

    /**
     * Map database instruction type to display value.
     *
     * @param string $dbType
     * @return string
     */
    private function mapInstructionType(string $dbType): string
    {
        return __('notifications.instruction_types.' . $dbType, [], 'en') ?: ucfirst($dbType);
    }

    /**
     * Select appropriate date field and format for subject line.
     *
     * @param InstructionRequests $request
     * @return string
     */
    private function selectAndFormatDate(InstructionRequests $request): string
    {
        if ($request->instruction_type === 'asynchronous') {
            return $this->formatCompactDate($request->asynchronous_instruction_ready_date);
        }

        return $this->formatCompactDateTime($request->preferred_datetime);
    }

    /**
     * Format datetime as compact format (2025-05-22 12:55pm).
     *
     * @param string $datetime
     * @return string
     */
    private function formatCompactDateTime(string $datetime): string
    {
        try {
            return Carbon::parse($datetime)->format('Y-m-d g:ia');
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    /**
     * Format date as compact format (2025-05-22).
     *
     * @param string $date
     * @return string
     */
    private function formatCompactDate(string $date): string
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Build instructor subject line.
     *
     * @param string $type Mapped instruction type
     * @param string $class Department + course number
     * @param InstructionRequests $request
     * @return string
     */
    private function buildInstructorSubject(string $type, string $class, InstructionRequests $request): string
    {
        $datePhrase = ($request->instruction_type === 'asynchronous')
            ? __('notifications.subjects.instructor.date_phrases.by', ['date' => $this->formatCompactDate($request->asynchronous_instruction_ready_date)], 'en')
            : __('notifications.subjects.instructor.date_phrases.on', ['datetime' => $this->formatCompactDateTime($request->preferred_datetime)], 'en');

        return __('notifications.subjects.instructor.confirmation', [
            'type' => $type,
            'class' => $class,
            'date_phrase' => $datePhrase
        ], 'en');
    }

    /**
     * Build librarian subject line.
     *
     * @param string $type Mapped instruction type
     * @param string $dateFormatted Formatted date string
     * @param string $campus Campus name
     * @param string $class Department + course number
     * @param string $instructor Instructor name
     * @return string
     */
    private function buildLibrarianSubject(string $type, string $dateFormatted, string $campus, string $class, string $instructor): string
    {
        return __('notifications.subjects.librarian.new_request', [
            'type' => $type,
            'date' => $dateFormatted,
            'campus' => $campus,
            'class' => $class,
            'instructor' => $instructor
        ], 'en');
    }

    /**
     * Get campus librarian users for notifications.
     *
     * @param array|string $librarianIds Array or JSON string of librarian IDs
     * @return Collection
     */
    private function getCampusLibrarians(array|string $librarianIds): Collection
    {
        $ids = is_string($librarianIds) ? json_decode($librarianIds, true) : $librarianIds;
        return User::whereIn('id', $ids)->get();
    }
}
