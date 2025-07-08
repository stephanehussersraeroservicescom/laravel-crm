<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Opportunity Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Opportunity
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />
    <x-atomic.atoms.feedback.flash-message type="error" :message="session('error')" />

    <!-- Search and Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                span="wide"
                label="Search"
                placeholder="Search opportunities, projects, airlines..."
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Assigned User Filter -->
            <x-atomic.molecules.forms.form-field-group label="Assigned To">
                <x-atomic.atoms.forms.form-select wire:model.live="filterAssignedTo">
                    <option value="">All Assigned Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

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
                    @foreach($aircraftTypes as $aircraftType)
                        <option value="{{ $aircraftType->id }}">{{ $aircraftType->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <!-- Project Filter -->
            <x-atomic.molecules.forms.form-field-group label="Project">
                <x-atomic.atoms.forms.form-select wire:model.live="filterProject">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->airline?->name ?? 'No Airline' }})</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Type Filter -->
            <x-atomic.molecules.forms.form-field-group label="Type">
                <x-atomic.atoms.forms.form-select wire:model.live="filterType">
                    <option value="">All Types</option>
                    @foreach($opportunityTypes as $type)
                        <option value="{{ $type->value }}">{{ ucfirst($type->value ?? $type->name ?? 'Unknown') }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Status Filter -->
            <x-atomic.molecules.forms.form-field-group label="Status">
                <x-atomic.atoms.forms.form-select wire:model.live="filterStatus">
                    <option value="">All Statuses</option>
                    @foreach($opportunityStatuses as $status)
                        <option value="{{ $status->value }}">{{ ucfirst($status->value ?? $status->name ?? 'Unknown') }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Cabin Class Filter -->
            <x-atomic.molecules.forms.form-field-group label="Cabin Class">
                <x-atomic.atoms.forms.form-select wire:model.live="filterCabinClass">
                    <option value="">All Classes</option>
                    @foreach($cabinClasses as $class)
                        <option value="{{ $class->value }}">{{ str_replace('_', ' ', ucwords($class->value ?? $class->name ?? 'unknown', '_')) }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <x-atomic.atoms.forms.form-checkbox 
                wire:model.live="showDeleted"
                label="Show deleted opportunities"
            />
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
        </div>
    </div>

    <!-- Opportunities Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('name')">
                            Name
                            @if($sortBy === 'name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project / Airline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type / Cabin
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('potential_value')">
                            Value
                            @if($sortBy === 'potential_value')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($opportunities as $opportunity)
                        <tr class="hover:bg-gray-300 {{ $opportunity->trashed() ? 'bg-red-50 opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.molecules.data.opportunity-name-cell 
                                    :name="$opportunity->name"
                                    :description="$opportunity->description"
                                    :attachmentCount="$opportunity->attachments ? $opportunity->attachments->count() : 0"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.molecules.data.project-airline-cell 
                                    :projectName="$opportunity->project?->name"
                                    :airlineName="$opportunity->project?->airline?->name"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.molecules.data.type-cabin-cell 
                                    :type="$opportunity->type?->value ?? $opportunity->type"
                                    :cabinClass="$opportunity->cabin_class?->value ?? $opportunity->cabin_class"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-atomic.atoms.data.currency-display :value="$opportunity->potential_value" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($opportunity->trashed())
                                    <x-atomic.atoms.feedback.smart-status-badge status="deleted" />
                                    <x-atomic.molecules.feedback.deletion-info 
                                        :deletedBy="$opportunity->deletedBy?->name"
                                        :deletedAt="$opportunity->deleted_at?->format('M j, Y')"
                                    />
                                @else
                                    @php
                                        $statusValue = $opportunity->status?->value ?? $opportunity->status ?? 'unknown';
                                    @endphp
                                    <x-atomic.atoms.feedback.smart-status-badge :status="$statusValue" />
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $opportunity->assignedTo?->name ?: 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($opportunity->trashed())
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="success" 
                                        wire:click="restore({{ $opportunity->id }})" 
                                        onclick="return confirm('Are you sure you want to restore this opportunity?')"
                                    >
                                        Restore
                                    </x-atomic.atoms.buttons.action-link>
                                @else
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="primary" 
                                        wire:click="openEditModal({{ $opportunity->id }})"
                                    >
                                        Edit
                                    </x-atomic.atoms.buttons.action-link>
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="danger" 
                                        wire:click="delete({{ $opportunity->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this opportunity?')"
                                    >
                                        Delete
                                    </x-atomic.atoms.buttons.action-link>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No opportunities found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openCreateModal">
                                    Create your first opportunity
                                </x-atomic.atoms.buttons.action-link>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $opportunities->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-[70%] max-w-5xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $modalMode === 'create' ? 'Create New Opportunity' : 'Edit Opportunity' }}
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
                            <!-- Airline Filter (for filtering projects) -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group name="modalAirlineFilter">
                                    <x-slot name="label">
                                        Filter by Airline 
                                        <span class="text-xs text-gray-500">(optional - helps narrow down project list)</span>
                                    </x-slot>
                                    <x-atomic.atoms.forms.form-select wire:model.live="modalAirlineFilter" class="bg-gray-50">
                                        <option value="">All Airlines</option>
                                        @foreach($airlines as $airline)
                                            <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Project -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Project" 
                                    name="project_id" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select wire:model.live="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach($filteredProjects as $project)
                                            <option value="{{ $project->id }}">
                                                {{ $project->name }} ({{ $project->airline?->name ?? 'No Airline' }})
                                            </option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                    @if($modalAirlineFilter && $filteredProjects->count() === 0)
                                        <div class="text-xs text-gray-500 mt-1">No projects found for selected airline</div>
                                    @elseif($modalAirlineFilter)
                                        <div class="text-xs text-gray-500 mt-1">Showing {{ $filteredProjects->count() }} project(s) for selected airline</div>
                                    @endif
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Type -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Type" 
                                name="type" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model.live="type" required>
                                    <option value="">Select Type</option>
                                    @foreach($opportunityTypes as $type)
                                        <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Cabin Class -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Cabin Class" 
                                name="cabin_class"
                            >
                                <x-atomic.atoms.forms.form-select wire:model.live="cabin_class">
                                    <option value="">Select Cabin Class</option>
                                    @foreach($cabinClasses as $class)
                                        <option value="{{ $class->value }}">
                                            {{ str_replace('_', ' ', ucwords($class->value, '_')) }}
                                        </option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Opportunity Name (moved here below type and cabin class) -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group name="name">
                                    <x-slot name="label">
                                        Opportunity Name
                                        @if($project_id || $type || $cabin_class)
                                            <span class="text-xs text-gray-500 ml-2">(Auto-generated based on selections)</span>
                                        @endif
                                    </x-slot>
                                    <div class="relative">
                                        <x-atomic.atoms.forms.form-input 
                                            type="text" 
                                            wire:model.live="name" 
                                            placeholder="{{ !$project_id && !$type && !$cabin_class ? 'Select project, type, and cabin class first' : 'You can add additional details here' }}"
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

                            <!-- Probability -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Probability (%)" 
                                name="probability" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-input 
                                    type="number" 
                                    wire:model="probability" 
                                    min="0" 
                                    max="100" 
                                    required
                                />
                            </x-atomic.molecules.forms.form-field-group>


                            <!-- Status -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Status" 
                                name="status" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="status" required>
                                    @foreach($opportunityStatuses as $status)
                                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Seat Configuration Section -->
                            <div class="md:col-span-2">
                                <div class="border-t pt-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Seat Configuration</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Price per Linear Yard -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Price per Linear Yard ($)" 
                                            name="price_per_linear_yard"
                                        >
                                            <x-atomic.atoms.forms.form-input 
                                                type="number" 
                                                wire:model="price_per_linear_yard" 
                                                min="100" 
                                                max="300" 
                                                step="0.01" 
                                                placeholder="e.g., 175.50"
                                            />
                                        </x-atomic.molecules.forms.form-field-group>

                                        <!-- Linear Yards per Seat -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Linear Yards per Seat" 
                                            name="linear_yards_per_seat"
                                        >
                                            <x-atomic.atoms.forms.form-input 
                                                type="number" 
                                                wire:model="linear_yards_per_seat" 
                                                min="0.5" 
                                                max="5.0" 
                                                step="0.1" 
                                                placeholder="e.g., 2.5"
                                            />
                                        </x-atomic.molecules.forms.form-field-group>

                                        <!-- Seats in Opportunity -->
                                        <x-atomic.molecules.forms.form-field-group 
                                            label="Seats in Opportunity" 
                                            name="seats_in_opportunity"
                                        >
                                            <x-atomic.atoms.forms.form-input 
                                                type="number" 
                                                wire:model="seats_in_opportunity" 
                                                min="1" 
                                                placeholder="e.g., 150"
                                            />
                                        </x-atomic.molecules.forms.form-field-group>
                                    </div>

                                    <!-- Calculated Values -->
                                    @if($price_per_linear_yard && $linear_yards_per_seat && $seats_in_opportunity)
                                        @php
                                            $perAircraftValue = $price_per_linear_yard * $linear_yards_per_seat * $seats_in_opportunity;
                                            $project = $project_id ? \App\Models\Project::find($project_id) : null;
                                            $numberOfAircraft = $project?->number_of_aircraft ?? 0;
                                            $totalValue = $perAircraftValue * $numberOfAircraft;
                                        @endphp
                                        <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <h5 class="text-sm font-medium text-green-900 mb-2">Calculated Values</h5>
                                            <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                                                <div>
                                                    <span class="text-green-700">Total Linear Yards per Aircraft:</span>
                                                    <span class="font-medium ml-2">{{ number_format($linear_yards_per_seat * $seats_in_opportunity, 1) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-green-700">Value per Aircraft:</span>
                                                    <span class="font-medium ml-2">${{ number_format($perAircraftValue, 2) }}</span>
                                                </div>
                                            </div>
                                            @if($numberOfAircraft > 0)
                                                <div class="border-t border-green-200 pt-3">
                                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                                        <div>
                                                            <span class="text-green-700">Number of Aircraft:</span>
                                                            <span class="font-medium ml-2">{{ number_format($numberOfAircraft) }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-green-700 font-semibold">Total Opportunity Value:</span>
                                                            <span class="font-bold ml-2 text-green-800">${{ number_format($totalValue, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-xs text-orange-600 mt-2">
                                                    Select a project to see total opportunity value
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Assigned To -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Assigned To" 
                                    name="assigned_to" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select wire:model="assigned_to" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Certification Status -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Certification Status" 
                                name="certification_status_id"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="certification_status_id">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Description" 
                                    name="description"
                                >
                                    <x-atomic.atoms.forms.form-textarea 
                                        wire:model="description" 
                                        rows="3"
                                    />
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Comments -->
                            <div class="md:col-span-2">
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Comments" 
                                    name="comments"
                                >
                                    <x-atomic.atoms.forms.form-textarea 
                                        wire:model="comments" 
                                        rows="2"
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
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Opportunity
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