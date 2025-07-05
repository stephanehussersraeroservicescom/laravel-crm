<x-table-container title="Contacts for {{ $subcontractor->name }}">
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Please correct the following errors:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($success)
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        Contact {{ $editing ? 'updated' : 'added' }} successfully!
                    </p>
                </div>
            </div>
        </div>
    @endif
    <x-management-panel title="Search & Filter Contacts">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-field label="Search" name="search" placeholder="Search by name, email, or phone..." />
            <x-form-field label="Filter by Role" name="roleFilter" type="select" :options="$availableRoles" placeholder="All Roles" />
            <div class="space-y-1 flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Show deleted contacts</span>
                </label>
            </div>
        </div>
    </x-management-panel>

    <x-management-panel title="{{ $editing ? 'Edit Contact' : 'Add New Contact' }}">
        <x-form-grid formAction="save">
            <x-form-field label="Name" name="name" required />
            <x-form-field label="Email" name="email" type="email" />
            <x-form-field label="Role" name="role" placeholder="e.g., Manager, Engineer..." />
            <x-form-field label="Phone" name="phone" />
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                    {{ $editing ? 'Update Contact' : 'Add Contact' }}
                </button>
                @if($editing)
                    <button type="button" wire:click="cancelEdit" class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">
                        Cancel
                    </button>
                @endif
            </div>
        </x-form-grid>
    </x-management-panel>
    <x-table-box>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Role</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Phone</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contacts as $contact)
                    <tr class="{{ $contact->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                        <td class="px-3 sm:px-6 py-4">
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                @if($contact->trashed())
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                        Deleted {{ $contact->deleted_at->diffForHumans() }}
                                    </span>
                                @endif
                                <!-- Mobile-only info -->
                                <div class="mt-1 md:hidden">
                                    @if($contact->role)
                                        <div class="text-xs text-gray-500">{{ $contact->role }}</div>
                                    @endif
                                    @if($contact->phone)
                                        <div class="text-xs text-gray-500">{{ $contact->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:underline text-sm">
                                    {{ $contact->email }}
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            @if($contact->role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $contact->role }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="text-blue-600 hover:underline text-sm">
                                    {{ $contact->phone }}
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                <button wire:click="edit({{ $contact->id }})" 
                                        class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200 text-left px-2 py-1 rounded hover:bg-blue-50">
                                    Edit
                                </button>
                                @if($contact->trashed())
                                    <button wire:click="restore({{ $contact->id }})" 
                                            class="text-green-600 hover:text-green-900 font-medium transition-colors duration-200 text-left px-2 py-1 rounded hover:bg-green-50">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="delete({{ $contact->id }})" 
                                            class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200 text-left px-2 py-1 rounded hover:bg-red-50" 
                                            onclick="return confirm('Are you sure you want to delete this contact?')">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            @if($search || $roleFilter)
                                No contacts found matching your search criteria.
                            @else
                                No contacts added yet. Add your first contact above.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-box>
</x-table-container>
