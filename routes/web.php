<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectWorkerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkerAttendanceController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopDrawingController;
use App\Http\Controllers\TaskImportController;
use App\Http\Controllers\WorkerImportController;
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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');

// Task Import (MUST BE BEFORE project resource routes)
Route::get('/project/{project}/task/import/template', [TaskImportController::class, 'downloadTemplate'])->name('task.import.template');
Route::post('/project/{project}/task/import', [TaskImportController::class, 'import'])->name('task.import');
Route::get('/project/{project}/task/import/errors', [TaskImportController::class, 'downloadErrorReport'])->name('task.import.errors');

// Project
Route::resource('project', ProjectController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

Route::get('/project', [ProjectController::class, 'index'])->name('project');

Route::resource('task', TaskController::class)->only(['destroy', 'update', 'edit']);
Route::post('project/{project}/task', [TaskController::class, 'store'])->name('task.store');
Route::patch('task/{task}/toggle', [TaskController::class, 'toggle'])->name('task.toggle');

// Worker
Route::resource('worker', ProjectWorkerController::class)->only(['show', 'destroy', 'edit']);

Route::post('/project/{project}/workers',[ProjectWorkerController::class, 'store'])->name('project.workers.store');
Route::get('/project/{project}/attendance',[WorkerAttendanceController::class, 'index'])->name('attendance.index');
Route::get('/project/{project}/workers', [ProjectWorkerController::class, 'index'])->name('project.workers');
Route::post('/project/{project}/workers', [ProjectWorkerController::class, 'store'])->name('project.workers.store');
Route::delete('/project/{project}/workers/{worker}', [ProjectWorkerController::class, 'destroy'])->name('project.workers.destroy');
Route::get('/project/{project}/workers/{worker}/edit', [ProjectWorkerController::class, 'edit'])->name('project.workers.edit');
Route::put('/project/{project}/workers/{worker}', [ProjectWorkerController::class, 'update'])->name('project.workers.update');

// Worker Import
Route::get('/project/{project}/workers/import', [WorkerImportController::class, 'showForm'])->name('project.workers.import.form');
Route::post('/project/{project}/workers/import', [WorkerImportController::class, 'import'])->name('project.workers.import');
Route::get('/project/{project}/workers/import/template', [WorkerImportController::class, 'downloadTemplate'])->name('project.workers.import.template');
Route::get('/project/{project}/workers/import/errors', [WorkerImportController::class, 'downloadErrorReport'])->name('project.workers.import.errors');

Route::post('/absensi/toggle',[WorkerAttendanceController::class, 'toggle'])->name('absensi.toggle');

// Absen CRUD
Route::get('/project/{project}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/project/{project}/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/project/{project}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/project/{project}/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::get('/project/{project}/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
Route::put('/project/{project}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
Route::delete('/project/{project}/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
Route::get('/project/{project}/attendance/date-data', [AttendanceController::class, 'getAttendanceForDate'])->name('attendance.date.data');
Route::get('/project/{project}/attendance/dates', [AttendanceController::class, 'getAttendanceDates'])->name('attendance.dates.list');

// Kalender
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/project/{project}/gantt', function ($projectId) {
    $project = \App\Models\Project::findOrFail($projectId);
    return view('projectntask.gantt', compact('project'));
})->name('project.gantt');

// Shop Drawing
Route::get('/project/{project}/shopdrawing', [ShopDrawingController::class, 'index'])->name('shopdrawing.index');
Route::post('/project/{project}/shopdrawing', [ShopDrawingController::class, 'store'])->name('shopdrawing.store');
Route::get('/project/{project}/shopdrawing/{shopDrawing}/download', [ShopDrawingController::class, 'download'])->name('shopdrawing.download');
Route::delete('/project/{project}/shopdrawing/{shopDrawing}', [ShopDrawingController::class, 'destroy'])->name('shopdrawing.destroy');

// User Management
Route::group(['middleware' => ['auth', 'role:administrator']], function () {
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/users/{id}/assign-role', [UserController::class, 'showAssignRoleForm'])->name('users.assign-role-form');
    Route::put('/users/{id}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{id}/remove-role', [UserController::class, 'removeRole'])->name('users.remove-role');
});

// Protect project value access with project.value middleware
Route::group(['middleware' => ['auth', 'project.value']], function () {
    // Add routes that should only be accessible to administrators here
    // For example, if there are API routes to fetch project values
});
