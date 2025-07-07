@props([
    'designStatus' => null,
    'commercialStatus' => null,
    'size' => 'small', // small, default
])

@php
$containerClasses = $size === 'small' ? 'text-xs space-y-1' : 'text-sm space-y-2';
@endphp

<div class="{{ $containerClasses }}">
    @if($designStatus)
        <x-atomic.atoms.feedback.status-badge variant="primary" :size="$size">
            Design: {{ $designStatus }}
        </x-atomic.atoms.feedback.status-badge>
    @endif
    @if($commercialStatus)
        <x-atomic.atoms.feedback.status-badge variant="success" :size="$size">
            Commercial: {{ $commercialStatus }}
        </x-atomic.atoms.feedback.status-badge>
    @endif
</div>