<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\Airline;
use App\Models\AircraftType;
use App\Models\Status;
use App\Models\User;
use App\Models\Subcontractor;
use App\Observers\ProjectObserver;
use App\Observers\AirlineObserver;
use App\Observers\AircraftTypeObserver;
use App\Observers\StatusObserver;
use App\Observers\UserObserver;
use App\Observers\SubcontractorObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Project::observe(ProjectObserver::class);
        Airline::observe(AirlineObserver::class);
        AircraftType::observe(AircraftTypeObserver::class);
        Status::observe(StatusObserver::class);
        User::observe(UserObserver::class);
        Subcontractor::observe(SubcontractorObserver::class);
    }
}
