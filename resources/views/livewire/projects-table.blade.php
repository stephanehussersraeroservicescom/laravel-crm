<x-table-container title="Projects">
    <x-management-panel title="Filter & Search Projects">
        <x-table-controls>
            <x-form-field label="Region" name="region" type="select" :options="$regions" placeholder="All Regions" />
            <x-form-field label="Account Executive" name="accountExecutive" type="select" :options="$executives" placeholder="All Executives" />
            <x-form-field label="Search" name="search" placeholder="Project name..." />
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Show deleted projects</span>
                </label>
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                    Add Project
                </button>
            </div>
        </x-table-controls>
    </x-management-panel>

    <x-table-box>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Airline</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Region</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Account Executive</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Aircraft Type</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell"># Aircraft</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Design Status</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Commercial Status</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                    <tr class="{{ $project->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                        <td class="px-3 sm:px-6 py-4">
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                @if($project->trashed())
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                        Deleted {{ $project->deleted_at->diffForHumans() }}
                                    </span>
                                @endif
                                <!-- Mobile-only info -->
                                <div class="mt-1 md:hidden">
                                    <div class="text-xs text-gray-500">{{ optional($project->airline)->region ?? '—' }}</div>
                                    @if($project->airline && $project->airline->account_executive)
                                        <div class="text-xs text-gray-500">AE: {{ $project->airline->account_executive }}</div>
                                    @endif
                                    @if($project->aircraftType)
                                        <div class="text-xs text-gray-500">Aircraft: {{ $project->aircraftType->name }}</div>
                                    @endif
                                    @if($project->number_of_aircraft)
                                        <div class="text-xs text-gray-500"># Aircraft: {{ $project->number_of_aircraft }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ optional($project->airline)->name ?? '—' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-sm text-gray-900">{{ optional($project->airline)->region ?? '—' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <div class="text-sm text-gray-900">{{ optional($project->airline)->account_executive ?? '—' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <div class="text-sm text-gray-900">{{ $project->aircraftType->name ?? '—' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                            <div class="text-sm text-gray-900">{{ $project->number_of_aircraft ?? '—' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                            @if($project->designStatus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $project->designStatus->status }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                            @if($project->commercialStatus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $project->commercialStatus->status }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                            @if($project->trashed())
                                <button wire:click="restore({{ $project->id }})" 
                                        class="text-green-600 hover:text-green-900 font-medium transition-colors duration-200">
                                    Restore
                                </button>
                            @else
                                <button wire:click="edit({{ $project->id }})" 
                                        class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                    Edit
                                </button>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                            @if($project->trashed())
                                <button wire:click="forceDelete({{ $project->id }})" 
                                        class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200" 
                                        onclick="return confirm('Are you sure you want to permanently delete this project? This action cannot be undone.')">
                                    Delete Permanently
                                </button>
                            @else
                                <button wire:click="delete({{ $project->id }})" 
                                        class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200" 
                                        onclick="return confirm('Are you sure you want to delete this project?')">
                                    Delete
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                            No projects found. Add your first project above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-box>
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
</x-table-container>
