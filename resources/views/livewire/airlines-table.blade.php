<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Airlines
        </h2>
    </x-slot>
    <div class="py-4 max-w-4xl mx-auto">
        <form wire:submit.prevent="save" class="mb-6 flex gap-4 items-end">
            <div>
                <label class="block font-semibold mb-1">Airline Name</label>
                <input type="text" wire:model.live="name" class="rounded border-gray-300" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Region</label>
                <input type="text" wire:model.live="region" class="rounded border-gray-300" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Account Executive</label>
                <input type="text" wire:model.live="account_executive" class="rounded border-gray-300">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                    {{ $editing ? 'Update' : 'Add Airline' }}
                </button>
                @if($editing)
                    <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 underline">Cancel</button>
                @endif
            </div>
        </form>
        <table class="min-w-full border rounded shadow bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Name</th>
                    <th class="px-3 py-2 border">Region</th>
                    <th class="px-3 py-2 border">Account Executive</th>
                    <th class="px-3 py-2 border"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($airlines as $airline)
                <tr>
                    <td class="px-3 py-2 border">{{ $airline->name }}</td>
                    <td class="px-3 py-2 border">{{ $airline->region }}</td>
                    <td class="px-3 py-2 border">{{ $airline->account_executive }}</td>
                    <td class="px-3 py-2 border">
                        <button wire:click="edit({{ $airline->id }})" class="text-blue-600 underline mr-2">Edit</button>
                        <button wire:click="delete({{ $airline->id }})" class="text-red-600 underline">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
