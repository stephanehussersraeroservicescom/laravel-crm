<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Opportunity Pipeline</h1>
        @can('create', App\Models\Opportunity::class)
            <button wire:click="openModal('create')" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Opportunity
            </button>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search opportunities...">
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select wire:model.live="filterProject" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="filterType" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cabin Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class</label>
                <select wire:model.live="filterCabinClass" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Classes</option>
                    @foreach($cabinClasses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Pipeline Board -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-7 gap-4">
            @foreach($statuses as $status => $label)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 min-h-96">
                    <!-- Column Header -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900">{{ $label }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $opportunitiesByStatus[$status]->count() }}
                            </span>
                        </div>
                    </div>

                    <!-- Opportunity Cards -->
                    <div class="p-2 space-y-2" 
                         ondrop="drop(event, '{{ $status }}')" 
                         ondragover="allowDrop(event)">
                        @foreach($opportunitiesByStatus[$status] as $opportunity)
                            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow cursor-move"
                                 draggable="true" 
                                 ondragstart="drag(event, {{ $opportunity->id }})">
                                
                                <!-- Opportunity Header -->
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $opportunity->name ?: $opportunity->type_display }}
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $opportunity->project->name }}
                                        </p>
                                    </div>
                                    <div class="ml-2 flex-shrink-0">
                                        <div class="flex space-x-1">
                                            @can('update', $opportunity)
                                                <button wire:click="openModal('edit', {{ $opportunity->id }})" 
                                                        class="text-gray-400 hover:text-blue-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            @endcan
                                            @can('delete', $opportunity)
                                                <button wire:click="deleteOpportunity({{ $opportunity->id }})" 
                                                        onclick="return confirm('Are you sure?')"
                                                        class="text-gray-400 hover:text-red-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <!-- Opportunity Details -->
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $opportunity->type === 'vertical' ? 'bg-blue-100 text-blue-800' : ($opportunity->type === 'panels' ? 'bg-green-100 text-green-800' : ($opportunity->type === 'covers' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $opportunity->type_display }}
                                        </span>
                                        <span class="text-gray-500">{{ $opportunity->cabin_class_display }}</span>
                                    </div>

                                    @if($opportunity->probability)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-500">Probability:</span>
                                            <span class="font-medium">{{ $opportunity->probability }}%</span>
                                        </div>
                                    @endif

                                    @if($opportunity->potential_value)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-500">Value:</span>
                                            <span class="font-medium text-green-600">${{ number_format($opportunity->potential_value) }}</span>
                                        </div>
                                    @endif

                                    @if($opportunity->team && $opportunity->team->mainSubcontractor)
                                        <div class="text-xs text-gray-500 truncate">
                                            Lead: {{ $opportunity->team->mainSubcontractor->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>
                
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="saveOpportunity">
                        <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">
                                    {{ $modalMode === 'create' ? 'Create New Opportunity' : 'Edit Opportunity' }}
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Project -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Project *</label>
                                    <select wire:model="opportunity.project_id" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('opportunity.project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select wire:model="opportunity.type" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($types as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('opportunity.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Cabin Class -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabin Class *</label>
                                    <select wire:model="opportunity.cabin_class" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Cabin Class</option>
                                        @foreach($cabinClasses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('opportunity.cabin_class') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select wire:model="opportunity.status"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('opportunity.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Probability -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Probability (%)</label>
                                    <input wire:model="opportunity.probability" type="number" min="0" max="100"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('opportunity.probability') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Potential Value -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Potential Value ($)</label>
                                    <input wire:model="opportunity.potential_value" type="number" min="0" step="0.01"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('opportunity.potential_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Name -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input wire:model="opportunity.name" type="text"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('opportunity.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea wire:model="opportunity.description" rows="3"
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('opportunity.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Comments -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                                    <textarea wire:model="opportunity.comments" rows="2"
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('opportunity.comments') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="button" wire:click="closeModal" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $modalMode === 'create' ? 'Create' : 'Update' }} Opportunity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev, opportunityId) {
    ev.dataTransfer.setData("opportunity_id", opportunityId);
}

function drop(ev, status) {
    ev.preventDefault();
    const opportunityId = ev.dataTransfer.getData("opportunity_id");
    @this.call('moveOpportunity', opportunityId, status);
}
</script>