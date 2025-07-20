<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\AircraftSeatConfiguration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        // Check if both airline and aircraft type are set
        if ($project->airline_id && $project->aircraft_type_id) {
            $this->createSeatConfiguration($project);
        }
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // If airline or aircraft type changed, check if we need to create a seat configuration
        if ($project->isDirty(['airline_id', 'aircraft_type_id']) && 
            $project->airline_id && 
            $project->aircraft_type_id) {
            
            // Check if configuration already exists
            $exists = AircraftSeatConfiguration::where('airline_id', $project->airline_id)
                ->where('aircraft_type_id', $project->aircraft_type_id)
                ->exists();
            
            if (!$exists) {
                $this->createSeatConfiguration($project);
            }
        }
    }

    /**
     * Create seat configuration for the project
     */
    private function createSeatConfiguration(Project $project)
    {
        try {
            // Load relationships
            $project->load(['airline', 'aircraftType']);
            
            // Check if configuration already exists
            $existingConfig = AircraftSeatConfiguration::where('airline_id', $project->airline_id)
                ->where('aircraft_type_id', $project->aircraft_type_id)
                ->first();
            
            if ($existingConfig) {
                Log::info("Seat configuration already exists for {$project->airline->name} - {$project->aircraftType->name}");
                return;
            }
            
            // Create a basic configuration first
            $configuration = AircraftSeatConfiguration::create([
                'airline_id' => $project->airline_id,
                'aircraft_type_id' => $project->aircraft_type_id,
                'version' => 'Standard',
                'first_class_seats' => 0,
                'business_class_seats' => 0,
                'premium_economy_seats' => 0,
                'economy_seats' => 0,
                'total_seats' => 0,
                'data_source' => 'pending_ai_lookup',
                'confidence_score' => 0,
            ]);
            
            Log::info("Created seat configuration for {$project->airline->name} - {$project->aircraftType->name}");
            
            // Run AI lookup asynchronously to populate the data
            $this->runAiLookup($project->airline, $project->aircraftType, $configuration);
            
        } catch (\Exception $e) {
            Log::error("Failed to create seat configuration for project {$project->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Run AI lookup to populate seat configuration data
     */
    private function runAiLookup($airline, $aircraftType, $configuration)
    {
        try {
            // Use partial names for better matching
            $airlineName = strtolower(explode(' ', $airline->name)[0]); // First word only
            $aircraftName = strtolower($aircraftType->name); // Keep original format
            
            // Run the AI population command
            $exitCode = Artisan::call('aircraft:populate-seats', [
                '--airline' => $airlineName,
                '--aircraft' => $aircraftName,
            ]);
            
            if ($exitCode === 0) {
                Log::info("AI successfully populated seat configuration for {$airline->name} - {$aircraftType->name}");
            } else {
                Log::warning("AI lookup failed for {$airline->name} - {$aircraftType->name}");
                
                // Update the configuration to indicate manual entry needed
                $configuration->update([
                    'data_source' => 'manual_required',
                    'confidence_score' => 0,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("AI lookup error: " . $e->getMessage());
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
