<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. Pantalla de inicio pública (Diseño verde/blanco)
Route::get('/', function () {
    return view('welcome');
});

// 2. Dashboard común (Punto de entrada para todos)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Rutas protegidas generales (Perfil de usuario)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------------------------------------------------------
// 4. RUTAS POR ROLES (Software de Germinación)
// ---------------------------------------------------------

Route::middleware(['auth'])->group(function () {

    // Rutas solo para el SUPER ADMIN
Route::middleware(['role:super_admin'])->group(function () {
    Route::get('/admin/usuarios', function () {
        return view('vistas_principales.super_admin.super_admin'); // <-- Agregamos "roles."
    })->name('admin.users');
});

// Rutas para ADMINISTRADORES
Route::middleware(['role:administrador'])->group(function () {
    Route::get('/gestion-semillas', function () {
        return view('vistas_principales.administrador.administrador'); // <-- Agregamos "roles."
    })->name('semillas.gestion');
});

// Rutas para ENCARGADOS
Route::middleware(['role:encargado'])->group(function () {
    Route::get('/registro-diario', function () {
        return view('vistas_principales.encargado.encargado'); // <-- Agregamos "roles."
    })->name('registro.diario');
});

});

require __DIR__.'/auth.php';