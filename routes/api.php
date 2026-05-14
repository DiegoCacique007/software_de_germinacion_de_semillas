<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorLecturaController;
use App\Http\Controllers\Api\MicroclimaActuadorController;

Route::post('/sensores/lecturas', [SensorLecturaController::class, 'store']);



Route::get('/microclima/actuadores/{actuador}', [MicroclimaActuadorController::class, 'show']);
