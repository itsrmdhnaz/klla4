<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SpreadsheetCredentialController;
use App\Http\Controllers\Admin\MasterData\PegawaiController;
use App\Http\Controllers\Admin\MasterData\SpreadsheetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterNipController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return view('user.index');
    })->name('index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('admin.master-data.pegawai.index'); // Temporary redirect
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Master Data Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('master-data')->name('master-data.')->group(function () {
        
        // Employee Management
        Route::controller(PegawaiController::class)->group(function () {
            Route::get('/pegawai', 'index')->name('pegawai.index');
            Route::get('/pegawai/data', 'data')->name('pegawai.data');
            Route::post('/pegawai', 'store')->name('pegawai.store');
            Route::get('/pegawai/{nip}', 'show')->name('pegawai.show');
            Route::put('/pegawai/{nip}', 'update')->name('pegawai.update');
            Route::delete('/pegawai/{nip}', 'destroy')->name('pegawai.destroy');
            Route::get('/pegawai-branches', 'getBranches')->name('pegawai.branches');
        });

        // Branch Management
        Route::controller(\App\Http\Controllers\Admin\MasterData\CabangController::class)->group(function () {
            Route::get('/cabang', 'index')->name('cabang.index');
            Route::get('/cabang/data', 'data')->name('cabang.data');
            Route::post('/cabang', 'store')->name('cabang.store');
            Route::get('/cabang/{id}', 'show')->name('cabang.show');
            Route::put('/cabang/{id}', 'update')->name('cabang.update');
            Route::delete('/cabang/{id}', 'destroy')->name('cabang.destroy');
        });

        // Spreadsheet Configuration
        Route::controller(SpreadsheetController::class)->group(function () {
            Route::get('/spreadsheet', 'index')->name('spreadsheet.index');
            Route::get('/spreadsheet/data', 'data')->name('spreadsheet.data');
            Route::post('/spreadsheet', 'store')->name('spreadsheet.store');
            Route::get('/spreadsheet/{id}', 'show')->name('spreadsheet.show');
            Route::put('/spreadsheet/{id}', 'update')->name('spreadsheet.update');
            Route::delete('/spreadsheet/{id}', 'destroy')->name('spreadsheet.destroy');
        });

        // Spreadsheet Sheets Configuration
        Route::controller(\App\Http\Controllers\Admin\MasterData\SpreadsheetSheetController::class)->group(function () {
            // API endpoints first (more specific routes)
            Route::get('/spreadsheet-sheet/spreadsheets', 'getSpreadsheets')->name('spreadsheet-sheet.spreadsheets');
            Route::get('/spreadsheet-sheet/branches', 'getBranches')->name('spreadsheet-sheet.branches');
            
            // CRUD endpoints
            Route::get('/spreadsheet-sheet', 'index')->name('spreadsheet-sheet.index');
            Route::get('/spreadsheet-sheet/data', 'data')->name('spreadsheet-sheet.data');
            Route::post('/spreadsheet-sheet', 'store')->name('spreadsheet-sheet.store');
            Route::get('/spreadsheet-sheet/{id}', 'show')->name('spreadsheet-sheet.show');
            Route::put('/spreadsheet-sheet/{id}', 'update')->name('spreadsheet-sheet.update');
            Route::delete('/spreadsheet-sheet/{id}', 'destroy')->name('spreadsheet-sheet.destroy');
        });

        // Spreadsheet Columns Configuration
        Route::controller(\App\Http\Controllers\Admin\MasterData\SpreadsheetColumnController::class)->group(function () {
            // API endpoints first (more specific routes)
            Route::get('/spreadsheet-column/sheets', 'getSheets')->name('spreadsheet-column.sheets');
            
            // CRUD endpoints
            Route::get('/spreadsheet-column', 'index')->name('spreadsheet-column.index');
            Route::get('/spreadsheet-column/data', 'data')->name('spreadsheet-column.data');
            Route::post('/spreadsheet-column', 'store')->name('spreadsheet-column.store');
            Route::get('/spreadsheet-column/{id}', 'show')->name('spreadsheet-column.show');
            Route::put('/spreadsheet-column/{id}', 'update')->name('spreadsheet-column.update');
            Route::delete('/spreadsheet-column/{id}', 'destroy')->name('spreadsheet-column.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Spreadsheet Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('spreadsheet')->name('spreadsheet.')->group(function () {
        
        // Spreadsheet List & Configuration
        Route::get('/list', function () {
            return view('admin.spreadsheet.list');
        })->name('list');

        // Spreadsheet Reader
        Route::controller(\App\Http\Controllers\Admin\SpreadsheetReaderController::class)->group(function () {
            Route::get('/reader', 'index')->name('reader');
            Route::get('/reader/{spreadsheetId}/sheets', 'getAccessibleSheets')->name('reader.sheets');
            Route::get('/reader/sheet/{sheetId}/columns', 'getAccessibleColumns')->name('reader.columns');
            Route::post('/reader/read', 'readData')->name('reader.read');
            Route::post('/reader/batch', 'readBatchData')->name('reader.batch');
            Route::post('/reader/preview', 'preview')->name('reader.preview');
        });

        // Secure Credential Access
        Route::get('/{spreadsheet}/credential', [SpreadsheetCredentialController::class, 'serveCredential'])
            ->name('credential')
            ->middleware('check.spreadsheet.access:read');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        // Reports functionality will be added here
        Route::get('/daily', function () {
            return view('admin.reports.daily');
        })->name('daily');

        Route::get('/analytics', function () {
            return view('admin.reports.analytics');
        })->name('analytics');

        Route::get('/export', function () {
            return view('admin.reports.export');
        })->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | System Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('system')->name('system.')->group(function () {
        // Settings
        Route::get('/settings', function () {
            return view('admin.system.settings');
        })->name('settings');

        // User Management
        Route::get('/users', function () {
            return view('admin.system.users');
        })->name('users');

        // Role & Permission Management with Spatie (Team-based)
        // Route::controller(\App\Http\Controllers\Admin\RolePermissionController::class)->group(function () {
        //     Route::get('/roles-permissions', 'index')->name('roles-permissions');
        //     Route::get('/roles-permissions/data', 'data')->name('roles-permissions.data');
        //     Route::post('/roles-permissions/assign-role', 'assignRole')->name('roles-permissions.assign-role');
        //     Route::delete('/roles-permissions/remove-role', 'removeRole')->name('roles-permissions.remove-role');
        //     Route::post('/roles-permissions/create-team-role', 'createTeamRole')->name('roles-permissions.create-team-role');
        //     Route::get('/roles-permissions/user/{userId}', 'getUserPermissions')->name('roles-permissions.user-permissions');
        //     Route::get('/roles-permissions/teams/{teamId}/permissions', 'getTeamPermissions')->name('roles-permissions.team-permissions');
        //     Route::post('/roles-permissions/assign-user-to-team', 'assignUserToTeam')->name('roles-permissions.assign-user-to-team');
        //     // New permission assignment routes
        //     Route::post('/roles-permissions/assign-permission-to-user', 'assignPermissionToUser')->name('roles-permissions.assign-permission-to-user');
        //     Route::post('/roles-permissions/assign-permission-to-role', 'assignPermissionToRole')->name('roles-permissions.assign-permission-to-role');
        //     Route::post('/roles-permissions/assign-permission-to-group', 'assignPermissionToGroup')->name('roles-permissions.assign-permission-to-group');
        //     Route::delete('/roles-permissions/remove-permission', 'removePermission')->name('roles-permissions.remove-permission');
        //     // Hierarchy data route (replace all old hierarchy routes)
        //     Route::get('/roles-permissions/hierarchy', 'hierarchy')->name('roles-permissions.hierarchy');
        // });
    });
});

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

// Register by NIP
Route::get('/register-nip', [RegisterNipController::class, 'showForm'])->name('register-nip.form');
Route::post('/register-nip', [RegisterNipController::class, 'register'])->name('register-nip.submit');
Route::get('/register-nip/sent', [RegisterNipController::class, 'sent'])->name('register-nip.sent');

// Set password via email link
Route::get('/set-password/{token}', [SetPasswordController::class, 'showForm'])->name('set-password.form');
Route::post('/set-password/{token}', [SetPasswordController::class, 'setPassword'])->name('set-password.submit');

// Forgot password (custom, design like login)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('forgot-password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendReset'])->name('forgot-password.send');
Route::get('/forgot-password/sent', [ForgotPasswordController::class, 'sent'])->name('forgot-password.sent');
