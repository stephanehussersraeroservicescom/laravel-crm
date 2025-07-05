<div>
    <x-table-container title="Project Opportunities">
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

        <!-- Search & Filter Management Panel -->
        <x-management-panel title="Search & Filter Opportunities">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-form-field label="Search" name="search" placeholder="Search opportunities, teams, projects..." />
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Project</label>
                    <select wire:model.live="selectedProject" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Opportunity Type</label>
                    <select wire:model.live="opportunityTypeFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="vertical_surface">Vertical Surface</option>
                        <option value="panel">Panel</option>
                        <option value="cover">Cover</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="space-y-1 flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Show deleted teams</span>
                    </label>
                </div>
            </div>
        </x-management-panel>

        <!-- Add/Edit Management Panel -->
        <x-management-panel title="{{ $editing ? 'Edit Team Assignment' : 'Assign Team to Opportunity' }}">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="formSelectedProject" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('formSelectedProject') border-red-500 ring-red-500 @enderror" 
                                required>
                            <option value="">Select Project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('formSelectedProject') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Opportunity <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="selectedOpportunity" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('selectedOpportunity') border-red-500 ring-red-500 @enderror" 
                                required>
                            <option value="">Select Opportunity...</option>
                            @foreach($formOpportunities as $opportunity)
                                <option value="{{ $opportunity['value'] }}">{{ $opportunity['label'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedOpportunity') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Team Lead <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="mainSubcontractor" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('mainSubcontractor') border-red-500 ring-red-500 @enderror" 
                                required>
                            <option value="">Select Team Lead...</option>
                            @foreach($subcontractors as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('mainSubcontractor') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="role" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-500 ring-red-500 @enderror" 
                                required>
                            <option value="">Select Role...</option>
                            @foreach($availableRoles as $roleOption)
                                <option value="{{ $roleOption }}">{{ $roleOption }}</option>
                            @endforeach
                        </select>
                        @error('role') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Supporting Team Members <span class="text-gray-500 text-sm font-normal">(Optional)</span>
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
                        <label class="block text-sm font-medium text-gray-700">Team Notes</label>
                        <textarea wire:model.live="notes" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 ring-red-500 @enderror" 
                                  rows="3" 
                                  placeholder="Notes about this team assignment..."></textarea>
                        @error('notes') 
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                        {{ $editing ? 'Update Team' : 'Assign Team' }}
                    </button>
                    @if($editing)
                        <button type="button" wire:click="cancelEdit" class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </x-management-panel>
        
        <!-- Opportunities Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Opportunities & Team Assignments</h3>
                <p class="text-sm text-gray-600">Manage team assignments for project opportunities</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project & Opportunity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type & Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Team Lead
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supporting Team
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($opportunitiesWithTeams as $item)
                            @php
                                $opportunity = $item['opportunity'];
                                $team = $item['team'];
                                $project = $item['project'];
                            @endphp
                            <tr class="{{ $team && $team->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                <!-- Project & Opportunity -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $project->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ optional($project->airline)->name ?? 'No Airline' }}
                                    </div>
                                    @if($opportunity->type === 'other' && $opportunity->name)
                                        <div class="text-xs text-blue-600 font-medium mt-1">
                                            {{ $opportunity->name }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Type & Details -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeConfig = match($opportunity->type) {
                                            'vertical_surface' => ['label' => 'Vertical Surface', 'class' => 'bg-blue-100 text-blue-800'],
                                            'panel' => ['label' => 'Panel', 'class' => 'bg-green-100 text-green-800'],
                                            'cover' => ['label' => 'Cover', 'class' => 'bg-purple-100 text-purple-800'],
                                            'other' => ['label' => 'Other', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            default => ['label' => ucfirst($opportunity->type), 'class' => 'bg-gray-100 text-gray-800']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeConfig['class'] }}">
                                        {{ $typeConfig['label'] }}
                                    </span>
                                    @if($opportunity->cabin_class)
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ ucfirst($opportunity->cabin_class) }} Cabin
                                        </div>
                                    @endif
                                    @if($opportunity->probability)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ round($opportunity->probability * 100) }}% probability
                                        </div>
                                    @endif
                                </td>

                                <!-- Team Lead -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($team)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ optional($team->mainSubcontractor)->name ?? 'No Lead' }}
                                        </div>
                                        @if($team->role)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-1">
                                                {{ $team->role }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500 italic">No team assigned</span>
                                    @endif
                                </td>

                                <!-- Supporting Team -->
                                <td class="px-6 py-4">
                                    @if($team && $team->supportingSubcontractors->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($team->supportingSubcontractors->take(3) as $supporter)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $supporter->name }}
                                                </span>
                                            @endforeach
                                            @if($team->supportingSubcontractors->count() > 3)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ $team->supportingSubcontractors->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">None</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($team)
                                        @if($team->trashed())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Deleted
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active Team
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            No Team
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if($team)
                                            <button wire:click="edit({{ $team->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 text-xs">
                                                Edit
                                            </button>
                                            @if($team->trashed())
                                                <button wire:click="restore({{ $team->id }})" 
                                                        class="text-green-600 hover:text-green-900 text-xs">
                                                    Restore
                                                </button>
                                            @else
                                                <button wire:click="delete({{ $team->id }})" 
                                                        class="text-red-600 hover:text-red-900 text-xs"
                                                        onclick="return confirm('Remove team assignment?')">
                                                    Remove
                                                </button>
                                            @endif
                                        @else
                                            <button wire:click="assignTeam({{ $opportunity->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 text-xs">
                                                Assign Team
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No opportunities found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No opportunities match your current filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-table-container>
</div>