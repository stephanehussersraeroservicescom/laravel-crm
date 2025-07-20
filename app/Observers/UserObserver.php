<?php

namespace App\Observers;

use App\Models\User;
use App\Services\CachedDataService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if (in_array($user->role, ['sales', 'manager'])) {
            CachedDataService::clearSpecificCache('sales_users');
            CachedDataService::clearSpecificCache('sales_manager_users');
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Clear cache if role changed or if user is/was sales/manager
        if ($user->isDirty('role') || 
            in_array($user->role, ['sales', 'manager']) || 
            in_array($user->getOriginal('role'), ['sales', 'manager'])) {
            CachedDataService::clearSpecificCache('sales_users');
            CachedDataService::clearSpecificCache('sales_manager_users');
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (in_array($user->role, ['sales', 'manager'])) {
            CachedDataService::clearSpecificCache('sales_users');
            CachedDataService::clearSpecificCache('sales_manager_users');
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        if (in_array($user->role, ['sales', 'manager'])) {
            CachedDataService::clearSpecificCache('sales_users');
            CachedDataService::clearSpecificCache('sales_manager_users');
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        if (in_array($user->role, ['sales', 'manager'])) {
            CachedDataService::clearSpecificCache('sales_users');
            CachedDataService::clearSpecificCache('sales_manager_users');
        }
    }
}