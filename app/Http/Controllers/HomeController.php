<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequest;
use App\Services\InstructionRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Instructor;
use App\Models\InstructionRequestDetails;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $instructionRequestService;

    public function __construct(InstructionRequestService $instructionRequestService)
    {
        $this->instructionRequestService = $instructionRequestService;
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {


        // Count all instructors
        $instructorCount = Instructor::count();

        // Sum all instruction_duration fields in instruction_request_details
        $totalInstructionHours = InstructionRequestDetails::sum('instruction_duration');

        // Get all pending instruction requests
//        $pendingRequests = InstructionRequestDetails::where('status', 'pending')->get();
        $pendingRequests = $this->instructionRequestService->getRequestsByStatus('pending');

        // Get all in-progress instruction requests
        $inProgressRequests = $this->instructionRequestService->getRequestsByStatus('in progress');

        // Get all completed instruction requests
        $completedRequests = $this->instructionRequestService->getRequestsByStatus('completed');

        // Pass the data to the view
        return view('dashboard.index', compact(
            'instructorCount',
            'totalInstructionHours',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests'
        ));
    }
}
