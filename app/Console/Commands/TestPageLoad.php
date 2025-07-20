<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Opportunity;
use App\Services\CachedDataService;

class TestPageLoad extends Command
{
    protected $signature = 'test:pageload {--clear-cache : Clear cache before testing}';
    protected $description = 'Simulate page loads to test query optimization';

    public function handle()
    {
        if ($this->option('clear-cache')) {
            Cache::flush();
            $this->info('Cache cleared.');
        }

        $this->info('🔍 Testing Page Load Query Counts');
        $this->newLine();

        // Test 1: Projects Page (ProjectsTable component)
        $this->testProjectsPage();
        
        // Test 2: Opportunities Page (OpportunityManagement component)
        $this->testOpportunitiesPage();
        
        // Test 3: Cached data performance over multiple requests
        $this->testMultipleRequests();
    }

    private function testProjectsPage()
    {
        $this->info('📊 Projects Page Simulation');
        $this->line(str_repeat('=', 50));

        DB::enableQueryLog();
        
        // Simulate ProjectsTable render method
        $projects = Project::with(['airline', 'aircraftType', 'designStatus', 'commercialStatus', 'owner'])
            ->limit(10)
            ->get();
            
        // Load dropdown data using cached service
        $airlines = CachedDataService::getAirlines();
        $aircraftTypes = CachedDataService::getAircraftTypes();
        $statuses = CachedDataService::getStatuses();
        $salesUsers = CachedDataService::getSalesUsers();
        
        // Simulate accessing relationship data in view
        foreach ($projects as $project) {
            $project->airline?->name;
            $project->aircraftType?->name;
            $project->designStatus?->status;
            $project->owner?->name;
        }
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $this->line(sprintf('  Total queries: %d', count($queries)));
        $this->line(sprintf('  Projects loaded: %d', $projects->count()));
        $this->line(sprintf('  Airlines cached: %d', $airlines->count()));
        $this->line(sprintf('  Aircraft types cached: %d', $aircraftTypes->count()));
        $this->newLine();
    }

    private function testOpportunitiesPage()
    {
        $this->info('📊 Opportunities Page Simulation');
        $this->line(str_repeat('=', 50));

        DB::enableQueryLog();
        
        // Simulate OpportunityManagement render method
        $opportunities = Opportunity::with(['project.airline', 'project.aircraftType', 'certificationStatus', 'assignedTo'])
            ->limit(10)
            ->get();
            
        // Load dropdown data using cached service
        $projects = Project::with(['airline', 'aircraftType'])
            ->select('projects.*')
            ->distinct()
            ->orderBy('name')
            ->limit(20)
            ->get();
            
        $airlines = CachedDataService::getAirlines();
        $aircraftTypes = CachedDataService::getAircraftTypes();
        
        // Simulate accessing relationship data in view
        foreach ($opportunities as $opportunity) {
            $opportunity->name;
            $opportunity->project?->name;
            $opportunity->project?->airline?->name;
            $opportunity->assignedTo?->name;
        }
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $this->line(sprintf('  Total queries: %d', count($queries)));
        $this->line(sprintf('  Opportunities loaded: %d', $opportunities->count()));
        $this->line(sprintf('  Projects loaded: %d', $projects->count()));
        $this->newLine();
    }

    private function testMultipleRequests()
    {
        $this->info('📊 Multiple Request Simulation (Cache Benefits)');
        $this->line(str_repeat('=', 50));

        // Simulate 5 different users loading pages
        $totalQueries = 0;
        
        for ($i = 1; $i <= 5; $i++) {
            DB::enableQueryLog();
            
            // Each user loads dropdown data
            CachedDataService::getAirlines();
            CachedDataService::getAircraftTypes();
            CachedDataService::getStatuses();
            CachedDataService::getSalesUsers();
            
            $userQueries = count(DB::getQueryLog());
            $totalQueries += $userQueries;
            DB::disableQueryLog();
            
            $this->line(sprintf('  User %d queries: %d', $i, $userQueries));
        }
        
        $this->newLine();
        $this->line(sprintf('  Total queries for 5 users: %d', $totalQueries));
        $this->line(sprintf('  Without cache would be: %d+ queries', 5 * 4)); // 4 services * 5 users
        $this->line(sprintf('  Cache efficiency: %.1f%%', (1 - ($totalQueries / 20)) * 100));
    }
}