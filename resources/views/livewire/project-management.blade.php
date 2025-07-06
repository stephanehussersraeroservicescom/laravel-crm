<div class="space-y-6">
    <!-- Header -->
    <div class="w-full mx-auto md:max-w-[90%] pt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Project Management</h1>
            <button wire:click="openCreateModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Project
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search projects, airlines, comments..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <!-- Aircraft Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aircraft Type</label>
                <select wire:model.live="filterAircraftType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Aircraft Types</option>
                    @foreach($aircraftTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Design Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Design Status</label>
                <select wire:model.live="filterDesignStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Design Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Commercial Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commercial Status</label>
                <select wire:model.live="filterCommercialStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Commercial Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted projects</span>
            </label>
            
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
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
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $project->name }}</div>
                                        @if($project->comment)
                                            <div class="text-sm text-gray-500">{{ Str::limit($project->comment, 50) }}</div>
                                        @endif
                                    </div>
                                    @if($project->attachments && $project->attachments->count() > 0)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span class="text-xs text-blue-500 ml-1">{{ $project->attachments->count() }}</span>
                                        </div>
                                    @endif
                                </div>
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
                                <div class="text-xs space-y-1">
                                    @if($project->designStatus)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Design: {{ $project->designStatus->status }}
                                        </span>
                                    @endif
                                    @if($project->commercialStatus)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Commercial: {{ $project->commercialStatus->status }}
                                        </span>
                                    @endif
                                </div>
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
                                <div class="flex flex-col space-y-1">
                                    <div class="flex space-x-2">
                                        <button wire:click="openEditModal({{ $project->id }})" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors">
                                            Edit
                                        </button>
                                        @if($project->trashed())
                                            <button wire:click="delete({{ $project->id }})" 
                                                    onclick="return confirm('Are you sure you want to restore this project?')"
                                                    class="text-green-600 hover:text-green-900 transition-colors">
                                                Restore
                                            </button>
                                        @else
                                            <button wire:click="delete({{ $project->id }})" 
                                                    onclick="return confirm('Are you sure you want to delete this project?')"
                                                    class="text-red-600 hover:text-red-900 transition-colors">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                    @if(!$project->trashed() && $project->opportunities && $project->opportunities->count() > 0)
                                        <button wire:click="updateOpportunityNames({{ $project->id }})" 
                                                class="text-purple-600 hover:text-purple-900 transition-colors text-xs"
                                                title="Update related opportunity names to match current project details">
                                            Sync Names
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No projects found. 
                                <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800">
                                    Create your first project
                                </button>
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Airline *</label>
                                <select wire:model.live="airline_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Airline</option>
                                    @foreach($airlines as $airline)
                                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                    @endforeach
                                </select>
                                @error('airline_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Aircraft Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Aircraft Type</label>
                                <select wire:model.live="aircraft_type_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Aircraft Type</option>
                                    @foreach($aircraftTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('aircraft_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Number of Aircraft -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Number of Aircraft</label>
                                <input type="number" wire:model="number_of_aircraft" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('number_of_aircraft') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Project Name (moved here between number of aircraft and design status) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Project Name *
                                    @if($airline_id || $aircraft_type_id)
                                        <span class="text-xs text-gray-500 ml-2">(Auto-generated based on selections)</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="text" wire:model.live="name" required 
                                           placeholder="{{ !$airline_id && !$aircraft_type_id ? 'Select airline and/or aircraft type first' : 'You can add additional details here' }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @if($nameManuallyEdited && $autoGeneratedName && $name !== $autoGeneratedName)
                                        <div class="text-xs text-blue-600 mt-1">
                                            <span class="cursor-pointer hover:underline" wire:click="$set('name', autoGeneratedName); $set('nameManuallyEdited', false)">
                                                Reset to auto-generated: {{ $autoGeneratedName }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Owner -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Owner *</label>
                                <select wire:model="owner_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Owner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('owner_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Design Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Design Status</label>
                                <select wire:model="design_status_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Design Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </select>
                                @error('design_status_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Commercial Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Commercial Status</label>
                                <select wire:model="commercial_status_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Commercial Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </select>
                                @error('commercial_status_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Comment -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                                <textarea wire:model="comment" rows="3" 
                                          placeholder="Add any relevant notes about this project..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-700">{{ $attachment->name }}</span>
                                                        <span class="text-xs text-gray-500">({{ $attachment->formatted_file_size }})</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" 
                                                           class="text-blue-600 hover:text-blue-800 text-xs">
                                                            Download
                                                        </a>
                                                        <button type="button" wire:click="deleteExistingAttachment({{ $attachment->id }})"
                                                                class="text-red-600 hover:text-red-800 text-xs">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- New File Upload -->
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="file" wire:model="attachments" multiple 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                    @error('attachments.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    
                                    <!-- Preview selected files -->
                                    @if($attachments && count($attachments) > 0)
                                        <div class="mt-2">
                                            <h5 class="text-xs font-medium text-gray-600 mb-1">Files to upload:</h5>
                                            <div class="space-y-1">
                                                @foreach($attachments as $index => $file)
                                                    @if($file)
                                                        <div class="flex items-center justify-between p-1 bg-blue-50 rounded border text-xs">
                                                            <span class="text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                                            <button type="button" wire:click="removeAttachment({{ $index }})"
                                                                    class="text-red-600 hover:text-red-800">
                                                                Remove
                                                            </button>
                                                        </div>
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
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Project
                            </button>
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
                        <button wire:click="cancelDeleteAttachment" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button wire:click="confirmDeleteAttachment"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>