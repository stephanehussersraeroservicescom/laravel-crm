@props([
    'direction' => 'vertical', // vertical, horizontal
    'spacing' => 'default', // tight, default, loose
])

@php
$directionClasses = match($direction) {
    'horizontal' => 'flex space-x-2',
    default => 'flex flex-col space-y-1',
};

$spacingClasses = match($spacing) {
    'tight' => $direction === 'horizontal' ? 'space-x-1' : 'space-y-0.5',
    'loose' => $direction === 'horizontal' ? 'space-x-4' : 'space-y-2',
    default => $direction === 'horizontal' ? 'space-x-2' : 'space-y-1',
};

$classes = str_replace(
    $direction === 'horizontal' ? 'space-x-2' : 'space-y-1',
    $spacingClasses,
    $directionClasses
);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>