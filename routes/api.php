<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorLecturaController;
use App\Http\Controllers\Api\DispositivoControlController;

Route::post('/sensores/lecturas', [SensorLecturaController::class, 'store']);

Route::get('/dispositivo/incubadora/{incubadora}/ordenes-pendientes', [DispositivoControlController::class, 'pendientes']);
Route::post('/dispositivo/orden/{orden}/confirmar', [DispositivoControlController::class, 'confirmar']);
