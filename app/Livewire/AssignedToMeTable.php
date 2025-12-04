<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssignedToMeTable extends Component
{
    /**
     * Get ALL assigned requests for current librarian (NO LIMIT)
     */
    public function getAssignedRequestsProperty()
    {
        return InstructionRequests::with(['detail', 'instructor', 'classes', 'campus'])
            ->where('status', 'assigned')
            ->whereHas('detail', function ($query) {
                $query->where('assigned_librarian_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get(); // NO TAKE/LIMIT - Get all results
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.assigned-to-me-table');
    }
}
