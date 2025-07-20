<?php

namespace App\Observers;

use App\Models\Status;
use App\Services\CachedDataService;

class StatusObserver
{
    /**
     * Handle the Status "created" event.
     */
    public function created(Status $status): void
    {
        CachedDataService::clearSpecificCache('statuses');
    }

    /**
     * Handle the Status "updated" event.
     */
    public function updated(Status $status): void
    {
        CachedDataService::clearSpecificCache('statuses');
    }

    /**
     * Handle the Status "deleted" event.
     */
    public function deleted(Status $status): void
    {
        CachedDataService::clearSpecificCache('statuses');
    }

    /**
     * Handle the Status "restored" event.
     */
    public function restored(Status $status): void
    {
        CachedDataService::clearSpecificCache('statuses');
    }

    /**
     * Handle the Status "force deleted" event.
     */
    public function forceDeleted(Status $status): void
    {
        CachedDataService::clearSpecificCache('statuses');
    }
}