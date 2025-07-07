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
                            Airline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aircraft Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aircraft Count
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
                                <x-atomic.molecules.data.project-name-cell 
                                    :name="$project->name"
                                    :comment="$project->comment"
                                    :attachmentCount="$project->attachments ? $project->attachments->count() : 0"
                                />
                            </td>
                            
                            <!-- Airline -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $project->airline?->name ?? 'No Airline' }}</div>
                            </td>
                            
                            <!-- Aircraft Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $project->aircraftType->name ?? 'Not specified' }}
                                </div>
                            </td>
                            
                            <!-- Aircraft Count -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $project->number_of_aircraft ?? 'Not specified' }}
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.molecules.feedback.status-group 
                                    :designStatus="$project->designStatus?->status"
                                    :commercialStatus="$project->commercialStatus?->status"
                                />
                            </td>
                            
                            <!-- Owner -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $project->owner?->name ?? 'No Owner' }}</div>
                            </td>
                            
                            <!-- Comment -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ Str::limit($project->comment, 30) ?: 'No comment' }}</div>
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
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
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