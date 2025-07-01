<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadAnalyticsController;
use App\Http\Controllers\DebugController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('user.index');
})->name('index');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

// API Routes untuk Analytics
Route::prefix('api/analytics')->group(function () {
    Route::get('/payment-method', [LeadAnalyticsController::class, 'getPaymentMethodData']);
    Route::get('/program', [LeadAnalyticsController::class, 'getProgramData']);
    Route::get('/model', [LeadAnalyticsController::class, 'getModelData']);
    Route::get('/status', [LeadAnalyticsController::class, 'getStatusData']);
});

// Debug Routes
Route::get('/debug-sheets', function() {
    try {
        $service = new \App\Services\GoogleSheetsService();
        $sheets = $service->getSheetNames();
        return response()->json([
            'success' => true,
            'available_sheets' => $sheets
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
Route::get('/debug/raw-data', [DebugController::class, 'showRawData']);
