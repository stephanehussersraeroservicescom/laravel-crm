<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subcontractors</h2>
    </x-slot>
    <div class="py-4 max-w-6xl mx-auto">
        <form wire:submit.prevent="save" class="mb-6 flex gap-4 items-end">
            <div>
                <label class="block font-semibold mb-1">Subcontractor Name</label>
                <input type="text" wire:model.live="name" class="rounded border-gray-300" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Comment</label>
                <textarea wire:model.live="comment" class="rounded border-gray-300" rows="1"></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-1">Parent Companies</label>
                <select wire:model.live="selectedParents" multiple class="rounded border-gray-300 h-20">
                    <option value="">Select parent companies...</option>
                    @foreach($availableParents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</div>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                    {{ $editing ? 'Update' : 'Add Subcontractor' }}
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
                    <th class="px-3 py-2 border">Comment</th>
                    <th class="px-3 py-2 border">Parent Companies</th>
                    <th class="px-3 py-2 border">Contacts</th>
                    <th class="px-3 py-2 border"></th>
                </tr>
            </thead>
            <tbody>
            @foreach($subcontractors as $sub)
                <tr>
                    <td class="px-3 py-2 border">{{ $sub->name }}</td>
                    <td class="px-3 py-2 border">{{ $sub->comment ?? '—' }}</td>
                    <td class="px-3 py-2 border">
                        @if($sub->parents->count() > 0)
                            @foreach($sub->parents as $parent)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                    {{ $parent->name }}
                                </span>
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-3 py-2 border">
                        <a href="{{ route('contacts.index', $sub) }}" class="text-blue-600 hover:underline">
                            {{ $sub->contacts->count() }} contact{{ $sub->contacts->count() === 1 ? '' : 's' }}
                        </a>
                    </td>
                    <td class="px-3 py-2 border">
                        <button wire:click="edit({{ $sub->id }})" class="text-blue-600 underline mr-2">Edit</button>
                        <button wire:click="delete({{ $sub->id }})" class="text-red-600 underline">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
