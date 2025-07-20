<?php

namespace App\Observers;

use App\Models\Subcontractor;
use App\Services\CachedDataService;

class SubcontractorObserver
{
    /**
     * Handle the Subcontractor "created" event.
     */
    public function created(Subcontractor $subcontractor): void
    {
        CachedDataService::clearSpecificCache('subcontractors');
    }

    /**
     * Handle the Subcontractor "updated" event.
     */
    public function updated(Subcontractor $subcontractor): void
    {
        CachedDataService::clearSpecificCache('subcontractors');
    }

    /**
     * Handle the Subcontractor "deleted" event.
     */
    public function deleted(Subcontractor $subcontractor): void
    {
        CachedDataService::clearSpecificCache('subcontractors');
    }

    /**
     * Handle the Subcontractor "restored" event.
     */
    public function restored(Subcontractor $subcontractor): void
    {
        CachedDataService::clearSpecificCache('subcontractors');
    }

    /**
     * Handle the Subcontractor "force deleted" event.
     */
    public function forceDeleted(Subcontractor $subcontractor): void
    {
        CachedDataService::clearSpecificCache('subcontractors');
    }
}