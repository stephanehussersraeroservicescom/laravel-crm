@props(['action' => null])

<button {{ $attributes->merge(['class' => 'text-indigo-600 hover:text-indigo-900', 'type' => 'button']) }}
    @if($action) wire:click="{{ $action }}" @endif>
    {{ $slot ?? 'Edit' }}
</button>