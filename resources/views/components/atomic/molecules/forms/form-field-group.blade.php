@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'error' => null,
    'help' => null,
])

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <label 
            @if($name) for="{{ $name }}" @endif 
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        {{ $slot }}
    </div>
    
    @if($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    
    @if($error)
        <span class="text-red-500 text-xs mt-1 block">{{ $error }}</span>
    @elseif($name && $errors->has($name))
        <span class="text-red-500 text-xs mt-1 block">{{ $errors->first($name) }}</span>
    @endif
</div>