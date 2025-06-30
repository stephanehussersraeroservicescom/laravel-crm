<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Projects
        </h2>
    </x-slot>

    <div class="py-4 max-w-7xl mx-auto">
        <livewire:projects-table />
    </div>
</x-app-layout>
