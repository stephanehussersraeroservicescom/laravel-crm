<div>
    <x-table-container title="Airlines">
        <!-- Management Panel -->
        <x-management-panel :editing="$editing" entity-name="Airline">
            <form wire:submit.prevent="save">
                <x-form-grid :cols="4">
                    <x-form-field label="Airline Name" required>
                        <input type="text" 
                               wire:model.live="name" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               required>
                    </x-form-field>

                    <x-form-field label="Region" required>
                        <select wire:model.live="region" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                required>
                            <option value="">Select Region...</option>
                            @foreach($availableRegions as $regionOption)
                                <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Account Executive">
                        <select wire:model.live="account_executive" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Account Executive...</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <div class="flex flex-col justify-end space-y-2">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                            {{ $editing ? 'Update' : 'Add Airline' }}
                        </button>
                        @if($editing)
                            <button type="button" 
                                    wire:click="cancelEdit" 
                                    class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">
                                Cancel
                            </button>
                        @endif
                    </div>
                </x-form-grid>
            </form>
        </x-management-panel>
        
        <!-- Table Controls -->
        <x-table-controls search-placeholder="Search airlines...">
            <!-- Any additional controls can go here -->
        </x-table-controls>
        
        <!-- Airlines Table -->
        <x-table-box>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Account Executive</th>
                        @if($showDeleted)
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                        @endif
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($airlines as $airline)
                        <tr class="{{ $airline->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ $airline->name }}</div>
                                    @if($airline->trashed())
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                            Deleted
                                        </span>
                                    @endif
                                    <!-- Mobile-only info -->
                                    <div class="mt-1 md:hidden">
                                        @if($airline->account_executive)
                                            <div class="text-xs text-gray-500">AE: {{ $airline->account_executive }}</div>
                                        @endif
                                        @if($showDeleted)
                                            <div class="text-xs text-gray-500">
                                                @if($airline->trashed())
                                                    Status: Deleted {{ $airline->deleted_at->diffForHumans() }}
                                                @else
                                                    Status: Active
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $airline->region }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="text-sm text-gray-500">{{ $airline->account_executive ?? 'Not assigned' }}</span>
                            </td>
                            @if($showDeleted)
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                    @if($airline->trashed())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Deleted {{ $airline->deleted_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                @if(!$airline->trashed())
                                    <button wire:click="editAirline({{ $airline->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium transition-colors duration-200">
                                        Edit
                                    </button>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                @if($airline->trashed())
                                    <button wire:click="restoreAirline({{ $airline->id }})" 
                                            class="text-green-600 hover:text-green-900 text-sm font-medium transition-colors duration-200">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="deleteAirline({{ $airline->id }})" 
                                            wire:confirm="Are you sure you want to delete this airline?"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium transition-colors duration-200">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($airlines->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        @if($search)
                            No airlines found matching "{{ $search }}".
                        @elseif($showDeleted)
                            No airlines found (including deleted).
                        @else
                            No airlines found. Add one above to get started.
                        @endif
                    </div>
                </div>
            @endif
        </x-table-box>
        
        <!-- Pagination -->
        @if($airlines->hasPages())
            <div class="mt-6">
                {{ $airlines->links() }}
            </div>
        @endif
    </x-table-container>
</div>
