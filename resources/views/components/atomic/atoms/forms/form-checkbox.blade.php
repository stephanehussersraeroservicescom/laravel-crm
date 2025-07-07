@props([
    'label' => null,
    'disabled' => false,
])

@php
$classes = 'rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
$finalClasses = trim("{$classes} {$disabledClasses}");
@endphp

<label class="flex items-center">
    <input 
        type="checkbox"
        {{ $attributes->merge(['class' => $finalClasses]) }}
        @if($disabled) disabled @endif
    >
    @if($label)
        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
    @else
        <span class="ml-2 text-sm text-gray-700">{{ $slot }}</span>
    @endif
</label>