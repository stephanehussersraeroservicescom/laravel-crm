<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OpportunityPolicy
{
    /**
     * Determine whether the user can view any opportunities.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_opportunities');
    }

    /**
     * Determine whether the user can view the opportunity.
     */
    public function view(User $user, Opportunity $opportunity): bool
    {
        return $user->hasPermissionTo('view_opportunities');
    }

    /**
     * Determine whether the user can create opportunities.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_opportunities');
    }

    /**
     * Determine whether the user can update the opportunity.
     */
    public function update(User $user, Opportunity $opportunity): bool
    {
        // Admins and project managers can edit any opportunity
        if ($user->hasPermissionTo('edit_opportunities')) {
            return true;
        }

        // Users can edit opportunities they created or are assigned to
        return $opportunity->created_by === $user->id || $opportunity->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the opportunity.
     */
    public function delete(User $user, Opportunity $opportunity): bool
    {
        // Only admins and project managers can delete opportunities
        return $user->hasPermissionTo('delete_opportunities');
    }

    /**
     * Determine whether the user can restore the opportunity.
     */
    public function restore(User $user, Opportunity $opportunity): bool
    {
        return $user->hasPermissionTo('delete_opportunities');
    }

    /**
     * Determine whether the user can permanently delete the opportunity.
     */
    public function forceDelete(User $user, Opportunity $opportunity): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view financial data.
     */
    public function viewFinancialData(User $user, Opportunity $opportunity): bool
    {
        return $user->hasPermissionTo('view_financial_data');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): bool
    {
        return $user->hasPermissionTo('bulk_operations');
    }
}