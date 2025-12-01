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
            ->with('campus')
            ->when($campusFilter, fn($q) => $q->where('campus_id', $campusFilter))
            ->get();

        return $librarians->map(function($user) {
            return [
                'id' => $user->id,
                'display_name' => $user->display_name,
                'campus_name' => $user->campus?->name ?? '',
                'total_active' => $this->countTotalActive($user->id),
                'assigned_count' => $this->countAssigned($user->id),
                'accepted_count' => $this->countAccepted($user->id),
                'scheduled_count' => $this->countScheduled($user->id),
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

    private function countScheduled(int $librarianId): int
    {
        return InstructionRequests::whereHas('detail', fn($q) =>
            $q->where('assigned_librarian_id', $librarianId)
        )
        ->where('status', 'scheduled')
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
