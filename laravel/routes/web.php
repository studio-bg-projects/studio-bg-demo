<?php

use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);

Route::get('/ambulance-patrol', [\App\Http\Controllers\AmbulancePatrolController::class, 'index']);
Route::get('/ambulance-patrol/proxy', [\App\Http\Controllers\AmbulancePatrolController::class, 'proxy']);

Route::get('/virtual-project-manager', [\App\Http\Controllers\VirtualProjectManagerController::class, 'index']);
Route::post('/virtual-project-manager/session', [\App\Http\Controllers\VirtualProjectManagerController::class, 'session']);
Route::get('/virtual-project-manager/tasks', [\App\Http\Controllers\VirtualProjectManagerController::class, 'listTasks']);
Route::post('/virtual-project-manager/tasks', [\App\Http\Controllers\VirtualProjectManagerController::class, 'storeTask']);
Route::patch('/virtual-project-manager/tasks/{task}', [\App\Http\Controllers\VirtualProjectManagerController::class, 'updateTask']);
Route::delete('/virtual-project-manager/tasks/{task}', [\App\Http\Controllers\VirtualProjectManagerController::class, 'deleteTask']);

Route::get('/vehicle-inspections', [\App\Http\Controllers\VehicleInspectionsController::class, 'index']);
Route::get('/vehicle-inspections/create', [\App\Http\Controllers\VehicleInspectionsController::class, 'create']);
Route::post('/vehicle-inspections/create', [\App\Http\Controllers\VehicleInspectionsController::class, 'create']);
Route::get('/vehicle-inspections/process/{vehicleInspectionId}', [\App\Http\Controllers\VehicleInspectionsController::class, 'process']);
Route::get('/vehicle-inspections/reset/{vehicleInspectionId}', [\App\Http\Controllers\VehicleInspectionsController::class, 'reset']);
Route::get('/vehicle-inspections/view/{vehicleInspectionId}', [\App\Http\Controllers\VehicleInspectionsController::class, 'view']);
Route::get('/vehicle-inspections/delete/{vehicleInspectionId}', [\App\Http\Controllers\VehicleInspectionsController::class, 'delete']);

Route::get('/test', [\App\Http\Controllers\TestController::class, 'index']);
