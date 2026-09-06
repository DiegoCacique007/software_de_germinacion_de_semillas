@php
    $usuario = Auth::user();

    $nombreUsuario = $usuario->name ?? 'Usuario';
    $correoUsuario = $usuario->email ?? 'Sin correo registrado';
    $rolUsuario = $usuario->role ?? 'usuario';

    if ($usuario && method_exists($usuario, 'getRoleNames')) {
        $rolSpatie = $usuario->getRoleNames()->first();

        if ($rolSpatie) {
            $rolUsuario = $rolSpatie;
        }
    }

    $rolNormalizado = strtolower(trim((string) $rolUsuario));

    $rolVisible = strtoupper(
        str_replace(['_', '-'], ' ', $rolUsuario)
    );

    $fotoUsuario = $usuario && !empty($usuario->foto_perfil)
        ? asset('storage/' . $usuario->foto_perfil)
        : null;

    $cantidadAlertas = 0;

    if (isset($alertasActivas)) {
        $cantidadAlertas = is_countable($alertasActivas)
            ? count($alertasActivas)
            : (int) $alertasActivas;
    }

    $cantidadActividad = isset($actividadNoLeida)
        ? (int) $actividadNoLeida
        : 0;

    $rutaInicio = url('/');

    if (
        in_array($rolNormalizado, [
            'super_admin',
            'super-admin',
            'super admin',
            'superadmin',
            'admin',
        ], true)
        && Route::has('super_admin.dashboard')
    ) {
        $rutaInicio = route('super_admin.dashboard');
    } elseif (
        $rolNormalizado === 'administrador'
        && Route::has('semillas.gestion')
    ) {
        $rutaInicio = route('semillas.gestion');
    } elseif (Route::has('dashboard')) {
        $rutaInicio = route('dashboard');
    }

    $modulosDisponibles = [];

    if (
        in_array($rolNormalizado, [
            'super_admin',
            'super-admin',
            'super admin',
            'superadmin',
            'admin',
        ], true)
    ) {
        $modulosDisponibles = [
            [
                'label' => 'Dashboard Global',
                'description' => 'Panel principal del sistema',
                'route' => 'super_admin.dashboard',
            ],
            [
                'label' => 'Usuarios',
                'description' => 'Administración de usuarios',
                'route' => 'super_admin.usuarios.index',
            ],
            [
                'label' => 'Alertas',
                'description' => 'Incidencias registradas',
                'route' => 'super_admin.alertas.index',
            ],
            [
                'label' => 'Tipos de alerta',
                'description' => 'Catálogo de alertas',
                'route' => 'super_admin.tipos-alerta.index',
            ],
            [
                'label' => 'Niveles de alerta',
                'description' => 'Niveles de prioridad',
                'route' => 'super_admin.niveles-alerta.index',
            ],
            [
                'label' => 'Estados de alerta',
                'description' => 'Estados disponibles',
                'route' => 'super_admin.estados-alerta.index',
            ],
            [
                'label' => 'Incubadoras',
                'description' => 'Gestión de dispositivos',
                'route' => 'super_admin.incubadoras.index',
            ],
            [
                'label' => 'Estados de incubadora',
                'description' => 'Catálogo de estados',
                'route' => 'super_admin.estados-incubadora.index',
            ],
            [
                'label' => 'Posiciones de incubadora',
                'description' => 'Ubicación de dispositivos',
                'route' => 'super_admin.posiciones-incubadora.index',
            ],
            [
                'label' => 'Asignaciones de incubadora',
                'description' => 'Usuarios e incubadoras',
                'route' => 'super_admin.asignaciones-incubadora.index',
            ],
            [
                'label' => 'Lecturas de microclima',
                'description' => 'Temperatura y humedad',
                'route' => 'super_admin.lecturas-microclima.index',
            ],
            [
                'label' => 'Controles',
                'description' => 'Automatización del prototipo',
                'route' => 'super_admin.controles-incubadora.index',
            ],
            [
                'label' => 'Tipos de control',
                'description' => 'Catálogo de controles',
                'route' => 'super_admin.tipos-control-incubadora.index',
            ],
            [
                'label' => 'Modos de control',
                'description' => 'Automático y manual',
                'route' => 'super_admin.modos-control-incubadora.index',
            ],
            [
                'label' => 'Especies',
                'description' => 'Catálogo de semillas',
                'route' => 'super_admin.especies.index',
            ],
            [
                'label' => 'Condiciones óptimas',
                'description' => 'Parámetros ambientales',
                'route' => 'super_admin.condiciones-optimas-especie.index',
            ],
            [
                'label' => 'Lotes',
                'description' => 'Lotes de germinación',
                'route' => 'super_admin.lotes.index',
            ],
            [
                'label' => 'Estados de lote',
                'description' => 'Estados de los lotes',
                'route' => 'super_admin.estados-lote.index',
            ],
            [
                'label' => 'Frascos',
                'description' => 'Contenedores registrados',
                'route' => 'super_admin.frascos.index',
            ],
            [
                'label' => 'Estados de frasco',
                'description' => 'Estados de los frascos',
                'route' => 'super_admin.estados-frasco.index',
            ],
            [
                'label' => 'Etapas de desarrollo',
                'description' => 'Fases de germinación',
                'route' => 'super_admin.etapas-desarrollo.index',
            ],
            [
                'label' => 'Seguimientos de lote',
                'description' => 'Control biológico',
                'route' => 'super_admin.seguimientos-lote.index',
            ],
            [
                'label' => 'Seguimientos de frasco',
                'description' => 'Control de frascos',
                'route' => 'super_admin.seguimientos-frascos.index',
            ],
            [
                'label' => 'Evidencias de lote',
                'description' => 'Fotografías y archivos',
                'route' => 'super_admin.evidencias-lote.index',
            ],
            [
                'label' => 'Registros biológicos',
                'description' => 'Observaciones del cultivo',
                'route' => 'super_admin.registros-biologicos.index',
            ],
        ];
    }

    if ($rolNormalizado === 'administrador') {
        $modulosDisponibles = [
            [
                'label' => 'Gestión de semillas',
                'description' => 'Administración de semillas',
                'route' => 'semillas.gestion',
            ],
        ];
    }

    $modulosBusqueda = collect($modulosDisponibles)
        ->filter(fn ($modulo) => Route::has($modulo['route']))
        ->map(fn ($modulo) => [
            'label' => $modulo['label'],
            'description' => $modulo['description'],
            'url' => route($modulo['route']),
        ])
        ->values()
        ->all();
@endphp


<style>
    [x-cloak] {
        display: none !important;
    }

    /* ================================================================
       TIPOGRAFÍA
       Misma familia visual utilizada por el sidebar
       ================================================================ */

    .microseed-topbar {
        --microseed-navigation-font:
            'Instrument Sans',
            ui-sans-serif,
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            'Segoe UI',
            sans-serif;

        position: relative;
        z-index: 90;

        width: 100%;
        height: 78px;

        display: flex;
        align-items: center;
        flex-shrink: 0;

        border-bottom: 1px solid #e5eaed;

        background: rgba(255, 255, 255, 0.98);

        box-shadow:
            0 5px 20px rgba(20, 66, 85, 0.045);

        font-family: var(--microseed-navigation-font);
        font-size: 14px;
        line-height: 1.5;

        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .microseed-topbar *,
    .microseed-topbar *::before,
    .microseed-topbar *::after {
        box-sizing: border-box;
        font-family: var(--microseed-navigation-font);
    }

    .microseed-topbar button {
        margin: 0;
        padding: 0;

        border: 0;
        outline: 0;

        background: transparent;
        color: inherit;

        font: inherit;

        cursor: pointer;

        appearance: none;
        -webkit-appearance: none;
    }

    .microseed-topbar a {
        color: inherit;
        text-decoration: none;
    }


    /* ================================================================
       ESTRUCTURA TOPBAR
       ================================================================ */

    .microseed-topbar-content {
        width: 100%;
        height: 100%;
        min-width: 0;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 18px;

        padding: 0 16px 0 24px;
    }

    .microseed-topbar-left {
        min-width: 0;
        flex: 1;

        display: flex;
        align-items: center;

        gap: 12px;
    }

    .microseed-topbar-actions {
        display: flex;
        align-items: center;

        flex-shrink: 0;

        gap: 5px;
    }


    /* ================================================================
       BUSCADOR
       ================================================================ */

    .microseed-search-wrapper {
        position: relative;

        width: min(550px, 100%);
    }

    .microseed-search {
        position: relative;

        width: 100%;
        height: 46px;

        display: flex;
        align-items: center;
    }

    .microseed-search-icon {
        position: absolute;

        left: 16px;
        z-index: 2;

        width: 18px;
        height: 18px;

        color: #8294a3;

        pointer-events: none;
    }

    .microseed-search-input {
        width: 100%;
        height: 46px;

        padding:
            0
            48px
            0
            47px;

        color: #334155;

        background: #f4f7f7;

        border: 1px solid transparent;
        border-radius: 15px;

        outline: 0;

        font-size: 13px;
        font-weight: 500;

        transition:
            background 0.17s ease,
            border-color 0.17s ease,
            box-shadow 0.17s ease;
    }

    .microseed-search-input::placeholder {
        color: #99a5ae;
    }

    .microseed-search-input:focus {
        background: #ffffff;

        border-color:
            rgba(59, 180, 156, 0.38);

        box-shadow:
            0 0 0 4px
            rgba(59, 180, 156, 0.09);
    }

    .microseed-search-results {
        position: absolute;

        top: calc(100% + 10px);
        left: 0;

        z-index: 160;

        width: 100%;
        max-height: 390px;

        overflow-y: auto;

        background: #ffffff;

        border: 1px solid #e2e8ea;
        border-radius: 17px;

        box-shadow:
            0 24px 60px
            rgba(15, 23, 42, 0.18);
    }

    .microseed-search-results-header {
        position: sticky;

        top: 0;
        z-index: 2;

        padding: 11px 14px;

        color: #94a3b8;

        background: rgba(255, 255, 255, 0.98);

        border-bottom: 1px solid #edf2f2;

        font-size: 9px;
        font-weight: 700;

        letter-spacing: 0.13em;

        text-transform: uppercase;
    }

    .microseed-search-result {
        width: 100%;

        display: flex;
        align-items: center;

        gap: 11px;

        padding: 11px 13px;

        text-align: left;

        transition:
            background 0.16s ease;
    }

    .microseed-search-result:hover,
    .microseed-search-result:focus-visible {
        background: #effaf8;
        outline: none;
    }

    .microseed-search-result-icon {
        width: 37px;
        height: 37px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        color: #216a73;

        background:
            linear-gradient(
                135deg,
                rgba(33, 106, 115, 0.10),
                rgba(59, 180, 156, 0.14)
            );

        border-radius: 12px;
    }

    .microseed-search-result-text {
        min-width: 0;
    }

    .microseed-search-result-title {
        display: block;

        overflow: hidden;

        color: #334155;

        font-size: 12px;
        font-weight: 700;

        line-height: 1.2;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-search-result-description {
        display: block;

        margin-top: 3px;

        overflow: hidden;

        color: #94a3b8;

        font-size: 10px;
        font-weight: 500;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-search-empty {
        padding: 28px 16px;

        color: #94a3b8;

        font-size: 12px;
        font-weight: 500;

        text-align: center;
    }


    /* ================================================================
       BOTONES SUPERIORES
       ================================================================ */

    .microseed-action-wrapper {
        position: relative;
    }

    .microseed-action-button {
        position: relative;

        width: 43px;
        height: 43px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #64748b;

        border-radius: 13px;

        transition:
            color 0.17s ease,
            background 0.17s ease,
            transform 0.17s ease;
    }

    .microseed-action-button:hover,
    .microseed-action-active {
        color: #216a73;

        background: #eef8f7;
    }

    .microseed-action-button:hover {
        transform: translateY(-1px);
    }

    .microseed-action-button:focus-visible {
        outline: none;

        box-shadow:
            0 0 0 4px
            rgba(59, 180, 156, 0.10);
    }

    .microseed-action-button svg {
        width: 20px;
        height: 20px;
    }

    .microseed-counter {
        position: absolute;

        top: 0;
        right: -1px;

        min-width: 18px;
        height: 18px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 0 4px;

        color: #ffffff;

        background: #dc3545;

        border: 2px solid #ffffff;
        border-radius: 9999px;

        font-size: 8px;
        font-weight: 800;

        line-height: 1;
    }

    .microseed-counter-green {
        background: #3bb49c;
    }


    /* ================================================================
       DROPDOWN GENERAL
       ================================================================ */

    .microseed-dropdown {
        position: absolute;

        top: calc(100% + 12px);
        right: 0;

        z-index: 170;

        width: 310px;

        overflow: hidden;

        background: #ffffff;

        border: 1px solid #e2e8ea;
        border-radius: 18px;

        box-shadow:
            0 24px 60px
            rgba(15, 23, 42, 0.20);
    }

    .microseed-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 12px;

        padding: 15px 16px;

        border-bottom: 1px solid #eef2f2;
    }

    .microseed-dropdown-title {
        margin: 0;

        color: #334155;

        font-size: 13px;
        font-weight: 700;
    }

    .microseed-dropdown-count {
        padding: 4px 8px;

        color: #1f756a;

        background: #e9f8f4;

        border-radius: 999px;

        font-size: 9px;
        font-weight: 700;
    }

    .microseed-notification {
        display: flex;
        align-items: flex-start;

        gap: 11px;

        padding: 14px 16px;
    }

    .microseed-notification-icon {
        width: 39px;
        height: 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        color: #dc3545;

        background: #fff1f2;

        border-radius: 12px;
    }

    .microseed-notification-icon svg {
        width: 18px;
        height: 18px;
    }

    .microseed-notification-title {
        display: block;

        color: #334155;

        font-size: 12px;
        font-weight: 700;
    }

    .microseed-notification-description {
        display: block;

        margin-top: 3px;

        color: #94a3b8;

        font-size: 10px;
        font-weight: 500;

        line-height: 1.45;
    }

    .microseed-dropdown-empty {
        padding: 27px 18px;

        text-align: center;
    }

    .microseed-empty-icon {
        width: 46px;
        height: 46px;

        margin: 0 auto;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #216a73;

        background: #eaf8f5;

        border-radius: 14px;
    }

    .microseed-empty-title {
        margin: 10px 0 0;

        color: #475569;

        font-size: 12px;
        font-weight: 700;
    }

    .microseed-empty-description {
        margin: 4px 0 0;

        color: #94a3b8;

        font-size: 10px;
        font-weight: 500;
    }

    .microseed-dropdown-footer {
        display: block;

        padding: 11px 15px;

        color: #1c607a;

        background: #f8faf9;

        border-top: 1px solid #eef2f2;

        font-size: 11px;
        font-weight: 700;

        text-align: center;

        transition:
            background 0.16s ease;
    }

    .microseed-dropdown-footer:hover {
        background: #effaf8;
    }


    /* ================================================================
       PERFIL - ZONA HOVER
       ================================================================ */

    .microseed-profile-wrapper {
        position: relative;

        margin-left: 3px;
        margin-right: 5px;

        flex-shrink: 0;

        /*
         * El dropdown vive dentro de este contenedor.
         * Al mover el mouse del botón hacia el dropdown,
         * seguimos dentro de la misma zona hover.
         */
        padding-bottom: 12px;
        margin-bottom: -12px;
    }

    .microseed-profile-button {
        width: 238px;
        height: 52px;

        display: flex;
        align-items: center;

        gap: 11px;

        padding:
            5px
            9px
            5px
            6px;

        color: #334155;

        background: transparent;

        border-radius: 16px;

        transition:
            background 0.17s ease,
            box-shadow 0.17s ease,
            transform 0.17s ease;
    }

    .microseed-profile-button:hover,
    .microseed-profile-active {
        background:
            linear-gradient(
                135deg,
                rgba(33, 106, 115, 0.055),
                rgba(59, 180, 156, 0.08)
            );

        box-shadow:
            inset 0 0 0 1px
            rgba(59, 180, 156, 0.13);
    }

    .microseed-profile-button:hover {
        transform: translateY(-1px);
    }

    .microseed-profile-button:focus-visible {
        outline: none;

        box-shadow:
            0 0 0 4px
            rgba(59, 180, 156, 0.10);
    }


    /* ================================================================
       AVATAR PEQUEÑO
       ================================================================ */

    .microseed-profile-avatar {
        position: relative;

        width: 44px;
        height: 44px;

        flex: 0 0 44px;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: visible;

        color: #ffffff;

        background:
            linear-gradient(
                135deg,
                #216a73 0%,
                #3bb49c 100%
            );

        border:
            1px solid
            rgba(59, 180, 156, 0.35);

        border-radius: 14px;

        box-shadow:
            0 8px 18px
            rgba(33, 106, 115, 0.13);

        font-size: 13px;
        font-weight: 800;
    }

    .microseed-profile-avatar img {
        width: 44px;
        height: 44px;

        display: block;

        object-fit: cover;

        border-radius: 14px;
    }

    .microseed-profile-online {
        position: absolute;

        right: -3px;
        bottom: -3px;

        width: 11px;
        height: 11px;

        display: block;

        background: #3bb49c;

        border: 2px solid #ffffff;
        border-radius: 999px;

        box-shadow:
            0 0 0 2px
            rgba(59, 180, 156, 0.12);
    }

    .microseed-profile-information {
        min-width: 0;

        flex: 1;

        display: block;

        text-align: left;
    }

    .microseed-profile-name {
        display: block;

        max-width: 155px;

        overflow: hidden;

        color: #334155;

        font-size: 13px;
        font-weight: 700;

        line-height: 1.15;

        letter-spacing: -0.015em;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-profile-role {
        display: block;

        max-width: 155px;

        margin-top: 4px;

        overflow: hidden;

        color: #3b9a96;

        font-size: 9px;
        font-weight: 700;

        line-height: 1;

        letter-spacing: 0.10em;

        text-overflow: ellipsis;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .microseed-profile-chevron {
        width: 15px;
        height: 15px;

        flex-shrink: 0;

        color: #94a3b8;

        transition:
            transform 0.18s ease;
    }

    .microseed-profile-chevron-open {
        transform: rotate(180deg);
    }


    /* ================================================================
       DROPDOWN PERFIL
       Inspirado directamente en sidebar
       ================================================================ */

    .microseed-profile-dropdown {
        top: calc(100% + 2px);

        width: 320px;

        border: 0;

        border-radius: 20px;

        box-shadow:
            0 26px 65px
            rgba(15, 23, 42, 0.20);
    }


    /* ================================================================
       CABECERA PERFIL
       MISMO LENGUAJE DEL SIDEBAR
       ================================================================ */

    .microseed-profile-header {
        position: relative;

        min-height: 108px;

        overflow: hidden;

        padding:
            17px
            17px
            16px;

        color: #ffffff;

        background:
            radial-gradient(
                circle at 88% 6%,
                rgba(94, 224, 194, 0.26),
                transparent 43%
            ),
            radial-gradient(
                circle at 5% 100%,
                rgba(18, 63, 84, 0.24),
                transparent 42%
            ),
            linear-gradient(
                135deg,
                #123f54 0%,
                #176475 50%,
                #2b9691 100%
            );
    }

    .microseed-profile-header::before {
        content: '';

        position: absolute;

        top: -72px;
        right: -55px;

        width: 180px;
        height: 180px;

        background:
            rgba(255, 255, 255, 0.065);

        border-radius: 999px;

        pointer-events: none;
    }

    .microseed-profile-header::after {
        content: '';

        position: absolute;

        right: 18px;
        bottom: 0;
        left: 18px;

        height: 1px;

        background:
            linear-gradient(
                90deg,
                transparent,
                rgba(113, 231, 205, 0.65),
                transparent
            );

        pointer-events: none;
    }

    .microseed-profile-header-row {
        position: relative;

        z-index: 2;

        display: flex;
        align-items: center;

        gap: 14px;
    }

    .microseed-profile-large-avatar {
        width: 62px;
        height: 62px;

        flex: 0 0 62px;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;

        color: #ffffff;

        background:
            rgba(10, 52, 66, 0.72);

        border:
            1px solid
            rgba(126, 231, 211, 0.52);

        border-radius: 16px;

        box-shadow:
            0 12px 26px
            rgba(7, 37, 49, 0.22);

        font-size: 19px;
        font-weight: 800;
    }

    .microseed-profile-large-avatar img {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;
    }

    .microseed-profile-header-information {
        min-width: 0;
    }

    .microseed-profile-dropdown-name {
        display: block;

        max-width: 210px;

        overflow: hidden;

        color: #ffffff;

        font-size: 15px;
        font-weight: 800;

        line-height: 1.2;

        letter-spacing: -0.025em;

        text-overflow: ellipsis;
        white-space: nowrap;

        text-shadow:
            0 2px 8px
            rgba(7, 37, 49, 0.2);
    }

    .microseed-profile-dropdown-email {
        display: block;

        max-width: 210px;

        margin-top: 5px;

        overflow: hidden;

        color:
            rgba(232, 255, 249, 0.82);

        font-size: 11px;
        font-weight: 500;

        line-height: 1.2;

        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-profile-dropdown-role {
        display: inline-flex;
        align-items: center;

        width: fit-content;

        margin-top: 8px;

        padding:
            4px
            10px;

        color: #ffffff;

        background:
            rgba(255, 255, 255, 0.14);

        border:
            1px solid
            rgba(255, 255, 255, 0.24);

        border-radius: 999px;

        font-size: 9px;
        font-weight: 700;

        letter-spacing: 0.07em;

        text-transform: uppercase;
    }


    /* ================================================================
       ACCIONES PERFIL
       ================================================================ */

    .microseed-profile-body {
        padding: 8px;
    }

    .microseed-profile-body form {
        margin: 0;
    }

    .microseed-profile-action {
        width: 100%;
        min-height: 54px;

        display: flex;
        align-items: center;

        gap: 11px;

        margin: 0;

        padding: 9px;

        color: #475569;

        background: transparent;

        border: 0;
        border-radius: 15px;

        outline: none;

        text-align: left;

        cursor: pointer;

        transition:
            color 0.16s ease,
            background 0.16s ease,
            transform 0.16s ease;
    }

    .microseed-profile-action:hover {
        color: #216a73;

        background:
            rgba(236, 254, 255, 0.90);

        transform: translateX(2px);
    }

    .microseed-profile-action-icon {
        width: 39px;
        height: 39px;

        flex: 0 0 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #216a73;

        background:
            linear-gradient(
                135deg,
                rgba(33, 106, 115, 0.10),
                rgba(59, 180, 156, 0.14)
            );

        border-radius: 13px;

        transition:
            color 0.16s ease,
            background 0.16s ease,
            box-shadow 0.16s ease;
    }

    .microseed-profile-action:hover
    .microseed-profile-action-icon {
        color: #ffffff;

        background:
            linear-gradient(
                135deg,
                #216a73 0%,
                #3bb49c 100%
            );

        box-shadow:
            0 8px 18px
            rgba(33, 106, 115, 0.18);
    }

    .microseed-profile-action-icon svg {
        width: 18px;
        height: 18px;
    }

    .microseed-profile-action-text {
        min-width: 0;

        display: block;
    }

    .microseed-profile-action-label {
        display: block;

        color: inherit;

        font-size: 12px;
        font-weight: 700;

        line-height: 1.2;

        white-space: nowrap;
    }

    .microseed-profile-action-description {
        display: block;

        margin-top: 3px;

        color: #94a3b8;

        font-size: 10px;
        font-weight: 500;

        line-height: 1.2;

        white-space: nowrap;
    }

    .microseed-profile-photo-input {
        display: none !important;
    }

    .microseed-profile-separator {
        height: 1px;

        margin: 6px 8px;

        background: #eef2f2;
    }


    /* ================================================================
       CERRAR SESIÓN
       ================================================================ */

    .microseed-profile-action-danger {
        color: #dc3545;
    }

    .microseed-profile-action-danger:hover {
        color: #c92f40;

        background: #fff1f2;
    }

    .microseed-profile-action-danger
    .microseed-profile-action-icon {
        color: #dc3545;

        background:
            rgba(220, 53, 69, 0.08);
    }

    .microseed-profile-action-danger:hover
    .microseed-profile-action-icon {
        color: #ffffff;

        background: #dc3545;
    }


    /* ================================================================
       MÓVIL
       ================================================================ */

    .microseed-mobile-menu-button {
        display: none;

        width: 42px;
        height: 42px;

        align-items: center;
        justify-content: center;

        color: #64748b;

        background: #f5f7f7;

        border-radius: 12px;
    }

    .microseed-mobile-panel {
        position: absolute;

        top: 100%;
        right: 0;
        left: 0;

        z-index: 150;

        padding: 12px;

        background: #ffffff;

        border-top: 1px solid #e5eaed;

        box-shadow:
            0 18px 35px
            rgba(15, 23, 42, 0.12);
    }

    .microseed-mobile-user {
        display: flex;
        align-items: center;

        gap: 11px;

        padding: 12px;

        background: #f7faf9;

        border-radius: 14px;
    }

    .microseed-mobile-link {
        width: 100%;
        min-height: 43px;

        margin-top: 5px;

        display: flex;
        align-items: center;

        gap: 11px;

        padding: 10px 12px;

        color: #64748b;

        background: transparent;

        border-radius: 11px;

        font-size: 12px;
        font-weight: 600;

        transition:
            color 0.16s ease,
            background 0.16s ease;
    }

    .microseed-mobile-link:hover {
        color: #216a73;

        background: #effaf8;
    }

    .microseed-mobile-link-danger {
        color: #dc3545;
    }


    /* ================================================================
       MODAL LOGOUT
       ================================================================ */

    .microseed-logout-backdrop {
        position: fixed;

        inset: 0;

        z-index: 99999;

        width: 100vw;
        height: 100vh;

        display: grid;
        place-items: center;

        padding: 16px;

        background:
            rgba(15, 23, 42, 0.45);

        backdrop-filter: blur(2px);
    }

    .microseed-logout-modal {
        width: min(340px, 100%);

        overflow: hidden;

        background: #ffffff;

        border:
            1px solid
            rgba(59, 180, 156, 0.22);

        border-radius: 20px;

        box-shadow:
            0 22px 60px
            rgba(15, 23, 42, 0.24);
    }

    .microseed-logout-modal-header {
        padding:
            22px
            22px
            14px;

        text-align: center;
    }

    .microseed-logout-icon {
        width: 50px;
        height: 50px;

        margin:
            0
            auto
            13px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #216a73;

        background:
            linear-gradient(
                135deg,
                rgba(33, 106, 115, 0.10),
                rgba(59, 180, 156, 0.16)
            );

        border-radius: 16px;

        box-shadow:
            inset 0 0 0 1px
            rgba(59, 180, 156, 0.16);
    }

    .microseed-logout-icon svg {
        width: 24px;
        height: 24px;
    }

    .microseed-logout-title {
        margin: 0;

        color: #334155;

        font-size: 17px;
        font-weight: 800;

        line-height: 1.2;
    }

    .microseed-logout-description {
        max-width: 265px;

        margin:
            8px
            auto
            0;

        color: #64748b;

        font-size: 12px;
        font-weight: 500;

        line-height: 1.45;
    }

    .microseed-logout-actions {
        display: grid;

        grid-template-columns:
            1fr
            1fr;

        gap: 10px;

        padding:
            14px
            20px
            20px;
    }

    .microseed-logout-cancel,
    .microseed-logout-confirm {
        min-height: 41px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border-radius: 13px;

        font-size: 11px;
        font-weight: 700;
    }

    .microseed-logout-cancel {
        color: #475569;

        background: #f1f5f4;
    }

    .microseed-logout-cancel:hover {
        background: #e6ecec;
    }

    .microseed-logout-confirm {
        color: #ffffff;

        background:
            linear-gradient(
                135deg,
                #216a73,
                #3bb49c
            );

        box-shadow:
            0 10px 22px
            rgba(33, 106, 115, 0.20);
    }

    .microseed-logout-confirm:hover {
        filter: brightness(0.96);
    }


    /* ================================================================
       RESPONSIVE
       ================================================================ */

    @media (max-width: 1100px) {
        .microseed-search-wrapper {
            max-width: 400px;
        }

        .microseed-profile-button {
            width: 210px;
        }

        .microseed-profile-name,
        .microseed-profile-role {
            max-width: 132px;
        }
    }

    @media (max-width: 900px) {
        .microseed-profile-button {
            width: 48px;
            height: 48px;

            padding: 2px;

            justify-content: center;
        }

        .microseed-profile-information,
        .microseed-profile-chevron {
            display: none;
        }
    }

    @media (max-width: 760px) {
        .microseed-topbar {
            height: 70px;
        }

        .microseed-topbar-content {
            gap: 8px;

            padding:
                0
                12px;
        }

        .microseed-search-wrapper {
            max-width: 250px;
        }

        .microseed-dropdown {
            position: fixed;

            top: 76px;
            right: 12px;
            left: 12px;

            width: auto;
        }

        .microseed-profile-dropdown {
            width: auto;
        }
    }

    @media (max-width: 560px) {
        .microseed-search-wrapper,
        .microseed-topbar-actions {
            display: none;
        }

        .microseed-topbar-left {
            display: none;
        }

        .microseed-topbar-content {
            justify-content: flex-end;
        }

        .microseed-profile-wrapper {
            display: none;
        }

        .microseed-mobile-menu-button {
            display: flex;
        }
    }

    @media (max-width: 460px) {
        .microseed-logout-modal {
            width: min(315px, 100%);
        }

        .microseed-logout-actions {
            grid-template-columns: 1fr;
        }
    }
</style>


<nav
    class="microseed-topbar"

    x-data="{
        searchValue: '',
        searchOpen: false,
        openPanel: null,
        mobileOpen: false,
        logoutModalOpen: false,

        modules: @js($modulosBusqueda),

        get filteredModules() {
            const query = this.searchValue
                .trim()
                .toLowerCase();

            if (!query) {
                return this.modules.slice(0, 6);
            }

            return this.modules
                .filter(module => {
                    return module.label
                            .toLowerCase()
                            .includes(query)
                        || module.description
                            .toLowerCase()
                            .includes(query);
                })
                .slice(0, 8);
        },

        togglePanel(panel) {
            this.openPanel =
                this.openPanel === panel
                    ? null
                    : panel;

            this.searchOpen = false;
        },

        openProfile() {
            this.searchOpen = false;
            this.openPanel = 'profile';
        },

        closeProfile() {
            if (this.openPanel === 'profile') {
                this.openPanel = null;
            }
        },

        openLogoutModal() {
            this.openPanel = null;
            this.mobileOpen = false;
            this.logoutModalOpen = true;
        },

        closeLogoutModal() {
            this.logoutModalOpen = false;
        },

        confirmLogout() {
            this.$refs.logoutForm.submit();
        },

        goToModule(url) {
            if (url) {
                window.location.href = url;
            }
        },

        init() {
            window.addEventListener(
                'keydown',
                event => {
                    if (
                        (event.ctrlKey || event.metaKey)
                        && event.key.toLowerCase() === 'k'
                    ) {
                        event.preventDefault();

                        this.openPanel = null;
                        this.searchOpen = true;

                        this.$nextTick(() => {
                            this.$refs.globalSearch?.focus();
                        });
                    }

                    if (event.key === 'Escape') {
                        this.searchOpen = false;
                        this.openPanel = null;
                        this.mobileOpen = false;
                        this.logoutModalOpen = false;
                    }
                }
            );
        }
    }"
>
    <div class="microseed-topbar-content">

        {{-- ========================================================= --}}
        {{-- BUSCADOR --}}
        {{-- ========================================================= --}}

        <div class="microseed-topbar-left">
            <div
                class="microseed-search-wrapper"
                @click.outside="searchOpen = false"
            >
                <div class="microseed-search">
                    <svg
                        class="microseed-search-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>

                    <input
                        x-ref="globalSearch"
                        x-model="searchValue"
                        type="search"
                        class="microseed-search-input"
                        placeholder="Buscar módulos..."
                        autocomplete="off"

                        @focus="
                            searchOpen = true;
                            openPanel = null;
                        "

                        @input="searchOpen = true"

                        @keydown.enter.prevent="
                            if (filteredModules.length) {
                                goToModule(
                                    filteredModules[0].url
                                );
                            }
                        "
                    >
                </div>

                <div
                    x-show="searchOpen"
                    x-cloak
                    x-transition.opacity
                    class="microseed-search-results"
                >
                    <div class="microseed-search-results-header">
                        Acceso rápido
                    </div>

                    <template x-if="filteredModules.length > 0">
                        <div>
                            <template
                                x-for="module in filteredModules"
                                :key="module.url"
                            >
                                <button
                                    type="button"
                                    class="microseed-search-result"
                                    @click="goToModule(module.url)"
                                >
                                    <span class="microseed-search-result-icon">
                                        <svg
                                            width="17"
                                            height="17"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 12h14m-6-6 6 6-6 6"
                                            />
                                        </svg>
                                    </span>

                                    <span class="microseed-search-result-text">
                                        <span
                                            class="microseed-search-result-title"
                                            x-text="module.label"
                                        ></span>

                                        <span
                                            class="microseed-search-result-description"
                                            x-text="module.description"
                                        ></span>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="filteredModules.length === 0">
                        <div class="microseed-search-empty">
                            No se encontraron módulos.
                        </div>
                    </template>
                </div>
            </div>
        </div>


        {{-- ========================================================= --}}
        {{-- ACCIONES --}}
        {{-- ========================================================= --}}

        <div class="microseed-topbar-actions">

            {{-- NOTIFICACIONES --}}

            <div
                class="microseed-action-wrapper"

                @click.outside="
                    if (openPanel === 'notifications') {
                        openPanel = null;
                    }
                "
            >
                <button
                    type="button"
                    class="microseed-action-button"

                    :class="
                        openPanel === 'notifications'
                            ? 'microseed-action-active'
                            : ''
                    "

                    aria-label="Notificaciones"

                    @click="togglePanel('notifications')"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0"
                        />
                    </svg>

                    @if($cantidadAlertas > 0)
                        <span class="microseed-counter">
                            {{ $cantidadAlertas > 99
                                ? '99+'
                                : $cantidadAlertas }}
                        </span>
                    @endif
                </button>

                <div
                    x-show="openPanel === 'notifications'"
                    x-cloak
                    x-transition.opacity
                    class="microseed-dropdown"
                >
                    <div class="microseed-dropdown-header">
                        <p class="microseed-dropdown-title">
                            Notificaciones
                        </p>

                        <span class="microseed-dropdown-count">
                            {{ $cantidadAlertas }}
                            {{ $cantidadAlertas === 1
                                ? 'activa'
                                : 'activas' }}
                        </span>
                    </div>

                    @if($cantidadAlertas > 0)
                        <div class="microseed-notification">
                            <span class="microseed-notification-icon">
                                <svg
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v3m0 4h.01M10.3 3.9 1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"
                                    />
                                </svg>
                            </span>

                            <span>
                                <span class="microseed-notification-title">
                                    Alertas pendientes
                                </span>

                                <span class="microseed-notification-description">
                                    Existen {{ $cantidadAlertas }}
                                    {{ $cantidadAlertas === 1
                                        ? 'incidencia pendiente de revisión.'
                                        : 'incidencias pendientes de revisión.' }}
                                </span>
                            </span>
                        </div>
                    @else
                        <div class="microseed-dropdown-empty">
                            <span class="microseed-empty-icon">
                                <svg
                                    width="21"
                                    height="21"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m5 13 4 4L19 7"
                                    />
                                </svg>
                            </span>

                            <p class="microseed-empty-title">
                                Sin alertas activas
                            </p>

                            <p class="microseed-empty-description">
                                El sistema opera sin incidencias.
                            </p>
                        </div>
                    @endif

                    @if(Route::has('super_admin.alertas.index'))
                        <a
                            href="{{ route('super_admin.alertas.index') }}"
                            class="microseed-dropdown-footer"
                        >
                            Ver todas las alertas
                        </a>
                    @endif
                </div>
            </div>


            {{-- ACTIVIDAD --}}

            <div
                class="microseed-action-wrapper activity-action"

                @click.outside="
                    if (openPanel === 'activity') {
                        openPanel = null;
                    }
                "
            >
                <button
                    type="button"
                    class="microseed-action-button"

                    :class="
                        openPanel === 'activity'
                            ? 'microseed-action-active'
                            : ''
                    "

                    aria-label="Actividad"

                    @click="togglePanel('activity')"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 15a4 4 0 01-4 4H8l-5 3v-7a4 4 0 01-1-2.7V7a4 4 0 014-4h11a4 4 0 014 4v8z"
                        />
                    </svg>

                    @if($cantidadActividad > 0)
                        <span class="microseed-counter microseed-counter-green">
                            {{ $cantidadActividad > 99
                                ? '99+'
                                : $cantidadActividad }}
                        </span>
                    @endif
                </button>

                <div
                    x-show="openPanel === 'activity'"
                    x-cloak
                    x-transition.opacity
                    class="microseed-dropdown"
                >
                    <div class="microseed-dropdown-header">
                        <p class="microseed-dropdown-title">
                            Actividad
                        </p>

                        <span class="microseed-dropdown-count">
                            {{ $cantidadActividad }} nuevas
                        </span>
                    </div>

                    <div class="microseed-dropdown-empty">
                        <span class="microseed-empty-icon">
                            <svg
                                width="21"
                                height="21"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 8h10M7 12h7m-9 9 3.5-3H18a3 3 0 003-3V6a3 3 0 00-3-3H6a3 3 0 00-3 3v9a3 3 0 002 2.8V21z"
                                />
                            </svg>
                        </span>

                        <p class="microseed-empty-title">
                            Sin actividad nueva
                        </p>

                        <p class="microseed-empty-description">
                            Las novedades aparecerán en este apartado.
                        </p>
                    </div>
                </div>
            </div>


            {{-- ===================================================== --}}
            {{-- PERFIL - HOVER COMO SIDEBAR --}}
            {{-- ===================================================== --}}

            <div
                class="microseed-profile-wrapper"

                @mouseenter="openProfile()"
                @mouseleave="closeProfile()"

                @click.outside="
                    if (openPanel === 'profile') {
                        openPanel = null;
                    }
                "
            >
                <button
                    type="button"
                    class="microseed-profile-button"

                    :class="
                        openPanel === 'profile'
                            ? 'microseed-profile-active'
                            : ''
                    "

                    aria-label="Menú de usuario"

                    :aria-expanded="
                        openPanel === 'profile'
                    "

                    @click="togglePanel('profile')"
                >
                    <span class="microseed-profile-avatar">
                        @if($fotoUsuario)
                            <img
                                src="{{ $fotoUsuario }}"
                                alt="Foto de {{ $nombreUsuario }}"
                            >
                        @else
                            {{ strtoupper(
                                substr(
                                    $nombreUsuario,
                                    0,
                                    1
                                )
                            ) }}
                        @endif

                        <span class="microseed-profile-online"></span>
                    </span>

                    <span class="microseed-profile-information">
                        <span class="microseed-profile-name">
                            {{ $nombreUsuario }}
                        </span>

                        <span class="microseed-profile-role">
                            {{ $rolVisible }}
                        </span>
                    </span>

                    <svg
                        class="microseed-profile-chevron"

                        :class="
                            openPanel === 'profile'
                                ? 'microseed-profile-chevron-open'
                                : ''
                        "

                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m19 9-7 7-7-7"
                        />
                    </svg>
                </button>


                {{-- DROPDOWN DEL PERFIL --}}

                <div
                    x-show="openPanel === 'profile'"
                    x-cloak
                    x-transition.opacity
                    class="microseed-dropdown microseed-profile-dropdown"
                >

                    {{-- HEADER --}}

                    <div class="microseed-profile-header">
                        <div class="microseed-profile-header-row">

                            <span class="microseed-profile-large-avatar">
                                @if($fotoUsuario)
                                    <img
                                        src="{{ $fotoUsuario }}"
                                        alt="Foto de {{ $nombreUsuario }}"
                                    >
                                @else
                                    {{ strtoupper(
                                        substr(
                                            $nombreUsuario,
                                            0,
                                            1
                                        )
                                    ) }}
                                @endif
                            </span>

                            <span class="microseed-profile-header-information">

                                <span class="microseed-profile-dropdown-name">
                                    {{ $nombreUsuario }}
                                </span>

                                <span class="microseed-profile-dropdown-email">
                                    {{ $correoUsuario }}
                                </span>

                                <span class="microseed-profile-dropdown-role">
                                    {{ $rolVisible }}
                                </span>

                            </span>
                        </div>
                    </div>


                    {{-- OPCIONES --}}

                    <div class="microseed-profile-body">

                        {{-- MI PERFIL --}}

                        @if(Route::has('profile.edit'))
                            <a
                                href="{{ route('profile.edit') }}"
                                class="microseed-profile-action"
                            >
                                <span class="microseed-profile-action-icon">
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"
                                        />
                                    </svg>
                                </span>

                                <span class="microseed-profile-action-text">
                                    <span class="microseed-profile-action-label">
                                        Mi perfil
                                    </span>

                                    <span class="microseed-profile-action-description">
                                        Ver y editar cuenta
                                    </span>
                                </span>
                            </a>
                        @endif


                        {{-- CAMBIAR FOTO --}}

                        @if(Route::has('perfil.foto.update'))
                            <form
                                id="topbar-photo-form"
                                method="POST"
                                action="{{ route('perfil.foto.update') }}"
                                enctype="multipart/form-data"
                            >
                                @csrf
                                @method('PATCH')

                                <label
                                    for="topbar_foto_perfil"
                                    class="microseed-profile-action"
                                >
                                    <span class="microseed-profile-action-icon">
                                        <svg
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.2 5.2 18.8 8.8M9 13l6.6-6.6a2 2 0 112.8 2.8L11.8 15.8a2 2 0 01-.9.5L7 17l.7-4a2 2 0 01.5-.8L9 13z"
                                            />
                                        </svg>
                                    </span>

                                    <span class="microseed-profile-action-text">
                                        <span class="microseed-profile-action-label">
                                            Cambiar fotografía
                                        </span>

                                        <span class="microseed-profile-action-description">
                                            Actualizar imagen de usuario
                                        </span>
                                    </span>
                                </label>

                                <input
                                    id="topbar_foto_perfil"
                                    type="file"
                                    name="foto_perfil"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    class="microseed-profile-photo-input"

                                    onchange="
                                        document
                                            .getElementById(
                                                'topbar-photo-form'
                                            )
                                            .submit()
                                    "
                                >
                            </form>
                        @endif


                        <div class="microseed-profile-separator"></div>


                        {{-- LOGOUT --}}

                        <button
                            type="button"
                            class="
                                microseed-profile-action
                                microseed-profile-action-danger
                            "
                            @click="openLogoutModal()"
                        >
                            <span class="microseed-profile-action-icon">
                                <svg
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                            </span>

                            <span class="microseed-profile-action-text">
                                <span class="microseed-profile-action-label">
                                    Cerrar sesión
                                </span>

                                <span class="microseed-profile-action-description">
                                    Salir del sistema
                                </span>
                            </span>
                        </button>

                    </div>
                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- BOTÓN MÓVIL --}}
        {{-- ========================================================= --}}

        <button
            type="button"
            class="microseed-mobile-menu-button"
            aria-label="Abrir menú"

            @click="mobileOpen = !mobileOpen"
        >
            <svg
                x-show="!mobileOpen"
                width="21"
                height="21"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>

            <svg
                x-show="mobileOpen"
                x-cloak
                width="21"
                height="21"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18 18 6M6 6l12 12"
                />
            </svg>
        </button>

    </div>


    {{-- ============================================================= --}}
    {{-- MENÚ MÓVIL --}}
    {{-- ============================================================= --}}

    <div
        x-show="mobileOpen"
        x-cloak
        x-transition.opacity
        class="microseed-mobile-panel"
    >
        <div class="microseed-mobile-user">

            <span class="microseed-profile-avatar">
                @if($fotoUsuario)
                    <img
                        src="{{ $fotoUsuario }}"
                        alt="Foto de {{ $nombreUsuario }}"
                    >
                @else
                    {{ strtoupper(
                        substr(
                            $nombreUsuario,
                            0,
                            1
                        )
                    ) }}
                @endif

                <span class="microseed-profile-online"></span>
            </span>

            <span class="microseed-profile-information">
                <span class="microseed-profile-name">
                    {{ $nombreUsuario }}
                </span>

                <span class="microseed-profile-role">
                    {{ $rolVisible }}
                </span>
            </span>
        </div>


        <a
            href="{{ $rutaInicio }}"
            class="microseed-mobile-link"
        >
            <svg
                width="18"
                height="18"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 13h6V4H4v9zm10 7h6V4h-6v16zM4 20h6v-5H4v5z"
                />
            </svg>

            Dashboard
        </a>


        @if(Route::has('profile.edit'))
            <a
                href="{{ route('profile.edit') }}"
                class="microseed-mobile-link"
            >
                <svg
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"
                    />
                </svg>

                Mi perfil
            </a>
        @endif


        <button
            type="button"
            class="
                microseed-mobile-link
                microseed-mobile-link-danger
            "
            @click="openLogoutModal()"
        >
            <svg
                width="18"
                height="18"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
            </svg>

            Cerrar sesión
        </button>
    </div>


    {{-- ============================================================= --}}
    {{-- FORM LOGOUT --}}
    {{-- ============================================================= --}}

    <form
        x-ref="logoutForm"
        method="POST"
        action="{{ route('logout') }}"
        style="display: none;"
    >
        @csrf
    </form>


    {{-- ============================================================= --}}
    {{-- CONFIRMACIÓN LOGOUT --}}
    {{-- ============================================================= --}}

    <div
        x-show="logoutModalOpen"
        x-cloak
        x-transition.opacity
        class="microseed-logout-backdrop"

        @click.self="closeLogoutModal()"
        @keydown.escape.window="closeLogoutModal()"
    >
        <div
            x-show="logoutModalOpen"
            class="microseed-logout-modal"
        >
            <div class="microseed-logout-modal-header">

                <div class="microseed-logout-icon">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                </div>

                <h3 class="microseed-logout-title">
                    ¿Deseas cerrar sesión?
                </h3>

                <p class="microseed-logout-description">
                    Se cerrará tu sesión actual y tendrás que
                    iniciar sesión nuevamente para acceder al sistema.
                </p>
            </div>

            <div class="microseed-logout-actions">

                <button
                    type="button"
                    class="microseed-logout-cancel"
                    @click="closeLogoutModal()"
                >
                    No, cancelar
                </button>

                <button
                    type="button"
                    class="microseed-logout-confirm"
                    @click="confirmLogout()"
                >
                    Sí, cerrar sesión
                </button>

            </div>
        </div>
    </div>

</nav>
