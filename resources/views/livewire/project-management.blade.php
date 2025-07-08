<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Project Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Project
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />

    <!-- Search and Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                span="wide"
                label="Search"
                placeholder="Search projects, airlines, comments..."
                wire:model.live.debounce.300ms="search"
            />

            <!-- Per Page -->
            <x-atomic.molecules.forms.form-field-group label="Show">
                <x-atomic.atoms.forms.form-select wire:model.live="perPage">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Airline Filter -->
            <x-atomic.molecules.forms.form-field-group label="Airline">
                <x-atomic.atoms.forms.form-select wire:model.live="filterAirline">
                    <option value="">All Airlines</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Aircraft Type Filter -->
            <x-atomic.molecules.forms.form-field-group label="Aircraft Type">
                <x-atomic.atoms.forms.form-select wire:model.live="filterAircraftType">
                    <option value="">All Aircraft Types</option>
                    @foreach($aircraftTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Design Status Filter -->
            <x-atomic.molecules.forms.form-field-group label="Design Status">
                <x-atomic.atoms.forms.form-select wire:model.live="filterDesignStatus">
                    <option value="">All Design Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Commercial Status Filter -->
            <x-atomic.molecules.forms.form-field-group label="Commercial Status">
                <x-atomic.atoms.forms.form-select wire:model.live="filterCommercialStatus">
                    <option value="">All Commercial Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <x-atomic.atoms.forms.form-checkbox 
                wire:model.live="showDeleted"
                label="Show deleted projects"
            />
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('name')">
                            Project Name
                            @if($sortBy === 'name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="leading-tight">
                                Number of<br>Aircraft
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Owner
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Comment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($projects as $project)
                        <tr class="hover:bg-gray-50 {{ $project->trashed() ? 'bg-red-50 opacity-75' : '' }}">
                            <!-- Project Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                        @if($project->attachments && $project->attachments->count() > 0)
                                            <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ $project->attachments->count() }} file{{ $project->attachments->count() > 1 ? 's' : '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Number of Aircraft -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $project->number_of_aircraft ?? 'Not specified' }}
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($project->designStatus)
                                        <div class="text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Design: {{ $project->designStatus->status }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($project->commercialStatus)
                                        <div class="text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Commercial: {{ $project->commercialStatus->status }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$project->designStatus && !$project->commercialStatus)
                                        <div class="text-xs text-gray-500">No status set</div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Owner -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $project->owner?->name ?? 'No Owner' }}</div>
                            </td>
                            
                            <!-- Comment -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ $project->comment ? Str::limit($project->comment, 100) : 'No comment' }}
                                </div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <x-atomic.molecules.actions.action-group>
                                    <x-atomic.molecules.actions.action-group direction="horizontal">
                                        <x-atomic.atoms.buttons.action-link 
                                            variant="primary" 
                                            wire:click="openEditModal({{ $project->id }})"
                                        >
                                            Edit
                                        </x-atomic.atoms.buttons.action-link>
                                        @if($project->trashed())
                                            <x-atomic.atoms.buttons.action-link 
                                                variant="success" 
                                                wire:click="delete({{ $project->id }})" 
                                                onclick="return confirm('Are you sure you want to restore this project?')"
                                            >
                                                Restore
                                            </x-atomic.atoms.buttons.action-link>
                                        @else
                                            <x-atomic.atoms.buttons.action-link 
                                                variant="danger" 
                                                wire:click="delete({{ $project->id }})" 
                                                onclick="return confirm('Are you sure you want to delete this project?')"
                                            >
                                                Delete
                                            </x-atomic.atoms.buttons.action-link>
                                        @endif
                                    </x-atomic.molecules.actions.action-group>
                                    @if(!$project->trashed() && $project->opportunities && $project->opportunities->count() > 0)
                                        <x-atomic.atoms.buttons.action-link 
                                            variant="secondary" 
                                            wire:click="updateOpportunityNames({{ $project->id }})" 
                                            class="text-xs"
                                            title="Update related opportunity names to match current project details"
                                        >
                                            Sync Names
                                        </x-atomic.atoms.buttons.action-link>
                                    @endif
                                </x-atomic.molecules.actions.action-group>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No projects found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openCreateModal">
                                    Create your first project
                                </x-atomic.atoms.buttons.action-link>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $projects->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $modalMode === 'create' ? 'Create New Project' : 'Edit Project' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <form wire:submit.prevent="save" class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Airline -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Airline" 
                                name="airline_id" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model.live="airline_id" required>
                                    <option value="">Select Airline</option>
                                    @foreach($airlines as $airline)
                                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Aircraft Type -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Aircraft Type" 
                                name="aircraft_type_id"
                            >
                                <x-atomic.atoms.forms.form-select wire:model.live="aircraft_type_id">
                                    <option value="">Select Aircraft Type</option>
                                    @foreach($aircraftTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Number of Aircraft -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Number of Aircraft" 
                                name="number_of_aircraft"
                            >
                                <x-atomic.atoms.forms.form-input 
                                    type="number" 
                                    wire:model="number_of_aircraft" 
                                    min="1"
                                />
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Project Name (moved here between number of aircraft and design status) -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    name="name" 
                                    :required="true"
                                >
                                    <x-slot name="label">
                                        Project Name
                                        @if($airline_id || $aircraft_type_id)
                                            <span class="text-xs text-gray-500 ml-2">(Auto-generated based on selections)</span>
                                        @endif
                                    </x-slot>
                                    <div class="relative">
                                        <x-atomic.atoms.forms.form-input 
                                            type="text" 
                                            wire:model.live="name" 
                                            required
                                            placeholder="{{ !$airline_id && !$aircraft_type_id ? 'Select airline and/or aircraft type first' : 'You can add additional details here' }}"
                                        />
                                        @if($nameManuallyEdited && $autoGeneratedName && $name !== $autoGeneratedName)
                                            <div class="text-xs text-blue-600 mt-1">
                                                <span class="cursor-pointer hover:underline" wire:click="$set('name', autoGeneratedName); $set('nameManuallyEdited', false)">
                                                    Reset to auto-generated: {{ $autoGeneratedName }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Owner -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Owner" 
                                name="owner_id" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="owner_id" required>
                                    <option value="">Select Owner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Design Status -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Design Status" 
                                name="design_status_id"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="design_status_id">
                                    <option value="">Select Design Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Commercial Status -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Commercial Status" 
                                name="commercial_status_id"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="commercial_status_id">
                                    <option value="">Select Commercial Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Comment -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Comment" 
                                    name="comment"
                                >
                                    <x-atomic.atoms.forms.form-textarea 
                                        wire:model="comment" 
                                        rows="3" 
                                        placeholder="Add any relevant notes about this project..."
                                    />
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Forecasting Section -->
                            <div class="md:col-span-2">
                                <div class="border-t pt-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Forecasting Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Linefit/Retrofit -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Project Type" 
                                            name="linefit_retrofit"
                                        >
                                            <x-atomic.atoms.forms.form-select wire:model.live="linefit_retrofit">
                                                <option value="">Select Type</option>
                                                <option value="linefit">Linefit</option>
                                                <option value="retrofit">Retrofit</option>
                                            </x-atomic.atoms.forms.form-select>
                                        </x-atomic.molecules.forms.form-field-group>

                                        <!-- Project Duration -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Project Duration (Years)" 
                                            name="project_lifecycle_duration"
                                        >
                                            <x-atomic.atoms.forms.form-select wire:model.live="project_lifecycle_duration">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'year' : 'years' }}</option>
                                                @endfor
                                            </x-atomic.atoms.forms.form-select>
                                        </x-atomic.molecules.forms.form-field-group>

                                        <!-- Start Year -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Expected Start Year" 
                                            name="expected_start_year"
                                        >
                                            <x-atomic.atoms.forms.form-select wire:model.live="expected_start_year">
                                                <option value="">Select Year</option>
                                                @for($year = 2024; $year <= 2035; $year++)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endfor
                                            </x-atomic.atoms.forms.form-select>
                                        </x-atomic.molecules.forms.form-field-group>
                                    </div>

                                    <!-- Aircraft Distribution -->
                                    @if($show_distribution)
                                        <div class="mt-6">
                                            <h5 class="text-md font-medium text-gray-800 mb-3">Aircraft Distribution by Year</h5>
                                            <div class="bg-gray-50 p-4 rounded-lg">
                                                <div class="grid gap-3" style="grid-template-columns: repeat({{ min(count($distribution_years), 6) }}, minmax(0, 1fr));">
                                                    @foreach($distribution_years as $index => $year)
                                                        <div class="text-center">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $year }}</label>
                                                            <x-atomic.atoms.forms.form-input 
                                                                type="number" 
                                                                wire:model.live="aircraft_distribution.{{ $index }}"
                                                                min="0"
                                                                class="text-center"
                                                                placeholder="0"
                                                            />
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if(!empty($aircraft_distribution))
                                                    @php
                                                        $totalDistributed = array_sum($aircraft_distribution);
                                                        $isMatching = $number_of_aircraft && $totalDistributed == $number_of_aircraft;
                                                        $hasExpected = !empty($number_of_aircraft);
                                                    @endphp
                                                    <div class="mt-3 text-center">
                                                        @if($hasExpected)
                                                            @if($isMatching)
                                                                <div class="inline-flex items-center px-3 py-2 bg-green-100 border border-green-300 rounded-md text-green-800 text-sm font-medium">
                                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Total Aircraft: {{ $totalDistributed }} / {{ $number_of_aircraft }} ✓
                                                                </div>
                                                            @else
                                                                <div class="inline-flex items-center px-3 py-2 bg-red-100 border border-red-300 rounded-md text-red-800 text-sm font-medium">
                                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Total Aircraft: {{ $totalDistributed }} / {{ $number_of_aircraft }} ⚠️
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 text-sm font-medium">
                                                                Total Aircraft: {{ $totalDistributed }}
                                                                <span class="text-xs text-gray-500 ml-2">(Set "Number of Aircraft" above for validation)</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- File Attachments -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">File Attachments</label>
                                
                                <!-- Existing Attachments -->
                                @if($existingAttachments && count($existingAttachments) > 0)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-600 mb-2">Current Files:</h4>
                                        <div class="space-y-2">
                                            @foreach($existingAttachments as $attachment)
                                                <x-atomic.molecules.feedback.attachment-item 
                                                    :name="$attachment->name"
                                                    :size="$attachment->formatted_file_size"
                                                    :downloadUrl="asset('storage/' . $attachment->file_path)"
                                                    :canDelete="true"
                                                    :deleteAction="'deleteExistingAttachment(' . $attachment->id . ')'"
                                                />
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- New File Upload -->
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <x-atomic.atoms.forms.form-file-input 
                                            wire:model="attachments" 
                                            :multiple="true"
                                        />
                                    </div>
                                    @error('attachments.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    
                                    <!-- Preview selected files -->
                                    @if($attachments && count($attachments) > 0)
                                        <div class="mt-2">
                                            <h5 class="text-xs font-medium text-gray-600 mb-1">Files to upload:</h5>
                                            <div class="space-y-1">
                                                @foreach($attachments as $index => $file)
                                                    @if($file)
                                                        <x-atomic.molecules.feedback.attachment-item 
                                                            :name="$file->getClientOriginalName()"
                                                            :canDelete="true"
                                                            :deleteAction="'removeAttachment(' . $index . ')'"
                                                            variant="preview"
                                                            class="text-xs"
                                                        />
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <x-atomic.atoms.buttons.secondary-button type="button" wire:click="closeModal">
                                Cancel
                            </x-atomic.atoms.buttons.secondary-button>
                            <x-atomic.atoms.buttons.primary-button type="submit">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Project
                            </x-atomic.atoms.buttons.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Attachment Confirmation Modal -->
    @if($attachmentToDelete)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Attachment</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Are you sure you want to delete this file? This action cannot be undone.
                    </p>
                    <div class="flex justify-center space-x-3 mt-4">
                        <x-atomic.atoms.buttons.secondary-button wire:click="cancelDeleteAttachment">
                            Cancel
                        </x-atomic.atoms.buttons.secondary-button>
                        <x-atomic.atoms.buttons.primary-button 
                            wire:click="confirmDeleteAttachment"
                            class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
                        >
                            Delete
                        </x-atomic.atoms.buttons.primary-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>