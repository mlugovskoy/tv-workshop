<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\RepairController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clients', ClientController::class);
Route::get('/device/clients', [ClientController::class, 'deviceClients'])->name('device.clients');

Route::apiResource('devices', DeviceController::class);
Route::apiResource('repairs', RepairController::class);
Route::patch('repairs/{repair}/status', [RepairController::class, 'changeStatus'])->name('repairs.status');
