<div class="space-y-6">
    <!-- Header -->
    <div class="w-full mx-auto md:max-w-[90%] pt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Contact Management</h1>
            <button wire:click="openCreateModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Contact
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
                       placeholder="Search contacts, roles, companies..."
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
            <!-- Subcontractor Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subcontractor</label>
                <select wire:model.live="filterSubcontractor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Subcontractors</option>
                    @foreach($subcontractors as $subcontractor)
                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select wire:model.live="filterRole" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted contacts</span>
            </label>
            
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Contacts Table -->
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('email')">
                            Email
                            @if($sortBy === 'email')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Phone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('role')">
                            Role
                            @if($sortBy === 'role')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('subcontractor_name')">
                            Subcontractor
                            @if($sortBy === 'subcontractor_name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-300 {{ $contact->trashed() ? 'bg-red-50 opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $contact->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="mailto:{{ $contact->email }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors">
                                    {{ $contact->email }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($contact->phone)
                                    <a href="tel:{{ $contact->phone }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors">
                                        {{ $contact->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">No phone</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($contact->role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $contact->role->label() }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No role</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $contact->subcontractor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="openEditModal({{ $contact->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Edit
                                </button>
                                @if($contact->trashed())
                                    <button wire:click="delete({{ $contact->id }})" 
                                            onclick="return confirm('Are you sure you want to restore this contact?')"
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="delete({{ $contact->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this contact?')"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No contacts found. 
                                <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800">
                                    Create your first contact
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $contacts->links() }}
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
                            {{ $modalMode === 'create' ? 'Create New Contact' : 'Edit Contact' }}
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
                            <!-- Subcontractor -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subcontractor *</label>
                                <select wire:model="subcontractor_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Subcontractor</option>
                                    @foreach($subcontractors as $subcontractor)
                                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                    @endforeach
                                </select>
                                @error('subcontractor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" wire:model="name" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" wire:model="email" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel" wire:model="phone" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select wire:model="role" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>