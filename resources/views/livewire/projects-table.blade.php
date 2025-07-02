<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Projects
        </h2>
    </x-slot>
    <div class="py-4 max-w-7xl mx-auto">
    <!-- Management Panel -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Filter & Search Projects</h3>
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Show deleted projects</span>
                </label>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-4">
        <div>
            <label class="block font-semibold mb-1">Region</label>
            <select wire:model.live="region" class="rounded border-gray-300">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}">{{ $region }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-1">Account Executive</label>
            <select wire:model.live="accountExecutive" class="rounded border-gray-300">
                <option value="">All Executives</option>
                @foreach($executives as $exec)
                    <option value="{{ $exec }}">{{ $exec }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-1">Search</label>
            <input type="text" wire:model.live="search" placeholder="Project name..." class="rounded border-gray-300">
        </div>
        <div>
            <button wire:click="openModal" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                Add Project
            </button>
        </div>
        </div>
    </div>
    <!-- End Management Panel -->

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border text-left">Project Name</th>
                    <th class="px-3 py-2 border text-left">Airline</th>
                    <th class="px-3 py-2 border text-left">Region</th>
                    <th class="px-3 py-2 border text-left">Account Executive</th>
                    <th class="px-3 py-2 border text-left">Aircraft Type</th>
                    <th class="px-3 py-2 border text-left"># Aircraft</th>
                    <th class="px-3 py-2 border text-left">Design Status</th>
                    <th class="px-3 py-2 border text-left">Commercial Status</th>
                    <th class="px-3 py-2 border text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 {{ $project->trashed() ? 'bg-red-50' : '' }}">
                        <td class="px-3 py-2 border font-semibold">
                            {{ $project->name }}
                            @if($project->trashed())
                                <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded ml-2">Deleted</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border">{{ $project->airline->name ?? '—' }}</td>
                        <td class="px-3 py-2 border">{{ $project->airline->region ?? '—' }}</td>
                        <td class="px-3 py-2 border">{{ $project->airline->account_executive ?? '—' }}</td>
                        <td class="px-3 py-2 border">{{ $project->aircraftType->name ?? '—' }}</td>
                        <td class="px-3 py-2 border">{{ $project->number_of_aircraft ?? '—' }}</td>
                        <td class="px-3 py-2 border">
                            @if($project->designStatus)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    {{ $project->designStatus->status }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 border">
                            @if($project->commercialStatus)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    {{ $project->commercialStatus->status }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 border">
                            @if($project->trashed())
                                <button wire:click="restore({{ $project->id }})" class="text-green-600 underline mr-2">Restore</button>
                                <button wire:click="forceDelete({{ $project->id }})" class="text-red-600 underline" 
                                        onclick="return confirm('Are you sure you want to permanently delete this project? This action cannot be undone.')">
                                    Delete Permanently
                                </button>
                            @else
                                <button wire:click="edit({{ $project->id }})" class="text-blue-600 underline mr-2">Edit</button>
                                <button wire:click="delete({{ $project->id }})" class="text-red-600 underline" 
                                        onclick="return confirm('Are you sure you want to delete this project?')">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-8 text-center text-gray-500">
                            No projects found. Add your first project above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-8 border w-auto max-w-2xl min-w-96 shadow-lg rounded-md bg-white" style="margin: 3rem auto; width: 95%; max-width: 900px;">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">
                        {{ $editing ? 'Edit Project' : 'Add New Project' }}
                    </h3>
                    
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
                    
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div>
                            <label class="block font-semibold mb-2">
                                Project Name 
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="name" 
                                   class="w-full rounded border-gray-300 @error('name') border-red-500 ring-red-500 @enderror" 
                                   required>
                            @error('name') 
                                <div class="mt-1 text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-semibold mb-2">
                                    Select Existing Airline
                                    <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="selectedAirline" 
                                        class="w-full rounded border-gray-300 @error('selectedAirline') border-red-500 ring-red-500 @enderror">
                                    <option value="">Choose existing airline...</option>
                                    @foreach($airlines as $airline)
                                        <option value="{{ $airline->id }}">{{ $airline->name }} ({{ $airline->region }})</option>
                                    @endforeach
                                </select>
                                @error('selectedAirline') 
                                    <div class="mt-1 text-red-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                @if(!$selectedAirline)
                                    <button type="button" wire:click="$toggle('showNewAirlineForm')" class="text-blue-600 underline text-sm">
                                        {{ $showNewAirlineForm ? 'Hide new airline form' : 'Or add new airline' }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if(!$selectedAirline && $showNewAirlineForm)
                            <div class="border-2 border-gray-200 rounded-lg p-6 bg-gray-50 space-y-4">
                                <h4 class="font-semibold text-gray-700 mb-4">Create New Airline</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold mb-2">
                                            Airline Name
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model.live="newAirlineName" 
                                               class="w-full rounded border-gray-300 @error('newAirlineName') border-red-500 ring-red-500 @enderror">
                                        @error('newAirlineName') 
                                            <div class="mt-1 text-red-600 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-2">
                                            Region
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model.live="newAirlineRegion" 
                                                class="w-full rounded border-gray-300 @error('newAirlineRegion') border-red-500 ring-red-500 @enderror">
                                            <option value="">Select Region...</option>
                                            @foreach($availableRegions as $region)
                                                <option value="{{ $region }}">{{ $region }}</option>
                                            @endforeach
                                        </select>
                                        @error('newAirlineRegion') 
                                            <div class="mt-1 text-red-600 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-2">Account Executive</label>
                                        <select wire:model.live="newAirlineAccountExecutive" class="w-full rounded border-gray-300">
                                            <option value="">Select...</option>
                                            @foreach($salesUsers as $user)
                                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-semibold mb-2">Aircraft Type</label>
                                <select wire:model.live="aircraft_type_id" class="w-full rounded border-gray-300">
                                    <option value="">Select Aircraft Type...</option>
                                    @foreach($aircraftTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold mb-2">Number of Aircraft</label>
                                <input type="number" wire:model.live="number_of_aircraft" class="w-full rounded border-gray-300" min="1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-semibold mb-2">Design Status</label>
                                <select wire:model.live="design_status_id" class="w-full rounded border-gray-300">
                                    <option value="">Select Status...</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold mb-2">Commercial Status</label>
                                <select wire:model.live="commercial_status_id" class="w-full rounded border-gray-300">
                                    <option value="">Select Status...</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold mb-2">Comment</label>
                            <textarea wire:model.live="comment" class="w-full rounded border-gray-300" rows="3"></textarea>
                        </div>

                        @if(!$editing)
                            <div>
                                <label class="block font-semibold mb-3">Opportunities to Create</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach($opportunities as $key => $label)
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model.live="selectedOpportunities" value="{{ $key }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <label class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Select which opportunities you want to create for this project. Each will create entries for all cabin classes (First, Business, Premium Economy, Economy).</p>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-8 pt-6">
                            <button type="button" wire:click="closeModal" class="px-6 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                {{ $editing ? 'Update Project' : 'Create Project' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Subcontractor Confirmation Popup -->
    @if($showSubcontractorConfirm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-1/3 mx-auto p-8 border w-auto max-w-md shadow-lg rounded-md bg-white" style="margin: 20% auto; width: 90%; max-width: 500px;">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Project Created Successfully!
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Would you like to add subcontractors to this project now?
                    </p>
                    <div class="flex justify-center space-x-4">
                        <button wire:click="addSubcontractorsLater" class="px-6 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                            Maybe Later
                        </button>
                        <button wire:click="addSubcontractorsNow" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Yes, Add Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
