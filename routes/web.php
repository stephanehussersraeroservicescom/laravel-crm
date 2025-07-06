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



// Routes for listing entities
Route::middleware(['auth'])->group(function () {
    Route::get('/subcontractors', \App\Livewire\SubcontractorsTable::class)
        ->name('subcontractors.index');

    Route::get('/subcontractors/{subcontractor}/contacts', \App\Livewire\ContactsTable::class)
        ->name('contacts.index');

    Route::get('/manage/airlines', \App\Livewire\AirlinesTable::class)
        ->name('manage.airlines');

    Route::get('/projects', \App\Livewire\ProjectsTable::class)
        ->name('projects.index');

    Route::get('/opportunities', \App\Livewire\ProjectOpportunity::class)
        ->name('opportunities.index');

    Route::get('/opportunities/{project}', \App\Livewire\ProjectOpportunity::class)
        ->name('project.opportunities');

    // Routes for creating new entities
    Route::get('/subcontractors/create', \App\Livewire\SubcontractorCreate::class)
        ->name('subcontractors.create');

    Route::get('/airlines/create', \App\Livewire\AirlineCreate::class)
        ->name('airlines.create');

    // New Management Components
    Route::get('/manage/opportunities', \App\Livewire\OpportunityManagement::class)
        ->name('manage.opportunities');

    Route::get('/manage/projects', \App\Livewire\ProjectManagement::class)
        ->name('manage.projects');

    Route::get('/manage/teams', \App\Livewire\TeamManagement::class)
        ->name('manage.teams');

    Route::get('/manage/contacts', \App\Livewire\ContactManagement::class)
        ->name('manage.contacts');
});

