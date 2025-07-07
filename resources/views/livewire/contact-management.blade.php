<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Contact Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Contact
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
                placeholder="Search contacts, roles, companies..."
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
            <!-- Subcontractor Filter -->
            <x-atomic.molecules.forms.form-field-group label="Subcontractor">
                <x-atomic.atoms.forms.form-select wire:model.live="filterSubcontractor">
                    <option value="">All Subcontractors</option>
                    @foreach($subcontractors as $subcontractor)
                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Role Filter -->
            <x-atomic.molecules.forms.form-field-group label="Role">
                <x-atomic.atoms.forms.form-select wire:model.live="filterRole">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="flex justify-between items-center mt-6">
            <!-- Show Deleted Checkbox -->
            <x-atomic.atoms.forms.form-checkbox 
                wire:model.live="showDeleted"
                label="Show deleted contacts"
            />
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
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
                                    <x-atomic.atoms.feedback.status-badge variant="primary">
                                        {{ $contact->role->label() }}
                                    </x-atomic.atoms.feedback.status-badge>
                                @else
                                    <span class="text-gray-400">No role</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $contact->subcontractor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <x-atomic.atoms.buttons.action-link 
                                    variant="primary" 
                                    wire:click="openEditModal({{ $contact->id }})"
                                >
                                    Edit
                                </x-atomic.atoms.buttons.action-link>
                                @if($contact->trashed())
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="success" 
                                        wire:click="delete({{ $contact->id }})" 
                                        onclick="return confirm('Are you sure you want to restore this contact?')"
                                    >
                                        Restore
                                    </x-atomic.atoms.buttons.action-link>
                                @else
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="danger" 
                                        wire:click="delete({{ $contact->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this contact?')"
                                    >
                                        Delete
                                    </x-atomic.atoms.buttons.action-link>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No contacts found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openCreateModal">
                                    Create your first contact
                                </x-atomic.atoms.buttons.action-link>
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
                                <x-atomic.molecules.forms.form-field-group 
                                    label="Subcontractor" 
                                    name="subcontractor_id" 
                                    :required="true"
                                >
                                    <x-atomic.atoms.forms.form-select wire:model="subcontractor_id" required>
                                        <option value="">Select Subcontractor</option>
                                        @foreach($subcontractors as $subcontractor)
                                            <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                        @endforeach
                                    </x-atomic.atoms.forms.form-select>
                                </x-atomic.molecules.forms.form-field-group>
                            </div>

                            <!-- Name -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Name" 
                                name="name" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-input 
                                    type="text" 
                                    wire:model="name" 
                                    required 
                                />
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Email -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Email" 
                                name="email" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-input 
                                    type="email" 
                                    wire:model="email" 
                                    required 
                                />
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Phone -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Phone" 
                                name="phone"
                            >
                                <x-atomic.atoms.forms.form-input 
                                    type="tel" 
                                    wire:model="phone" 
                                />
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Role -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Role" 
                                name="role"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="role">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <x-atomic.atoms.buttons.secondary-button type="button" wire:click="closeModal">
                                Cancel
                            </x-atomic.atoms.buttons.secondary-button>
                            <x-atomic.atoms.buttons.primary-button type="submit">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Contact
                            </x-atomic.atoms.buttons.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>