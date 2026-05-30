<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VehicleColorController;

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

});
