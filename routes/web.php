<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VehicleColorController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\VehicleTypeController;
use App\Http\Controllers\admin\PersonnelTypeController;
use App\Http\Controllers\admin\PersonnelController;
use App\Http\Controllers\admin\ContractController;
use App\Http\Controllers\admin\AttendanceController;
use App\Http\Controllers\admin\ShiftController;
use App\Http\Controllers\admin\VacationController;
use App\Http\Controllers\admin\ZoneController;
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
    Route::delete('admin/vehicles/image/{id}', [VehicleController::class, 'deleteImage'])
        ->name('admin.vehicles.delete-image');

    Route::resource('admin/vehicle-types', VehicleTypeController::class)
        ->names('admin.vehicle-types');

    Route::resource('admin/personnel-types', PersonnelTypeController::class)
        ->names('admin.personnel-types');

    Route::resource('admin/personnels', PersonnelController::class)
        ->names('admin.personnels');
        
    Route::resource('admin/contracts', ContractController::class)
        ->names('admin.contracts');

    Route::get('admin/attendances/personnel-day-info', [AttendanceController::class, 'personnelDayInfo'])
        ->name('admin.attendances.personnel-day-info');

    Route::resource('admin/attendances', AttendanceController::class)
        ->names('admin.attendances');

    Route::resource('admin/shifts', ShiftController::class)
        ->names('admin.shifts');

    Route::get('admin/zones-polygons/{id?}', [ZoneController::class, 'polygons'])
        ->name('admin.zones.polygons');
    
    Route::resource('admin/zones', ZoneController::class)
        ->names('admin.zones');

    Route::get('admin/vacations/personnel-info', [VacationController::class, 'getPersonnelVacationInfo'])
        ->name('admin.vacations.personnel-info');

    Route::post('admin/vacations/{id}/approve', [VacationController::class, 'approve'])
        ->name('admin.vacations.approve');

    Route::post('admin/vacations/{id}/reject', [VacationController::class, 'reject'])
        ->name('admin.vacations.reject');
        
    Route::resource('admin/vacations', VacationController::class)
        ->names('admin.vacations');

});
