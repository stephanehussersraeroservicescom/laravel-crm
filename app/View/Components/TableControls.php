<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TableControls extends Component
{
    public function __construct(
        public bool $showSearch = true,
        public bool $showDeleted = true,
        public string $searchPlaceholder = 'Search...'
    ) {}

    public function render(): View
    {
        return view('components.table-controls');
    }
}
