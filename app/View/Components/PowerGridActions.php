<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PowerGridActions extends Component
{
    public function __construct(
        public $row,
        public bool $canEdit = true,
        public bool $canDelete = true,
        public ?string $editRoute = null,
        public ?string $deleteEvent = 'confirmDelete',
        public ?string $size = 'w-4 h-4'
    ) {}

    public function render(): View
    {
        return view('components.table-actions');
    }
}
