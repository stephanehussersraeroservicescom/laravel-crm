@props([
    'title' => 'Create New Item',
    'showForm' => false,
    'toggleAction' => 'toggleForm',
    'toggleText' => '+ Add New',
    'backText' => '← Back to list',
])

<div class="space-y-2">
    {{ $slot }}
    
    <!-- Toggle to show form -->
    <button type="button" 
            wire:click="{{ $toggleAction }}"
            class="text-sm text-blue-600 hover:text-blue-800">
        {{ $showForm ? $backText : $toggleText }}
    </button>
    
    <!-- Inline Form -->
    @if($showForm)
        <div class="border border-blue-300 rounded-md p-3 bg-blue-50">
            <div class="text-sm font-medium text-gray-700 mb-2">{{ $title }}</div>
            {{ $form ?? '' }}
        </div>
    @endif
</div>