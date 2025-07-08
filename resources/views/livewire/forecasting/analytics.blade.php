<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Forecasting Analytics</h1>
        <p class="text-gray-600">Visual representation of revenue forecasts and trends with individual chart filters</p>
    </div>

    <!-- Charts with Individual Filters -->
    <div class="space-y-8">
        
        <!-- 5-Year Revenue Forecast Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">5-Year Revenue Forecast (Baseline + Conservative + Optimistic)</h3>
            
            <div class="flex gap-6">
                <!-- Forecast Chart Filters (Left Side) -->
                <div class="w-80 flex-shrink-0">
                    <div class="bg-gray-50 rounded-lg p-4 sticky top-4">
                        <h4 class="font-medium text-gray-900 mb-4">Filters</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Airline</label>
                                <select wire:model.live="forecastAirline" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Airlines</option>
                                    @foreach($airlines as $airline)
                                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                                <select wire:model.live="forecastRegion" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Regions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region }}">{{ $region }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person</label>
                                <select wire:model.live="forecastUser" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">All Sales People</option>
                                    @foreach($salesUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($forecastAirline || $forecastRegion || $forecastUser)
                            <div class="pt-2">
                                <button wire:click="clearForecastFilters" class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                    Clear Filters
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Chart Area (Right Side) -->
                <div class="flex-1 h-[2000px]" x-data="forecastChart()" x-init="initChart()">
                    <canvas id="yearlyRevenueChart" wire:key="yearly-{{ $forecastAirline }}-{{ $forecastRegion }}-{{ $forecastUser }}"></canvas>
                </div>
            </div>
        </div>

        <!-- Row with Region and Type Charts Side by Side -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Revenue by Region Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Revenue by Region</h3>
                
                <div class="flex gap-4">
                    <!-- Region Chart Filters (Left Side) -->
                    <div class="w-64 flex-shrink-0">
                        <div class="bg-gray-50 rounded-lg p-4 sticky top-4">
                            <h4 class="font-medium text-gray-900 mb-4">Filters</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                    <select wire:model.live="regionYear" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        @foreach($yearRange as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Airline</label>
                                    <select wire:model.live="regionAirline" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Airlines</option>
                                        @foreach($airlines as $airline)
                                            <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person</label>
                                    <select wire:model.live="regionUser" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Sales People</option>
                                        @foreach($salesUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($regionAirline || $regionUser)
                                <div class="pt-2">
                                    <button wire:click="clearRegionFilters" class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                        Clear Filters
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Area (Right Side) -->
                    <div class="flex-1 h-[2000px]" x-data="regionChart()" x-init="initChart()">
                        <canvas id="revenueByRegionChart" wire:key="region-{{ $regionYear }}-{{ $regionAirline }}-{{ $regionUser }}"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue by Opportunity Type Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Revenue by Opportunity Type</h3>
                
                <div class="flex gap-4">
                    <!-- Type Chart Filters (Left Side) -->
                    <div class="w-64 flex-shrink-0">
                        <div class="bg-gray-50 rounded-lg p-4 sticky top-4">
                            <h4 class="font-medium text-gray-900 mb-4">Filters</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                    <select wire:model.live="typeYear" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        @foreach($yearRange as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Airline</label>
                                    <select wire:model.live="typeAirline" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Airlines</option>
                                        @foreach($airlines as $airline)
                                            <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                                    <select wire:model.live="typeRegion" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Regions</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region }}">{{ $region }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sales Person</label>
                                    <select wire:model.live="typeUser" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Sales People</option>
                                        @foreach($salesUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($typeAirline || $typeRegion || $typeUser)
                                <div class="pt-2">
                                    <button wire:click="clearTypeFilters" class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                                        Clear Filters
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Area (Right Side) -->
                    <div class="flex-1 h-[2000px]" x-data="typeChart()" x-init="initChart()">
                        <canvas id="revenueByTypeChart" wire:key="type-{{ $typeYear }}-{{ $typeAirline }}-{{ $typeRegion }}-{{ $typeUser }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function forecastChart() {
            return {
                chart: null,
                chartData: @json($forecastData),

                initChart() {
                    this.createChart();
                    
                    this.$wire.on('forecastUpdated', async () => {
                        await this.updateChart();
                    });
                },

                async updateChart() {
                    try {
                        const newData = await this.$wire.getForecastDataForJs();
                        this.chartData = newData;
                        this.createChart();
                    } catch (error) {
                        console.error('Error updating forecast chart:', error);
                    }
                },

                createChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const ctx = document.getElementById('yearlyRevenueChart');
                    if (ctx) {
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(this.chartData.yearlyRevenue),
                                datasets: [
                                    {
                                        label: 'Baseline (High Probability ≥70%)',
                                        data: Object.values(this.chartData.yearlyRevenue).map(d => d.baseline),
                                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                        borderColor: 'rgb(34, 197, 94)',
                                        borderWidth: 2
                                    },
                                    {
                                        label: 'Conservative (High + Medium ≥40%)',
                                        data: Object.values(this.chartData.yearlyRevenue).map(d => d.conservative),
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2
                                    },
                                    {
                                        label: 'Optimistic (All Opportunities)',
                                        data: Object.values(this.chartData.yearlyRevenue).map(d => d.optimistic),
                                        backgroundColor: 'rgba(147, 51, 234, 0.8)',
                                        borderColor: 'rgb(147, 51, 234)',
                                        borderWidth: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Revenue projections for next 5 years',
                                        font: { size: 18 }
                                    },
                                    legend: {
                                        position: 'top',
                                        labels: { font: { size: 14 } }
                                    }
                                },
                                scales: {
                                    x: {
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Year',
                                            font: { size: 16 }
                                        },
                                        ticks: { font: { size: 14 } }
                                    },
                                    y: {
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Revenue ($)',
                                            font: { size: 16 }
                                        },
                                        beginAtZero: true,
                                        ticks: {
                                            font: { size: 14 },
                                            callback: function(value) {
                                                return '$' + value.toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }
        }

        function regionChart() {
            return {
                chart: null,
                chartData: @json($regionData),

                initChart() {
                    this.createChart();
                    
                    this.$wire.on('regionUpdated', async () => {
                        await this.updateChart();
                    });
                },

                async updateChart() {
                    try {
                        const newData = await this.$wire.getRegionDataForJs();
                        this.chartData = newData;
                        this.createChart();
                    } catch (error) {
                        console.error('Error updating region chart:', error);
                    }
                },

                createChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const ctx = document.getElementById('revenueByRegionChart');
                    if (ctx) {
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: Object.keys(this.chartData.revenueByRegion),
                                datasets: [{
                                    label: 'Revenue',
                                    data: Object.values(this.chartData.revenueByRegion),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.8)',
                                        'rgba(54, 162, 235, 0.8)',
                                        'rgba(255, 205, 86, 0.8)',
                                        'rgba(75, 192, 192, 0.8)',
                                        'rgba(153, 102, 255, 0.8)',
                                        'rgba(255, 159, 64, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 205, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Revenue breakdown by region',
                                        font: { size: 18 }
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        ticks: { font: { size: 14 } }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            font: { size: 14 },
                                            callback: function(value) {
                                                return '$' + value.toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }
        }

        function typeChart() {
            return {
                chart: null,
                chartData: @json($typeData),

                initChart() {
                    this.createChart();
                    
                    this.$wire.on('typeUpdated', async () => {
                        await this.updateChart();
                    });
                },

                async updateChart() {
                    try {
                        const newData = await this.$wire.getTypeDataForJs();
                        this.chartData = newData;
                        this.createChart();
                    } catch (error) {
                        console.error('Error updating type chart:', error);
                    }
                },

                createChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const ctx = document.getElementById('revenueByTypeChart');
                    if (ctx) {
                        this.chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: Object.keys(this.chartData.revenueByType).map(t => t.charAt(0).toUpperCase() + t.slice(1)),
                                datasets: [{
                                    data: Object.values(this.chartData.revenueByType),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.8)',
                                        'rgba(54, 162, 235, 0.8)',
                                        'rgba(255, 205, 86, 0.8)',
                                        'rgba(75, 192, 192, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 205, 86, 1)',
                                        'rgba(75, 192, 192, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Revenue breakdown by opportunity type',
                                        font: { size: 18 }
                                    },
                                    legend: {
                                        position: 'right',
                                        labels: { font: { size: 14 } }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                label += '$' + context.parsed.toLocaleString();
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }
        }
    </script>
</div>