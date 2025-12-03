<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SchedulerDashboardController extends Controller
{
    /**
     * Display the scheduler dashboard with status metrics
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Box 1: Received Requests (unassigned, waiting for librarian)
        $receivedCount = InstructionRequests::where('status', 'received')->count();

        // Box 2: Expiring Soon (received requests with instruction date within next 14 days)
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(14)->endOfDay();

        $expiringSoonCount = InstructionRequests::where('status', 'received')
            ->whereHas('detail', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('instruction_datetime', [$startDate, $endDate]);
            })
            ->count();

        // Box 3: Rejected Requests (need reassignment)
        $rejectedCount = InstructionRequests::where('status', 'rejected')->count();

        // Box 4: Active Work (assigned + accepted + scheduled)
        $activeWorkCount = InstructionRequests::whereIn('status', ['assigned', 'accepted', 'scheduled'])
            ->count();

        return view('mockups.scheduler-dashboard', compact(
            'receivedCount',
            'expiringSoonCount',
            'rejectedCount',
            'activeWorkCount'
        ));
    }
}
