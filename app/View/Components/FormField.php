<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class FormField extends Component
{
    public array $options;

    public function __construct(
        public string $label = '',
        public string $name = '',
        public string $type = 'text',
        public bool $required = false,
        public string $help = '',
        public string $placeholder = '',
        array|Collection $options = [],
        public int $rows = 3
    ) {
        // Convert Collection to array if needed
        $this->options = $options instanceof Collection ? $options->toArray() : $options;
    }

    public function render(): View
    {
        return view('components.form-field');
    }
}
