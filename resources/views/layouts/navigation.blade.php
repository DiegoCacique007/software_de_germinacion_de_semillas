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

    $fotoUsuario = $usuario && isset($usuario->foto_perfil) && $usuario->foto_perfil
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
        && \Illuminate\Support\Facades\Route::has('super_admin.dashboard')
    ) {
        $rutaInicio = route('super_admin.dashboard');
    } elseif (
        $rolNormalizado === 'administrador'
        && \Illuminate\Support\Facades\Route::has('semillas.gestion')
    ) {
        $rutaInicio = route('semillas.gestion');
    } elseif (\Illuminate\Support\Facades\Route::has('dashboard')) {
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
        ->filter(function ($modulo) {
            return \Illuminate\Support\Facades\Route::has($modulo['route']);
        })
        ->map(function ($modulo) {
            return [
                'label' => $modulo['label'],
                'description' => $modulo['description'],
                'url' => route($modulo['route']),
            ];
        })
        ->values()
        ->all();
@endphp

<style>
    [x-cloak] {
        display: none !important;
    }

    .microseed-topbar,
    .microseed-topbar *,
    .microseed-topbar button,
    .microseed-topbar input,
    .microseed-topbar label,
    .microseed-topbar a,
    .microseed-topbar p,
    .microseed-topbar span {
        box-sizing: border-box;
        font-family:
            'Instrument Sans',
            ui-sans-serif,
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            'Segoe UI',
            sans-serif;
    }

    .microseed-topbar button {
        margin: 0;
        border: 0;
        outline: none;
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

    .microseed-topbar {
        position: relative;
        z-index: 90;
        width: 100%;
        height: 82px;
        display: flex;
        align-items: center;
        flex-shrink: 0;
        border-bottom: 1px solid #e5eaf0;
        background: rgba(255, 255, 255, 0.97);
        box-shadow: 0 5px 20px rgba(15, 23, 42, 0.05);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }

    .microseed-topbar-content {
        min-width: 0;
        width: 100%;
        height: 100%;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 0 24px;
    }

    .microseed-topbar-left {
        min-width: 0;
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .microseed-search-wrapper {
        position: relative;
        width: min(550px, 100%);
    }

    .microseed-search {
        position: relative;
        width: 100%;
        height: 48px;
        display: flex;
        align-items: center;
    }

    .microseed-search-icon {
        position: absolute;
        left: 17px;
        z-index: 2;
        width: 19px;
        height: 19px;
        color: #94a3b8;
        pointer-events: none;
    }

    .microseed-search-input {
        width: 100%;
        height: 48px;
        padding: 0 52px 0 49px;
        border: 1px solid transparent;
        border-radius: 16px;
        outline: none;
        color: #334155;
        background: #f3f5f9;
        font-size: 13px;
        font-weight: 500;
        transition:
            border-color 0.17s ease,
            background 0.17s ease,
            box-shadow 0.17s ease;
    }

    .microseed-search-input::placeholder {
        color: #9da7b6;
    }

    .microseed-search-input:focus {
        border-color: rgba(59, 180, 156, 0.38);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.09);
    }

    .microseed-search-key {
        position: absolute;
        right: 11px;
        min-width: 30px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        color: #94a3b8;
        background: #ffffff;
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.04);
        font-size: 10px;
        font-weight: 700;
        pointer-events: none;
    }

    .microseed-search-results {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        z-index: 150;
        width: 100%;
        max-height: 390px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 17px;
        background: #ffffff;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .microseed-search-results::-webkit-scrollbar {
        width: 5px;
    }

    .microseed-search-results::-webkit-scrollbar-track {
        background: transparent;
    }

    .microseed-search-results::-webkit-scrollbar-thumb {
        border-radius: 9999px;
        background: rgba(100, 116, 139, 0.28);
    }

    .microseed-search-results-header {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: 12px 14px;
        border-bottom: 1px solid #eef2f6;
        color: #94a3b8;
        background: rgba(255, 255, 255, 0.97);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.13em;
        text-transform: uppercase;
        backdrop-filter: blur(12px);
    }

    .microseed-search-result {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 13px;
        color: inherit;
        text-align: left;
        transition: background 0.16s ease;
    }

    .microseed-search-result:hover,
    .microseed-search-result:focus-visible {
        outline: none;
        background: #f0fdfa;
    }

    .microseed-search-result-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        color: #1c607a;
        background: rgba(59, 180, 156, 0.11);
    }

    .microseed-search-result-title {
        display: block;
        overflow: hidden;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
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

    .microseed-topbar-actions {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        gap: 6px;
    }

    .microseed-action-wrapper {
        position: relative;
    }

    .microseed-action-button {
        position: relative;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        color: #64748b;
        transition:
            color 0.17s ease,
            background 0.17s ease,
            transform 0.17s ease;
    }

    .microseed-action-button:hover,
    .microseed-action-active {
        color: #1c607a;
        background: #f0fdfa;
        transform: translateY(-1px);
    }

    .microseed-action-button:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.1);
    }

    .microseed-action-button svg {
        width: 20px;
        height: 20px;
    }

    .microseed-counter {
        position: absolute;
        top: 1px;
        right: 0;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid #ffffff;
        border-radius: 9999px;
        color: #ffffff;
        background: #ef4444;
        font-size: 8px;
        font-weight: 800;
        line-height: 1;
    }

    .microseed-counter-green {
        background: #10b981;
    }

    .microseed-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        z-index: 150;
        width: 310px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
    }

    .microseed-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 16px;
        border-bottom: 1px solid #eef2f6;
    }

    .microseed-dropdown-title {
        margin: 0;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .microseed-dropdown-count {
        padding: 4px 8px;
        border-radius: 9999px;
        color: #047857;
        background: #ecfdf5;
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
        border-radius: 12px;
        color: #dc2626;
        background: #fef2f2;
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
        padding: 28px 18px;
        text-align: center;
    }

    .microseed-empty-icon {
        width: 46px;
        height: 46px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        color: #3bb49c;
        background: #ecfdf5;
    }

    .microseed-empty-title {
        margin: 11px 0 0;
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
        border-top: 1px solid #eef2f6;
        color: #1c607a;
        background: #f8fafc;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        transition: background 0.16s ease;
    }

    .microseed-dropdown-footer:hover {
        background: #f0fdfa;
    }

    .microseed-profile-wrapper {
        position: relative;
        margin-left: 7px;
        flex-shrink: 0;
    }

    .microseed-profile-button {
        min-width: 170px;
        height: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 9px;
        border: 0;
        border-radius: 14px;
        color: #334155;
        background: transparent;
        box-shadow: none;
        transition:
            background 0.17s ease,
            transform 0.17s ease,
            box-shadow 0.17s ease;
    }

    .microseed-profile-button:hover,
    .microseed-profile-active {
        background: #f5f7fa;
        transform: translateY(-1px);
        box-shadow: none;
    }

    .microseed-profile-button:focus-visible {
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.1);
    }

    .microseed-profile-avatar {
        position: relative;
        width: 38px;
        height: 38px;
        min-width: 38px;
        max-width: 38px;
        min-height: 38px;
        max-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: visible;
        border: 0;
        border-radius: 11px;
        color: #ffffff;
        background: linear-gradient(135deg, #144255, #3bb49c);
        box-shadow: none;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
    }

    .microseed-profile-avatar img {
        width: 38px;
        height: 38px;
        min-width: 38px;
        max-width: 38px;
        min-height: 38px;
        max-height: 38px;
        display: block;
        border-radius: 11px;
        object-fit: cover;
    }

    .microseed-profile-online {
        position: absolute;
        right: -3px;
        bottom: -3px;
        width: 11px;
        height: 11px;
        display: block;
        border: 2px solid #ffffff;
        border-radius: 9999px;
        background: #10b981;
        box-shadow: none;
    }

    .microseed-profile-information {
        min-width: 0;
        flex: 1;
        display: block;
        text-align: left;
    }

    .microseed-profile-name {
        display: block;
        max-width: 122px;
        overflow: hidden;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.015em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-profile-role {
        display: block;
        max-width: 122px;
        margin-top: 4px;
        overflow: hidden;
        color: #3bb49c;
        font-size: 9px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: 0.12em;
        text-overflow: ellipsis;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .microseed-profile-chevron {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: #94a3b8;
        transition: transform 0.18s ease;
    }

    .microseed-profile-dropdown {
        width: 292px;
    }

    .microseed-profile-header {
        padding: 19px;
        color: #ffffff;
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.18),
                transparent 38%
            ),
            linear-gradient(
                135deg,
                #144255 0%,
                #1c607a 58%,
                #3bb49c 120%
            );
    }

    .microseed-profile-header-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .microseed-profile-large-avatar {
        width: 50px;
        height: 50px;
        min-width: 50px;
        max-width: 50px;
        min-height: 50px;
        max-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 14px;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.13);
        font-size: 17px;
        font-weight: 700;
    }

    .microseed-profile-large-avatar img {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        object-fit: cover;
    }

    .microseed-profile-dropdown-name {
        display: block;
        overflow: hidden;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-profile-dropdown-email {
        display: block;
        margin-top: 4px;
        overflow: hidden;
        color: rgba(255, 255, 255, 0.68);
        font-size: 10px;
        font-weight: 500;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .microseed-profile-dropdown-role {
        display: block;
        width: fit-content;
        margin-top: 8px;
        padding: 4px 8px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 9999px;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 0.09em;
    }

    .microseed-profile-body {
        padding: 9px;
    }

    .microseed-profile-body form {
        margin: 0;
    }

    .microseed-profile-action {
        width: 100%;
        min-height: 43px;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 9px 11px;
        border-radius: 11px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition:
            color 0.16s ease,
            background 0.16s ease;
    }

    .microseed-profile-action:hover {
        color: #1c607a;
        background: #f0fdfa;
    }

    .microseed-profile-action svg {
        width: 18px;
        height: 18px;
    }

    .microseed-profile-action-danger {
        color: #e11d48;
    }

    .microseed-profile-action-danger:hover {
        color: #be123c;
        background: #fff1f2;
    }

    .microseed-mobile-menu-button {
        display: none;
        width: 42px;
        height: 42px;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: #64748b;
        background: #f5f7fa;
    }

    .microseed-mobile-panel {
        position: absolute;
        top: 100%;
        right: 0;
        left: 0;
        z-index: 140;
        padding: 12px;
        border-top: 1px solid #e5eaf0;
        background: #ffffff;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12);
    }

    .microseed-mobile-user {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 12px;
        border-radius: 14px;
        background: #f8fafc;
    }

    .microseed-mobile-link {
        width: 100%;
        min-height: 43px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 12px;
        border-radius: 11px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        transition: background 0.16s ease;
    }

    .microseed-mobile-link:hover {
        color: #1c607a;
        background: #f0fdfa;
    }

    @media (max-width: 1100px) {
        .microseed-search-wrapper {
            max-width: 400px;
        }

        .microseed-profile-button {
            min-width: 48px;
            width: 48px;
            height: 48px;
            padding: 5px;
            justify-content: center;
        }

        .microseed-profile-information,
        .microseed-profile-chevron {
            display: none;
        }
    }

    @media (max-width: 760px) {
        .microseed-topbar {
            height: 72px;
        }

        .microseed-topbar-content {
            gap: 8px;
            padding: 0 12px;
        }

        .microseed-search-wrapper {
            max-width: 250px;
        }

        .microseed-search {
            height: 44px;
        }

        .microseed-search-input {
            height: 44px;
            padding-right: 13px;
        }

        .microseed-search-key {
            display: none;
        }

        .microseed-action-button {
            width: 39px;
            height: 39px;
        }

        .microseed-action-wrapper.activity-action {
            display: none;
        }

        .microseed-dropdown {
            position: fixed;
            top: 78px;
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

    /*
        FIX FINAL:
        Mantiene el perfil superior derecho compacto aunque profile/edit.blade.php
        tenga clases parecidas como .microseed-profile-avatar.
    */
    .microseed-topbar .microseed-profile-wrapper {
        position: relative !important;
        margin-left: 7px !important;
        flex-shrink: 0 !important;
    }

    .microseed-topbar .microseed-profile-button {
        min-width: 170px !important;
        height: 50px !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 6px 9px !important;
        border: 0 !important;
        border-radius: 14px !important;
        color: #334155 !important;
        background: transparent !important;
        box-shadow: none !important;
        cursor: pointer !important;
    }

    .microseed-topbar .microseed-profile-button:hover,
    .microseed-topbar .microseed-profile-active {
        background: #f5f7fa !important;
        transform: translateY(-1px) !important;
        box-shadow: none !important;
    }

    .microseed-topbar .microseed-profile-avatar {
        position: relative !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        max-width: 38px !important;
        min-height: 38px !important;
        max-height: 38px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 11px !important;
        color: #ffffff !important;
        background: linear-gradient(135deg, #144255, #3bb49c) !important;
        box-shadow: none !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
    }

    .microseed-topbar .microseed-profile-avatar img {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        max-width: 38px !important;
        min-height: 38px !important;
        max-height: 38px !important;
        display: block !important;
        border-radius: 11px !important;
        object-fit: cover !important;
    }

    .microseed-topbar .microseed-profile-online {
        position: absolute !important;
        right: -3px !important;
        bottom: -3px !important;
        width: 11px !important;
        height: 11px !important;
        display: block !important;
        border: 2px solid #ffffff !important;
        border-radius: 9999px !important;
        background: #10b981 !important;
        box-shadow: none !important;
    }

    .microseed-topbar .microseed-profile-information {
        min-width: 0 !important;
        flex: 1 !important;
        display: block !important;
        text-align: left !important;
    }

    .microseed-topbar .microseed-profile-name {
        display: block !important;
        max-width: 122px !important;
        overflow: hidden !important;
        color: #334155 !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        letter-spacing: -0.015em !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .microseed-topbar .microseed-profile-role {
        display: block !important;
        max-width: 122px !important;
        margin-top: 4px !important;
        overflow: hidden !important;
        color: #3bb49c !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        letter-spacing: 0.12em !important;
        text-overflow: ellipsis !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    .microseed-topbar .microseed-profile-chevron {
        width: 14px !important;
        height: 14px !important;
        flex-shrink: 0 !important;
        color: #94a3b8 !important;
        transition: transform 0.18s ease !important;
    }

    .microseed-topbar .microseed-profile-dropdown .microseed-profile-large-avatar {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
        min-height: 50px !important;
        max-height: 50px !important;
        border-radius: 14px !important;
    }

    .microseed-topbar .microseed-profile-dropdown .microseed-profile-large-avatar img {
        width: 50px !important;
        height: 50px !important;
        border-radius: 14px !important;
        object-fit: cover !important;
    }

    @media (max-width: 1100px) {
        .microseed-topbar .microseed-profile-button {
            min-width: 48px !important;
            width: 48px !important;
            height: 48px !important;
            padding: 5px !important;
            justify-content: center !important;
        }

        .microseed-topbar .microseed-profile-information,
        .microseed-topbar .microseed-profile-chevron {
            display: none !important;
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
        modules: @js($modulosBusqueda),

        get filteredModules() {
            const query = this.searchValue.trim().toLowerCase();

            if (!query) {
                return this.modules.slice(0, 6);
            }

            return this.modules.filter(module => {
                return module.label.toLowerCase().includes(query)
                    || module.description.toLowerCase().includes(query);
            }).slice(0, 8);
        },

        togglePanel(panel) {
            this.openPanel = this.openPanel === panel
                ? null
                : panel;

            this.searchOpen = false;
        },

        goToModule(url) {
            if (url) {
                window.location.href = url;
            }
        },

        init() {
            window.addEventListener('keydown', event => {
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
                }
            });
        }
    }"
>
    <div class="microseed-topbar-content">
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
                                goToModule(filteredModules[0].url);
                            }
                        "
                    >
                </div>

                <div
                    x-show="searchOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
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

                                    <span class="min-w-0">
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

        <div class="microseed-topbar-actions">
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
                            {{ $cantidadAlertas > 99 ? '99+' : $cantidadAlertas }}
                        </span>
                    @endif
                </button>

                <div
                    x-show="openPanel === 'notifications'"
                    x-cloak
                    x-transition.origin.top.right
                    class="microseed-dropdown"
                >
                    <div class="microseed-dropdown-header">
                        <p class="microseed-dropdown-title">
                            Notificaciones
                        </p>

                        <span class="microseed-dropdown-count">
                            {{ $cantidadAlertas }}
                            {{ $cantidadAlertas === 1 ? 'activa' : 'activas' }}
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

                    @if(\Illuminate\Support\Facades\Route::has('super_admin.alertas.index'))
                        <a
                            href="{{ route('super_admin.alertas.index') }}"
                            class="microseed-dropdown-footer"
                        >
                            Ver todas las alertas
                        </a>
                    @endif
                </div>
            </div>

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
                            {{ $cantidadActividad > 99 ? '99+' : $cantidadActividad }}
                        </span>
                    @endif
                </button>

                <div
                    x-show="openPanel === 'activity'"
                    x-cloak
                    x-transition.origin.top.right
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

            <div
                class="microseed-profile-wrapper"
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
                    aria-label="Abrir menú de usuario"
                    @click="togglePanel('profile')"
                >
                    <span class="microseed-profile-avatar">
                        @if($fotoUsuario)
                            <img
                                src="{{ $fotoUsuario }}"
                                alt="Foto de {{ $nombreUsuario }}"
                            >
                        @else
                            {{ strtoupper(substr($nombreUsuario, 0, 1)) }}
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
                                ? 'rotate-180'
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

                <div
                    x-show="openPanel === 'profile'"
                    x-cloak
                    x-transition.origin.top.right
                    class="microseed-dropdown microseed-profile-dropdown"
                >
                    <div class="microseed-profile-header">
                        <div class="microseed-profile-header-row">
                            <span class="microseed-profile-large-avatar">
                                @if($fotoUsuario)
                                    <img
                                        src="{{ $fotoUsuario }}"
                                        alt="Foto de {{ $nombreUsuario }}"
                                    >
                                @else
                                    {{ strtoupper(substr($nombreUsuario, 0, 1)) }}
                                @endif
                            </span>

                            <span class="min-w-0">
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

                    <div class="microseed-profile-body">
                        @if(\Illuminate\Support\Facades\Route::has('profile.edit'))
                            <a
                                href="{{ route('profile.edit') }}"
                                class="microseed-profile-action"
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
                                        d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"
                                    />
                                </svg>

                                Mi perfil
                            </a>
                        @endif

                        @if(\Illuminate\Support\Facades\Route::has('perfil.foto.update'))
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

                                    Cambiar fotografía
                                </label>

                                <input
                                    id="topbar_foto_perfil"
                                    type="file"
                                    name="foto_perfil"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    class="hidden"
                                    onchange="document.getElementById('topbar-photo-form').submit()"
                                >
                            </form>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="microseed-profile-action microseed-profile-action-danger"
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
                                        d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>

                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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

    <div
        x-show="mobileOpen"
        x-cloak
        x-transition
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
                    {{ strtoupper(substr($nombreUsuario, 0, 1)) }}
                @endif

                <span class="microseed-profile-online"></span>
            </span>

            <span class="min-w-0">
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

        @if(\Illuminate\Support\Facades\Route::has('profile.edit'))
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

        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="microseed-mobile-link text-red-500"
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
                        d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 013-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>

                Cerrar sesión
            </button>
        </form>
    </div>
</nav>
