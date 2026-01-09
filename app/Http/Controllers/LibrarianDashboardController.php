<?php

namespace App\Http\Controllers;

use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Support\Facades\Auth;

class LibrarianDashboardController extends Controller
{
    protected InstructionRequestService $instructionRequestService;

    public function __construct(InstructionRequestService $instructionRequestService)
    {
        $this->instructionRequestService = $instructionRequestService;
    }

    public function index()
    {
        $librarian = Auth::user();

        $assignedToMeCount = InstructionRequests::query()
            ->where('status', 'assigned')
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where('assigned_librarian_id', $librarian->id);
            })
            ->count();

        $myActiveRequestsCount = InstructionRequests::query()
            ->whereIn('status', ['accepted', 'in_progress'])
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where('assigned_librarian_id', $librarian->id);
            })
            ->count();

        $receivedRequestsCount = InstructionRequests::query()
            ->where('status', 'received')
            ->count();

        $completedRequestsCount = InstructionRequests::query()
            ->where('status', 'completed')
            ->whereHas('detail', function ($query) use ($librarian) {
                $query->where('assigned_librarian_id', $librarian->id);
            })
            ->count();

        return view('dashboard.librarian', compact(
            'assignedToMeCount',
            'myActiveRequestsCount',
            'receivedRequestsCount',
            'completedRequestsCount'
        ));
    }
}
