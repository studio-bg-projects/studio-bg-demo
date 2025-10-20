<?php

use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);

Route::get('/ambulance-patrol', [\App\Http\Controllers\AmbulancePatrolController::class, 'index']);
Route::get('/ambulance-patrol/proxy', [\App\Http\Controllers\AmbulancePatrolController::class, 'proxy']);

Route::get('/virtual-project-manager', [\App\Http\Controllers\VirtualProjectManagerController::class, 'index']);
Route::post('/virtual-project-manager/session', [\App\Http\Controllers\VirtualProjectManagerController::class, 'session']);

Route::get('/vehicle-inspections', [\App\Http\Controllers\VehicleInspectionsController::class, 'index']);
Route::get('/vehicle-inspections/create', [\App\Http\Controllers\VehicleInspectionsController::class, 'create']);
Route::post('/vehicle-inspections/create', [\App\Http\Controllers\VehicleInspectionsController::class, 'create']);
Route::get('/vehicle-inspections/process/{id}', [\App\Http\Controllers\VehicleInspectionsController::class, 'process']);
Route::get('/vehicle-inspections/reset/{id}', [\App\Http\Controllers\VehicleInspectionsController::class, 'reset']);
Route::get('/vehicle-inspections/view/{id}', [\App\Http\Controllers\VehicleInspectionsController::class, 'view']);
Route::get('/vehicle-inspections/delete/{id}', [\App\Http\Controllers\VehicleInspectionsController::class, 'delete']);
