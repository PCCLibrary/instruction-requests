<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Support\Facades\Log;

class AdminOldLocks extends Component
{
    protected $listeners = ['refreshOldLocks' => '$refresh'];

    public function unlockRequest($requestId)
    {
        try {
            $request = InstructionRequests::find($requestId);

            if (!$request || !$request->isLocked()) {
                session()->flash('error', 'Request is not eligible for admin unlock.');
                return;
            }

            // Check if lock is at least 60 minutes old
            if (!$request->locked_at || now()->diffInMinutes($request->locked_at) < 60) {
                session()->flash('error', 'Request lock is not old enough for admin unlock (must be 60+ minutes).');
                return;
            }

            app(InstructionRequestService::class)->unlockRequest($requestId, true, false);

            Log::info('Admin force unlocked old request', [
                'admin_user_id' => auth()->id(),
                'admin_user_name' => auth()->user()->display_name,
                'request_id' => $requestId,
                'locked_duration_minutes' => $request->locked_at->diffInMinutes(now()),
                'locked_by_user_id' => $request->locked_by,
                'locked_by_user_name' => $request->lockedBy?->display_name
            ]);

            session()->flash('success', "Request #{$requestId} unlocked successfully.");

        } catch (\Exception $e) {
            Log::error('Admin failed to unlock old request', [
                'admin_user_id' => auth()->id(),
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', "Failed to unlock request: {$e->getMessage()}");
        }
    }

    public function unlockAllOld()
    {
        $oldRequests = InstructionRequests::where('locked', true)
            ->where('locked_at', '<=', now()->subHour())
            ->get();

        $unlockedCount = 0;
        $errors = [];

        foreach ($oldRequests as $request) {
            try {
                app(InstructionRequestService::class)->unlockRequest($request->id, true, false);
                $unlockedCount++;
            } catch (\Exception $e) {
                $errors[] = "Request #{$request->id}";
                Log::error('Admin bulk unlock failed for request', [
                    'admin_user_id' => auth()->id(),
                    'request_id' => $request->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Admin bulk unlocked old requests', [
            'admin_user_id' => auth()->id(),
            'admin_user_name' => auth()->user()->display_name,
            'total_requests' => $oldRequests->count(),
            'successful_unlocks' => $unlockedCount,
            'failed_unlocks' => count($errors)
        ]);

        $message = "Unlocked {$unlockedCount} old lock(s).";
        if (!empty($errors)) {
            $message .= " Errors with: " . implode(', ', $errors);
        }

        session()->flash($unlockedCount > 0 ? 'success' : 'error', $message);
    }

    public function render()
    {
        $oldLocks = InstructionRequests::where('locked', true)
            ->where('locked_at', '<=', now()->subHour())
            ->with(['instructor', 'campus', 'classes', 'lockedBy'])
            ->orderBy('locked_at')
            ->get();

        return view('livewire.admin-old-locks', [
            'oldLocks' => $oldLocks
        ]);
    }
}
