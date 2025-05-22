<?php

namespace App\Livewire;

use App\Models\Campus;
use Livewire\Component;

class CampusReorderTable extends Component
{
    public $campuses;
    public $originalOrder;
    public $hasChanges = false;

    public function mount()
    {
        $this->loadCampuses();
    }

    public function loadCampuses()
    {
        $this->campuses = Campus::ordered()->get();
        $this->originalOrder = $this->campuses->pluck('id')->toArray();
        $this->hasChanges = false;
    }

    public function updateOrder($orderedIds)
    {
        // Reorder the campuses collection based on the new order
        $reorderedCampuses = collect();
        foreach ($orderedIds as $id) {
            $campus = $this->campuses->firstWhere('id', $id);
            if ($campus) {
                $reorderedCampuses->push($campus);
            }
        }
        $this->campuses = $reorderedCampuses;
        $this->hasChanges = true;
    }

    public function saveOrder()
    {
        try {
            foreach ($this->campuses as $index => $campus) {
                Campus::where('id', $campus->id)
                    ->update(['sort_order' => ($index + 1) * 10]);
            }

            $this->loadCampuses(); // Reload to get fresh data
            $this->dispatch('orderSaved');

        } catch (\Exception $e) {
            $this->dispatch('orderError', ['message' => 'Failed to save order']);
        }
    }

    public function cancelOrder()
    {
        $this->loadCampuses(); // Reset to original state
        $this->dispatch('orderCancelled');
    }

    public function render()
    {
        return view('livewire.campus-reorder-table');
    }
}
