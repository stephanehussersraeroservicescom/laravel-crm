<x-table-container title="Subcontractors">
    <x-management-panel :editing="$editing" entity-name="Subcontractor">
        <form wire:submit.prevent="save" class="space-y-6">
            <x-form-grid :cols="3">
                <x-form-field label="Subcontractor Name" required>
                    <input type="text" wire:model.live="name" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           required>
                </x-form-field>
                
                <x-form-field label="Comment">
                    <textarea wire:model.live="comment" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" 
                              rows="2" 
                              placeholder="Optional comment..."></textarea>
                </x-form-field>
                
                <x-form-field label="Parent Companies" help="Hold Ctrl/Cmd to select multiple">
                    <select wire:model.live="selectedParents" multiple 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-16">
                        <option value="">Select parent companies...</option>
                        @foreach($availableParents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </x-form-grid>
            
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
    </x-management-panel>
    
    <x-table-controls search-placeholder="Search subcontractors..." :show-search="false">
        <!-- Additional filter options can go here -->
    </x-table-controls>
    
    <x-table-box>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Comment</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Parent Companies</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($subcontractors as $sub)
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
                            <div class="text-sm text-gray-900">{{ $sub->comment ?? '—' }}</div>
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
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('contacts.index', $sub) }}" class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                {{ $sub->contacts->count() }} contact{{ $sub->contacts->count() === 1 ? '' : 's' }}
                            </a>
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                            <button wire:click="edit({{ $sub->id }})" 
                                    class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                Edit
                            </button>
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                            <button wire:click="delete({{ $sub->id }})" 
                                    class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-box>
</x-table-container>
