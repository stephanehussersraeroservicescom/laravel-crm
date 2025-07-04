<div class="py-6 px-4 sm:px-6 lg:px-8 {{ $maxWidth }} mx-auto">
    @if($title)
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
        </x-slot>
    @endif
    
    {{ $slot }}
</div>