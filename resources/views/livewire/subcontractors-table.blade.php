<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subcontractors</h2>
    </x-slot>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ $editing ? 'Edit Subcontractor' : 'Add New Subcontractor' }}</h3>
            </div>
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Subcontractor Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="name" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               required>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Comment</label>
                        <textarea wire:model.live="comment" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" 
                                  rows="2" 
                                  placeholder="Optional comment..."></textarea>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Parent Companies</label>
                        <select wire:model.live="selectedParents" multiple 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-16">
                            <option value="">Select parent companies...</option>
                            @foreach($availableParents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 pt-2">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                        {{ $editing ? 'Update Subcontractor' : 'Add Subcontractor' }}
                    </button>
                    @if($editing)
                        <button type="button" wire:click="cancelEdit" 
                                class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>
        <!-- End Management Panel -->
        
        <!-- Filter Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Filter Options</h3>
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Show deleted subcontractors</span>
                </label>
            </div>
        </div>
        
        <!-- Subcontractors Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Comment</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Parent Companies</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subcontractors as $sub)
                            <tr class="{{ $sub->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">{{ $sub->name }}</div>
                                        @if($sub->trashed())
                                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                                Deleted {{ $sub->deleted_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        <!-- Mobile-only info -->
                                        <div class="mt-1 md:hidden">
                                            @if($sub->comment)
                                                <div class="text-xs text-gray-500">{{ $sub->comment }}</div>
                                            @endif
                                            @if($sub->parents->count() > 0)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Parents: {{ $sub->parents->pluck('name')->join(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="text-sm text-gray-900">{{ $sub->comment ?? '' }}</div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                    @if($sub->parents->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($sub->parents as $parent)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $parent->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm"></span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-blue-600 font-medium">
                                        {{ $sub->contacts->count() }} contact{{ $sub->contacts->count() === 1 ? '' : 's' }}
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                    @if($sub->trashed())
                                        <button wire:click="restore({{ $sub->id }})" 
                                                class="text-green-600 hover:text-green-900 font-medium transition-colors duration-200">
                                            Restore
                                        </button>
                                    @else
                                        <button wire:click="edit({{ $sub->id }})" 
                                                class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200 mr-3">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $sub->id }})" 
                                                class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to delete this subcontractor?')">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No subcontractors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>