@props([
    'placeholder' => '',
    'rows' => 3,
    'disabled' => false,
])

@php
$classes = 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm';
$disabledClasses = $disabled ? 'bg-gray-100 cursor-not-allowed' : '';
$finalClasses = trim("{$classes} {$disabledClasses}");
@endphp

<textarea 
    {{ $attributes->merge(['class' => $finalClasses]) }}
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    @if($disabled) disabled @endif
>{{ $slot }}</textarea>