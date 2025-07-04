<div>
    <x-table-container title="Project Subcontractor Teams">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Please correct the following errors:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-management-panel title="{{ $editing ? 'Edit Project Team' : 'Add New Project Team' }}">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="selectedProject" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('selectedProject') border-red-500 ring-red-500 @enderror {{ $highlightedProject ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}" 
                                required>
                            <option value="">Select Project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $highlightedProject == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ optional($project->airline)->name ?? 'No Airline' }})
                                    {{ $highlightedProject == $project->id ? ' ✨ Just Created' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedProject') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Main Subcontractor <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="mainSubcontractor" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('mainSubcontractor') border-red-500 ring-red-500 @enderror" 
                                required>
                            <option value="">Select Main Subcontractor...</option>
                            @foreach($subcontractors as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('mainSubcontractor') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select wire:model.live="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-500 ring-red-500 @enderror">
                            <option value="">Select Role...</option>
                            @foreach($availableRoles as $roleOption)
                                <option value="{{ $roleOption }}">{{ $roleOption }}</option>
                            @endforeach
                        </select>
                        @error('role') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Opportunity</label>
                        <select wire:model.live="selectedOpportunity" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('selectedOpportunity') border-red-500 ring-red-500 @enderror">
                            <option value="">None (General Team)</option>
                            @foreach($projectOpportunities as $opportunity)
                                <option value="{{ $opportunity['value'] }}">{{ $opportunity['label'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedOpportunity') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Supporting Subcontractors <span class="text-gray-500 text-sm font-normal">(Optional)</span>
                        </label>
                        <select wire:model.live="supportingSubcontractors" multiple 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-20 @error('supportingSubcontractors') border-red-500 ring-red-500 @enderror">
                            @foreach($subcontractors as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</div>
                        @error('supportingSubcontractors') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea wire:model.live="notes" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 ring-red-500 @enderror" 
                                  rows="3" 
                                  placeholder="Optional notes about this team..."></textarea>
                        @error('notes') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="flex flex-col justify-end">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                                {{ $editing ? 'Update Team' : 'Add Team' }}
                            </button>
                            @if($editing)
                                <button type="button" wire:click="cancelEdit" class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </x-management-panel>
        
        <!-- Filter Panel -->
        <x-management-panel title="Filter Options">
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Show deleted project teams</span>
                </label>
            </div>
        </x-management-panel>
        
        <!-- Project Teams Table -->
        <x-table-box>
            @if(!$selectedProject)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Showing all project teams. Select a specific project above to filter the results.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Airline</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Main Subcontractor</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Supporting Subcontractors</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Role</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Opportunity</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Notes</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teams as $team)
                        <tr class="{{ $team->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ optional($team->project)->name ?? 'No Project' }}</div>
                                    @if($team->trashed())
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                            Deleted {{ $team->deleted_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    <!-- Mobile-only info -->
                                    <div class="mt-1 md:hidden">
                                        @if($team->supportingSubcontractors->count() > 0)
                                            <div class="text-xs text-gray-500">
                                                Support: {{ $team->supportingSubcontractors->pluck('name')->join(', ') }}
                                            </div>
                                        @endif
                                        @if($team->role)
                                            <div class="text-xs text-gray-500">Role: {{ $team->role }}</div>
                                        @endif
                                        @if($team->opportunity_type && $team->opportunity_id)
                                            <div class="text-xs text-gray-500">
                                                Opportunity: {{ match($team->opportunity_type) {
                                                    'vertical_surfaces' => 'Vertical Surfaces',
                                                    'panels' => 'Panels', 
                                                    'covers' => 'Covers',
                                                    default => ucfirst(str_replace('_', ' ', $team->opportunity_type))
                                                } }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $team->project ? optional($team->project->airline)->name ?? '—' : '—' }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-blue-800">{{ optional($team->mainSubcontractor)->name ?? 'No Main Subcontractor' }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                @if($team->supportingSubcontractors->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($team->supportingSubcontractors as $supporter)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $supporter->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">No supporting subcontractors</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                @if($team->role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $team->role }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                @if($team->opportunity_type && $team->opportunity_id)
                                    @php
                                        $opportunityLabel = match($team->opportunity_type) {
                                            'vertical_surfaces' => 'Vertical Surfaces',
                                            'panels' => 'Panels',
                                            'covers' => 'Covers',
                                            default => ucfirst(str_replace('_', ' ', $team->opportunity_type))
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $opportunityLabel }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($team->opportunity)
                                            {{ $team->opportunity->cabin_class ? ucfirst($team->opportunity->cabin_class) . ' Cabin' : 'ID: ' . $team->opportunity_id }}
                                        @else
                                            ID: {{ $team->opportunity_id }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">General Team</span>
                                @endif
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden xl:table-cell">
                                <div class="text-sm text-gray-900">{{ $team->notes ?: '—' }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                <button wire:click="edit({{ $team->id }})" 
                                        class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                    Edit
                                </button>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                <button wire:click="delete({{ $team->id }})" 
                                        class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                No project teams created yet. Add your first team above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-box>
    </x-table-container>
</div>
