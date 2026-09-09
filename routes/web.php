<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Api\MicroclimaActuadorController;

use App\Http\Controllers\Encargado\DashboardController as EncargadoDashboardController;
use App\Http\Controllers\Encargado\IncubadoraController as EncargadoIncubadoraController;
use App\Http\Controllers\Encargado\AlertaController as EncargadoAlertaController;

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\AlertaController;
use App\Http\Controllers\SuperAdmin\AsignacionIncubadoraController;
use App\Http\Controllers\SuperAdmin\CondicionOptimaEspecieController;
use App\Http\Controllers\SuperAdmin\ControlIncubadoraController;
use App\Http\Controllers\SuperAdmin\EspecieController;
use App\Http\Controllers\SuperAdmin\EstadoAlertaController;
use App\Http\Controllers\SuperAdmin\EstadoFrascoController;
use App\Http\Controllers\SuperAdmin\EstadoIncubadoraController as SuperAdminEstadoIncubadoraController;
use App\Http\Controllers\SuperAdmin\EstadoLoteController;
use App\Http\Controllers\SuperAdmin\EtapaDesarrolloController;
use App\Http\Controllers\SuperAdmin\EvidenciaLoteController;
use App\Http\Controllers\SuperAdmin\FrascoController;
use App\Http\Controllers\SuperAdmin\IncubadoraController as SuperAdminIncubadoraController;
use App\Http\Controllers\SuperAdmin\LecturaMicroclimaController;
use App\Http\Controllers\SuperAdmin\LoteController;
use App\Http\Controllers\SuperAdmin\ModoControlIncubadoraController;
use App\Http\Controllers\SuperAdmin\NivelAlertaController;
use App\Http\Controllers\SuperAdmin\PosicionIncubadoraController;
use App\Http\Controllers\SuperAdmin\RegistroBiologicoController;
use App\Http\Controllers\SuperAdmin\SeguimientoFrascoController;
use App\Http\Controllers\SuperAdmin\SeguimientoLoteController;
use App\Http\Controllers\SuperAdmin\TipoAlertaController;
use App\Http\Controllers\SuperAdmin\TipoControlIncubadoraController;
use App\Http\Controllers\SuperAdmin\PerfilFotoController;

Route::get('/', fn() => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (!$user->activo) {
            abort(403, 'Tu cuenta se encuentra inactiva.');
        }

        return match ($user->rol_clave) {
            'super_admin' => redirect()->route('super_admin.dashboard'),
            'administrador' => redirect()->route('administrador.dashboard'),
            'encargado' => redirect()->route('encargado.dashboard'),
            default => abort(403, 'Rol no permitido.'),
        };
    })->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::patch('/perfil/foto', [PerfilFotoController::class, 'update'])
        ->name('perfil.foto.update');

    Route::get('/perfil/foto', fn() => redirect()->route('dashboard'));
});


/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('super-admin')
    ->name('super_admin.')
    ->middleware(['auth', 'verified', 'check.role:super_admin'])
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/dashboard/tiempo-real', [SuperAdminDashboardController::class, 'tiempoReal'])
            ->name('dashboard.tiempo-real');

        Route::post(
            '/microclima/actuadores/{actuador}',
            [MicroclimaActuadorController::class, 'update']
        )->name('microclima.actuadores.update');

        Route::get(
            '/incubadoras/{incubadora}/ultima-lectura',
            [SuperAdminDashboardController::class, 'getUltimaLectura']
        )->name('incubadoras.ultima-lectura');


        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        */

        Route::resource('usuarios', SuperAdminUserController::class)
            ->only(['index', 'store', 'update']);


        /*
        |--------------------------------------------------------------------------
        | Alertas
        |--------------------------------------------------------------------------
        */

        Route::resource('alertas', AlertaController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('tipos-alerta', TipoAlertaController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('niveles-alerta', NivelAlertaController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('estados-alerta', EstadoAlertaController::class)
            ->except(['create', 'show', 'edit']);


        /*
        |--------------------------------------------------------------------------
        | Incubadoras
        |--------------------------------------------------------------------------
        */

        Route::resource('incubadoras', SuperAdminIncubadoraController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('estados-incubadora', SuperAdminEstadoIncubadoraController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('posiciones-incubadora', PosicionIncubadoraController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('asignaciones-incubadora', AsignacionIncubadoraController::class)
            ->except(['create', 'show', 'edit']);


        /*
        |--------------------------------------------------------------------------
        | Monitoreo
        |--------------------------------------------------------------------------
        */

        Route::resource('lecturas-microclima', LecturaMicroclimaController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('controles-incubadora', ControlIncubadoraController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('tipos-control-incubadora', TipoControlIncubadoraController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('modos-control-incubadora', ModoControlIncubadoraController::class)
            ->except(['create', 'show', 'edit']);


        /*
        |--------------------------------------------------------------------------
        | Germinación
        |--------------------------------------------------------------------------
        */

        Route::resource('especies', EspecieController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('condiciones-optimas-especie', CondicionOptimaEspecieController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('lotes', LoteController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('estados-lote', EstadoLoteController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('frascos', FrascoController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('estados-frasco', EstadoFrascoController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('etapas-desarrollo', EtapaDesarrolloController::class)
            ->except(['create', 'show', 'edit']);


        /*
        |--------------------------------------------------------------------------
        | Seguimiento biológico
        |--------------------------------------------------------------------------
        */

        Route::resource('seguimientos-lote', SeguimientoLoteController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('seguimientos-frasco', SeguimientoFrascoController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('evidencias-lote', EvidenciaLoteController::class)
            ->except(['create', 'show', 'edit']);

        Route::resource('registros-biologicos', RegistroBiologicoController::class)
            ->except(['create', 'show', 'edit']);


        /*
        |--------------------------------------------------------------------------
        | Reportes
        |--------------------------------------------------------------------------
        */

        Route::get('/reportes/microclima/pdf', [ReporteController::class, 'microclimaPdf'])
            ->name('reportes.microclima.pdf');

        Route::get('/reportes/biologico/pdf', [ReporteController::class, 'biologicoPdf'])
            ->name('reportes.biologico.pdf');
    });


/*
|--------------------------------------------------------------------------
| ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::prefix('administrador')
    ->name('administrador.')
    ->middleware(['auth', 'verified', 'check.role:administrador'])
    ->group(function () {

        Route::get(
            '/dashboard',
            fn() => view('vistas_principales.administrador.dashboard')
        )->name('dashboard');

        Route::resource('usuarios', SuperAdminUserController::class)
            ->only(['index', 'store', 'update']);
    });


/*
|--------------------------------------------------------------------------
| ENCARGADO
|--------------------------------------------------------------------------
*/

Route::prefix('encargado')
    ->name('encargado.')
    ->middleware(['auth', 'verified', 'check.role:encargado'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [EncargadoDashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Mis incubadoras
        |--------------------------------------------------------------------------
        */

        Route::get('/incubadoras', [EncargadoIncubadoraController::class, 'index'])
            ->name('incubadoras.index');

        Route::get('/incubadoras/{incubadora}', [EncargadoIncubadoraController::class, 'show'])
            ->name('incubadoras.show');


        /*
        |--------------------------------------------------------------------------
        | Mis alertas
        |--------------------------------------------------------------------------
        */

        Route::resource('alertas', EncargadoAlertaController::class)
            ->only(['index', 'update']);
    });


require __DIR__ . '/auth.php';
