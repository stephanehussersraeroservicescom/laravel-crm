<div class="space-y-6">
    <!-- Header -->
    <div class="w-full mx-auto md:max-w-[90%] pt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Subcontractor Management</h1>
            <button wire:click="openCreateModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Team
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search and Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Search Opportunity Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Opportunity Type</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="searchOpportunityType" 
                       placeholder="panels, vertical, etc."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Search Cabin Area -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Cabin Area</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="searchCabinArea" 
                       placeholder="first class, economy, etc."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select wire:model.live="filterRole" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($teamRoles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                <select wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Airline Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Airline</label>
                <select wire:model.live="filterAirline" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Airlines</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select wire:model.live="filterProject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->airline?->name ?? 'No Airline' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Main Subcontractor Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Main Subcontractor</label>
                <select wire:model.live="filterMainSubcontractor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Subcontractors</option>
                    @foreach($subcontractors as $subcontractor)
                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-6">
            <!-- Opportunity Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opportunity Type</label>
                <select wire:model.live="filterOpportunityType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($opportunityTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cabin Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class</label>
                <select wire:model.live="filterCabinClass" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach($cabinClasses as $class)
                        <option value="{{ $class->value }}">{{ $class->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted teams</span>
            </label>
            
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Teams Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project / Airline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Opportunity
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subcontractors
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('role')">
                            Role
                            @if($sortBy === 'role')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notes
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teams as $team)
                        <tr class="hover:bg-gray-300 {{ $team->trashed() ? 'bg-red-50 opacity-75' : '' }}">
                            <!-- Project / Airline (Column 1) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $team->opportunity?->project?->name ?? 'Unknown Project' }}</div>
                                <div class="text-sm text-gray-500">{{ $team->opportunity?->project?->airline?->name ?? 'Unknown Airline' }}</div>
                            </td>
                            
                            <!-- Opportunity (Column 2) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $team->opportunity?->name ?: 'Untitled Opportunity' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $team->opportunity?->type?->label() ?? 'Unknown Type' }} - 
                                    @if($team->opportunity?->cabin_class)
                                        {{ $team->opportunity->cabin_class->label() }}
                                    @else
                                        All Classes
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Subcontractors (Regrouped - Column 3) -->
                            <td class="px-6 py-4">
                                <!-- Main Subcontractor (Bold) -->
                                <div class="font-bold text-gray-900 mb-1">{{ $team->mainSubcontractor?->name ?? 'Unknown Subcontractor' }}</div>
                                
                                <!-- Supporting Subcontractors (Standard) -->
                                @if($team->supportingSubcontractors->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($team->supportingSubcontractors->take(3) as $supporting)
                                            <div class="text-sm text-gray-600">{{ $supporting->name }}</div>
                                        @endforeach
                                        @if($team->supportingSubcontractors->count() > 3)
                                            <div class="text-xs text-gray-400">
                                                +{{ $team->supportingSubcontractors->count() - 3 }} more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Role (Column 4) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $team->role?->label() ?? 'Unknown Role' }}
                                </span>
                            </td>
                            
                            <!-- Notes (Column 5) -->
                            <td class="px-6 py-4">
                                @if($team->notes)
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $team->notes }}">
                                        {{ Str::limit($team->notes, 50) }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No notes</span>
                                @endif
                            </td>
                            
                            <!-- Actions (Column 6) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="openEditModal({{ $team->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Edit
                                </button>
                                @if($team->trashed())
                                    <button wire:click="delete({{ $team->id }})" 
                                            onclick="return confirm('Are you sure you want to restore this team?')"
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="delete({{ $team->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this team?')"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No teams found. 
                                <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800">
                                    Create your first team
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $teams->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $modalMode === 'create' ? 'Create New Team' : 'Edit Team' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <form wire:submit.prevent="save" class="mt-4 space-y-4">
                        <!-- Selection Summary -->
                        @if($selected_airline_id)
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="text-sm font-medium text-blue-900">
                                    @php
                                        $airline = $airlines->find($selected_airline_id);
                                        $summary = $totalProjectsForAirline . ' active projects for ' . $airline->name;
                                        
                                        if ($selected_opportunity_type) {
                                            $selectedType = collect($opportunityTypes)->firstWhere('value', $selected_opportunity_type);
                                            $opportunityLabel = $selectedType ? $selectedType->label() : 'opportunities';
                                            $summary .= ' and ' . $totalOpportunitiesForSelection . ' related ' . strtolower($opportunityLabel) . ' opportunities';
                                        } else {
                                            $summary .= ' and ' . $totalOpportunitiesForSelection . ' related opportunities';
                                        }
                                    @endphp
                                    📊 {{ $summary }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($modalMode === 'edit' && $selectedTeam)
                                <!-- Show read-only fields in edit mode -->
                                <div class="md:col-span-2">
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Team Assignment Details</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Airline</label>
                                                <div class="text-sm text-gray-900">{{ $selectedTeam->opportunity?->project?->airline?->name ?? 'Not Set' }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Project</label>
                                                <div class="text-sm text-gray-900">{{ $selectedTeam->opportunity?->project?->name ?? 'Not Set' }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Opportunity Type</label>
                                                <div class="text-sm text-gray-900">{{ $selectedTeam->opportunity?->type?->label() ?? 'Not Set' }}</div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600">Cabin Class</label>
                                                <div class="text-sm text-gray-900">{{ $selectedTeam->opportunity?->cabin_class?->label() ?? 'All Classes' }}</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <label class="block text-xs font-medium text-gray-600">Opportunity</label>
                                            <div class="text-sm text-gray-900">{{ $selectedTeam->opportunity?->name ?? 'Not Set' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Airline Selection (Create mode only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Airline *</label>
                                    <select wire:model.live="selected_airline_id" required 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Airline</option>
                                        @foreach($airlines as $airline)
                                            <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selected_airline_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                
                                <!-- Project Creation Dialog (only when airline has NO projects at all) -->
                                @if($selected_airline_id && $totalProjectsForAirline === 0)
                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <div class="text-sm text-yellow-800 mb-3">
                                            ⚠ No projects found for {{ $airlines->find($selected_airline_id)->name }}.
                                        </div>
                                        <div class="text-sm text-yellow-700 mb-3">
                                            Would you like to create the first project for this airline?
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="button" 
                                                    wire:click="createProject"
                                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                Yes, Create Project
                                            </button>
                                            <button type="button" 
                                                    wire:click="cancelNoProject"
                                                    class="px-3 py-1 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                                No, Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Opportunity Type Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Opportunity Type *</label>
                                <select wire:model.live="selected_opportunity_type" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        {{ !$selected_airline_id ? 'disabled' : '' }}>
                                    <option value="">{{ $selected_airline_id ? 'Select Type' : 'Select Airline First' }}</option>
                                    @foreach($opportunityTypes as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('selected_opportunity_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                
                                <!-- Combined Project/Opportunity Creation Dialog -->
                                @if($selected_airline_id && $selected_opportunity_type && !$selected_cabin_class && $totalProjectsForAirline > 0 && $totalOpportunitiesForSelection === 0)
                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <div class="text-sm text-yellow-800 mb-3">
                                            @php
                                                $selectedType = collect($opportunityTypes)->firstWhere('value', $selected_opportunity_type);
                                                $airlineName = $airlines->find($selected_airline_id)->name;
                                            @endphp
                                            ⚠ No {{ $selectedType ? $selectedType->label() : 'opportunities' }} found for {{ $airlineName }}.
                                        </div>
                                        <div class="text-sm text-yellow-700 mb-3">
                                            {{ $airlineName }} has {{ $totalProjectsForAirline }} existing project(s). Would you like to:
                                        </div>
                                        <div class="space-y-2">
                                            <button type="button" 
                                                    wire:click="createOpportunityInExistingProject"
                                                    class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 text-left">
                                                ✓ Add {{ $selectedType ? $selectedType->label() : 'opportunity' }} to existing project
                                            </button>
                                            <button type="button" 
                                                    wire:click="createProject"
                                                    class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 text-left">
                                                + Create new project for {{ $selectedType ? $selectedType->label() : 'this type' }}
                                            </button>
                                            <button type="button" 
                                                    wire:click="cancelNoOpportunity"
                                                    class="w-full px-3 py-2 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 text-left">
                                                ✕ Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Cabin Class Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class *</label>
                                <select wire:model.live="selected_cabin_class" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        {{ !$selected_opportunity_type ? 'disabled' : '' }}>
                                    <option value="">{{ $selected_opportunity_type ? 'Select Cabin Class' : 'Select Type First' }}</option>
                                    @foreach($cabinClasses as $class)
                                        <option value="{{ $class->value }}">{{ $class->label() }}</option>
                                    @endforeach
                                </select>
                                @error('selected_cabin_class') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                
                                <!-- Combined Project/Opportunity Creation Dialog (after cabin class) -->
                                @if($selected_airline_id && $selected_opportunity_type && $selected_cabin_class && $totalProjectsForAirline > 0 && $opportunityCount === 0)
                                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <div class="text-sm text-yellow-800 mb-3">
                                            @php
                                                $selectedType = collect($opportunityTypes)->firstWhere('value', $selected_opportunity_type);
                                                $selectedClass = collect($cabinClasses)->firstWhere('value', $selected_cabin_class);
                                                $airlineName = $airlines->find($selected_airline_id)->name;
                                            @endphp
                                            ⚠ No {{ $selectedType ? $selectedType->label() : 'opportunities' }} found for {{ $selectedClass ? $selectedClass->label() : 'cabin class' }} on {{ $airlineName }}.
                                        </div>
                                        <div class="text-sm text-yellow-700 mb-3">
                                            {{ $airlineName }} has {{ $totalProjectsForAirline }} existing project(s). Would you like to:
                                        </div>
                                        <div class="space-y-2">
                                            <button type="button" 
                                                    wire:click="createOpportunityInExistingProject"
                                                    class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 text-left">
                                                ✓ Add {{ $selectedType ? $selectedType->label() : 'opportunity' }} {{ $selectedClass ? $selectedClass->label() : 'cabin class' }} to existing project
                                            </button>
                                            <button type="button" 
                                                    wire:click="createProject"
                                                    class="w-full px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 text-left">
                                                + Create new project for {{ $selectedType ? $selectedType->label() : 'this type' }} {{ $selectedClass ? $selectedClass->label() : 'cabin class' }}
                                            </button>
                                            <button type="button" 
                                                    wire:click="cancelNoOpportunity"
                                                    class="w-full px-3 py-2 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 text-left">
                                                ✕ Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Project Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Project *
                                    @if($selected_cabin_class && $projectCount > 0)
                                        <span class="ml-2 text-xs text-purple-600">({{ $filteredProjects->count() }} matching projects)</span>
                                    @endif
                                </label>
                                <select wire:model.live="selected_project_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        {{ !$selected_cabin_class ? 'disabled' : '' }}>
                                    <option value="">{{ $selected_cabin_class ? 'Select Project' : 'Select Cabin Class First' }}</option>
                                    @foreach($filteredProjects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('selected_project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Opportunity Selection Result -->
                            @if($selected_airline_id && $selected_project_id && $selected_opportunity_type && $selected_cabin_class)
                                <div class="md:col-span-2">
                                    @if($filteredOpportunities->count() > 0)
                                        <div class="bg-green-50 border border-green-200 rounded-md p-3">
                                            <div class="text-sm font-medium text-green-800">
                                                @if($filteredOpportunities->count() === 1)
                                                    ✓ Opportunity found: {{ $filteredOpportunities->first()->name ?: 'Auto-generated' }}
                                                @else
                                                    ✓ {{ $filteredOpportunities->count() }} opportunities found matching your criteria
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                            <div class="text-sm font-medium text-yellow-800 mb-3">
                                                ⚠ No opportunity exists for this combination
                                            </div>
                                            <div class="text-sm text-yellow-700 mb-3">
                                                Would you like to create a new opportunity for this project?
                                            </div>
                                            <div class="flex space-x-2">
                                                <button type="button" 
                                                        wire:click="createOpportunity"
                                                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                    Yes, Create Opportunity
                                                </button>
                                                <button type="button" 
                                                        wire:click="cancelNoOpportunity"
                                                        class="px-3 py-1 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                                    No, Cancel
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @endif

                            <!-- Main Subcontractor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Main Subcontractor *</label>
                                <div class="space-y-2">
                                    <select wire:model="main_subcontractor_id" required 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            {{ $showNewSubcontractorForm ? 'disabled' : '' }}>
                                        <option value="">Select Main Subcontractor</option>
                                        @foreach($subcontractors as $subcontractor)
                                            <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('main_subcontractor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    
                                    <!-- Toggle to add new subcontractor -->
                                    <button type="button" 
                                            wire:click="toggleNewSubcontractorForm"
                                            class="text-sm text-blue-600 hover:text-blue-800">
                                        {{ $showNewSubcontractorForm ? '← Back to list' : '+ Add New Subcontractor' }}
                                    </button>
                                    
                                    <!-- New Subcontractor Form -->
                                    @if($showNewSubcontractorForm)
                                        <div class="border border-blue-300 rounded-md p-3 bg-blue-50">
                                            <div class="text-sm font-medium text-gray-700 mb-2">Create New Subcontractor</div>
                                            
                                            <div class="space-y-2">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Name *</label>
                                                    <input type="text" 
                                                           wire:model.live.debounce.300ms="newSubcontractorName" 
                                                           placeholder="Enter subcontractor name"
                                                           class="w-full px-2 py-1 text-sm border {{ $errors->has('newSubcontractorName') ? 'border-red-300' : 'border-gray-300' }} rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    @error('newSubcontractorName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Comment</label>
                                                    <textarea wire:model="newSubcontractorComment" 
                                                              rows="2"
                                                              placeholder="Optional comment about this subcontractor"
                                                              class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                                    @error('newSubcontractorComment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                
                                                <button type="button" 
                                                        wire:click="createNewSubcontractor"
                                                        class="w-full px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                    Create Subcontractor
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select wire:model="role" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Role</option>
                                    @foreach($teamRoles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Supporting Subcontractors -->
                            <div class="md:col-span-2">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Supporting Subcontractors</label>
                                    <button type="button" wire:click="addSupportingSubcontractor" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        + Add Supporting Subcontractor
                                    </button>
                                </div>
                                
                                @if(count($supportingSubcontractors) > 0)
                                    <div class="space-y-2">
                                        @foreach($supportingSubcontractors as $index => $supportingId)
                                            <div class="flex items-center space-x-2">
                                                <select wire:model="supportingSubcontractors.{{ $index }}" 
                                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="">Select Subcontractor</option>
                                                    @foreach($subcontractors as $subcontractor)
                                                        @if($subcontractor->id != $main_subcontractor_id && !in_array($subcontractor->id, array_filter($supportingSubcontractors)))
                                                            <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                                        @elseif($subcontractor->id == $supportingId)
                                                            <option value="{{ $subcontractor->id }}" selected>{{ $subcontractor->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="button" wire:click="removeSupportingSubcontractor({{ $index }})" 
                                                        class="text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 py-2">
                                        No supporting subcontractors added yet.
                                    </div>
                                @endif
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea wire:model="notes" rows="3" 
                                          placeholder="Add any relevant notes about this team..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>