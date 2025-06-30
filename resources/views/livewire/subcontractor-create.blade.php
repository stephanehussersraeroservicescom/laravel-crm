<div class="max-w-lg mx-auto mt-8 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Add New Subcontractor</h2>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label class="block font-semibold mb-1">Name</label>
            <input type="text" wire:model.live="name" class="border rounded w-full" required>
            @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Subcontractor</button>
    </form>
    @if($success)
        <div class="mt-4 text-green-700 font-semibold">Subcontractor added successfully!</div>
    @endif
</div>
