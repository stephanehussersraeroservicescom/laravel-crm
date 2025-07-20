<?php

namespace App\Observers;

use App\Models\AircraftType;
use App\Services\CachedDataService;

class AircraftTypeObserver
{
    /**
     * Handle the AircraftType "created" event.
     */
    public function created(AircraftType $aircraftType): void
    {
        CachedDataService::clearSpecificCache('aircraft_types');
    }

    /**
     * Handle the AircraftType "updated" event.
     */
    public function updated(AircraftType $aircraftType): void
    {
        CachedDataService::clearSpecificCache('aircraft_types');
    }

    /**
     * Handle the AircraftType "deleted" event.
     */
    public function deleted(AircraftType $aircraftType): void
    {
        CachedDataService::clearSpecificCache('aircraft_types');
    }

    /**
     * Handle the AircraftType "restored" event.
     */
    public function restored(AircraftType $aircraftType): void
    {
        CachedDataService::clearSpecificCache('aircraft_types');
    }

    /**
     * Handle the AircraftType "force deleted" event.
     */
    public function forceDeleted(AircraftType $aircraftType): void
    {
        CachedDataService::clearSpecificCache('aircraft_types');
    }
}