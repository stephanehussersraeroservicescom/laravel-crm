<div>
    <div class="flex flex-col md:flex-row md:items-end md:gap-6 gap-4 mb-6">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Region</label>
            <select wire:model.live="region" class="rounded border-gray-300">
                <option value="">All</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}">{{ $region }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Account Executive</label>
            <select wire:model.live="accountExecutive" class="rounded border-gray-300">
                <option value="">All</option>
                @foreach($executives as $exec)
                    <option value="{{ $exec }}">{{ $exec }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Search</label>
            <input type="text" wire:model.live="search" placeholder="Project name..." class="rounded border-gray-300">
        </div>
        <div class="flex-1 text-right">
            <label class="block font-semibold text-gray-700 mb-1">Sort by:</label>
            <button wire:click="sortBy('region')" class="px-3 py-1 border rounded bg-white mr-1 {{ $sortField==='region'?'font-bold text-blue-700':'' }}">Region</button>
            <button wire:click="sortBy('name')" class="px-3 py-1 border rounded bg-white mr-1 {{ $sortField==='name'?'font-bold text-blue-700':'' }}">Airline</button>
            <button wire:click="sortBy('potential')" class="px-3 py-1 border rounded bg-white {{ $sortField==='potential'?'font-bold text-blue-700':'' }}">Potential</button>
        </div>
    </div>

  
    
    <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
        @forelse($projects as $project)
            <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col border border-blue-100 hover:shadow-xl transition-shadow">
                <div class="text-lg font-semibold mb-2">{{ $project->name }}</div>
                <div class="mb-1 text-sm text-gray-500">{{ $project->airline->name ?? 'N/A' }} <span class="text-xs text-gray-400">({{ $project->airline->region ?? '—' }})</span></div>
                <div class="mb-1 text-sm text-gray-500">
                    <span class="font-semibold">Account Exec:</span> {{ $project->airline->account_executive ?? '-' }}
                </div>
                <div class="mb-2 text-sm">
                    <span class="font-semibold">Aircraft:</span> {{ $project->aircraftType->name ?? '-' }}
                    <span class="font-semibold ml-2"># Aircraft:</span> {{ $project->number_of_aircraft }}
                </div>
                <div class="mb-2">
                    <span class="inline-block text-xs px-2 py-1 bg-blue-50 rounded border border-blue-200 mr-1">Verticals: {{ $project->verticalSurfaces->count() }}</span>
                    <span class="inline-block text-xs px-2 py-1 bg-blue-50 rounded border border-blue-200 mr-1">Panels: {{ $project->panels->count() }}</span>
                    <span class="inline-block text-xs px-2 py-1 bg-blue-50 rounded border border-blue-200">Covers: {{ $project->covers->count() }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="block text-green-700 font-bold text-xl">
                        € {{ number_format(rand(100_000,500_000), 0, '.', ' ') }}
                        {{-- Replace this with your own $project->potential when ready --}}
                    </span>
                    <a href="#" class="text-blue-600 font-semibold hover:underline">View</a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-gray-400 italic p-8 text-center">No projects found.</div>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $projects->links() }}
    </div>
</div>
