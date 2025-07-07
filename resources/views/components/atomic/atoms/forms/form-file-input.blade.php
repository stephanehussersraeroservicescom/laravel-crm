@props([
    'multiple' => false,
    'accept' => '',
    'disabled' => false,
])

@php
$classes = 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
$finalClasses = trim("{$classes} {$disabledClasses}");
@endphp

<input 
    type="file"
    {{ $attributes->merge(['class' => $finalClasses]) }}
    @if($multiple) multiple @endif
    @if($accept) accept="{{ $accept }}" @endif
    @if($disabled) disabled @endif
>