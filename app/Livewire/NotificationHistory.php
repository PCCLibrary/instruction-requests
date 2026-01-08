<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\InstructionRequestService;
use App\Models\InstructionRequests;

class NotificationHistory extends Component
{
    public $requestId;

    public function mount($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getNotificationLogsProperty()
    {
        $request = InstructionRequests::with('campus')->findOrFail($this->requestId);
        $logs = DB::table('notification_logs')
            ->where('instruction_request_id', $this->requestId)
            ->orderBy('sent_at', 'desc')
            ->get();

        $grouped = [];

        foreach ($logs as $log) {
            if ($log->recipient_type === 'scheduler') {
                $key = $log->notification_type . '|' . $log->sent_at;

                if (!isset($grouped[$key])) {
                    $grouped[$key] = (object)[
                        'is_group' => true,
                        'notification_type' => $log->notification_type,
                        'recipient_type' => 'scheduler',
                        'sent_at' => $log->sent_at,
                        'campus_name' => $request->campus->name,
                        'count' => 0,
                        'log_ids' => [],
                    ];
                }

                $grouped[$key]->count++;
                $grouped[$key]->log_ids[] = $log->id;
            } else {
                $grouped[$log->id] = (object)[
                    'is_group' => false,
                    'id' => $log->id,
                    'notification_type' => $log->notification_type,
                    'recipient_type' => $log->recipient_type,
                    'recipient_email' => $log->recipient_email,
                    'sent_at' => $log->sent_at,
                ];
            }
        }

        return collect(array_values($grouped));
    }

    public function resendNotification($logId)
    {
        try {
            app(InstructionRequestService::class)->resendNotification($this->requestId, $logId);

            $this->dispatch('notification-sent', [
                'message' => 'Notification resent successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification-error', [
                'message' => 'Failed to resend notification: ' . $e->getMessage()
            ]);
        }
    }

    public function resendGroup($logIds)
    {
        try {
            app(InstructionRequestService::class)->resendNotificationGroup($this->requestId, $logIds);

            $this->dispatch('notification-sent', [
                'message' => 'Notifications resent successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification-error', [
                'message' => 'Failed to resend notifications: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notification-history');
    }
}
