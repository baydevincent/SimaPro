<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ProjectWorkerController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DailyReportController;
use App\Http\Controllers\Api\ShopDrawingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group with JWT authentication.
|
*/

// Public routes (no authentication required)
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Projects
    Route::apiResource('projects', ProjectController::class);

    // Tasks
    Route::apiResource('tasks', TaskController::class)->except(['index', 'create', 'edit']);
    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);

    // Project Workers
    Route::get('projects/{project}/workers', [ProjectWorkerController::class, 'index']);
    Route::post('projects/{project}/workers', [ProjectWorkerController::class, 'store']);
    Route::get('projects/{project}/workers/{worker}', [ProjectWorkerController::class, 'show']);
    Route::put('projects/{project}/workers/{worker}', [ProjectWorkerController::class, 'update']);
    Route::delete('projects/{project}/workers/{worker}', [ProjectWorkerController::class, 'destroy']);

    // Attendance
    Route::get('projects/{project}/attendance', [AttendanceController::class, 'index']);
    Route::post('projects/{project}/attendance', [AttendanceController::class, 'store']);
    Route::get('projects/{project}/attendance/{attendance}', [AttendanceController::class, 'show']);
    Route::put('projects/{project}/attendance/{attendance}', [AttendanceController::class, 'update']);
    Route::delete('projects/{project}/attendance/{attendance}', [AttendanceController::class, 'destroy']);

    // Daily Reports
    Route::get('projects/{project}/daily-reports', [DailyReportController::class, 'index']);
    Route::post('projects/{project}/daily-reports', [DailyReportController::class, 'store']);
    Route::get('projects/{project}/daily-reports/{report}', [DailyReportController::class, 'show']);
    Route::put('projects/{project}/daily-reports/{report}', [DailyReportController::class, 'update']);
    Route::delete('projects/{project}/daily-reports/{report}', [DailyReportController::class, 'destroy']);
    Route::get('projects/{project}/daily-reports/{report}/download-pdf', [DailyReportController::class, 'downloadPdf']);

    // Shop Drawings
    Route::get('projects/{project}/shop-drawings', [ShopDrawingController::class, 'index']);
    Route::post('projects/{project}/shop-drawings', [ShopDrawingController::class, 'store']);
    Route::delete('projects/{project}/shop-drawings/{shopDrawing}', [ShopDrawingController::class, 'destroy']);
});
