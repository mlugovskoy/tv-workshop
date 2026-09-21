<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\RepairController;
use Illuminate\Support\Facades\Route;
use Native\Desktop\Facades\AutoUpdater;

Route::apiResource('clients', ClientController::class);
Route::get('/device/clients', [ClientController::class, 'deviceClients'])->name('device.clients');

Route::apiResource('devices', DeviceController::class);
Route::apiResource('repairs', RepairController::class);
Route::patch('/repairs/{repair}/status', [RepairController::class, 'changeStatus'])->name('repairs.status');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::post('/app/check-update', function () {
    AutoUpdater::checkForUpdates();

    return response()->json([
        'message' => 'Проверка обновлений запущена'
    ]);
});
