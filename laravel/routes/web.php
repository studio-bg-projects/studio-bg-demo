<?php

use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);

Route::get('/ambulance-patrol', [\App\Http\Controllers\AmbulancePatrolController::class, 'index']);
Route::get('/ambulance-patrol/proxy', [\App\Http\Controllers\AmbulancePatrolController::class, 'proxy']);

Route::get('/virtual-project-manager', [\App\Http\Controllers\VirtualProjectManagerController::class, 'index'])
  ->name('virtual-project-manager.index');
Route::post('/virtual-project-manager/session', [\App\Http\Controllers\VirtualProjectManagerController::class, 'session'])
  ->name('virtual-project-manager.session');

Route::get('/vehicles-inspection', [\App\Http\Controllers\VehiclesInspectionController::class, 'index']);
