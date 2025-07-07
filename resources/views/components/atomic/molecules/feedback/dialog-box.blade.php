@props([
    'type' => 'warning', // warning, info
    'title' => '',
    'message' => null,
    'actions' => [], // array of ['label' => 'Yes, Create', 'action' => 'createProject', 'variant' => 'primary']
])

@php
$boxClasses = match($type) {
    'info' => 'bg-green-50 border-green-200',
    default => 'bg-yellow-50 border-yellow-200',
};

$textClasses = match($type) {
    'info' => 'text-green-800',
    default => 'text-yellow-800',
};

$messageClasses = match($type) {
    'info' => 'text-green-700',
    default => 'text-yellow-700',
};
@endphp

<div class="mt-2 p-3 {{ $boxClasses }} border rounded-md">
    @if($title)
        <div class="text-sm {{ $textClasses }} mb-3">
            {{ $title }}
        </div>
    @endif
    
    @if($message)
        <div class="text-sm {{ $messageClasses }} mb-3">
            {{ $message }}
        </div>
    @endif
    
    {{ $slot }}
    
    @if(count($actions) > 0)
        <div class="{{ count($actions) > 2 ? 'space-y-2' : 'flex space-x-2' }}">
            @foreach($actions as $action)
                @php
                    $buttonClasses = match($action['variant'] ?? 'primary') {
                        'secondary' => 'bg-gray-500 hover:bg-gray-600',
                        default => 'bg-blue-600 hover:bg-blue-700',
                    };
                    $fullWidth = count($actions) > 2 ? 'w-full' : '';
                @endphp
                <button type="button" 
                        wire:click="{{ $action['action'] }}"
                        class="{{ $fullWidth }} px-3 py-{{ count($actions) > 2 ? '2' : '1' }} text-sm {{ $buttonClasses }} text-white rounded-md {{ count($actions) > 2 ? 'text-left' : '' }}">
                    {{ $action['label'] }}
                </button>
            @endforeach
        </div>
    @endif
</div>