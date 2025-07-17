<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subcontractors
        </h2>
    </x-slot>
    <div class="py-4 max-w-6xl mx-auto">
        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $editing ? 'Edit Subcontractor' : 'Add New Subcontractor' }}
                </h3>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show deleted subcontractors</span>
                    </label>
                </div>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Subcontractor Name</label>
                        <input type="text" wire:model.live="name" class="w-full rounded border-gray-300" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Comment</label>
                        <textarea wire:model.live="comment" class="w-full rounded border-gray-300" rows="3"></textarea>
                    </div>
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">Parent Subcontractors</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-32 overflow-y-auto border rounded p-2">
                        @foreach($availableParents as $parent)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="selectedParents" value="{{ $parent->id }}" class="rounded border-gray-300 text-blue-600 mr-2">
                                <span class="text-sm">{{ $parent->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                        {{ $editing ? 'Update Subcontractor' : 'Add Subcontractor' }}
                    </button>
                    @if($editing)
                        <button type="button" wire:click="cancelEdit" class="text-gray-500 underline">Cancel</button>
                    @endif
                </div>
            </form>
        </div>
        <!-- End Management Panel -->
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Subcontractors</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                        @if($showDeleted)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subcontractors as $subcontractor)
                        <tr class="{{ $subcontractor->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $subcontractor->name }}</div>
                                @if($subcontractor->trashed())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Deleted
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $subcontractor->comment ?: 'No comment' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($subcontractor->parents->count() > 0)
                                        @foreach($subcontractor->parents as $parent)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                                {{ $parent->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">No parent subcontractors</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $subcontractor->contacts->count() }} contact(s)
                                </div>
                            </td>
                            @if($showDeleted)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($subcontractor->trashed())
                                        <span class="text-red-600 text-sm">Deleted {{ $subcontractor->deleted_at->diffForHumans() }}</span>
                                    @else
                                        <span class="text-green-600 text-sm">Active</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($subcontractor->trashed())
                                    <button wire:click="restore({{ $subcontractor->id }})" 
                                            class="text-green-600 hover:text-green-900 mr-3">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="edit({{ $subcontractor->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $subcontractor->id }})" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this subcontractor?')">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No subcontractors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>