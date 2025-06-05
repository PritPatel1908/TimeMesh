<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PunchLogController;
use App\Http\Controllers\DashboardController;

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

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public API endpoint for cron job
Route::get('/api/cron/process-punch-logs/{secret_token}', [PunchLogController::class, 'runProcessCommand'])
    ->name('cron.process-punch-logs')
    ->middleware('throttle:10,1'); // Rate limit: 10 requests per minute

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Auto Process Page
    Route::get('/auto-process', function () {
        return view('auto-process');
    })->name('auto-process');

    // Users Management
    Route::get('/users', function () {
        return view('users');
    })->name('users.index');

    // Punch Logs
    Route::get('/punch-logs', [PunchLogController::class, 'index'])->name('punch-logs.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/daily/download', [ReportController::class, 'downloadDailyReport'])->name('reports.daily.download');
    Route::get('/reports/monthly/download', [ReportController::class, 'downloadMonthlyReport'])->name('reports.monthly.download');

    // Users API
    Route::prefix('api')->group(function () {
        Route::apiResource('users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy',
        ]);

        // User Import
        Route::post('/users/import', [UserController::class, 'import'])->name('api.users.import');

        // PunchLogs
        Route::post('/punch-logs', [PunchLogController::class, 'store'])->name('punch-logs.store');
        Route::get('/punch-logs/latest', [PunchLogController::class, 'getLatestLogs'])->name('punch-logs.latest');
        Route::get('/punch-logs/process-unprocessed', [PunchLogController::class, 'processUnprocessedLogs'])->name('punch-logs.process-unprocessed');
        Route::get('/punch-logs/run-process-command', [PunchLogController::class, 'runProcessCommand'])->name('punch-logs.run-process-command');
        Route::get('/punch-logs/check-and-process', [PunchLogController::class, 'processUnprocessedLogs'])->name('punch-logs.check-and-process');
    });
});
