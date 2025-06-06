<?php

use App\Http\Controllers\order\orderController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\ProductsController;
use App\Http\Livewire\ReportsController;
use App\Http\Livewire\RolesController;
use App\Http\Livewire\UsersController;


use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:Comercial|Admin']], function () {   
    Route::get('reports', ReportsController::class);
});

Route::group(['middleware' => ['role:Admin']], function () {
    Route::get('users', UsersController::class);
    Route::get('roles', RolesController::class);
    Route::get('role', RolesController::class);
    Route::get('permisos', PermisosController::class);
    Route::get('asignar', AsignarController::class);
    Route::get('coins', CoinsController::class);
});
