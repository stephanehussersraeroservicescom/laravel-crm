<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Subcontractor Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Team
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />

    <!-- Search and Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Search Opportunity Type -->
            <x-atomic.molecules.forms.form-field-group label="Search Opportunity Type">
                <x-atomic.atoms.forms.form-input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchOpportunityType" 
                    placeholder="panels, vertical, etc."
                />
            </x-atomic.molecules.forms.form-field-group>
            
            <!-- Search Cabin Area -->
            <x-atomic.molecules.forms.form-field-group label="Search Cabin Area">
                <x-atomic.atoms.forms.form-input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchCabinArea" 
                    placeholder="first class, economy, etc."
                />
            </x-atomic.molecules.forms.form-field-group>

            <!-- Role Filter -->
            <x-atomic.molecules.forms.form-field-group label="Role">
                <x-atomic.atoms.forms.form-select wire:model.live="filterRole">
                    <option value="">All Roles</option>
                    @foreach($teamRoles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
            
            <!-- Per Page -->
            <x-atomic.molecules.forms.form-field-group label="Show">
                <x-atomic.atoms.forms.form-select wire:model.live="perPage">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Airline Filter -->
            <x-atomic.molecules.forms.form-field-group label="Airline">
                <x-atomic.atoms.forms.form-select wire:model.live="filterAirline">
                    <option value="">All Airlines</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Project Filter -->
            <x-atomic.molecules.forms.form-field-group label="Project">
                <x-atomic.atoms.forms.form-select wire:model.live="filterProject">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->airline?->name ?? 'No Airline' }})</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Main Subcontractor Filter -->
            <x-atomic.molecules.forms.form-field-group label="Main Subcontractor">
                <x-atomic.atoms.forms.form-select wire:model.live="filterMainSubcontractor">
                    <option value="">All Subcontractors</option>
                    @foreach($subcontractors as $subcontractor)
                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-6">
            <!-- Opportunity Type Filter -->
            <x-atomic.molecules.forms.form-field-group label="Opportunity Type">
                <x-atomic.atoms.forms.form-select wire:model.live="filterOpportunityType">
                    <option value="">All Types</option>
                    @foreach($opportunityTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Cabin Class Filter -->
            <x-atomic.molecules.forms.form-field-group label="Cabin Class">
                <x-atomic.atoms.forms.form-select wire:model.live="filterCabinClass">
                    <option value="">All Classes</option>
                    @foreach($cabinClasses as $class)
                        <option value="{{ $class->value }}">{{ $class->label() }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <x-atomic.atoms.forms.form-checkbox 
                wire:model.live="showDeleted"
                label="Show deleted teams"
            />
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
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
                                <x-atomic.molecules.data.project-airline-cell 
                                    :projectName="$team->opportunity?->project?->name ?? 'Unknown Project'"
                                    :airlineName="$team->opportunity?->project?->airline?->name ?? 'Unknown Airline'"
                                />
                            </td>
                            
                            <!-- Opportunity (Column 2) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.molecules.data.team-opportunity-cell 
                                    :opportunityName="$team->opportunity?->name"
                                    :opportunityType="$team->opportunity?->type?->label()"
                                    :cabinClass="$team->opportunity?->cabin_class?->label()"
                                />
                            </td>
                            
                            <!-- Subcontractors (Regrouped - Column 3) -->
                            <td class="px-6 py-4">
                                <x-atomic.molecules.data.subcontractor-list 
                                    :mainSubcontractor="$team->mainSubcontractor?->name"
                                    :supportingSubcontractors="$team->supportingSubcontractors"
                                />
                            </td>
                            
                            <!-- Role (Column 4) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.atoms.feedback.status-badge variant="primary">
                                    {{ $team->role?->label() ?? 'Unknown Role' }}
                                </x-atomic.atoms.feedback.status-badge>
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
                                <x-atomic.atoms.buttons.action-link 
                                    variant="primary" 
                                    wire:click="openEditModal({{ $team->id }})"
                                >
                                    Edit
                                </x-atomic.atoms.buttons.action-link>
                                @if($team->trashed())
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="success" 
                                        wire:click="delete({{ $team->id }})" 
                                        onclick="return confirm('Are you sure you want to restore this team?')"
                                    >
                                        Restore
                                    </x-atomic.atoms.buttons.action-link>
                                @else
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="danger" 
                                        wire:click="delete({{ $team->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this team?')"
                                    >
                                        Delete
                                    </x-atomic.atoms.buttons.action-link>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No teams found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openCreateModal">
                                    Create your first team
                                </x-atomic.atoms.buttons.action-link>
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
                            <x-atomic.molecules.feedback.alert-box type="info" class="mb-6">
                                {{ $summary }}
                            </x-atomic.molecules.feedback.alert-box>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Airline Selection -->
                            <div>
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Airline" 
                                    name="selected_airline_id" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select wire:model.live="selected_airline_id" required>
                                        <option value="">Select Airline</option>
                                        @foreach($airlines as $airline)
                                            <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                                
                                <!-- Project Creation Dialog (only when airline has NO projects at all) -->
                                @if($selected_airline_id && $totalProjectsForAirline === 0)
                                    <x-atomic.molecules.feedback.dialog-box 
                                        type="warning"
                                        title="⚠ No projects found for {{ $airlines->find($selected_airline_id)->name }}."
                                        message="Would you like to create the first project for this airline?"
                                        :actions="[
                                            ['label' => 'Yes, Create Project', 'action' => 'createProject', 'variant' => 'primary'],
                                            ['label' => 'No, Cancel', 'action' => 'cancelNoProject', 'variant' => 'secondary']
                                        ]"
                                    />
                                @endif
                            </div>

                            <!-- Opportunity Type Selection -->
                            <div>
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Opportunity Type" 
                                    name="selected_opportunity_type" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select 
                                        wire:model.live="selected_opportunity_type" 
                                        required
                                        :disabled="!$selected_airline_id"
                                    >
                                        <option value="">{{ $selected_airline_id ? 'Select Type' : 'Select Airline First' }}</option>
                                        @foreach($opportunityTypes as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                                
                                <!-- Combined Project/Opportunity Creation Dialog -->
                                @if($selected_airline_id && $selected_opportunity_type && !$selected_cabin_class && $totalProjectsForAirline > 0 && $totalOpportunitiesForSelection === 0)
                                    @php
                                        $selectedType = collect($opportunityTypes)->firstWhere('value', $selected_opportunity_type);
                                        $airlineName = $airlines->find($selected_airline_id)->name;
                                        $typeLabel = $selectedType ? $selectedType->label() : 'opportunity';
                                    @endphp
                                    <x-atomic.molecules.feedback.dialog-box 
                                        type="warning"
                                        title="⚠ No {{ $typeLabel }} found for {{ $airlineName }}."
                                        message="{{ $airlineName }} has {{ $totalProjectsForAirline }} existing project(s). Would you like to:"
                                        :actions="[
                                            ['label' => '✓ Add ' . $typeLabel . ' to existing project', 'action' => 'createOpportunityInExistingProject', 'variant' => 'primary'],
                                            ['label' => '+ Create new project for ' . $typeLabel, 'action' => 'createProject', 'variant' => 'primary'],
                                            ['label' => '✕ Cancel', 'action' => 'cancelNoOpportunity', 'variant' => 'secondary']
                                        ]"
                                    />
                                @endif
                            </div>

                            <!-- Cabin Class Selection -->
                            <div>
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Cabin Class" 
                                    name="selected_cabin_class" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select 
                                        wire:model.live="selected_cabin_class" 
                                        required
                                        :disabled="!$selected_opportunity_type"
                                    >
                                        <option value="">{{ $selected_opportunity_type ? 'Select Cabin Class' : 'Select Type First' }}</option>
                                        @foreach($cabinClasses as $class)
                                            <option value="{{ $class->value }}">{{ $class->label() }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                                
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
                                <x-atomic.molecules.forms.form-field-group 
                                    name="selected_project_id" 
                                    :required="true"
                                >
                                    <x-slot name="label">
                                        Project
                                        @if($selected_cabin_class && $projectCount > 0)
                                            <span class="ml-2 text-xs text-purple-600">({{ $filteredProjects->count() }} matching projects)</span>
                                        @endif
                                    </x-slot>
                                    <x-atomic.atoms.forms.form-select 
                                        wire:model.live="selected_project_id" 
                                        required
                                        :disabled="!$selected_cabin_class"
                                    >
                                        <option value="">{{ $selected_cabin_class ? 'Select Project' : 'Select Cabin Class First' }}</option>
                                        @foreach($filteredProjects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Opportunity Selection Result -->
                            @if($selected_airline_id && $selected_project_id && $selected_opportunity_type && $selected_cabin_class)
                                <div class="md:col-span-2">
                                    @if($filteredOpportunities->count() > 0)
                                        <x-atomic.molecules.feedback.alert-box type="success">
                                            @if($filteredOpportunities->count() === 1)
                                                ✓ Opportunity found: {{ $filteredOpportunities->first()->name ?: 'Auto-generated' }}
                                            @else
                                                ✓ {{ $filteredOpportunities->count() }} opportunities found matching your criteria
                                            @endif
                                        </x-atomic.molecules.feedback.alert-box>
                                    @else
                                        <x-atomic.molecules.feedback.dialog-box 
                                            type="warning"
                                            title="⚠ No opportunity exists for this combination"
                                            message="Would you like to create a new opportunity for this project?"
                                            :actions="[
                                                ['label' => 'Yes, Create Opportunity', 'action' => 'createOpportunity', 'variant' => 'primary'],
                                                ['label' => 'No, Cancel', 'action' => 'cancelNoOpportunity', 'variant' => 'secondary']
                                            ]"
                                        />
                                    @endif
                                </div>
                            @endif

                            <!-- Main Subcontractor -->
                            <div>
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Main Subcontractor" 
                                    name="main_subcontractor_id" 
                                    :required="true"
                                >
                                    <x-atomic.molecules.forms.inline-create-form
                                        title="Create New Subcontractor"
                                        :showForm="$showNewSubcontractorForm"
                                        toggleAction="toggleNewSubcontractorForm"
                                        toggleText="+ Add New Subcontractor"
                                    >
                                        <x-atomic.atoms.forms.form-select 
                                            wire:model="main_subcontractor_id" 
                                            required
                                            :disabled="$showNewSubcontractorForm"
                                        >
                                            <option value="">Select Main Subcontractor</option>
                                            @foreach($subcontractors as $subcontractor)
                                                <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                            @endforeach
                                        </x-atomic.atoms.forms.form-select>
                                        
                                        <x-slot name="form">
                                            <div class="space-y-2">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Name *</label>
                                                    <x-atomic.atoms.forms.form-input 
                                                        type="text" 
                                                        wire:model.live.debounce.300ms="newSubcontractorName" 
                                                        placeholder="Enter subcontractor name"
                                                        class="text-sm px-2 py-1 {{ $errors->has('newSubcontractorName') ? 'border-red-300' : '' }}"
                                                    />
                                                    @error('newSubcontractorName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Comment</label>
                                                    <x-atomic.atoms.forms.form-textarea 
                                                        wire:model="newSubcontractorComment" 
                                                        rows="2"
                                                        placeholder="Optional comment about this subcontractor"
                                                        class="text-sm px-2 py-1"
                                                    />
                                                    @error('newSubcontractorComment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                
                                                <button type="button" 
                                                        wire:click="createNewSubcontractor"
                                                        class="w-full px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                    Create Subcontractor
                                                </button>
                                            </div>
                                        </x-slot>
                                    </x-atomic.molecules.forms.inline-create-form>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Role -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Role" 
                                name="role" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="role" required>
                                    <option value="">Select Role</option>
                                    @foreach($teamRoles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Supporting Subcontractors -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.dynamic-select-list 
                                    label="Supporting Subcontractors"
                                    addButtonText="+ Add Supporting Subcontractor"
                                    addAction="addSupportingSubcontractor"
                                    removeAction="removeSupportingSubcontractor"
                                    :items="$supportingSubcontractors"
                                    wireModel="supportingSubcontractors"
                                    :options="$subcontractors"
                                    :excludeIds="[$main_subcontractor_id]"
                                    emptyText="No supporting subcontractors added yet."
                                />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Notes" 
                                    name="notes"
                                >
                                    <x-atomic.atoms.forms.form-textarea 
                                        wire:model="notes" 
                                        rows="3" 
                                        placeholder="Add any relevant notes about this team..."
                                    />
                                </x-atomic.molecules.forms.form-field-group>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <x-atomic.atoms.buttons.secondary-button type="button" wire:click="closeModal">
                                Cancel
                            </x-atomic.atoms.buttons.secondary-button>
                            <x-atomic.atoms.buttons.primary-button type="submit">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Team
                            </x-atomic.atoms.buttons.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>