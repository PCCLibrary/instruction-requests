<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Instructor;
use App\Models\InstructionRequestDetails;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected InstructionRequestService $instructionRequestService;

    public function __construct(InstructionRequestService $instructionRequestService)
    {
        $this->instructionRequestService = $instructionRequestService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {

        // Retrieve the currently authenticated user (librarian)
        $librarian = Auth::user();

        // Query InstructionRequests using whereHas to filter by related InstructionRequestDetails
        $myRequests = InstructionRequests::with('detail')
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'accepted'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $acceptedRequests = InstructionRequests::with('detail')
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'accepted'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $completedRequests = InstructionRequests::with('detail')
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'completed'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get();


        // Count all instructors
        $instructorCount = Instructor::count();

        // Sum all instruction_duration fields in instruction_request_details
        $totalInstructionHours = InstructionRequestDetails::sum('instruction_duration');

        // Get last 10 pending instruction requests for table
        $tableRequests = $this->instructionRequestService->getRequestsByStatus('received', 10);

        // get all pending requests
        $pendingRequests = $this->instructionRequestService->getRequestsByStatus('received');

        // Get all in-progress instruction requests
        $inProgressRequests = $this->instructionRequestService->getRequestsByStatus('assigned');

        // Get all in-progress instruction requests
//        $acceptedRequests = $this->instructionRequestService->getRequestsByStatus('accepted');

        // Get all completed instruction requests
//        $completedRequests = $this->instructionRequestService->getRequestsByStatus('completed');

        // Pass the data to the view
        return view('dashboard.index', compact(
            'librarian',
            'instructorCount',
            'totalInstructionHours',
            'myRequests',
            'pendingRequests',
            'acceptedRequests',
            'tableRequests',
            'inProgressRequests',
            'completedRequests'
        ));
    }
}
