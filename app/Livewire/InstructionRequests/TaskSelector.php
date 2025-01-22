<?php

namespace App\Livewire\InstructionRequests;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\InstructionRequests;

class TaskSelector extends Component
{
    public InstructionRequests $request;

    public array $selectedTasks = [];
    public string $otherDescription = '';

    public array $availableTasks = [
        'video' => 'Video',
        'non_video' => 'Non Video',
        'modified_tutorial' => 'Modified Tutorial',
        'embedded' => 'Embedded Librarian',
        'research_guide' => 'Research Guide',
        'handout' => 'Handout',
        'developed_assigment' => 'Developed Assignment',
        'other_materials' => 'Other'
    ];

    public function mount()
    {
        // Initialize selected tasks from the instruction request detail
        $this->selectedTasks = collect($this->availableTasks)
            ->keys()
            ->filter(fn ($task) => $this->request->detail->{$task})
            ->values()
            ->toArray();

        $this->otherDescription = $this->request->detail->other_describe ?? '';
    }

    public function toggleTask($task)
    {
        if (in_array($task, $this->selectedTasks)) {
            $this->selectedTasks = array_values(array_diff($this->selectedTasks, [$task]));
        } else {
            $this->selectedTasks[] = $task;
        }

        // Emit event for parent form if needed
        $this->dispatch('tasks-updated', $this->selectedTasks);
    }

    public function updatedOtherDescription()
    {
        $this->dispatch('other-description-updated', $this->otherDescription);
    }

    public function render()
    {
        return view('livewire.instruction-requests.task-selector');
    }
}
