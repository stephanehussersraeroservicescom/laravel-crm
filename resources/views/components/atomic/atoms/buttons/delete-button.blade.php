@props(['action' => null, 'confirmMessage' => 'Are you sure you want to delete this item?'])

<button {{ $attributes->merge(['class' => 'text-red-600 hover:text-red-900', 'type' => 'button']) }}
    @if($action) 
        wire:click="{{ $action }}" 
        onclick="return confirm('{{ $confirmMessage }}')"
    @endif>
    {{ $slot ?? 'Delete' }}
</button>