<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TableBox extends Component
{
    public function __construct(
        public bool $responsive = true,
        public string $shadow = 'shadow-sm'
    ) {}

    public function render(): View
    {
        return view('components.table-box');
    }
}
