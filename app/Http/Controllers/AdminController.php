<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InstructionRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $adminUsers = User::where('is_admin', true)->orderBy('display_name')->get();
        return view('admin.index', compact('adminUsers'));
    }

    public function toggleAssignmentAvailability(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $currentUser = Auth::user();

        // Only allow toggling admin users
        if (!$user->is_admin) {
            return redirect()->route('admin.index')->with('error', 'Can only toggle assignment availability for admin users.');
        }

        $oldStatus = $user->available_for_assignment;
        $user->available_for_assignment = !$user->available_for_assignment;
        $user->save();

        $status = $user->available_for_assignment ? 'enabled' : 'disabled';

        // Log the admin action
        Log::info("User {$currentUser->display_name} {$status} assignment availability for admin user {$user->display_name}", [
            'current_user_id' => $currentUser->id,
            'target_user_id' => $user->id,
            'action' => 'toggle_assignment_availability',
            'old_status' => $oldStatus,
            'new_status' => $user->available_for_assignment
        ]);

        return redirect()->route('admin.index')->with('success', "Assignment availability {$status} for {$user->display_name}.");
    }

    /**
     * System status endpoint for auto-refresh
     */
    public function systemStatus(): JsonResponse
    {
        $queuePending = DB::table('jobs')->count();
        $queueFailed = DB::table('failed_jobs')->count();
        $activeLocks = InstructionRequests::where('locked', true)->count();

        // Generate queue badge HTML directly
        if ($queueFailed > 0) {
            $queueHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">⚠️ ' . $queueFailed . ' failed' . ($queuePending > 0 ? ', ' . $queuePending . ' pending' : '') . '</span>';
        } elseif ($queuePending > 0) {
            $queueHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">⏳ ' . $queuePending . ' pending</span>';
        } else {
            $queueHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✅ Queue empty</span>';
        }

        // Generate locks badge HTML directly
        if ($activeLocks > 0) {
            $locksHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">🔒 ' . $activeLocks . ' locked</span>';
        } else {
            $locksHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✅ No locks</span>';
        }

        return response()->json([
            'queue_html' => $queueHtml,
            'locks_html' => $locksHtml,
        ]);
    }

    /**
     * Clear all application caches
     */
    public function clearCache(): RedirectResponse
    {
        $user = Auth::user();

        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Log the admin action
            Log::info("User {$user->display_name} cleared application cache", [
                'user_id' => $user->id,
                'action' => 'clear_cache'
            ]);

            return redirect()->route('admin.index')->with('success', 'All caches cleared successfully.');
        } catch (\Exception $e) {
            // Log the failed action
            Log::error("User {$user->display_name} failed to clear application cache: {$e->getMessage()}", [
                'user_id' => $user->id,
                'action' => 'clear_cache',
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.index')->with('error', 'Error clearing caches: ' . $e->getMessage());
        }
    }

    /**
     * Flush the mail queue
     */
    public function flushQueue(): RedirectResponse
    {
        $user = Auth::user();

        try {
            Artisan::call('queue:flush');

            // Log the admin action
            Log::warning("User {$user->display_name} flushed the mail queue", [
                'user_id' => $user->id,
                'action' => 'flush_queue'
            ]);

            return redirect()->route('admin.index')->with('success', 'Mail queue flushed successfully.');
        } catch (\Exception $e) {
            // Log the failed action
            Log::error("User {$user->display_name} failed to flush mail queue: {$e->getMessage()}", [
                'user_id' => $user->id,
                'action' => 'flush_queue',
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.index')->with('error', 'Error flushing queue: ' . $e->getMessage());
        }
    }

    /**
     * Restart queue workers gracefully
     */
    public function restartQueue(): RedirectResponse
    {
        $user = Auth::user();

        try {
            Artisan::call('queue:restart');

            // Log the admin action
            Log::info("User {$user->display_name} restarted queue workers", [
                'user_id' => $user->id,
                'action' => 'restart_queue'
            ]);

            return redirect()->route('admin.index')->with('success', 'Queue workers restarted successfully. Workers will restart after completing current jobs.');
        } catch (\Exception $e) {
            // Log the failed action
            Log::error("User {$user->display_name} failed to restart queue workers: {$e->getMessage()}", [
                'user_id' => $user->id,
                'action' => 'restart_queue',
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.index')->with('error', 'Error restarting queue: ' . $e->getMessage());
        }
    }
}
