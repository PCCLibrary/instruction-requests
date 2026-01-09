<?php

namespace App\Services;

use App\Models\User;
use App\Models\InstructionRequests;
use Illuminate\Support\Collection;

class LibrarianWorkloadService
{
    public function getWorkloadStatistics(?int $campusFilter = null): Collection
    {
        $librarians = User::orderedLibrariansScope()
            ->with(['campus', 'campuses'])
            ->when($campusFilter, function($q) use ($campusFilter) {
                $q->whereHas('campuses', function($campusQuery) use ($campusFilter) {
                    $campusQuery->where('campuses.id', $campusFilter);
                });
            })
            ->get();

        return $librarians->map(function($user) {
            return [
                'id' => $user->id,
                'display_name' => $user->display_name,
                'campuses_codes' => $user->campuses->pluck('code')->filter()->join(', '),
                'campuses_names' => $user->campuses->pluck('name')->join(', '),
                'total_active' => $this->countTotalActive($user->id),
                'assigned_count' => $this->countAssigned($user->id),
                'accepted_count' => $this->countAccepted($user->id),
                'in_progress_count' => $this->countInProgress($user->id),
                'async_count' => $this->countAsync($user->id),
                'in_person_count' => $this->countInPerson($user->id),
                'remote_count' => $this->countRemote($user->id),
            ];
        });
    }

    private function countTotalActive(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->whereNotIn('status', ['completed', 'rejected'])
        ->count();
    }

    private function countAssigned(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('status', 'assigned')
        ->count();
    }

    private function countAccepted(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('status', 'accepted')
        ->count();
    }

    private function countInProgress(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->whereIn('status', ['scheduled', 'in_progress'])  // Count both during transition
        ->count();
    }

    private function countAsync(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('instruction_type', 'asynchronous')
        ->whereNotIn('status', ['completed', 'rejected'])
        ->count();
    }

    private function countInPerson(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('instruction_type', 'on-campus')
        ->whereNotIn('status', ['completed', 'rejected'])
        ->count();
    }

    private function countRemote(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('instruction_type', 'remote')
        ->whereNotIn('status', ['completed', 'rejected'])
        ->count();
    }
}
