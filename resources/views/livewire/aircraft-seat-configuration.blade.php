<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Aircraft Seat Configuration</h1>
        <button wire:click="openCreateModal" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Configuration
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
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
                    @foreach($aircraftTypes as $aircraftType)
                        <option value="{{ $aircraftType->id }}">{{ $aircraftType->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Version Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                <select wire:model.live="filterVersion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Versions</option>
                    @foreach($versions as $version)
                        <option value="{{ $version }}">{{ $version }}</option>
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

        <div class="flex justify-end mt-4">
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Configurations Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('airline_id')">
                            Airline
                            @if($sortBy === 'airline_id')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('aircraft_type_id')">
                            Aircraft Type
                            @if($sortBy === 'aircraft_type_id')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('version')">
                            Version
                            @if($sortBy === 'version')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            First Class
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Business
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Premium Economy
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Economy
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('total_seats')">
                            Total
                            @if($sortBy === 'total_seats')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Source
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Confidence
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Updated
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($configurations as $config)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $config->airline->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $config->aircraftType->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $config->version }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $config->first_class_seats }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $config->business_class_seats }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $config->premium_economy_seats }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $config->economy_seats }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-bold text-blue-600">{{ $config->total_seats }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $config->data_source === 'manufacturer_baseline' ? 'bg-green-100 text-green-800' : 
                                       ($config->data_source === 'manual' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $config->data_source)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-2 w-16 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-{{ $config->confidence_score >= 0.8 ? 'green' : ($config->confidence_score >= 0.5 ? 'yellow' : 'red') }}-500 rounded-full" 
                                             style="width: {{ ($config->confidence_score * 100) }}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900">{{ round($config->confidence_score * 100) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $config->updated_at->format('M j, Y') }}
                                @if($config->updatedBy)
                                    <div class="text-xs text-gray-500">by {{ $config->updatedBy->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="performAiLookup({{ $config->id }})" 
                                        class="text-purple-600 hover:text-purple-900 transition-colors"
                                        title="AI Lookup & Update"
                                        wire:loading.attr="disabled"
                                        wire:target="performAiLookup({{ $config->id }})">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" 
                                         wire:loading.remove wire:target="performAiLookup({{ $config->id }})">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <svg class="w-4 h-4 inline-block animate-spin" fill="none" viewBox="0 0 24 24" 
                                         wire:loading wire:target="performAiLookup({{ $config->id }})">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                                <button wire:click="openEditModal({{ $config->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $config->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this configuration?')"
                                        class="text-red-600 hover:text-red-900 transition-colors">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-12 text-center text-gray-500">
                                No seat configurations found.
                                <button wire:click="openCreateModal" class="text-blue-600 hover:text-blue-800 ml-1">
                                    Create your first configuration
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $configurations->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>
                
                <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $modalMode === 'create' ? 'Create' : 'Edit' }} Seat Configuration
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Airline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Airline *</label>
                                <select wire:model="airline_id" required 
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Aircraft Type *</label>
                                <select wire:model="aircraft_type_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Aircraft Type</option>
                                    @foreach($aircraftTypes as $aircraftType)
                                        <option value="{{ $aircraftType->id }}">{{ $aircraftType->name }}</option>
                                    @endforeach
                                </select>
                                @error('aircraft_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Version -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Version *</label>
                                <input type="text" wire:model="version" required 
                                       placeholder="e.g., Standard, High-Density, Long-Range"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('version') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Data Source -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data Source</label>
                                <select wire:model="data_source" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="manual">Manual</option>
                                    <option value="manufacturer_baseline">Manufacturer Baseline</option>
                                    <option value="seatguru">SeatGuru</option>
                                    <option value="airline_website">Airline Website</option>
                                </select>
                                @error('data_source') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Seat Counts -->
                        <div class="border-t pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Seat Counts by Class</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- First Class -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Class</label>
                                    <input type="number" wire:model="first_class_seats" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('first_class_seats') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Business Class -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Class</label>
                                    <input type="number" wire:model="business_class_seats" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('business_class_seats') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Premium Economy -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Premium Economy</label>
                                    <input type="number" wire:model="premium_economy_seats" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('premium_economy_seats') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Economy -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Economy</label>
                                    <input type="number" wire:model="economy_seats" min="0" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('economy_seats') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Total Display -->
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-600">
                                    <strong>Total Seats:</strong> 
                                    <span class="text-lg font-bold text-blue-600">
                                        {{ ($first_class_seats ?? 0) + ($business_class_seats ?? 0) + ($premium_economy_seats ?? 0) + ($economy_seats ?? 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Confidence Score -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confidence Score</label>
                            <div class="flex items-center space-x-4">
                                <input type="range" wire:model="confidence_score" min="0" max="1" step="0.1" 
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                <span class="text-sm font-medium text-gray-900 min-w-0">{{ round(($confidence_score ?? 1) * 100) }}%</span>
                            </div>
                            @error('confidence_score') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
