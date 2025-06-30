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
        
        <form wire:submit.prevent="save" class="mb-6 flex gap-4 items-end flex-wrap">
            <div>
                <label class="block font-semibold mb-1">
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
                @if($highlightedProject)
                    <div class="text-xs text-blue-600 mt-1">👆 This project was just created</div>
                @endif
                @error('selectedProject') 
                    <div class="mt-1 text-red-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <div>
                <label class="block font-semibold mb-1">
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
                @error('mainSubcontractor') 
                    <div class="mt-1 text-red-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <div>
                <label class="block font-semibold mb-1">
                    Supporting Subcontractors
                    <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="supportingSubcontractors" multiple 
                        class="rounded border-gray-300 h-24 @error('supportingSubcontractors') border-red-500 ring-red-500 @enderror" 
                        required>
                    @foreach($subcontractors as $sub)
                        @if($sub->id != $mainSubcontractor)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endif
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</div>
                @error('supportingSubcontractors') 
                    <div class="mt-1 text-red-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <div>
                <label class="block font-semibold mb-1">
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
                @error('role') 
                    <div class="mt-1 text-red-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <div>
                <label class="block font-semibold mb-1">Opportunity Type</label>
                <select wire:model.live="opportunityType" 
                        class="rounded border-gray-300 @error('opportunityType') border-red-500 ring-red-500 @enderror">
                    <option value="">None (General Team)</option>
                    @foreach($opportunityTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('opportunityType') 
                    <div class="mt-1 text-red-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            @if($opportunityType && $selectedProject)
                <div>
                    <label class="block font-semibold mb-1">Specific Opportunity</label>
                    <select wire:model.live="opportunityId" 
                            class="rounded border-gray-300 @error('opportunityId') border-red-500 ring-red-500 @enderror">
                        <option value="">Select Opportunity...</option>
                        @if(isset($projectOpportunities[$opportunityType]))
                            @foreach($projectOpportunities[$opportunityType] as $opportunity)
                                <option value="{{ $opportunity['id'] }}">{{ $opportunity['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('opportunityId') 
                        <div class="mt-1 text-red-600 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif
            
            <div>
                <label class="block font-semibold mb-1">Notes</label>
                <textarea wire:model.live="notes" class="rounded border-gray-300" rows="1" placeholder="Additional notes..."></textarea>
            </div>
            
            <div>
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                    {{ $editing ? 'Update' : 'Add Team' }}
                </button>
                @if($editing)
                    <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 underline">Cancel</button>
                @endif
            </div>
        </form>
        
        <div class="bg-white rounded shadow overflow-x-auto {{ !$selectedProject ? 'opacity-50 pointer-events-none' : '' }}">
            @if(!$selectedProject)
                <div class="absolute inset-0 bg-gray-100 bg-opacity-75 flex items-center justify-center z-10 rounded">
                    <div class="text-center">
                        <div class="text-gray-500 text-lg font-medium mb-2">Select a Project First</div>
                        <div class="text-gray-400 text-sm">Choose a project above to view and manage its subcontractor teams</div>
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
                                    —
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
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                        {{ $opportunityTypes[$team->opportunity_type] ?? $team->opportunity_type }}
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
