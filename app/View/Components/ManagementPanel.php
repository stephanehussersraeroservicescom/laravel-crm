<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ManagementPanel extends Component
{
    public function __construct(
        public string $title = '',
        public bool $editing = false,
        public string $entityName = 'Item'
    ) {}

    public function render(): View
    {
        return view('components.management-panel');
    }
}
