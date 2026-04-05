<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\EstadoIncubadoraController as SuperAdminEstadoIncubadoraController;
use App\Http\Controllers\SuperAdmin\IncubadoraController as SuperAdminIncubadoraController;
use App\Http\Controllers\SuperAdmin\ControlManualController;

use App\Http\Controllers\Administrador\DashboardController as AdministradorDashboardController;
use App\Http\Controllers\Administrador\EspecieController;
use App\Http\Controllers\Administrador\CondicionOptimaEspecieController;
use App\Http\Controllers\Administrador\PosicionIncubadoraController;
use App\Http\Controllers\Administrador\LoteController;
use App\Http\Controllers\Administrador\FrascoController;
use App\Http\Controllers\Administrador\LecturaMicroclimaController;
use App\Http\Controllers\Administrador\ControlIncubadoraController;

use App\Http\Controllers\Encargado\DashboardController as EncargadoDashboardController;
use App\Http\Controllers\Encargado\SeguimientoLoteController;
use App\Http\Controllers\Encargado\SeguimientoFrascoController;
use App\Http\Controllers\Encargado\EvidenciaLoteController;
use App\Http\Controllers\Encargado\AlertaController;
use App\Http\Controllers\Encargado\RegistroBiologicoController;

use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'super_admin'   => redirect()->route('super_admin.dashboard'),
            'administrador' => redirect()->route('administrador.dashboard'),
            'encargado'     => redirect()->route('encargado.dashboard'),
            default         => abort(403, 'Rol no permitido.')
        };
    })->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

Route::prefix('super-admin')
    ->name('super_admin.')
    ->middleware(['auth', 'check.role:super_admin'])
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/control-manual', [ControlManualController::class, 'store'])->name('control-manual.store');

        Route::resource('usuarios', SuperAdminUserController::class)->except(['create', 'show', 'edit']);
        Route::resource('estados-incubadora', SuperAdminEstadoIncubadoraController::class)->except(['create', 'show', 'edit']);
        Route::resource('incubadoras', SuperAdminIncubadoraController::class)->except(['create', 'show', 'edit']);

        Route::resource('especies', EspecieController::class)->except(['create', 'show', 'edit']);
        Route::resource('condiciones-optimas-especie', CondicionOptimaEspecieController::class)->except(['create', 'show', 'edit']);
        Route::resource('posiciones-incubadora', PosicionIncubadoraController::class)->except(['create', 'show', 'edit']);
        Route::resource('lotes', LoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('frascos', FrascoController::class)->except(['create', 'show', 'edit']);
        Route::resource('lecturas-microclima', LecturaMicroclimaController::class)->except(['create', 'show', 'edit']);
        Route::resource('controles-incubadora', ControlIncubadoraController::class)->except(['create', 'show', 'edit']);

        Route::resource('seguimientos-lote', SeguimientoLoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('seguimientos-frasco', SeguimientoFrascoController::class)->except(['create', 'show', 'edit']);
        Route::resource('evidencias-lote', EvidenciaLoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('alertas', AlertaController::class)->except(['create', 'show', 'edit']);
        Route::resource('registros-biologicos', RegistroBiologicoController::class)->except(['create', 'show', 'edit']);

        Route::get('/reportes/microclima/pdf', [ReporteController::class, 'microclimaPdf'])->name('reportes.microclima.pdf');
        Route::get('/reportes/biologico/pdf', [ReporteController::class, 'biologicoPdf'])->name('reportes.biologico.pdf');
    });

Route::prefix('administrador')
    ->name('administrador.')
    ->middleware(['auth', 'check.role:administrador'])
    ->group(function () {

        Route::get('/dashboard', [AdministradorDashboardController::class, 'index'])->name('dashboard');

        Route::resource('especies', EspecieController::class)->except(['create', 'show', 'edit']);
        Route::resource('condiciones-optimas-especie', CondicionOptimaEspecieController::class)->except(['create', 'show', 'edit']);
        Route::resource('posiciones-incubadora', PosicionIncubadoraController::class)->except(['create', 'show', 'edit']);
        Route::resource('lotes', LoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('frascos', FrascoController::class)->except(['create', 'show', 'edit']);
        Route::resource('lecturas-microclima', LecturaMicroclimaController::class)->except(['create', 'show', 'edit']);
        Route::resource('controles-incubadora', ControlIncubadoraController::class)->except(['create', 'show', 'edit']);

        Route::get('/reportes/microclima/pdf', [ReporteController::class, 'microclimaPdf'])->name('reportes.microclima.pdf');
    });

Route::prefix('encargado')
    ->name('encargado.')
    ->middleware(['auth', 'check.role:encargado'])
    ->group(function () {

        Route::get('/dashboard', [EncargadoDashboardController::class, 'index'])->name('dashboard');

        Route::resource('seguimientos-lote', SeguimientoLoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('seguimientos-frasco', SeguimientoFrascoController::class)->except(['create', 'show', 'edit']);
        Route::resource('evidencias-lote', EvidenciaLoteController::class)->except(['create', 'show', 'edit']);
        Route::resource('alertas', AlertaController::class)->except(['create', 'show', 'edit']);
        Route::resource('registros-biologicos', RegistroBiologicoController::class)->except(['create', 'show', 'edit']);

        Route::get('/reportes/biologico/pdf', [ReporteController::class, 'biologicoPdf'])->name('reportes.biologico.pdf');
    });

require __DIR__.'/auth.php';
