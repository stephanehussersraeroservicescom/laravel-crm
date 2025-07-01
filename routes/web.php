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


Route::view('/projects', 'projects.index')->name('projects.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



// Create routes for listing entities

Route::get('/subcontractors', \App\Livewire\SubcontractorsTable::class)
    ->middleware(['auth'])
    ->name('subcontractors.index');

Route::get('/subcontractors/{subcontractor}/contacts', \App\Livewire\ContactsTable::class)
    ->middleware(['auth'])
    ->name('contacts.index');

Route::get('/airlines', \App\Livewire\AirlinesTable::class)
    ->middleware(['auth'])
    ->name('airlines.index');

Route::get('/project-teams', \App\Livewire\ProjectSubcontractorTeams::class)
    ->middleware(['auth'])
    ->name('project-teams.index');

Route::get('/project-teams/{project}', \App\Livewire\ProjectSubcontractorTeams::class)
    ->middleware(['auth'])
    ->name('project.teams');


// Create routes for creating new entities

Route::get('/subcontractors/create', \App\Livewire\SubcontractorCreate::class)
    ->middleware(['auth'])
    ->name('subcontractors.create');

