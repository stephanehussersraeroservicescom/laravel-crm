<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Team Management</h1>
        <button wire:click="openCreateModal" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Team
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search and Filter Panel -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search teams, opportunities, subcontractors..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select wire:model.live="filterProject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
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
            <!-- Opportunity Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opportunity</label>
                <select wire:model.live="filterOpportunity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Opportunities</option>
                    @foreach($opportunities as $opportunity)
                        @if(!$filterProject || $opportunity->project_id == $filterProject)
                            <option value="{{ $opportunity->id }}">
                                {{ $opportunity->name ?: 'Untitled' }} 
                                ({{ $opportunity->project->name }})
                            </option>
                        @endif
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

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="filterRole" 
                       placeholder="Filter by role..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex justify-end mt-4">
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
                            Opportunity
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project / Airline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Main Subcontractor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('role')">
                            Role
                            @if($sortBy === 'role')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Supporting Team
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
                        <tr class="hover:bg-gray-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">
                                    {{ $team->opportunity->name ?: 'Untitled Opportunity' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst($team->opportunity->type->value) }} - 
                                    @if($team->opportunity->cabin_class)
                                        {{ str_replace('_', ' ', ucwords($team->opportunity->cabin_class->value, '_')) }}
                                    @else
                                        All Classes
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $team->opportunity->project->name }}</div>
                                <div class="text-sm text-gray-500">{{ $team->opportunity->project->airline->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $team->mainSubcontractor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $team->role ?: 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
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
                                @else
                                    <span class="text-sm text-gray-400">No supporting team</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($team->notes)
                                    <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $team->notes }}">
                                        {{ Str::limit($team->notes, 50) }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No notes</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="openEditModal({{ $team->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $team->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this team?')"
                                        class="text-red-600 hover:text-red-900 transition-colors">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Opportunity -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Opportunity *</label>
                                <select wire:model="opportunity_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Opportunity</option>
                                    @foreach($opportunities as $opportunity)
                                        <option value="{{ $opportunity->id }}">
                                            {{ $opportunity->name ?: 'Untitled' }} 
                                            ({{ $opportunity->project->name }} - {{ $opportunity->project->airline->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('opportunity_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Main Subcontractor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Main Subcontractor *</label>
                                <select wire:model="main_subcontractor_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Main Subcontractor</option>
                                    @foreach($subcontractors as $subcontractor)
                                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                    @endforeach
                                </select>
                                @error('main_subcontractor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select wire:model="role" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
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
                                                        @if($subcontractor->id != $main_subcontractor_id)
                                                            <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
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