<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OpportunityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/auth/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword']);
});

// Protected routes
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'user']);
        Route::put('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'updateProfile']);
        Route::post('/change-password', [App\Http\Controllers\Api\V1\AuthController::class, 'changePassword']);
    });
    
    // Opportunities
    Route::apiResource('opportunities', OpportunityController::class);
    Route::get('opportunities/statistics', [OpportunityController::class, 'statistics']);
    Route::post('opportunities/bulk-update', [OpportunityController::class, 'bulkUpdate']);
    
    // Projects  
    Route::apiResource('projects', App\Http\Controllers\Api\V1\ProjectController::class);
    Route::get('projects/{project}/opportunities', [App\Http\Controllers\Api\V1\ProjectController::class, 'opportunities']);
    
    // Subcontractors
    Route::apiResource('subcontractors', App\Http\Controllers\Api\V1\SubcontractorController::class);
    Route::get('subcontractors/{subcontractor}/contacts', [App\Http\Controllers\Api\V1\SubcontractorController::class, 'contacts']);
    
    // Contacts
    Route::apiResource('contacts', App\Http\Controllers\Api\V1\ContactController::class);
    Route::post('contacts/{contact}/give-consent', [App\Http\Controllers\Api\V1\ContactController::class, 'giveConsent']);
    Route::post('contacts/{contact}/withdraw-consent', [App\Http\Controllers\Api\V1\ContactController::class, 'withdrawConsent']);
    
    // Airlines
    Route::apiResource('airlines', App\Http\Controllers\Api\V1\AirlineController::class);
    
    // Teams
    Route::apiResource('teams', App\Http\Controllers\Api\V1\TeamController::class);
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::apiResource('users', App\Http\Controllers\Api\V1\UserController::class);
        Route::post('users/{user}/assign-role', [App\Http\Controllers\Api\V1\UserController::class, 'assignRole']);
        Route::get('audit-logs', [App\Http\Controllers\Api\V1\AuditController::class, 'index']);
    });
});

// Health check route
Route::get('health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});