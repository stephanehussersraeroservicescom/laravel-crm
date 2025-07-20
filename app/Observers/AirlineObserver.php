<?php

namespace App\Observers;

use App\Models\Airline;
use App\Services\CachedDataService;

class AirlineObserver
{
    /**
     * Handle the Airline "created" event.
     */
    public function created(Airline $airline): void
    {
        CachedDataService::clearSpecificCache('airlines');
    }

    /**
     * Handle the Airline "updated" event.
     */
    public function updated(Airline $airline): void
    {
        CachedDataService::clearSpecificCache('airlines');
    }

    /**
     * Handle the Airline "deleted" event.
     */
    public function deleted(Airline $airline): void
    {
        CachedDataService::clearSpecificCache('airlines');
    }

    /**
     * Handle the Airline "restored" event.
     */
    public function restored(Airline $airline): void
    {
        CachedDataService::clearSpecificCache('airlines');
    }

    /**
     * Handle the Airline "force deleted" event.
     */
    public function forceDeleted(Airline $airline): void
    {
        CachedDataService::clearSpecificCache('airlines');
    }
}