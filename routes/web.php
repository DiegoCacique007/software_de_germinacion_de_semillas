<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| 2. RUTAS COMUNES (AUTENTICADOS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Punto de entrada general
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gestión de Perfil Personal
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 3. RUTAS POR ROLES (Software de Germinación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /**
     * ROL: SUPER ADMIN
     * Panel maestro y control de personal
     */
    Route::middleware(['role:super_admin'])->prefix('admin')->group(function () {
        
        // Pantalla de Bienvenida (Tarjetas de acceso rápido)
        Route::get('/panel', function () {
            return view('vistas_principales.super_admin.super_admin');
        })->name('admin.dashboard');

        // CRUD de Usuarios (Gestión con Modales)
        Route::resource('usuarios', UserController::class)->names([
            'index'   => 'admin.users',
            'store'   => 'admin.users.store',
            'update'  => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
    });

    /**
     * ROL: ADMINISTRADOR
     * Gestión técnica de semillas e inventario
     */
    Route::middleware(['role:administrador'])->group(function () {
        Route::get('/gestion-semillas', function () {
            return view('vistas_principales.administrador.administrador');
        })->name('semillas.gestion');
    });

    /**
     * ROL: ENCARGADO
     * Registro de bitácoras y riego
     */
    Route::middleware(['role:encargado'])->group(function () {
        Route::get('/registro-diario', function () {
            return view('vistas_principales.encargado.encargado');
        })->name('registro.diario');
    });

});

require __DIR__.'/auth.php';