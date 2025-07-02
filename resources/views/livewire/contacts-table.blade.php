<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contacts for {{ $subcontractor->name }}
        </h2>
    </x-slot>
    
    <div class="py-4 max-w-6xl mx-auto">
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
        
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <!-- Filter Section -->
            <div class="border-b border-gray-200 p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Filter Contacts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label class="block font-semibold mb-1 h-6">Search</label>
                        <input type="text" wire:model.live="search" 
                               class="rounded border-gray-300" 
                               placeholder="Search by name, email, or phone...">
                        <div class="min-h-[1.5rem] mt-1"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block font-semibold mb-1 h-6">Role</label>
                        <select wire:model.live="roleFilter" class="rounded border-gray-300">
                            <option value="">All Roles</option>
                            @foreach($availableRoles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                        <div class="min-h-[1.5rem] mt-1"></div>
                    </div>
                </div>
            </div>
            
            <!-- Add/Edit Form Section -->
            <div class="p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editing ? 'Edit Contact' : 'Add New Contact' }}</h3>
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div class="flex flex-col">
                            <label class="block font-semibold mb-1 h-6">
                                Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="name" 
                                   class="rounded border-gray-300 @error('name') border-red-500 ring-red-500 @enderror" 
                                   required>
                            <div class="min-h-[1.5rem] mt-1">
                                @error('name') 
                                    <div class="text-red-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex flex-col">
                            <label class="block font-semibold mb-1 h-6">Email</label>
                            <input type="email" wire:model.live="email" 
                                   class="rounded border-gray-300 @error('email') border-red-500 ring-red-500 @enderror">
                            <div class="min-h-[1.5rem] mt-1">
                                @error('email') 
                                    <div class="text-red-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex flex-col">
                            <label class="block font-semibold mb-1 h-6">Role</label>
                            <input type="text" wire:model.live="role" 
                                   class="rounded border-gray-300 @error('role') border-red-500 ring-red-500 @enderror" 
                                   placeholder="e.g., Manager, Engineer...">
                            <div class="min-h-[1.5rem] mt-1">
                                @error('role') 
                                    <div class="text-red-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex flex-col">
                            <label class="block font-semibold mb-1 h-6">Phone</label>
                            <input type="text" wire:model.live="phone" 
                                   class="rounded border-gray-300 @error('phone') border-red-500 ring-red-500 @enderror">
                            <div class="min-h-[1.5rem] mt-1">
                                @error('phone') 
                                    <div class="text-red-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-start">
                        <button type="submit" class="bg-blue-600 text-white rounded px-6 py-2 hover:bg-blue-700">
                            {{ $editing ? 'Update Contact' : 'Add Contact' }}
                        </button>
                        @if($editing)
                            <button type="button" wire:click="cancelEdit" class="ml-3 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                                Cancel
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <!-- End Management Panel -->
        
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border text-left">Name</th>
                        <th class="px-3 py-2 border text-left">Email</th>
                        <th class="px-3 py-2 border text-left">Role</th>
                        <th class="px-3 py-2 border text-left">Phone</th>
                        <th class="px-3 py-2 border text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">
                                <span class="font-semibold">{{ $contact->name }}</span>
                            </td>
                            <td class="px-3 py-2 border">
                                @if($contact->email)
                                    <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:underline">
                                        {{ $contact->email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">
                                @if($contact->role)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                        {{ $contact->role }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">
                                @if($contact->phone)
                                    <a href="tel:{{ $contact->phone }}" class="text-blue-600 hover:underline">
                                        {{ $contact->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">
                                <button wire:click="edit({{ $contact->id }})" class="text-blue-600 underline mr-2">Edit</button>
                                <button wire:click="delete({{ $contact->id }})" class="text-red-600 underline" 
                                        onclick="return confirm('Are you sure you want to delete this contact?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">
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
        </div>
    </div>
</div>
