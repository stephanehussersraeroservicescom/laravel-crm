<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseManagerController;
use App\Http\Controllers\QuoteController;

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
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::get('/create', [QuoteController::class, 'create'])->name('create');
        Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
        Route::get('/{quote}/edit', [QuoteController::class, 'edit'])->name('edit');
        Route::get('/{quote}/preview', [QuoteController::class, 'preview'])->name('preview');
        Route::get('/{quote}/download', [QuoteController::class, 'download'])->name('download');
        Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy');
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
    Route::get('/', [DatabaseManagerController::class, 'index'])
        ->name('index');
    Route::get('/customers', [DatabaseManagerController::class, 'customers'])
        ->name('customers');
        
    Route::get('/product-classes', [DatabaseManagerController::class, 'productClasses'])
        ->name('product-classes');
    Route::get('/products', [DatabaseManagerController::class, 'products'])
        ->name('products');
    Route::get('/contract-prices', [DatabaseManagerController::class, 'contractPrices'])
        ->name('contract-prices');
    Route::get('/airlines', [DatabaseManagerController::class, 'airlines'])
        ->name('airlines');
});

// Forecasting Routes (separate section)
Route::middleware(['auth'])->prefix('forecasting')->name('forecasting.')->group(function () {
    Route::get('/', \App\Livewire\Forecasting\Dashboard::class)
        ->name('dashboard');
    
    Route::get('/analytics', \App\Livewire\Forecasting\Analytics::class)
        ->name('analytics');
});