<?php

namespace App\Services;

use App\Models\InstructionRequests;
use App\Models\User;
use App\ValueObjects\NotificationPackage;
use App\Notifications\RequestReceivedNotification;
use App\Notifications\RequestAssignedNotification;
use App\Notifications\RequestAcceptedNotification;
use App\Notifications\RequestRejectedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * NotificationService
 *
 * Centralized service for handling instruction request notifications.
 * Provides clean separation between business logic and notification logic.
 */
class NotificationService
{
    /**
     * Send notifications based on status changes
     */
    public function sendStatusChangeNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        try {
            Log::info('Processing status change notifications', [
                'request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            switch ($newStatus) {
                case 'received':
                    $this->sendReceivedNotifications($request, $oldStatus, $newStatus);
                    break;
                case 'assigned':
                    $this->sendAssignedNotifications($request, $oldStatus, $newStatus);
                    break;
                case 'accepted':
                    $this->sendAcceptedNotifications($request, $oldStatus, $newStatus);
                    break;
                case 'rejected':
                    $this->sendRejectedNotifications($request, $oldStatus, $newStatus);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status change notifications', [
                'request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Prepare notification package for received status
     */
    public function prepareReceivedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $class = $request->department . $request->course_number;
        $dateFormatted = $this->selectAndFormatDate($request);

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for assigned status
     */
    public function prepareAssignedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $class = $request->department . $request->course_number;
        $dateFormatted = $this->selectAndFormatDate($request);

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for accepted status
     */
    public function prepareAcceptedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $class = $request->department . $request->course_number;
        $dateFormatted = $this->selectAndFormatDate($request);

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Prepare notification package for rejected status
     */
    public function prepareRejectedNotificationPackage(int $requestId): NotificationPackage
    {
        $request = $this->loadRequestWithRelationships($requestId);
        $templateData = $this->buildTemplateData($request);
        $dashboardUrl = $this->buildDashboardUrl($requestId);

        $type = $this->mapInstructionType($request->instruction_type);
        $class = $request->department . $request->course_number;
        $dateFormatted = $this->selectAndFormatDate($request);

        $instructorSubject = $this->buildInstructorSubject($type, $class, $request);
        $librarianSubject = $this->buildLibrarianSubject($type, $dateFormatted, $request->campus->name, $class, $request->instructor->name);

        return new NotificationPackage($templateData, $dashboardUrl, $instructorSubject, $librarianSubject);
    }

    /**
     * Send notifications for received status
     */
    private function sendReceivedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        $package = $this->prepareReceivedNotificationPackage($request->id);

        // Send to instructor
        $request->instructor->notify(new RequestReceivedNotification($package));

        // Send to campus librarians
        $campusLibrarians = $this->getCampusLibrarians($request->campus->librarian_ids);
        foreach ($campusLibrarians as $librarian) {
            $librarian->notify(new RequestReceivedNotification($package));
        }
    }

    /**
     * Send notifications for assigned status
     */
    private function sendAssignedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        if (!$request->detail || !$request->detail->assigned_librarian_id) {
            return;
        }

        $package = $this->prepareAssignedNotificationPackage($request->id);
        $assignedLibrarian = User::find($request->detail->assigned_librarian_id);

        if ($assignedLibrarian) {
            $assignedLibrarian->notify(new RequestAssignedNotification($package));
        }
    }

    /**
     * Send notifications for accepted status
     */
    private function sendAcceptedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        $package = $this->prepareAcceptedNotificationPackage($request->id);

        // Send to campus librarians
        $campusLibrarians = $this->getCampusLibrarians($request->campus->librarian_ids);
        foreach ($campusLibrarians as $librarian) {
            $librarian->notify(new RequestAcceptedNotification($package));
        }
    }

    /**
     * Send notifications for rejected status
     */
    private function sendRejectedNotifications(InstructionRequests $request, string $oldStatus, string $newStatus): void
    {
        $package = $this->prepareRejectedNotificationPackage($request->id);

        // Send to campus librarians
        $campusLibrarians = $this->getCampusLibrarians($request->campus->librarian_ids);
        foreach ($campusLibrarians as $librarian) {
            $librarian->notify(new RequestRejectedNotification($package));
        }
    }

    /**
     * Load request with all needed relationships
     */
    private function loadRequestWithRelationships(int $requestId): InstructionRequests
    {
        return InstructionRequests::with(['detail', 'instructor', 'classes', 'campus'])
            ->findOrFail($requestId);
    }

    /**
     * Build template data array
     */
    private function buildTemplateData(InstructionRequests $request): array
    {
        return [
            'instructor_name' => $request->instructor->name,
            'instructor_email' => $request->instructor->email,
            'department' => $request->department,
            'course_number' => $request->course_number,
            'course_crn' => $request->course_crn,
            'class_name' => $request->classes->course_name ?? ($request->department . $request->course_number),
            'campus_name' => $request->campus->name,
            'instruction_type' => $this->mapInstructionType($request->instruction_type),
            'preferred_datetime' => $request->preferred_datetime,
            'asynchronous_instruction_ready_date' => $request->asynchronous_instruction_ready_date,
            'date_formatted' => $this->selectAndFormatDate($request),
            'number_of_students' => $request->number_of_students,
            'class_description' => $request->class_description,
            'assignment_description' => $request->assignment_description,
            'request_id' => $request->id,
            'librarian_name' => $request->detail->assignedLibrarian->display_name ?? null,
            'detail' => [
                'assigned_librarian_id' => $request->detail->assigned_librarian_id ?? null,
            ]
        ];
    }

    /**
     * Build dashboard URL using the original working method
     */
    private function buildDashboardUrl(int $requestId): string
    {
        $dashboardUrl = sprintf(
            '%s/dashboard/instructionRequests/%d/edit',
            config('app.url'),
            $requestId
        );

        Log::debug('Dashboard URL Generation', [
            'request_id' => $requestId,
            'generated_url' => $dashboardUrl,
            'app_url' => config('app.url')
        ]);

        return $dashboardUrl;
    }

    /**
     * Map instruction type from database to display format using language files
     */
    private function mapInstructionType(string $dbType): string
    {
        return __('notifications.instruction_types.' . $dbType, [], 'en') ?: ucfirst($dbType);
    }

    /**
     * Select and format the appropriate date based on instruction type
     */
    private function selectAndFormatDate(InstructionRequests $request): string
    {
        if ($request->instruction_type === 'asynchronous' && $request->asynchronous_instruction_ready_date) {
            return $this->formatCompactDate($request->asynchronous_instruction_ready_date);
        }

        if ($request->preferred_datetime) {
            return $this->formatCompactDateTime($request->preferred_datetime);
        }

        return 'Date TBD';
    }

    /**
     * Format datetime in compact format: 2025-05-22 12:55pm
     */
    private function formatCompactDateTime(string $datetime): string
    {
        return \Carbon\Carbon::parse($datetime)->format('Y-m-d g:ia');
    }

    /**
     * Format date in compact format: 2025-05-22
     */
    private function formatCompactDate(string $date): string
    {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * Build instructor subject line using language files
     */
    private function buildInstructorSubject(string $type, string $class, InstructionRequests $request): string
    {
        $datePhrase = ($request->instruction_type === 'asynchronous')
            ? __('notifications.subjects.instructor.date_phrases.by', ['date' => $this->selectAndFormatDate($request)], 'en')
            : __('notifications.subjects.instructor.date_phrases.on', ['datetime' => $this->selectAndFormatDate($request)], 'en');

        return __('notifications.subjects.instructor.confirmation', [
            'type' => $type,
            'class' => $class,
            'date_phrase' => $datePhrase
        ], 'en');
    }

    /**
     * Build librarian subject line using language files
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
     * Get campus librarians from librarian_ids array or string
     */
    private function getCampusLibrarians($librarianIds): Collection
    {
        if (empty($librarianIds)) {
            return collect([]);
        }

        // Handle array input (which is what we actually get)
        if (is_array($librarianIds)) {
            $ids = array_filter($librarianIds, 'is_numeric');
            return User::whereIn('id', $ids)->get();
        }

        // Handle string input (legacy format)
        if (is_string($librarianIds)) {
            $ids = explode(',', $librarianIds);
            $ids = array_map('trim', $ids);
            $ids = array_filter($ids, 'is_numeric');
            return User::whereIn('id', $ids)->get();
        }

        return collect([]);
    }
}
