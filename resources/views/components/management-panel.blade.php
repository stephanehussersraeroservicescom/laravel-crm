<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">
            @if($title)
                {{ $title }}
            @else
                {{ $editing ? 'Edit ' . $entityName : 'Add New ' . $entityName }}
            @endif
        </h3>
    </div>
    
    {{ $slot }}
</div>