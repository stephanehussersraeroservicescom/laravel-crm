<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormField extends Component
{
    public function __construct(
        public string $label = '',
        public string $name = '',
        public string $type = 'text',
        public bool $required = false,
        public string $help = '',
        public string $placeholder = '',
        public array $options = [],
        public int $rows = 3
    ) {}

    public function render(): View
    {
        return view('components.form-field');
    }
}
