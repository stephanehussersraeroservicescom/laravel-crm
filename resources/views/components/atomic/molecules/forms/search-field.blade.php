@props([
    'placeholder' => 'Search...',
    'label' => 'Search',
    'span' => 'default', // default, wide (for lg:col-span-2)
])

@php
$spanClasses = $span === 'wide' ? 'lg:col-span-2' : '';
@endphp

<div {{ $attributes->merge(['class' => $spanClasses]) }}>
    <x-atomic.molecules.forms.form-field-group :label="$label">
        <x-atomic.atoms.forms.form-input 
            type="text" 
            :placeholder="$placeholder"
            {{ $attributes->only(['wire:model.live.debounce.300ms', 'wire:model.live']) }}
        />
    </x-atomic.molecules.forms.form-field-group>
</div>