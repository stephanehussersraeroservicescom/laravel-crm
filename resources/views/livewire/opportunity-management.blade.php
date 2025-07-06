<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Opportunity Management</h1>
        <button wire:click="openCreateModal" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Opportunity
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
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
                       placeholder="Search opportunities, projects, airlines..."
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Assigned User Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                <select wire:model.live="filterAssignedTo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Assigned Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

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

            <!-- Project Filter moved here for better layout -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select wire:model.live="filterProject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->airline->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($opportunityTypes as $type)
                        <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($opportunityStatuses as $status)
                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cabin Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class</label>
                <select wire:model.live="filterCabinClass" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach($cabinClasses as $class)
                        <option value="{{ $class->value }}">{{ str_replace('_', ' ', ucwords($class->value, '_')) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted opportunities</span>
            </label>
            
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
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
                                <div class="font-medium text-gray-900">
                                    {{ $opportunity->name ?: 'Untitled Opportunity' }}
                                </div>
                                @if($opportunity->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">
                                        {{ Str::limit($opportunity->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $opportunity->project->name }}</div>
                                <div class="text-sm text-gray-500">{{ $opportunity->project->airline->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ ucfirst($opportunity->type->value) }}</div>
                                @if($opportunity->cabin_class)
                                    <div class="text-sm text-gray-500">
                                        {{ str_replace('_', ' ', ucwords($opportunity->cabin_class->value, '_')) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    ${{ number_format($opportunity->potential_value) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($opportunity->trashed())
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Deleted
                                    </span>
                                    @if($opportunity->deletedBy)
                                        <div class="text-xs text-gray-500 mt-1">
                                            by {{ $opportunity->deletedBy->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $opportunity->deleted_at->format('M j, Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($opportunity->status->value)
                                            @case('active') bg-green-100 text-green-800 @break
                                            @case('inactive') bg-gray-100 text-gray-800 @break
                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                            @case('completed') bg-blue-100 text-blue-800 @break
                                            @case('cancelled') bg-red-100 text-red-800 @break
                                        @endswitch">
                                        {{ ucfirst($opportunity->status->value) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $opportunity->assignedTo?->name ?: 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($opportunity->trashed())
                                    <button wire:click="restore({{ $opportunity->id }})" 
                                            onclick="return confirm('Are you sure you want to restore this opportunity?')"
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="openEditModal({{ $opportunity->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $opportunity->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this opportunity?')"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No opportunities found. 
                                <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800">
                                    Create your first opportunity
                                </button>
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
                            <!-- Project -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project *</label>
                                <select wire:model="project_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }} ({{ $project->airline->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" wire:model="name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select wire:model="type" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Type</option>
                                    @foreach($opportunityTypes as $type)
                                        <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cabin Class -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class</label>
                                <select wire:model="cabin_class" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Cabin Class</option>
                                    @foreach($cabinClasses as $class)
                                        <option value="{{ $class->value }}">
                                            {{ str_replace('_', ' ', ucwords($class->value, '_')) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cabin_class') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Probability -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Probability (%) *</label>
                                <input type="number" wire:model="probability" min="0" max="100" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('probability') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Potential Value -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Potential Value ($) *</label>
                                <input type="number" wire:model="potential_value" min="0" step="0.01" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('potential_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select wire:model="status" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach($opportunityStatuses as $status)
                                        <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Assigned To -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To *</label>
                                <select wire:model="assigned_to" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Certification Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Certification Status</label>
                                <select wire:model="certification_status_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </select>
                                @error('certification_status_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="description" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Comments -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                                <textarea wire:model="comments" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('comments') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Opportunity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>