@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $roleClave = $user?->rol_clave;
    $roleNombre = $user?->rol_nombre ?? 'Sin rol';

    $isSuperAdmin = $user?->isSuperAdmin() ?? false;
    $isAdministrador = $user?->isAdministrador() ?? false;
    $isEncargado = $user?->isEncargado() ?? false;
    $hasValidRole = $isSuperAdmin || $isAdministrador || $isEncargado;

    $superAdminGroups = [
        [
            'key' => 'global',
            'title' => 'Global',
            'description' => 'Administración principal',
            'icon' => 'dashboard',
            'items' => [
                ['label' => 'Dashboard Global', 'route' => 'super_admin.dashboard', 'active' => ['super_admin.dashboard']],
                ['label' => 'Usuarios', 'route' => 'super_admin.usuarios.index', 'active' => ['super_admin.usuarios.*']],
            ],
        ],
        [
            'key' => 'alertas',
            'title' => 'Alertas',
            'description' => 'Incidencias del sistema',
            'icon' => 'alert',
            'items' => [
                ['label' => 'Alertas', 'route' => 'super_admin.alertas.index', 'active' => ['super_admin.alertas.*']],
                ['label' => 'Tipos de alerta', 'route' => 'super_admin.tipos-alerta.index', 'active' => ['super_admin.tipos-alerta.*']],
                ['label' => 'Niveles de alerta', 'route' => 'super_admin.niveles-alerta.index', 'active' => ['super_admin.niveles-alerta.*']],
                ['label' => 'Estados de alerta', 'route' => 'super_admin.estados-alerta.index', 'active' => ['super_admin.estados-alerta.*']],
            ],
        ],
        [
            'key' => 'incubadoras',
            'title' => 'Incubadoras',
            'description' => 'Dispositivos y asignaciones',
            'icon' => 'incubator',
            'items' => [
                ['label' => 'Incubadoras', 'route' => 'super_admin.incubadoras.index', 'active' => ['super_admin.incubadoras.*']],
                ['label' => 'Estados de incubadora', 'route' => 'super_admin.estados-incubadora.index', 'active' => ['super_admin.estados-incubadora.*']],
                ['label' => 'Posiciones de incubadora', 'route' => 'super_admin.posiciones-incubadora.index', 'active' => ['super_admin.posiciones-incubadora.*']],
                ['label' => 'Asignaciones de incubadora', 'route' => 'super_admin.asignaciones-incubadora.index', 'active' => ['super_admin.asignaciones-incubadora.*']],
            ],
        ],
        [
            'key' => 'monitoreo',
            'title' => 'Monitoreo',
            'description' => 'Lecturas y automatización',
            'icon' => 'chart',
            'items' => [
                ['label' => 'Lecturas microclima', 'route' => 'super_admin.lecturas-microclima.index', 'active' => ['super_admin.lecturas-microclima.*']],
                ['label' => 'Controles', 'route' => 'super_admin.controles-incubadora.index', 'active' => ['super_admin.controles-incubadora.*']],
                ['label' => 'Tipos de control', 'route' => 'super_admin.tipos-control-incubadora.index', 'active' => ['super_admin.tipos-control-incubadora.*']],
                ['label' => 'Modos de control', 'route' => 'super_admin.modos-control-incubadora.index', 'active' => ['super_admin.modos-control-incubadora.*']],
            ],
        ],
        [
            'key' => 'germinacion',
            'title' => 'Germinación',
            'description' => 'Especies, lotes y frascos',
            'icon' => 'seed',
            'items' => [
                ['label' => 'Especies', 'route' => 'super_admin.especies.index', 'active' => ['super_admin.especies.*']],
                ['label' => 'Condiciones óptimas', 'route' => 'super_admin.condiciones-optimas-especie.index', 'active' => ['super_admin.condiciones-optimas-especie.*']],
                ['label' => 'Lotes', 'route' => 'super_admin.lotes.index', 'active' => ['super_admin.lotes.*']],
                ['label' => 'Estados de lote', 'route' => 'super_admin.estados-lote.index', 'active' => ['super_admin.estados-lote.*']],
                ['label' => 'Frascos', 'route' => 'super_admin.frascos.index', 'active' => ['super_admin.frascos.*']],
                ['label' => 'Estados de frasco', 'route' => 'super_admin.estados-frasco.index', 'active' => ['super_admin.estados-frasco.*']],
                ['label' => 'Etapas de desarrollo', 'route' => 'super_admin.etapas-desarrollo.index', 'active' => ['super_admin.etapas-desarrollo.*']],
            ],
        ],
        [
            'key' => 'biologico',
            'title' => 'Biológico',
            'description' => 'Seguimiento y evidencias',
            'icon' => 'biology',
            'items' => [
                ['label' => 'Seguimientos lote', 'route' => 'super_admin.seguimientos-lote.index', 'active' => ['super_admin.seguimientos-lote.*']],
                ['label' => 'Seguimientos frasco', 'route' => 'super_admin.seguimientos-frasco.index', 'active' => ['super_admin.seguimientos-frasco.*']],
                ['label' => 'Evidencias lote', 'route' => 'super_admin.evidencias-lote.index', 'active' => ['super_admin.evidencias-lote.*']],
                ['label' => 'Registros biológicos', 'route' => 'super_admin.registros-biologicos.index', 'active' => ['super_admin.registros-biologicos.*']],
            ],
        ],
    ];

    $administradorGroups = [
        [
            'key' => 'administrador',
            'title' => 'Administrador',
            'description' => 'Gestión operativa',
            'icon' => 'dashboard',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'administrador.dashboard', 'active' => ['administrador.dashboard']],
                ['label' => 'Encargados', 'route' => 'administrador.usuarios.index', 'active' => ['administrador.usuarios.*']],
            ],
        ],
    ];

    $encargadoGroups = [
        [
            'key' => 'encargado',
            'title' => 'Encargado',
            'description' => 'Operación asignada',
            'icon' => 'dashboard',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'encargado.dashboard', 'active' => ['encargado.dashboard']],
            ],
        ],
    ];

    $menuGroups = match ($roleClave) {
        'super_admin' => $superAdminGroups,
        'administrador' => $administradorGroups,
        'encargado' => $encargadoGroups,
        default => [],
    };

    $initialGroup = null;

    foreach ($menuGroups as $group) {
        if (collect($group['items'])->contains(fn($item) => request()->routeIs(...$item['active']))) {
            $initialGroup = $group['key'];
            break;
        }
    }

    $dashboardRoute = match ($roleClave) {
        'super_admin' => 'super_admin.dashboard',
        'administrador' => 'administrador.dashboard',
        'encargado' => 'encargado.dashboard',
        default => 'dashboard',
    };

    $dashboardUrl = Route::has($dashboardRoute) ? route($dashboardRoute) : url('/');

    $iconSvg = function ($icon, $class = 'sidebar-svg') {
        return match ($icon) {
            'dashboard' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13h6V4H4v9zm10 7h6V4h-6v16zM4 20h6v-5H4v5z"/></svg>',
            'alert' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>',
            'incubator' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2zm3 4h6m-6 4h6m-6 4h3"/></svg>',
            'chart' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m4 14v-7m4 7V8m4 11v-4m4 4V9"/></svg>',
            'seed' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V12m0 0C8 12 5 9 5 5c4 0 7 3 7 7zm0 0c0-4 3-7 7-7 0 4-3 7-7 7z"/></svg>',
            'biology' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 2v6m4-6v6M8 8h8m-7 0l-4 9a3 3 0 002.75 4h8.5A3 3 0 0019 17l-4-9M9 15h6"/></svg>',
            'reports' => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>',
            default => '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>',
        };
    };
@endphp

<div class="microseed-sidebar relative z-50 h-dvh min-h-0 shrink-0"
     x-data="{
        sidebarOpen: false,
        openGroup: @js($initialGroup),
        openSidebar() { this.sidebarOpen = true; },
        closeSidebar() { this.sidebarOpen = false; this.openGroup = null; },
        openGroupOnHover(group) { if (this.sidebarOpen) this.openGroup = group; },
        toggleGroup(group) { this.sidebarOpen = true; this.openGroup = this.openGroup === group ? null : group; },
        closeGroup() { this.openGroup = null; }
     }"
     :class="sidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'"
     @mouseenter="openSidebar()"
     @mouseleave="closeSidebar()"
     @keydown.escape.window="closeSidebar()">

    <style>
        [x-cloak]{display:none!important}
        .microseed-sidebar{--sidebar-font:'Instrument Sans',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;width:78px;overflow:hidden;border-right:1px solid rgba(203,213,225,.72);background:radial-gradient(circle at 15% 0%,rgba(59,180,156,.08),transparent 22%),linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:8px 0 30px rgba(15,23,42,.07);font-family:var(--sidebar-font);font-size:14px;font-weight:400;line-height:1.5;font-synthesis:none;text-rendering:optimizeLegibility;-webkit-font-smoothing:antialiased;transition:width .22s cubic-bezier(.4,0,.2,1),box-shadow .22s ease}
        .microseed-sidebar *,.microseed-sidebar *::before,.microseed-sidebar *::after{box-sizing:border-box;font-family:var(--sidebar-font)}
        .microseed-sidebar button,.microseed-sidebar input,.microseed-sidebar select,.microseed-sidebar textarea{font-family:var(--sidebar-font)}
        .microseed-sidebar button{margin:0;border:0;outline:none;background:transparent;color:inherit;font-size:inherit;font-weight:inherit;line-height:inherit;appearance:none}
        .microseed-sidebar a{color:inherit;text-decoration:none}
        .microseed-sidebar.sidebar-collapsed{width:78px}
        .microseed-sidebar.sidebar-expanded{width:310px;box-shadow:12px 0 38px rgba(15,23,42,.11)}

        .sidebar-brand{position:relative;height:77px;display:flex;align-items:center;flex-shrink:0;overflow:hidden;padding:0 14px;background:radial-gradient(circle at 88% 6%,rgba(94,224,194,.26),transparent 43%),radial-gradient(circle at 5% 100%,rgba(18,63,84,.24),transparent 42%),linear-gradient(135deg,#123f54 0%,#176475 50%,#2b9691 100%);box-shadow:inset 0 -1px 0 rgba(255,255,255,.08),0 10px 26px rgba(20,66,85,.18)}
        .sidebar-brand::before{content:'';position:absolute;top:-72px;right:-55px;width:180px;height:180px;border-radius:9999px;background:rgba(255,255,255,.065);pointer-events:none}
        .sidebar-brand::after{content:'';position:absolute;right:18px;bottom:0;left:18px;height:1px;background:linear-gradient(90deg,transparent,rgba(113,231,205,.65),transparent);pointer-events:none}
        .sidebar-brand-link{position:relative;z-index:2;width:100%;min-width:0;display:flex;align-items:center;gap:16px}
        .sidebar-collapsed .sidebar-brand-link{justify-content:center}

        .sidebar-brand-logo{width:76px;height:88px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:visible;padding:0;margin:0;border:0;background:transparent;transition:transform .22s ease,filter .22s ease}
        .sidebar-brand-link:hover .sidebar-brand-logo{transform:translateY(-2px) scale(1.05)}
        .sidebar-brand-logo img{width:100%;height:100%;display:block;object-fit:contain;object-position:center;border:0;background:transparent;transform:scale(1.1);filter:drop-shadow(0 12px 18px rgba(6,34,46,.34)) saturate(1.15) contrast(1.06)}
        .sidebar-collapsed .sidebar-brand-logo{width:64px;height:76px}
        .sidebar-collapsed .sidebar-brand-logo img{transform:scale(1.16)}
        .sidebar-brand-fallback{color:#fff;font-size:17px;font-weight:800;letter-spacing:.03em}
        .sidebar-brand-information{min-width:0;flex:1;overflow:hidden}

        .sidebar-brand-name{display:block;overflow:hidden;color:#fff;font-size:20px;font-weight:800;line-height:1.08;letter-spacing:-.035em;text-overflow:ellipsis;white-space:nowrap;text-shadow:0 4px 13px rgba(8,39,52,.3)}
        .sidebar-brand-description{display:flex;align-items:center;gap:7px;margin-top:8px;overflow:hidden;color:rgba(224,255,249,.84);font-size:10px;font-weight:700;line-height:1;letter-spacing:.08em;text-overflow:ellipsis;text-transform:uppercase;white-space:nowrap}
        .sidebar-brand-description::before{content:'';width:7px;height:7px;flex-shrink:0;border-radius:9999px;background:#65e0c3;box-shadow:0 0 0 4px rgba(101,224,195,.12),0 0 13px rgba(101,224,195,.45)}
        .sidebar-svg{width:19px;height:19px;flex-shrink:0}

        .sidebar-main-scroll{min-height:0;overflow-x:hidden;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:rgba(148,163,184,.4) transparent}
        .sidebar-main-scroll::-webkit-scrollbar{width:5px}
        .sidebar-main-scroll::-webkit-scrollbar-track{background:transparent}
        .sidebar-main-scroll::-webkit-scrollbar-thumb{border-radius:9999px;background:rgba(148,163,184,.4)}
        .sidebar-main-scroll::-webkit-scrollbar-thumb:hover{background:rgba(100,116,139,.62)}

        .sidebar-menu-container{padding:14px 12px 18px}
        .sidebar-group{width:100%;margin-bottom:7px;border:1px solid transparent;border-radius:18px;transition:background .18s ease,box-shadow .18s ease,border-color .18s ease}
        .sidebar-group-open{border-color:rgba(59,180,156,.16);background:linear-gradient(135deg,rgba(33,106,115,.055),rgba(59,180,156,.08));box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 6px 18px rgba(33,106,115,.06)}

        .sidebar-group-button{width:100%;min-height:50px;display:flex;align-items:center;gap:11px;padding:4px;border-radius:16px;color:inherit;background:transparent;text-align:left;cursor:pointer;transition:background .18s ease,transform .18s ease}
        .sidebar-group-button:hover{background:rgba(236,254,255,.72)}
        .sidebar-group-button:focus-visible{box-shadow:0 0 0 3px rgba(59,180,156,.12)}

        .sidebar-icon-button{position:relative;width:44px;height:44px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:14px;color:#64748b;background:transparent;transition:color .18s ease,background .18s ease,transform .18s ease,box-shadow .18s ease}
        .sidebar-group-button:hover .sidebar-icon-button{color:#216a73;background:#ecfeff;transform:translateY(-1px)}
        .sidebar-icon-active{color:#fff;background:linear-gradient(135deg,#216a73 0%,#3bb49c 100%);box-shadow:0 10px 24px rgba(33,106,115,.24),inset 0 1px 0 rgba(255,255,255,.2)}
        .sidebar-group-button:hover .sidebar-icon-active{color:#fff;background:linear-gradient(135deg,#1c626b,#32a890)}

        .sidebar-group-content{min-width:0;flex:1;display:flex;align-items:center;justify-content:space-between;gap:8px;padding-right:9px}
        .sidebar-group-text{min-width:0;flex:1}
        .sidebar-group-title{display:block;overflow:hidden;color:#334155;font-size:13px;font-weight:700;line-height:1.2;letter-spacing:-.01em;text-overflow:ellipsis;white-space:nowrap}
        .sidebar-group-description{display:block;margin-top:3px;overflow:hidden;color:#94a3b8;font-size:10px;font-weight:500;line-height:1.2;text-overflow:ellipsis;white-space:nowrap}
        .sidebar-chevron{width:16px;height:16px;flex-shrink:0;color:#94a3b8;transition:transform .2s ease}
        .sidebar-submenu{display:flex;flex-direction:column;gap:4px;padding:2px 8px 11px 54px}

        .microseed-sidebar .sidebar-submenu-link{position:relative;width:100%;min-height:38px;display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0;padding:8px 10px 8px 13px;border:0;border-radius:12px;color:#64748b;background:transparent;font-size:12px!important;font-weight:600!important;line-height:1.3!important;text-align:left!important;cursor:pointer;transition:color .16s ease,background .16s ease,transform .16s ease,box-shadow .16s ease}
        .sidebar-submenu-label{min-width:0;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .sidebar-submenu-link::before{content:'';position:absolute;left:3px;width:3px;height:14px;border-radius:9999px;background:transparent;transition:background .16s ease}
        .sidebar-submenu-link:hover{color:#216a73!important;background:rgba(236,254,255,.9)!important;transform:translateX(2px)}
        .sidebar-submenu-link:hover::before{background:rgba(59,180,156,.5)}
        .sidebar-submenu-link:focus-visible{box-shadow:0 0 0 3px rgba(59,180,156,.12)}
        .sidebar-submenu-link-active{color:#1f6f78!important;background:linear-gradient(135deg,rgba(33,106,115,.1),rgba(59,180,156,.12))!important;box-shadow:inset 0 0 0 1px rgba(59,180,156,.14)}
        .sidebar-submenu-link-active::before{background:#3bb49c}
        .sidebar-active-dot{width:7px;height:7px;flex-shrink:0;border-radius:9999px;background:#3bb49c;box-shadow:0 0 0 4px rgba(59,180,156,.12)}

        .sidebar-permission-content{padding:4px 16px 16px 58px}
        .sidebar-permission-text{color:#64748b;font-size:10px;font-weight:500;line-height:1.55}
        .sidebar-permission-text+.sidebar-permission-text{margin-top:8px}
        .sidebar-permission-text strong{color:#475569;font-weight:700}

        @media(max-height:720px){
            .sidebar-brand{height:84px}
            .sidebar-brand-logo{width:59px;height:68px}
            .sidebar-collapsed .sidebar-brand-logo{width:54px;height:62px}
            .sidebar-brand-name{font-size:17px}
            .sidebar-brand-description{margin-top:6px;font-size:9px}
            .sidebar-menu-container{padding-top:9px}
            .sidebar-group{margin-bottom:3px}
            .sidebar-group-button{min-height:44px}
            .sidebar-icon-button{width:40px;height:40px}
            .sidebar-submenu-link{min-height:34px!important;padding-top:7px!important;padding-bottom:7px!important;font-size:11px!important}
        }
    </style>

    <div class="relative z-10 flex h-full min-h-0 flex-col">
        <div class="sidebar-brand">
            <a href="{{ $dashboardUrl }}" class="sidebar-brand-link" aria-label="Ir al dashboard de MicroSeed Control">
                <span class="sidebar-brand-logo">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo de MicroSeed Control" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                    <span class="sidebar-brand-fallback" style="display:none;">MC</span>
                </span>

                <span x-show="sidebarOpen" x-cloak x-transition.opacity.duration.160ms class="sidebar-brand-information">
                    <span class="sidebar-brand-name">MicroSeed Control</span>
                    <span class="sidebar-brand-description">{{ $roleNombre }}</span>
                </span>
            </a>
        </div>

        <nav class="sidebar-main-scroll min-h-0 flex-1">
            <div class="sidebar-menu-container">

                @if($hasValidRole)
                    @foreach($menuGroups as $group)
                        @php
                            $groupActive = collect($group['items'])->contains(fn($item) => request()->routeIs(...$item['active']));
                        @endphp

                        <div class="sidebar-group"
                             :class="openGroup === @js($group['key']) ? 'sidebar-group-open' : ''"
                             @mouseenter="openGroupOnHover(@js($group['key']))">

                            <button type="button"
                                    class="sidebar-group-button"
                                    aria-label="{{ $group['title'] }}"
                                    @click="toggleGroup(@js($group['key']))"
                                    :aria-expanded="openGroup === @js($group['key'])">

                                <span class="sidebar-icon-button {{ $groupActive ? 'sidebar-icon-active' : '' }}">
                                    {!! $iconSvg($group['icon']) !!}
                                </span>

                                <span x-show="sidebarOpen" x-cloak x-transition.opacity.duration.140ms class="sidebar-group-content">
                                    <span class="sidebar-group-text">
                                        <span class="sidebar-group-title">{{ $group['title'] }}</span>
                                        <span class="sidebar-group-description">{{ $group['description'] }}</span>
                                    </span>

                                    <svg class="sidebar-chevron" :class="openGroup === @js($group['key']) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </button>

                            <div x-show="sidebarOpen && openGroup === @js($group['key'])" x-cloak x-collapse class="sidebar-submenu">
                                @foreach($group['items'] as $item)
                                    @continue(!Route::has($item['route']))

                                    @php
                                        $isActive = request()->routeIs(...$item['active']);
                                    @endphp

                                    <a href="{{ route($item['route']) }}" class="sidebar-submenu-link {{ $isActive ? 'sidebar-submenu-link-active' : '' }}">
                                        <span class="sidebar-submenu-label">{{ $item['label'] }}</span>
                                        @if($isActive)<span class="sidebar-active-dot"></span>@endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($isSuperAdmin)
                        <div class="sidebar-group"
                             :class="openGroup === 'reportes' ? 'sidebar-group-open' : ''"
                             @mouseenter="openGroupOnHover('reportes')">

                            <button type="button"
                                    class="sidebar-group-button"
                                    aria-label="Reportes"
                                    @click="toggleGroup('reportes')"
                                    :aria-expanded="openGroup === 'reportes'">

                                <span class="sidebar-icon-button">
                                    {!! $iconSvg('reports') !!}
                                </span>

                                <span x-show="sidebarOpen" x-cloak x-transition.opacity.duration.140ms class="sidebar-group-content">
                                    <span class="sidebar-group-text">
                                        <span class="sidebar-group-title">Reportes</span>
                                        <span class="sidebar-group-description">Exportación de información</span>
                                    </span>

                                    <svg class="sidebar-chevron" :class="openGroup === 'reportes' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </button>

                            <div x-show="sidebarOpen && openGroup === 'reportes'" x-cloak x-collapse class="sidebar-submenu">
                                <a href="#" class="sidebar-submenu-link" @click.prevent="$dispatch('open-microclima-modal');closeSidebar();">
                                    <span class="sidebar-submenu-label">PDF Microclima</span>
                                </a>

                                <a href="#" class="sidebar-submenu-link" @click.prevent="$dispatch('open-biologico-modal');closeSidebar();">
                                    <span class="sidebar-submenu-label">PDF Biológico</span>
                                </a>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="sidebar-group"
                         :class="openGroup === 'sin-permisos' ? 'sidebar-group-open' : ''"
                         @mouseenter="openGroupOnHover('sin-permisos')">

                        <button type="button"
                                class="sidebar-group-button"
                                @click="toggleGroup('sin-permisos')"
                                :aria-expanded="openGroup === 'sin-permisos'">

                            <span class="sidebar-icon-button">{!! $iconSvg('alert') !!}</span>

                            <span x-show="sidebarOpen" x-cloak class="sidebar-group-content">
                                <span class="sidebar-group-text">
                                    <span class="sidebar-group-title">Sin permisos</span>
                                    <span class="sidebar-group-description">Rol no reconocido</span>
                                </span>
                            </span>
                        </button>

                        <div x-show="sidebarOpen && openGroup === 'sin-permisos'" x-cloak x-collapse class="sidebar-permission-content">
                            <p class="sidebar-permission-text">Rol detectado: <strong>{{ $roleClave ?: 'sin rol' }}</strong>.</p>
                            <p class="sidebar-permission-text">El usuario no tiene un rol válido asignado en MicroSeed Control.</p>
                        </div>
                    </div>
                @endif

            </div>
        </nav>
    </div>
</div>
