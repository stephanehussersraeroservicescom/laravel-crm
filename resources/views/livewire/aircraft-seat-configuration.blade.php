<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Aircraft Seat Configurations">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.secondary-button wire:click="openAiLookup" class="mr-2">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                AI Lookup
            </x-atomic.atoms.buttons.secondary-button>
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Configuration
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />
    <x-atomic.atoms.feedback.flash-message type="error" :message="session('error')" />

    <!-- Search and Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                label="Search"
                placeholder="Search airline, aircraft, cabin..."
                wire:model.live.debounce.300ms="search"
            />

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

            <!-- Cabin Class Filter -->
            <x-atomic.molecules.forms.form-field-group label="Cabin Class">
                <x-atomic.atoms.forms.form-select wire:model.live="filterCabinClass">
                    <option value="">All Cabin Classes</option>
                    @foreach($cabinClasses as $class)
                        <option value="{{ $class->value }}">{{ str_replace('_', ' ', ucwords($class->value, '_')) }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </div>

        <div class="flex justify-between items-center">
            <!-- Per Page -->
            <x-atomic.molecules.forms.form-field-group label="Show">
                <x-atomic.atoms.forms.form-select wire:model.live="perPage" class="w-32">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
        </div>
    </div>

    <!-- Configurations Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('airline_id')">
                            Airline
                            @if($sortBy === 'airline_id')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('aircraft_type_id')">
                            Aircraft Type
                            @if($sortBy === 'aircraft_type_id')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cabin Configuration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Source
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                            wire:click="sortBy('last_verified_at')">
                            Last Verified
                            @if($sortBy === 'last_verified_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($configurations as $configuration)
                        <tr class="hover:bg-gray-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $configuration->airline->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $configuration->aircraftType->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ str_replace('_', ' ', ucwords($configuration->cabin_class, '_')) }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-semibold">{{ $configuration->total_seats }}</span> seats
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $configuration->data_source)) }}</div>
                                    <div class="flex items-center">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $configuration->confidence_score * 100 }}%"></div>
                                        </div>
                                        <span class="ml-2 text-xs text-gray-600">{{ number_format($configuration->confidence_score * 100, 0) }}%</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $configuration->last_verified_at ? $configuration->last_verified_at->format('M j, Y') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <x-atomic.atoms.buttons.action-link 
                                    variant="primary" 
                                    wire:click="openEditModal({{ $configuration->id }})"
                                >
                                    Edit
                                </x-atomic.atoms.buttons.action-link>
                                <x-atomic.atoms.buttons.action-link 
                                    variant="danger" 
                                    wire:click="delete({{ $configuration->id }})" 
                                    onclick="return confirm('Are you sure you want to delete this configuration?')"
                                >
                                    Delete
                                </x-atomic.atoms.buttons.action-link>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No seat configurations found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openCreateModal">
                                    Create your first configuration
                                </x-atomic.atoms.buttons.action-link>
                                or use
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="openAiLookup">
                                    AI Lookup
                                </x-atomic.atoms.buttons.action-link>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $configurations->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $modalMode === 'create' ? 'Create New Configuration' : 'Edit Configuration' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <form wire:submit.prevent="save" class="mt-4 space-y-4">
                        <!-- Airline -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Airline" 
                            name="airline_id" 
                            :required="true"
                        >
                            <x-atomic.atoms.forms.form-select wire:model="airline_id" required>
                                <option value="">Select Airline</option>
                                @foreach($airlines as $airline)
                                    <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                @endforeach
                            </x-atomic.atoms.forms.form-select>
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Aircraft Type -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Aircraft Type" 
                            name="aircraft_type_id" 
                            :required="true"
                        >
                            <x-atomic.atoms.forms.form-select wire:model="aircraft_type_id" required>
                                <option value="">Select Aircraft Type</option>
                                @foreach($aircraftTypes as $aircraftType)
                                    <option value="{{ $aircraftType->id }}">{{ $aircraftType->name }}</option>
                                @endforeach
                            </x-atomic.atoms.forms.form-select>
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Cabin Class -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Cabin Class" 
                            name="cabin_class" 
                            :required="true"
                        >
                            <x-atomic.atoms.forms.form-select wire:model="cabin_class" required>
                                <option value="">Select Cabin Class</option>
                                @foreach($cabinClasses as $class)
                                    <option value="{{ $class->value }}">
                                        {{ str_replace('_', ' ', ucwords($class->value, '_')) }}
                                    </option>
                                @endforeach
                            </x-atomic.atoms.forms.form-select>
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Total Seats -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Total Seats" 
                            name="total_seats" 
                            :required="true"
                        >
                            <x-atomic.atoms.forms.form-input 
                                type="number" 
                                wire:model="total_seats" 
                                min="0" 
                                required
                            />
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Data Source -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Data Source" 
                            name="data_source"
                        >
                            <x-atomic.atoms.forms.form-input 
                                type="text" 
                                wire:model="data_source" 
                                placeholder="e.g., manual, seatguru, airline_website"
                            />
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Confidence Score -->
                        <x-atomic.molecules.forms.form-field-group 
                            label="Confidence Score" 
                            name="confidence_score" 
                            :required="true"
                        >
                            <x-atomic.atoms.forms.form-input 
                                type="number" 
                                wire:model="confidence_score" 
                                min="0" 
                                max="1" 
                                step="0.01" 
                                required
                            />
                            <div class="text-xs text-gray-500 mt-1">0 = No confidence, 1 = Full confidence</div>
                        </x-atomic.molecules.forms.form-field-group>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <x-atomic.atoms.buttons.secondary-button type="button" wire:click="closeModal">
                                Cancel
                            </x-atomic.atoms.buttons.secondary-button>
                            <x-atomic.atoms.buttons.primary-button type="submit">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Configuration
                            </x-atomic.atoms.buttons.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- AI Lookup Modal -->
    @if($showAiLookup)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            AI Seat Configuration Lookup
                        </h3>
                        <button wire:click="closeAiLookup" class="text-gray-400 hover:text-gray-600" @if($aiLookupInProgress) disabled @endif>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Select an airline and aircraft type to automatically look up seat configurations using AI.
                        </p>

                        <div class="space-y-4">
                            <!-- Airline Selection -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Airline" 
                                name="aiAirlineId" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="aiAirlineId" :disabled="$aiLookupInProgress">
                                    <option value="">Select Airline</option>
                                    @foreach($airlines as $airline)
                                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Aircraft Type Selection -->
                            <x-atomic.molecules.forms.form-field-group 
                                label="Aircraft Type" 
                                name="aiAircraftTypeId" 
                                :required="true"
                            >
                                <x-atomic.atoms.forms.form-select wire:model="aiAircraftTypeId" :disabled="$aiLookupInProgress">
                                    <option value="">Select Aircraft Type</option>
                                    @foreach($aircraftTypes as $aircraftType)
                                        <option value="{{ $aircraftType->id }}">{{ $aircraftType->name }}</option>
                                    @endforeach
                                </x-atomic.atoms.forms.form-select>
                            </x-atomic.molecules.forms.form-field-group>

                            <!-- Result/Status Display -->
                            @if($aiLookupResult)
                                <div class="p-4 rounded-lg {{ $aiLookupInProgress ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }}">
                                    @if($aiLookupInProgress)
                                        <div class="flex items-center">
                                            <svg class="animate-spin h-5 w-5 mr-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-blue-700">{{ $aiLookupResult }}</span>
                                        </div>
                                    @else
                                        <p class="text-gray-700">{{ $aiLookupResult }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-sm text-yellow-700">
                                        <p class="font-medium">Note:</p>
                                        <p>This will search multiple sources for seat configuration data. The process may take a few moments.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 mt-6 border-t">
                            <x-atomic.atoms.buttons.secondary-button wire:click="closeAiLookup" :disabled="$aiLookupInProgress">
                                Cancel
                            </x-atomic.atoms.buttons.secondary-button>
                            <x-atomic.atoms.buttons.primary-button wire:click="performAiLookup" :disabled="$aiLookupInProgress">
                                @if($aiLookupInProgress)
                                    <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Looking up...
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Lookup Seat Configuration
                                @endif
                            </x-atomic.atoms.buttons.primary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>