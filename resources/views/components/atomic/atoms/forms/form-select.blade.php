@props([
    'disabled' => false,
])

@php
$classes = 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500';
$disabledClasses = $disabled ? 'bg-gray-100 cursor-not-allowed' : '';
$finalClasses = trim("{$classes} {$disabledClasses}");
@endphp

<select 
    {{ $attributes->merge(['class' => $finalClasses]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</select>