@props(['striped' => false])

<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
            @if(isset($head))
                <thead class="bg-gray-50">
                    {{ $head }}
                </thead>
            @endif
            
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    
    @if(isset($pagination))
        <div class="px-6 py-4 border-t">
            {{ $pagination }}
        </div>
    @endif
</div>