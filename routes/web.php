<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VehicleColorController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\VehicleTypeController;
use App\Http\Controllers\admin\PersonnelTypeController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard');

    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.index');
    
    Route::resource('admin/colors', VehicleColorController::class)
    ->names('admin.colors');

    Route::resource('admin/brands', BrandController::class)
    ->names('admin.brands');

    Route::resource('admin/models', BrandModelController::class)
    ->names('admin.models');

    Route::resource('admin/vehicles', VehicleController::class)
        ->names('admin.vehicles');

    Route::resource('admin/vehicle-types', VehicleTypeController::class)
        ->names('admin.vehicle-types');

    Route::resource('admin/personnel-types', PersonnelTypeController::class)
        ->names('admin.personnel-types');

});
