@props(['action' => null])

<button {{ $attributes->merge(['class' => 'text-green-600 hover:text-green-900', 'type' => 'button']) }}
    @if($action) wire:click="{{ $action }}" @endif>
    {{ $slot ?? 'Restore' }}
</button>