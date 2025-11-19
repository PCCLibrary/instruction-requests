<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InstructionRequests;
use App\Services\NotificationService;
use App\Notifications\RequestReceivedNotification;
use App\Notifications\RequestAssignedNotification;
use App\Notifications\RequestAcceptedNotification;
use App\Notifications\RequestRejectedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminTestNotifications extends Component
{
    public $selectedRequestId;
    public $notificationType = 'assigned';
    public $emailAddress;
    public $previewHtml = '';
    public $previewSubject = '';
    public $sendSuccess = false;
    public $sendError = '';

    public function mount()
    {
        $this->emailAddress = auth()->user()->email;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedRequestId', 'notificationType'])) {
            $this->updatePreview();
        }
    }

    public function updatePreview()
    {
        $this->sendSuccess = false;
        $this->sendError = '';

        if (!$this->selectedRequestId || !$this->notificationType) {
            $this->previewHtml = '';
            $this->previewSubject = '';
            return;
        }

        try {
            $notificationService = app(NotificationService::class);

            $package = match($this->notificationType) {
                'received' => $notificationService->prepareReceivedNotificationPackage($this->selectedRequestId),
                'assigned' => $notificationService->prepareAssignedNotificationPackage($this->selectedRequestId),
                'accepted' => $notificationService->prepareAcceptedNotificationPackage($this->selectedRequestId),
                'rejected' => $notificationService->prepareRejectedNotificationPackage($this->selectedRequestId),
                default => null
            };

            if ($package) {
                $this->previewSubject = $package->librarianSubject;

                $viewName = match($this->notificationType) {
                    'received' => 'emails.librarian.new_request',
                    'assigned' => 'emails.librarian.assigned',
                    'accepted' => 'emails.librarian.accepted',
                    'rejected' => 'emails.librarian.rejected',
                };

                $this->previewHtml = view($viewName, [
                    'request' => $package->templateData,
                    'dashboardUrl' => $package->dashboardUrl,
                    'emailSubject' => $package->librarianSubject
                ])->render();
            }
        } catch (\Exception $e) {
            $this->previewHtml = '<p style="color: red;">Error generating preview: ' . $e->getMessage() . '</p>';
            $this->previewSubject = 'Error';
            Log::error('Failed to generate notification preview', [
                'admin_user_id' => auth()->id(),
                'request_id' => $this->selectedRequestId,
                'type' => $this->notificationType,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendTestEmail()
    {
        $this->sendSuccess = false;
        $this->sendError = '';

        if (!$this->selectedRequestId || !$this->notificationType || !$this->emailAddress) {
            $this->sendError = 'Please select a request, notification type, and enter an email address.';
            return;
        }

        if (!filter_var($this->emailAddress, FILTER_VALIDATE_EMAIL)) {
            $this->sendError = 'Please enter a valid email address.';
            return;
        }

        try {
            $notificationService = app(NotificationService::class);

            $package = match($this->notificationType) {
                'received' => $notificationService->prepareReceivedNotificationPackage($this->selectedRequestId),
                'assigned' => $notificationService->prepareAssignedNotificationPackage($this->selectedRequestId),
                'accepted' => $notificationService->prepareAcceptedNotificationPackage($this->selectedRequestId),
                'rejected' => $notificationService->prepareRejectedNotificationPackage($this->selectedRequestId),
                default => null
            };

            if ($package) {
                $notification = match($this->notificationType) {
                    'received' => new RequestReceivedNotification($package),
                    'assigned' => new RequestAssignedNotification($package),
                    'accepted' => new RequestAcceptedNotification($package),
                    'rejected' => new RequestRejectedNotification($package),
                };

                Notification::route('mail', $this->emailAddress)->notify($notification);

                Log::info('Admin sent test notification', [
                    'admin_user_id' => auth()->id(),
                    'admin_user_name' => auth()->user()->display_name,
                    'request_id' => $this->selectedRequestId,
                    'notification_type' => $this->notificationType,
                    'recipient_email' => $this->emailAddress
                ]);

                $this->sendSuccess = true;
            }
        } catch (\Exception $e) {
            $this->sendError = 'Failed to send email: ' . $e->getMessage();
            Log::error('Admin failed to send test notification', [
                'admin_user_id' => auth()->id(),
                'request_id' => $this->selectedRequestId,
                'type' => $this->notificationType,
                'email' => $this->emailAddress,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $recentRequests = InstructionRequests::with(['instructor', 'campus', 'librarian'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($request) {
                $date = $request->preferred_datetime
                    ? $request->preferred_datetime->format('m/d/Y')
                    : ($request->asynchronous_instruction_ready_date
                        ? \Carbon\Carbon::parse($request->asynchronous_instruction_ready_date)->format('m/d/Y')
                        : 'TBD');
                $campus = $request->campus->name;
                $class = $request->department . $request->course_number;
                $librarian = $request->librarian
                    ? $request->librarian->display_name
                    : 'Unassigned';

                return [
                    'id' => $request->id,
                    'label' => "{$date} - {$campus} - {$class} - {$librarian}"
                ];
            });

        return view('livewire.admin-test-notifications', [
            'recentRequests' => $recentRequests
        ]);
    }
}
