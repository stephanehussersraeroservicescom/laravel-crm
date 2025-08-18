@props(['title' => 'Filters'])

<div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
    @if(isset($search))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            {{ $search }}
        </div>
    @endif
    
    @if(isset($filters))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{ $filters }}
        </div>
    @endif
    
    @if(isset($actions))
        <div class="flex justify-between items-center mt-6">
            {{ $actions }}
        </div>
    @endif
</div>