<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormGrid extends Component
{
    public function __construct(
        public int $cols = 3,
        public string $gap = 'gap-4',
        public string $responsive = 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
    ) {
        // Override responsive if specific cols are provided
        if ($cols !== 3) {
            $this->responsive = "grid-cols-1 md:grid-cols-{$cols}";
        }
    }

    public function render(): View
    {
        return view('components.form-grid');
    }
}
