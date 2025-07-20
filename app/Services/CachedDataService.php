<?php

namespace App\Services;

use App\Models\Airline;
use App\Models\AircraftType;
use App\Models\Status;
use App\Models\User;
use App\Models\Subcontractor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class CachedDataService
{
    private const CACHE_TTL = 300; // 5 minutes
    
    /**
     * Get cached airlines with only necessary fields for dropdowns
     */
    public static function getAirlines(): Collection
    {
        return Cache::remember('airlines_dropdown', self::CACHE_TTL, function () {
            return Airline::select('id', 'name', 'region', 'account_executive_id')
                ->orderBy('name')
                ->get();
        });
    }
    
    /**
     * Get cached aircraft types
     */
    public static function getAircraftTypes(): Collection
    {
        return Cache::remember('aircraft_types_dropdown', self::CACHE_TTL, function () {
            return AircraftType::select('id', 'name', 'manufacturer', 'code')
                ->orderBy('name')
                ->get();
        });
    }
    
    /**
     * Get cached statuses
     */
    public static function getStatuses(): Collection
    {
        return Cache::remember('statuses_dropdown', self::CACHE_TTL, function () {
            return Status::select('id', 'status', 'type')
                ->orderBy('status')
                ->get();
        });
    }
    
    /**
     * Get cached sales users
     */
    public static function getSalesUsers(): Collection
    {
        return Cache::remember('sales_users_dropdown', self::CACHE_TTL, function () {
            return User::select('id', 'name', 'email', 'role')
                ->where('role', 'sales')
                ->orderBy('name')
                ->get();
        });
    }
    
    /**
     * Get cached sales and manager users
     */
    public static function getSalesAndManagerUsers(): Collection
    {
        return Cache::remember('sales_manager_users_dropdown', self::CACHE_TTL, function () {
            return User::select('id', 'name', 'email', 'role')
                ->whereIn('role', ['sales', 'manager'])
                ->orderBy('name')
                ->get();
        });
    }
    
    /**
     * Get cached subcontractors
     */
    public static function getSubcontractors(): Collection
    {
        return Cache::remember('subcontractors_dropdown', self::CACHE_TTL, function () {
            return Subcontractor::select('id', 'name', 'comment')
                ->orderBy('name')
                ->get();
        });
    }
    
    /**
     * Clear all cached data
     */
    public static function clearCache(): void
    {
        Cache::forget('airlines_dropdown');
        Cache::forget('aircraft_types_dropdown');
        Cache::forget('statuses_dropdown');
        Cache::forget('sales_users_dropdown');
        Cache::forget('sales_manager_users_dropdown');
        Cache::forget('subcontractors_dropdown');
    }
    
    /**
     * Clear specific cache key
     */
    public static function clearSpecificCache(string $key): void
    {
        Cache::forget($key . '_dropdown');
    }
}