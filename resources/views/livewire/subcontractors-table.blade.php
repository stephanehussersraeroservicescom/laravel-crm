<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subcontractors</h2>
    </x-slot>
    <div class="py-4 max-w-6xl mx-auto">
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editing ? 'Edit Subcontractor' : 'Add New Subcontractor' }}</h3>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="flex flex-col">
                        <label class="block font-semibold mb-2 h-6">Subcontractor Name</label>
                        <input type="text" wire:model.live="name" class="rounded border-gray-300 p-3" required>
                        <div class="min-h-[1.5rem] mt-2"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block font-semibold mb-2 h-6">Comment</label>
                        <textarea wire:model.live="comment" class="rounded border-gray-300 resize-none p-3" rows="1"></textarea>
                        <div class="min-h-[1.5rem] mt-2"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block font-semibold mb-2 h-6">Parent Companies</label>
                        <select wire:model.live="selectedParents" multiple class="rounded border-gray-300 h-20 p-2">
                            <option value="">Select parent companies...</option>
                            @foreach($availableParents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <div class="min-h-[1.5rem] mt-2">
                            <div class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-start gap-3">
                    <button type="submit" class="bg-blue-600 text-white rounded px-6 py-3 hover:bg-blue-700 font-medium">
                        {{ $editing ? 'Update' : 'Add Subcontractor' }}
                    </button>
                    @if($editing)
                        <button type="button" wire:click="cancelEdit" class="px-6 py-3 text-gray-600 border border-gray-300 rounded hover:bg-gray-50 font-medium">Cancel</button>
                    @endif
                </div>
            </form>
        </div>
        <!-- End Management Panel -->
        
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
