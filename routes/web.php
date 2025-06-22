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

// routes/web.php
// Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
// Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::view('/projects', 'projects.index')->name('projects.index');
