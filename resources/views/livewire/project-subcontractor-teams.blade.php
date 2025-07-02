<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project Subcontractor Teams</h2>
    </x-slot>
    
    <div class="py-4 max-w-7xl mx-auto">
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
        
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editing ? 'Edit Project Team' : 'Add New Project Team' }}</h3>
            <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-4">
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">
                        Project
                        <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="selectedProject" 
                            class="rounded border-gray-300 @error('selectedProject') border-red-500 ring-red-500 @enderror {{ $highlightedProject ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}" 
                            required>
                        <option value="">Select Project...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $highlightedProject == $project->id ? 'selected' : '' }}>
                                {{ $project->name }} ({{ $project->airline->name }})
                                {{ $highlightedProject == $project->id ? ' ✨ Just Created' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="min-h-[1.5rem] mt-1">
                        @if($highlightedProject)
                            <div class="text-xs text-blue-600">👆 This project was just created</div>
                        @endif
                        @error('selectedProject') 
                            <div class="text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">
                        Main Subcontractor
                        <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="mainSubcontractor" 
                            class="rounded border-gray-300 @error('mainSubcontractor') border-red-500 ring-red-500 @enderror" 
                            required>
                        <option value="">Select Main...</option>
                        @foreach($subcontractors as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                    <div class="min-h-[1.5rem] mt-1">
                        @error('mainSubcontractor') 
                            <div class="text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">
                        Role
                        <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="role" 
                            class="rounded border-gray-300 @error('role') border-red-500 ring-red-500 @enderror" 
                            required>
                        <option value="">Select Role...</option>
                        @foreach($availableRoles as $roleOption)
                            <option value="{{ $roleOption }}">{{ $roleOption }}</option>
                        @endforeach
                    </select>
                    <div class="min-h-[1.5rem] mt-1">
                        @error('role') 
                            <div class="text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">Opportunity</label>
                    <select wire:model.live="selectedOpportunity" 
                            class="rounded border-gray-300 @error('selectedOpportunity') border-red-500 ring-red-500 @enderror">
                        <option value="">None (General Team)</option>
                        @foreach($projectOpportunities as $opportunity)
                            <option value="{{ $opportunity['value'] }}">{{ $opportunity['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="min-h-[1.5rem] mt-1">
                        @error('selectedOpportunity') 
                            <div class="text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">
                        Supporting Subcontractors
                        <span class="text-gray-500 text-sm font-normal">(Optional)</span>
                    </label>
                    <select wire:model.live="supportingSubcontractors" multiple 
                            class="rounded border-gray-300 h-20 @error('supportingSubcontractors') border-red-500 ring-red-500 @enderror">
                        @foreach($subcontractors as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                    <div class="min-h-[1.5rem] mt-1">
                        <div class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple. Leave empty for no supporting subcontractors.</div>
                        @error('supportingSubcontractors') 
                            <div class="text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <label class="block font-semibold mb-1 h-6">Notes</label>
                    <textarea wire:model.live="notes" class="rounded border-gray-300 resize-none" rows="2" placeholder="Additional notes..."></textarea>
                    <div class="min-h-[1.5rem] mt-1"></div>
                </div>
            </div>
            
            <div class="flex justify-start">
                <button type="submit" class="bg-blue-600 text-white rounded px-6 py-2 hover:bg-blue-700">
                    {{ $editing ? 'Update Team' : 'Add Team' }}
                </button>
                @if($editing)
                    <button type="button" wire:click="cancelEdit" class="ml-3 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                @endif
            </div>
            </form>
        </div>
        <!-- End Management Panel -->
        
        <div class="bg-white rounded shadow overflow-x-auto">
            @if(!$selectedProject)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
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
            <div class="relative">
                <table class="min-w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border text-left">Project</th>
                        <th class="px-3 py-2 border text-left">Airline</th>
                        <th class="px-3 py-2 border text-left">Main Subcontractor</th>
                        <th class="px-3 py-2 border text-left">Supporting Subcontractors</th>
                        <th class="px-3 py-2 border text-left">Role</th>
                        <th class="px-3 py-2 border text-left">Opportunity</th>
                        <th class="px-3 py-2 border text-left">Notes</th>
                        <th class="px-3 py-2 border text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $team->project->name }}</td>
                            <td class="px-3 py-2 border">{{ $team->project->airline->name }}</td>
                            <td class="px-3 py-2 border">
                                <span class="font-semibold text-blue-800">{{ $team->mainSubcontractor->name }}</span>
                            </td>
                            <td class="px-3 py-2 border">
                                @if($team->supportingSubcontractors->count() > 0)
                                    @foreach($team->supportingSubcontractors as $supporter)
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                            {{ $supporter->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-xs">No supporting subcontractors</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">
                                @if($team->role)
                                    <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">
                                        {{ $team->role }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-2 border">
                                @if($team->opportunity_type && $team->opportunity_id)
                                    @php
                                        $opportunityLabel = match($team->opportunity_type) {
                                            'vertical_surfaces' => 'Vertical Surfaces',
                                            'panels' => 'Panels',
                                            'covers' => 'Covers',
                                            default => ucfirst(str_replace('_', ' ', $team->opportunity_type))
                                        };
                                    @endphp
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
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
                            <td class="px-3 py-2 border">{{ $team->notes ?: '—' }}</td>
                            <td class="px-3 py-2 border">
                                <button wire:click="edit({{ $team->id }})" class="text-blue-600 underline mr-2">Edit</button>
                                <button wire:click="delete({{ $team->id }})" class="text-red-600 underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                No project teams created yet. Add your first team above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
