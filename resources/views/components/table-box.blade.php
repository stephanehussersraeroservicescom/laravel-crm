<div class="bg-white rounded-lg {{ $shadow }} border border-gray-200 overflow-hidden">
    @if($responsive)
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>