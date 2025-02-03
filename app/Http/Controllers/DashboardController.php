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

        // Get accepted requests - limited to 10, newest first
        $myAssignedRequests = InstructionRequests::with(['detail', 'instructor', 'classes'])
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'assigned'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get accepted requests - limited to 10, newest first
        $myAcceptedRequests = InstructionRequests::with(['detail', 'instructor', 'classes'])
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'accepted'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get completed requests - limited to 10, newest first
        $completedRequests = InstructionRequests::with(['detail', 'instructor', 'classes'])
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where([
                    'assigned_librarian_id' => $librarian->id,
                    'status' => 'completed'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

//        // Count all instructors
//        $instructorCount = Instructor::count();
//
//        // Sum all instruction_duration fields in instruction_request_details
//        $totalInstructionHours = InstructionRequestDetails::sum('instruction_duration');

        // Get last 10 pending instruction requests for table (already limited)
        $tableRequests = $this->instructionRequestService->getRequestsByStatus('received', 10);

        // Get all pending requests - limited to 10, newest first
        $receivedRequests = $this->instructionRequestService->getRequestsByStatus('received', 10);

//        // Get all in-progress instruction requests - limited to 10, newest first
//        $inProgressRequests = $this->instructionRequestService->getRequestsByStatus('assigned', 10);

        // Pass the data to the view
        return view('dashboard.index', compact(
            'librarian',
//            'instructorCount',
//            'totalInstructionHours',
            'myAssignedRequests',
            'receivedRequests',
            'myAcceptedRequests',
            'tableRequests',
//            'inProgressRequests',
            'completedRequests'
        ));
    }
}
