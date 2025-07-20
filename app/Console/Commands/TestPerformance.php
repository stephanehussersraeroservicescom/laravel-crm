<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CachedDataService;
use App\Models\Airline;
use App\Models\AircraftType;
use App\Models\Status;
use App\Models\User;
use App\Models\Subcontractor;
use App\Models\Project;
use App\Models\Opportunity;

class TestPerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:performance {--clear-cache : Clear cache before testing}';

    /**
     * The console command description.
     */
    protected $description = 'Test database query performance and caching efficiency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear-cache')) {
            Cache::flush();
            $this->info('Cache cleared.');
        }

        $this->info('🚀 Starting Performance Tests');
        $this->newLine();

        // Test 1: Cached Data Service Performance
        $this->testCachedDataService();
        
        // Test 2: Query Count Comparison
        $this->testQueryCount();
        
        // Test 3: Cache Hit Rate
        $this->testCacheHitRate();
        
        // Test 4: Model Observer Cache Invalidation
        $this->testCacheInvalidation();

        $this->newLine();
        $this->info('✅ Performance tests completed!');
    }

    private function testCachedDataService()
    {
        $this->info('📊 Testing CachedDataService Performance');
        $this->line('=' . str_repeat('=', 50));

        $tests = [
            'Airlines' => fn() => CachedDataService::getAirlines(),
            'Aircraft Types' => fn() => CachedDataService::getAircraftTypes(),
            'Statuses' => fn() => CachedDataService::getStatuses(),
            'Sales Users' => fn() => CachedDataService::getSalesUsers(),
            'Subcontractors' => fn() => CachedDataService::getSubcontractors(),
        ];

        foreach ($tests as $name => $callback) {
            // First call (cache miss)
            $start = microtime(true);
            DB::enableQueryLog();
            $result1 = $callback();
            $queries1 = count(DB::getQueryLog());
            DB::disableQueryLog();
            $time1 = (microtime(true) - $start) * 1000;

            // Second call (cache hit)
            $start = microtime(true);
            DB::enableQueryLog();
            $result2 = $callback();
            $queries2 = count(DB::getQueryLog());
            DB::disableQueryLog();
            $time2 = (microtime(true) - $start) * 1000;

            $this->line(sprintf(
                '  %s: %d records | 1st: %.2fms (%d queries) | 2nd: %.2fms (%d queries)',
                str_pad($name, 15),
                $result1->count(),
                $time1,
                $queries1,
                $time2,
                $queries2
            ));
        }
        $this->newLine();
    }

    private function testQueryCount()
    {
        $this->info('🔍 Testing Query Count for Common Operations');
        $this->line('=' . str_repeat('=', 50));

        // Test loading projects with relationships (simulating ProjectsTable)
        DB::enableQueryLog();
        $projects = Project::with(['airline', 'aircraftType', 'designStatus', 'commercialStatus', 'owner'])
            ->limit(10)
            ->get();
        
        // Access relationships to trigger any N+1 queries
        foreach ($projects as $project) {
            $name = $project->name;
            $airline = $project->airline?->name;
            $aircraft = $project->aircraftType?->name;
            $status = $project->designStatus?->status;
        }
        
        $projectQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Test loading opportunities with relationships (simulating OpportunityManagement)
        DB::enableQueryLog();
        $opportunities = Opportunity::with(['project.airline', 'project.aircraftType', 'certificationStatus', 'assignedTo'])
            ->limit(10)
            ->get();
            
        foreach ($opportunities as $opportunity) {
            $name = $opportunity->name;
            $project = $opportunity->project?->name;
            $airline = $opportunity->project?->airline?->name;
        }
        
        $opportunityQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->line(sprintf('  Projects (10 records): %d queries', $projectQueries));
        $this->line(sprintf('  Opportunities (10 records): %d queries', $opportunityQueries));
        $this->newLine();
    }

    private function testCacheHitRate()
    {
        $this->info('💾 Testing Cache Hit Rate');
        $this->line('=' . str_repeat('=', 50));

        // Clear query log
        DB::enableQueryLog();
        
        // Multiple calls to cached services
        for ($i = 0; $i < 5; $i++) {
            CachedDataService::getAirlines();
            CachedDataService::getAircraftTypes();
            CachedDataService::getStatuses();
        }
        
        $totalQueries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $expectedQueries = 3; // Only first call of each should hit database
        $cacheEfficiency = (1 - ($totalQueries / 15)) * 100; // 15 total calls made

        $this->line(sprintf('  Total queries for 15 service calls: %d', $totalQueries));
        $this->line(sprintf('  Expected queries (cache working): %d', $expectedQueries));
        $this->line(sprintf('  Cache efficiency: %.1f%%', $cacheEfficiency));
        $this->newLine();
    }

    private function testCacheInvalidation()
    {
        $this->info('🔄 Testing Cache Invalidation');
        $this->line('=' . str_repeat('=', 50));

        // Test airline cache invalidation
        $airline = Airline::first();
        if ($airline) {
            // Load data into cache
            CachedDataService::getAirlines();
            
            // Check if cached
            $cached = Cache::has('airlines_dropdown');
            $this->line(sprintf('  Airlines cached before update: %s', $cached ? 'Yes' : 'No'));
            
            // Update airline (should trigger observer and clear cache)
            $airline->update(['name' => $airline->name . ' Updated']);
            
            // Check if cache was cleared
            $cachedAfter = Cache::has('airlines_dropdown');
            $this->line(sprintf('  Airlines cached after update: %s', $cachedAfter ? 'Yes' : 'No'));
            
            // Restore original name
            $airline->update(['name' => str_replace(' Updated', '', $airline->name)]);
        }
        $this->newLine();
    }
}