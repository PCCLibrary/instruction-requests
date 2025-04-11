<?php

namespace App\Livewire;

use App\Models\Instructor;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class InstructorSelector extends Component
{
    public ?int $instructorId = null;
    public string $search = '';
    public ?Instructor $selectedInstructor = null;

    public function mount(?int $instructorId = null)
    {
//        Log::info('InstructorSelector mounted', ['instructorId' => $instructorId]);
//        $this->instructorId = $instructorId;

        // If an instructor ID is provided, preselect that instructor
        if ($this->instructorId) {
            $this->selectedInstructor = Instructor::find($this->instructorId);
        }
    }

    public function getFilteredInstructorsProperty()
    {
        $query = Instructor::query();

        if (!empty($this->search)) {
            $search = strtolower($this->search);

            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('name')->get();
    }

    public function selectInstructor(int $instructorId)
    {
        $this->selectedInstructor = Instructor::find($instructorId);
        Log::info('Instructor selected', ['instructor' => $this->selectedInstructor->id]);
    }

    public function clearSearch()
    {
        $this->search = '';
    }

    public function confirmSelection()
    {
        if ($this->selectedInstructor) {
            // Create the data array to pass to the event
            $instructorData = [
                'id' => $this->selectedInstructor->id,
                'name' => $this->selectedInstructor->name,
                'display_name' => $this->selectedInstructor->display_name ?? '',
                'pronouns' => $this->selectedInstructor->pronouns ?? '',
                'email' => $this->selectedInstructor->email,
                'phone' => $this->selectedInstructor->phone ?? ''
            ];

            // Log before dispatching
            Log::info('Confirming instructor selection', ['instructor' => $instructorData]);

            // Dispatch browser event with instructor data
            $this->dispatch('instructor-selected', $instructorData);

            // Dispatch event to close modal
            $this->dispatch('close-instructor-modal');
        }
    }

    public function cancelSelection()
    {
        Log::info('Canceling instructor selection');

        // Dispatch event to close modal
        $this->dispatch('close-instructor-modal');
    }

    public function render()
    {
        // If we have a selected instructor but it's not in the filtered list
        // (due to search criteria), add it to ensure it's visible
        $instructors = $this->filteredInstructors;

        if ($this->selectedInstructor && !empty($this->search) &&
            !$instructors->contains('id', $this->selectedInstructor->id)) {
            // Create a merged collection with the selected instructor at the top
            $instructors = collect([$this->selectedInstructor])->merge($instructors);
        }

        return view('livewire.instructor-selector', [
            'filteredInstructors' => $instructors
        ]);
    }
}
