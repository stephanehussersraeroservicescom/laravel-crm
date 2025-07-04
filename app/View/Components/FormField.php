<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FormField extends Component
{
    public function __construct(
        public string $label = '',
        public bool $required = false,
        public string $help = ''
    ) {}

    public function render(): View
    {
        return view('components.form-field');
    }
}
