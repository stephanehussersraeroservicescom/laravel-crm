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
    
    // Quote Routes
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/', \App\Http\Controllers\QuoteController::class . '@index')->name('index');
        Route::get('/create', \App\Http\Controllers\QuoteController::class . '@create')->name('create');
        Route::get('/{quote}', \App\Http\Controllers\QuoteController::class . '@show')->name('show');
        Route::get('/{quote}/edit', \App\Http\Controllers\QuoteController::class . '@edit')->name('edit');
        Route::get('/{quote}/preview', \App\Http\Controllers\QuoteController::class . '@preview')->name('preview');
        Route::delete('/{quote}', \App\Http\Controllers\QuoteController::class . '@destroy')->name('destroy');
    });
    
    // Template Download Routes
    Route::prefix('admin/templates')->name('templates.')->group(function () {
        Route::get('/download/{template}', function ($template) {
            $allowedTemplates = [
                'product-roots' => 'product_roots_simple.csv',
                'product-series' => 'product_series_simple.csv', 
                'price-lists' => 'price_lists_simple.csv',
                'products-master' => 'products_master_template.csv'
            ];
            
            if (!array_key_exists($template, $allowedTemplates)) {
                abort(404);
            }
            
            $filePath = storage_path('app/templates/' . $allowedTemplates[$template]);
            
            if (!file_exists($filePath)) {
                abort(404);
            }
            
            return response()->download($filePath);
        })->name('download');
    });
});

// Database Manager Routes (restricted access)
Route::middleware(['auth'])->prefix('database-manager')->name('database-manager.')->group(function () {
    Route::get('/', [App\Http\Controllers\DatabaseManagerController::class, 'index'])
        ->name('index');
    Route::get('/customers', [App\Http\Controllers\DatabaseManagerController::class, 'customers'])
        ->name('customers');
    Route::get('/product-roots', [App\Http\Controllers\DatabaseManagerController::class, 'productRoots'])
        ->name('product-roots');
    Route::get('/stocked-products', [App\Http\Controllers\DatabaseManagerController::class, 'stockedProducts'])
        ->name('stocked-products');
    Route::get('/contract-prices', [App\Http\Controllers\DatabaseManagerController::class, 'contractPrices'])
        ->name('contract-prices');
    Route::get('/stocked-products', [App\Http\Controllers\DatabaseManagerController::class, 'stockedProducts'])
        ->name('stocked-products');
    Route::get('/airlines', [App\Http\Controllers\DatabaseManagerController::class, 'airlines'])
        ->name('airlines');
});

// Forecasting Routes (separate section)
Route::middleware(['auth'])->prefix('forecasting')->name('forecasting.')->group(function () {
    Route::get('/', \App\Livewire\Forecasting\Dashboard::class)
        ->name('dashboard');
    
    Route::get('/analytics', \App\Livewire\Forecasting\Analytics::class)
        ->name('analytics');
});