<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TableContainer extends Component
{
    public function __construct(
        public string $title = '',
        public bool $responsive = true,
        public string $maxWidth = 'max-w-7xl'
    ) {}

    public function render(): View
    {
        return view('components.table-container');
    }
}
