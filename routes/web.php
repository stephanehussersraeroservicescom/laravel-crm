<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/manage/airlines', \App\Livewire\AirlinesTable::class)
        ->name('manage.airlines');

    Route::get('/manage/opportunities', \App\Livewire\OpportunityManagement::class)
        ->name('manage.opportunities');

    Route::get('/manage/projects', \App\Livewire\ProjectManagement::class)
        ->name('manage.projects');

    Route::get('/manage/teams', \App\Livewire\TeamManagement::class)
        ->name('manage.teams');

    Route::get('/manage/contacts', \App\Livewire\ContactManagement::class)
        ->name('manage.contacts');
    
    Route::get('/manage/subcontractors', \App\Livewire\SubcontractorsTable::class)
        ->name('manage.subcontractors');
    
    Route::get('/manage/aircraft-seat-configurations', \App\Livewire\AircraftSeatConfiguration::class)
        ->name('manage.aircraft-seat-configurations');
});

// Forecasting Routes (separate section)
Route::middleware(['auth'])->prefix('forecasting')->name('forecasting.')->group(function () {
    Route::get('/', \App\Livewire\Forecasting\Dashboard::class)
        ->name('dashboard');
    
    Route::get('/analytics', \App\Livewire\Forecasting\Analytics::class)
        ->name('analytics');
});