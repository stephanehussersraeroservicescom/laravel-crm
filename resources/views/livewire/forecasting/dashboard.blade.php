<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Forecasting Dashboard</h1>
        <p class="text-gray-600">Strategic revenue forecasting and market analysis</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Year Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select wire:model.live="selectedYear" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    @foreach($yearRange as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Airline Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Airline</label>
                <select wire:model.live="selectedAirline" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Airlines</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Region Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                <select wire:model.live="selectedRegion" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sales User Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person</label>
                <select wire:model.live="selectedUser" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Sales People</option>
                    @foreach($salesUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Clear Filters Button -->
        @if($selectedAirline || $selectedRegion || $selectedUser)
        <div class="mt-4 flex justify-end">
            <button wire:click="clearFilters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                Clear Filters
            </button>
        </div>
        @endif
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Baseline Revenue -->
        <div class="bg-green-50 rounded-lg p-6 border border-green-200">
            <h3 class="text-lg font-medium text-green-800 mb-2">Baseline Revenue</h3>
            <p class="text-3xl font-bold text-green-900">${{ number_format($forecastData['baselineRevenue']) }}</p>
            <p class="text-sm text-green-600 mt-1">High Probability (≥70%)</p>
        </div>

        <!-- Conservative Revenue -->
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
            <h3 class="text-lg font-medium text-blue-800 mb-2">Conservative</h3>
            <p class="text-3xl font-bold text-blue-900">${{ number_format($forecastData['conservativeRevenue']) }}</p>
            <p class="text-sm text-blue-600 mt-1">High + Medium (≥40%)</p>
        </div>

        <!-- Optimistic Revenue -->
        <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
            <h3 class="text-lg font-medium text-purple-800 mb-2">Optimistic</h3>
            <p class="text-3xl font-bold text-purple-900">${{ number_format($forecastData['optimisticRevenue']) }}</p>
            <p class="text-sm text-purple-600 mt-1">All Probabilities</p>
        </div>

        <!-- Total Opportunities -->
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-2">Opportunities</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $forecastData['totalOpportunities'] }}</p>
            <p class="text-sm text-gray-600 mt-1">Active for {{ $selectedYear }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue by Type -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Opportunity Type</h3>
            <div class="space-y-3">
                @foreach($forecastData['revenueByType'] as $type => $revenue)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 capitalize">{{ $type }}</span>
                        <span class="font-medium">${{ number_format($revenue) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Revenue by Region -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Region</h3>
            <div class="space-y-3">
                @foreach($forecastData['revenueByRegion'] as $region => $revenue)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $region }}</span>
                        <span class="font-medium">${{ number_format($revenue) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Revenue by Linefit/Retrofit -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Project Type</h3>
            <div class="space-y-3">
                @foreach($forecastData['revenueByLinefitRetrofit'] as $type => $revenue)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 capitalize">{{ $type }}</span>
                        <span class="font-medium">${{ number_format($revenue) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Revenue by Sales Person (only show if no user filter is applied) -->
    @if(!$selectedUser && count($forecastData['revenueByUser']) > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Sales Person</h3>
        <div class="space-y-3">
            @foreach(array_slice($forecastData['revenueByUser'], 0, 10, true) as $userName => $revenue)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ $userName }}</span>
                    <span class="font-medium">${{ number_format($revenue) }}</span>
                </div>
            @endforeach
            @if(count($forecastData['revenueByUser']) > 10)
                <div class="text-sm text-gray-500 italic pt-2 border-t">
                    And {{ count($forecastData['revenueByUser']) - 10 }} more...
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Real Data Summary -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-medium text-blue-800 mb-4">📊 Real Forecasting Data Summary</h3>
        @php
            $totalOpps = App\Models\Opportunity::whereHas('project', function($q) {
                $q->whereNotNull('expected_start_year');
            })->count();
            $avgDuration = App\Models\Project::whereNotNull('project_lifecycle_duration')->avg('project_lifecycle_duration');
            $linefitCount = App\Models\Opportunity::whereHas('project', function($q) {
                $q->where('linefit_retrofit', 'linefit');
            })->count();
            $retrofitCount = App\Models\Opportunity::whereHas('project', function($q) {
                $q->where('linefit_retrofit', 'retrofit');
            })->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="font-medium text-blue-900">Total Opportunities</p>
                <p class="text-blue-700">{{ $totalOpps }} with forecasting data</p>
            </div>
            <div>
                <p class="font-medium text-blue-900">Average Duration</p>
                <p class="text-blue-700">{{ round($avgDuration, 1) }} years</p>
            </div>
            <div>
                <p class="font-medium text-blue-900">Linefit Projects</p>
                <p class="text-blue-700">{{ $linefitCount }} opportunities</p>
            </div>
            <div>
                <p class="font-medium text-blue-900">Retrofit Projects</p>
                <p class="text-blue-700">{{ $retrofitCount }} opportunities</p>
            </div>
        </div>
    </div>
</div>
