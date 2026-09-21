@php
    use Illuminate\Support\Facades\Route;

    $usuario=auth()->user();
    $nombre=$usuario?->name??'Usuario';
    $correo=$usuario?->email??'Sin correo registrado';
    $rolClave=$usuario?->rol_clave;
    $rolVisible=strtoupper($usuario?->rol_nombre??'Sin rol');
    $isSuperAdmin=$usuario?->isSuperAdmin()??false;
    $isEncargado=$usuario?->isEncargado()??false;
    $foto=$usuario?->foto_perfil?asset('storage/'.$usuario->foto_perfil):null;

    $cantidadAlertas=isset($alertasActivas)?(is_countable($alertasActivas)?count($alertasActivas):(int)$alertasActivas):0;
    $cantidadActividad=isset($actividadNoLeida)?(int)$actividadNoLeida:0;
    $modulos=[];

    if($isSuperAdmin){
        $modulos=[
            ['Dashboard Global','Panel principal del sistema','super_admin.dashboard'],
            ['Usuarios','Administración de usuarios','super_admin.usuarios.index'],
            ['Alertas','Incidencias registradas','super_admin.alertas.index'],
            ['Tipos de alerta','Catálogo de alertas','super_admin.tipos-alerta.index'],
            ['Niveles de alerta','Niveles de prioridad','super_admin.niveles-alerta.index'],
            ['Estados de alerta','Estados disponibles','super_admin.estados-alerta.index'],
            ['Incubadoras','Gestión de incubadoras','super_admin.incubadoras.index'],
            ['Estados de incubadora','Catálogo de estados','super_admin.estados-incubadora.index'],
            ['Posiciones','Posiciones de incubadora','super_admin.posiciones-incubadora.index'],
            ['Asignaciones','Usuarios e incubadoras','super_admin.asignaciones-incubadora.index'],
            ['Lecturas microclima','Monitoreo ambiental','super_admin.lecturas-microclima.index'],
            ['Controles','Automatización del prototipo','super_admin.controles-incubadora.index'],
            ['Tipos de control','Catálogo de controles','super_admin.tipos-control-incubadora.index'],
            ['Modos de control','Control manual y automático','super_admin.modos-control-incubadora.index'],
            ['Especies','Catálogo de semillas','super_admin.especies.index'],
            ['Condiciones óptimas','Parámetros ambientales','super_admin.condiciones-optimas-especie.index'],
            ['Lotes','Lotes de germinación','super_admin.lotes.index'],
            ['Estados de lote','Estados de lotes','super_admin.estados-lote.index'],
            ['Frascos','Contenedores registrados','super_admin.frascos.index'],
            ['Estados de frasco','Estados de frascos','super_admin.estados-frasco.index'],
            ['Etapas de desarrollo','Fases de germinación','super_admin.etapas-desarrollo.index'],
            ['Seguimientos lote','Control biológico','super_admin.seguimientos-lote.index'],
            ['Seguimientos frasco','Control de frascos','super_admin.seguimientos-frasco.index'],
            ['Evidencias lote','Fotografías y archivos','super_admin.evidencias-lote.index'],
            ['Registros biológicos','Observaciones del cultivo','super_admin.registros-biologicos.index'],
        ];
    }

    if($isEncargado){
        $modulos=[
            ['Dashboard','Panel principal del encargado','encargado.dashboard'],
            ['Mis incubadoras','Incubadoras asignadas','encargado.incubadoras.index'],
            ['Mis alertas','Incidencias que requieren seguimiento','encargado.alertas.index'],
            ['Mis lotes','Lotes de germinación asignados','encargado.lotes.index'],
            ['Mis frascos','Frascos pertenecientes a tus lotes','encargado.frascos.index'],
            ['Seguimientos de lote','Registrar y consultar lotes','encargado.seguimientos-lote.index'],
            ['Seguimientos de frasco','Registrar y consultar frascos','encargado.seguimientos-frasco.index'],
            ['Evidencias','Evidencias fotográficas','encargado.evidencias-lote.index'],
        ];
    }

    $modulos=collect($modulos)
        ->filter(fn($m)=>Route::has($m[2]))
        ->map(fn($m)=>['label'=>$m[0],'description'=>$m[1],'route'=>$m[2],'url'=>route($m[2])])
        ->values()
        ->all();

    $rutaAlertas=match(true){
        $isSuperAdmin&&Route::has('super_admin.alertas.index')=>route('super_admin.alertas.index'),
        $isEncargado&&Route::has('encargado.alertas.index')=>route('encargado.alertas.index'),
        default=>null,
    };
@endphp

<style>
    [x-cloak]{display:none!important}
    .microseed-topbar{--font:'Instrument Sans',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;position:relative;z-index:90;width:100%;height:78px;display:flex;align-items:center;flex-shrink:0;border-bottom:1px solid #e5eaed;background:rgba(255,255,255,.98);box-shadow:0 5px 20px rgba(20,66,85,.045);font-family:var(--font);font-size:14px;line-height:1.5;font-synthesis:none;text-rendering:optimizeLegibility;-webkit-font-smoothing:antialiased}
    .microseed-topbar *,.microseed-topbar *::before,.microseed-topbar *::after{box-sizing:border-box;font-family:var(--font)}
    .microseed-topbar button{border:0;background:transparent;color:inherit;font:inherit;cursor:pointer}
    .microseed-topbar a{color:inherit;text-decoration:none}
    .topbar-content{width:100%;height:100%;display:flex;align-items:center;justify-content:space-between;gap:18px;padding:0 18px 0 24px}
    .topbar-left{min-width:0;flex:1}
    .topbar-actions{display:flex;align-items:center;gap:5px;flex-shrink:0}

    .topbar-search-wrap{position:relative;width:min(550px,100%)}
    .topbar-search{position:relative;height:46px;display:flex;align-items:center}
    .topbar-search svg{position:absolute;left:16px;width:18px;height:18px;color:#8294a3;pointer-events:none}
    .topbar-search input{width:100%;height:46px;padding:0 45px 0 47px;border:1px solid transparent;border-radius:15px;outline:0;background:#f4f7f7;color:#334155;font-size:13px;font-weight:500}
    .topbar-search input:focus{background:#fff;border-color:rgba(59,180,156,.38);box-shadow:0 0 0 4px rgba(59,180,156,.09)}
    .topbar-results{position:absolute;top:56px;left:0;z-index:170;width:100%;max-height:390px;overflow:auto;background:#fff;border:1px solid #e2e8ea;border-radius:17px;box-shadow:0 24px 60px rgba(15,23,42,.18)}
    .topbar-results-title{padding:11px 14px;color:#94a3b8;border-bottom:1px solid #edf2f2;font-size:9px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .topbar-result{width:100%;display:flex;align-items:center;gap:11px;padding:11px 13px;text-align:left}
    .topbar-result:hover{background:#effaf8}
    .topbar-result-icon{width:36px;height:36px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#216a73;background:#eaf8f5;border-radius:11px}
    .topbar-result-title{display:block;color:#334155;font-size:12px;font-weight:700}
    .topbar-result-desc{display:block;margin-top:2px;color:#94a3b8;font-size:10px}
    .topbar-empty{padding:25px;text-align:center;color:#94a3b8;font-size:12px}

    .topbar-action-wrap{position:relative}
    .topbar-action{position:relative;width:43px;height:43px;display:flex;align-items:center;justify-content:center;color:#64748b;border-radius:13px}
    .topbar-action:hover,.topbar-action.active{color:#216a73;background:#eef8f7}
    .topbar-action svg{width:20px;height:20px}
    .topbar-counter{position:absolute;top:0;right:-1px;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;padding:0 4px;color:#fff;background:#dc3545;border:2px solid #fff;border-radius:999px;font-size:8px;font-weight:800}
    .topbar-counter.green{background:#3bb49c}

    .topbar-dropdown{position:absolute;top:55px;right:0;z-index:180;width:310px;overflow:hidden;background:#fff;border:1px solid #e2e8ea;border-radius:18px;box-shadow:0 24px 60px rgba(15,23,42,.2)}
    .topbar-dropdown-head{display:flex;align-items:center;justify-content:space-between;padding:15px 16px;border-bottom:1px solid #eef2f2}
    .topbar-dropdown-head strong{font-size:13px;color:#334155}
    .topbar-pill{padding:4px 8px;color:#1f756a;background:#e9f8f4;border-radius:999px;font-size:9px;font-weight:700}
    .topbar-notification{display:flex;gap:11px;padding:14px 16px}
    .topbar-notification-icon{width:39px;height:39px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#dc3545;background:#fff1f2;border-radius:12px}
    .topbar-notification-title{display:block;color:#334155;font-size:12px;font-weight:700}
    .topbar-notification-desc{display:block;margin-top:3px;color:#94a3b8;font-size:10px;line-height:1.45}
    .topbar-empty-state{padding:26px 16px;text-align:center;color:#64748b;font-size:11px}
    .topbar-dropdown-footer{display:block;padding:11px 15px;background:#f8faf9;border-top:1px solid #eef2f2;color:#1c607a;font-size:11px;font-weight:700;text-align:center}

    .topbar-profile-wrap{position:relative;margin-left:3px;padding-bottom:12px;margin-bottom:-12px}
    .topbar-profile{width:238px;height:52px;display:flex;align-items:center;gap:11px;padding:5px 9px 5px 6px;border-radius:16px}
    .topbar-profile:hover,.topbar-profile.active{background:linear-gradient(135deg,rgba(33,106,115,.055),rgba(59,180,156,.08))}
    .topbar-avatar{position:relative;width:44px;height:44px;flex:0 0 44px;display:flex;align-items:center;justify-content:center;color:#fff;background:linear-gradient(135deg,#216a73,#3bb49c);border-radius:14px;font-size:13px;font-weight:800}
    .topbar-avatar img{width:44px;height:44px;object-fit:cover;border-radius:14px}
    .topbar-online{position:absolute;right:-3px;bottom:-3px;width:11px;height:11px;background:#3bb49c;border:2px solid #fff;border-radius:999px}
    .topbar-user{min-width:0;flex:1;text-align:left}
    .topbar-user-name,.topbar-user-role{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .topbar-user-name{color:#334155;font-size:13px;font-weight:700}
    .topbar-user-role{margin-top:4px;color:#3b9a96;font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}

    /* PERFIL */
    .topbar-profile-dropdown{width:320px;max-height:calc(100vh - 95px);overflow-x:hidden;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:rgba(100,116,139,.30) transparent}
    .topbar-profile-dropdown::-webkit-scrollbar{width:5px}
    .topbar-profile-dropdown::-webkit-scrollbar-track{background:transparent}
    .topbar-profile-dropdown::-webkit-scrollbar-thumb{background:rgba(100,116,139,.28);border-radius:999px}
    .topbar-profile-dropdown::-webkit-scrollbar-thumb:hover{background:rgba(100,116,139,.45)}

    .topbar-profile-header{display:flex;align-items:center;gap:13px;padding:17px;color:#fff;background:linear-gradient(135deg,#123f54,#176475,#2b9691)}
    .topbar-profile-large{width:58px;height:58px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;background:rgba(10,52,66,.7);border-radius:15px;font-size:18px;font-weight:800}
    .topbar-profile-large img{width:100%;height:100%;object-fit:cover}
    .topbar-profile-name{display:block;max-width:210px;overflow:hidden;color:#fff;font-size:15px;font-weight:800;line-height:1.2;letter-spacing:-.025em;text-overflow:ellipsis;white-space:nowrap}
    .topbar-profile-email{display:block;max-width:210px;margin-top:5px;overflow:hidden;color:rgba(232,255,249,.82);font-size:11px;font-weight:500;line-height:1.2;text-overflow:ellipsis;white-space:nowrap}
    .topbar-profile-role{display:inline-flex;align-items:center;width:fit-content;margin-top:8px;padding:4px 10px;color:#fff;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.24);border-radius:999px;font-size:9px;font-weight:700;line-height:1;letter-spacing:.07em;text-transform:uppercase}
    .topbar-profile-body{padding:8px}
    .topbar-profile-body form{margin:0}
    .topbar-profile-link{width:100%;min-height:54px;display:flex;align-items:center;gap:11px;margin:0;padding:9px;color:#475569;background:transparent;border:0;border-radius:15px;outline:none;text-align:left;cursor:pointer;transition:color .16s ease,background .16s ease,transform .16s ease}
    .topbar-profile-link>span:last-child{display:block;color:inherit;font-size:12px;font-weight:700;line-height:1.2;white-space:nowrap}
    .topbar-profile-link:hover{color:#216a73;background:rgba(236,254,255,.9);transform:translateX(2px)}
    .topbar-profile-link.danger{color:#dc3545}
    .topbar-profile-link.danger:hover{color:#dc3545;background:#fff1f2}
    .topbar-profile-icon{width:39px;height:39px;flex:0 0 39px;display:flex;align-items:center;justify-content:center;color:#64748b;background:#eef8f7;border-radius:13px;transition:color .16s ease,background .16s ease}
    .topbar-profile-icon svg{width:19px;height:19px;display:block;fill:none;stroke:currentColor}
    .topbar-profile-link:hover .topbar-profile-icon{color:#216a73;background:#e5f5f2}
    .topbar-profile-link.danger .topbar-profile-icon{color:#dc3545;background:#fff1f2}
    .topbar-profile-link.danger:hover .topbar-profile-icon{color:#dc3545;background:#ffe7e9}
    .topbar-profile-photo{display:none!important}

    .mobile-button{display:none;width:42px;height:42px;align-items:center;justify-content:center;color:#64748b;background:#f5f7f7!important;border-radius:12px}
    .mobile-panel{position:absolute;top:100%;right:0;left:0;z-index:190;max-height:calc(100vh - 70px);overflow:auto;padding:12px;background:#fff;border-top:1px solid #e5eaed;box-shadow:0 18px 35px rgba(15,23,42,.12)}
    .mobile-user{display:flex;align-items:center;gap:11px;padding:12px;background:#f7faf9;border-radius:14px}
    .mobile-title{margin:14px 5px 6px;color:#94a3b8;font-size:9px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .mobile-link{width:100%;min-height:43px;display:flex;align-items:center;gap:10px;padding:10px 12px;margin-top:4px;color:#64748b;border-radius:11px;font-size:12px;font-weight:600}
    .mobile-link:hover,.mobile-link.active{color:#216a73;background:#effaf8}
    .mobile-icon{width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:#effaf8;border-radius:9px}

    .logout-backdrop{position:fixed;inset:0;z-index:99999;display:grid;place-items:center;padding:16px;background:rgba(15,23,42,.45);backdrop-filter:blur(2px)}
    .logout-modal{width:min(340px,100%);padding:22px;background:#fff;border-radius:20px;box-shadow:0 22px 60px rgba(15,23,42,.24);text-align:center}
    .logout-icon{width:50px;height:50px;margin:0 auto 13px;display:flex;align-items:center;justify-content:center;color:#216a73;background:#eaf8f5;border-radius:16px}
    .logout-icon svg{width:22px;height:22px;display:block;fill:none;stroke:currentColor}
    .logout-title{margin:0;color:#334155;font-size:17px;font-weight:800}
    .logout-text{margin:8px 0 18px;color:#64748b;font-size:12px;line-height:1.45}
    .logout-actions{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .logout-actions button{min-height:41px;border-radius:13px!important;font-size:11px!important;font-weight:700!important}
    .logout-cancel{background:#f1f5f4!important;color:#475569!important}
    .logout-confirm{background:linear-gradient(135deg,#216a73,#3bb49c)!important;color:#fff!important}

    @media(max-width:900px){.topbar-profile{width:48px;padding:2px}.topbar-user{display:none}}
    @media(max-width:760px){.microseed-topbar{height:70px}.topbar-content{padding:0 12px}.topbar-search-wrap{max-width:250px}.topbar-dropdown{position:fixed;top:76px;right:12px;left:12px;width:auto}}
    @media(max-width:560px){.topbar-left,.topbar-actions,.topbar-profile-wrap{display:none}.topbar-content{justify-content:flex-end}.mobile-button{display:flex}}
</style>

<nav class="microseed-topbar" x-data="{
search:'',searchOpen:false,panel:null,mobile:false,logout:false,modules:@js($modulos),
get filtered(){const q=this.search.trim().toLowerCase();return(q?this.modules.filter(m=>m.label.toLowerCase().includes(q)||m.description.toLowerCase().includes(q)):this.modules).slice(0,8)},
toggle(p){this.panel=this.panel===p?null:p;this.searchOpen=false},
go(url){if(url)window.location.href=url},
openLogout(){this.panel=null;this.mobile=false;this.logout=true},
confirmLogout(){this.$refs.logoutForm.submit()}
}">

    <div class="topbar-content">

        <div class="topbar-left">
            <div class="topbar-search-wrap" @click.outside="searchOpen=false">
                <div class="topbar-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="search" type="search" placeholder="Buscar módulos..." @focus="searchOpen=true;panel=null" @input="searchOpen=true" @keydown.enter.prevent="if(filtered.length)go(filtered[0].url)">
                </div>

                <div x-show="searchOpen" x-cloak class="topbar-results">
                    <div class="topbar-results-title">Acceso rápido</div>

                    <template x-if="filtered.length">
                        <div>
                            <template x-for="module in filtered" :key="module.url">
                                <button type="button" class="topbar-result" @click="go(module.url)">
                                    <span class="topbar-result-icon">→</span>
                                    <span>
                                <span class="topbar-result-title" x-text="module.label"></span>
                                <span class="topbar-result-desc" x-text="module.description"></span>
                            </span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="!filtered.length"><div class="topbar-empty">No se encontraron módulos.</div></template>
                </div>
            </div>
        </div>

        <div class="topbar-actions">

            <div class="topbar-action-wrap" @click.outside="if(panel==='alerts')panel=null">
                <button type="button" class="topbar-action" :class="panel==='alerts'?'active':''" @click="toggle('alerts')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 17h5l-2-2v-4a6 6 0 10-12 0v4l-2 2h5m6 0a3 3 0 01-6 0"/></svg>
                    @if($cantidadAlertas>0)<span class="topbar-counter">{{ $cantidadAlertas>99?'99+':$cantidadAlertas }}</span>@endif
                </button>

                <div x-show="panel==='alerts'" x-cloak class="topbar-dropdown">
                    <div class="topbar-dropdown-head"><strong>Notificaciones</strong><span class="topbar-pill">{{ $cantidadAlertas }} activas</span></div>

                    @if($cantidadAlertas>0)
                        <div class="topbar-notification">
                            <span class="topbar-notification-icon">!</span>
                            <span>
                        <span class="topbar-notification-title">Alertas pendientes</span>
                        <span class="topbar-notification-desc">Existen {{ $cantidadAlertas }} incidencias pendientes de revisión.</span>
                    </span>
                        </div>
                    @else
                        <div class="topbar-empty-state">Sin alertas activas.</div>
                    @endif

                    @if($rutaAlertas)<a href="{{ $rutaAlertas }}" class="topbar-dropdown-footer">Ver todas las alertas</a>@endif
                </div>
            </div>

            <div class="topbar-action-wrap" @click.outside="if(panel==='activity')panel=null">
                <button type="button" class="topbar-action" :class="panel==='activity'?'active':''" @click="toggle('activity')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 15a4 4 0 01-4 4H8l-5 3v-7a4 4 0 01-1-3V7a4 4 0 014-4h11a4 4 0 014 4v8z"/></svg>
                    @if($cantidadActividad>0)<span class="topbar-counter green">{{ $cantidadActividad }}</span>@endif
                </button>

                <div x-show="panel==='activity'" x-cloak class="topbar-dropdown">
                    <div class="topbar-dropdown-head"><strong>Actividad</strong><span class="topbar-pill">{{ $cantidadActividad }} nuevas</span></div>
                    <div class="topbar-empty-state">Sin actividad nueva.</div>
                </div>
            </div>

            <div class="topbar-profile-wrap" @mouseenter="panel='profile'" @mouseleave="if(panel==='profile')panel=null">
                <button type="button" class="topbar-profile" :class="panel==='profile'?'active':''" @click="toggle('profile')">
            <span class="topbar-avatar">
                @if($foto)<img src="{{ $foto }}" alt="Foto de {{ $nombre }}">@else{{ strtoupper(substr($nombre,0,1)) }}@endif
                <span class="topbar-online"></span>
            </span>
                    <span class="topbar-user">
                <span class="topbar-user-name">{{ $nombre }}</span>
                <span class="topbar-user-role">{{ $rolVisible }}</span>
            </span>
                </button>

                <div x-show="panel==='profile'" x-cloak class="topbar-dropdown topbar-profile-dropdown">

                    <div class="topbar-profile-header">
                        <span class="topbar-profile-large">@if($foto)<img src="{{ $foto }}" alt="Foto">@else{{ strtoupper(substr($nombre,0,1)) }}@endif</span>
                        <span>
                    <span class="topbar-profile-name">{{ $nombre }}</span>
                    <span class="topbar-profile-email">{{ $correo }}</span>
                    <span class="topbar-profile-role">{{ $rolVisible }}</span>
                </span>
                    </div>

                    <div class="topbar-profile-body">

                        @if(Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" class="topbar-profile-link">
                        <span class="topbar-profile-icon">
                            <svg viewBox="0 0 24 24" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="4"/>
                                <path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>
                        </span>
                                <span>Mi perfil</span>
                            </a>
                        @endif

                        @if(Route::has('perfil.foto.update'))
                            <form id="nav-photo-form" method="POST" action="{{ route('perfil.foto.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <label for="nav_foto" class="topbar-profile-link">
                            <span class="topbar-profile-icon">
                                <svg viewBox="0 0 24 24" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </span>
                                    <span>Cambiar fotografía</span>
                                </label>

                                <input id="nav_foto" type="file" name="foto_perfil" accept="image/png,image/jpeg,image/jpg,image/webp" class="topbar-profile-photo" onchange="document.getElementById('nav-photo-form').submit()">
                            </form>
                        @endif

                        <button type="button" class="topbar-profile-link danger" @click="openLogout()">
                    <span class="topbar-profile-icon">
                        <svg viewBox="0 0 24 24" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 17l5-5-5-5"/>
                            <path d="M15 12H3"/>
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        </svg>
                    </span>
                            <span>Cerrar sesión</span>
                        </button>

                    </div>
                </div>
            </div>

        </div>

        <button type="button" class="mobile-button" @click="mobile=!mobile">
            <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

    </div>

    <div x-show="mobile" x-cloak class="mobile-panel">
        <div class="mobile-user">
            <span class="topbar-avatar">@if($foto)<img src="{{ $foto }}" alt="Foto">@else{{ strtoupper(substr($nombre,0,1)) }}@endif</span>
            <span class="topbar-user">
            <span class="topbar-user-name">{{ $nombre }}</span>
            <span class="topbar-user-role">{{ $rolVisible }}</span>
        </span>
        </div>

        <div class="mobile-title">Módulos</div>

        @foreach($modulos as $modulo)
            @php $activo=request()->routeIs($modulo['route'])||request()->routeIs(str_replace('.index','.*',$modulo['route'])); @endphp
            <a href="{{ $modulo['url'] }}" class="mobile-link {{ $activo?'active':'' }}">
                <span class="mobile-icon">→</span>{{ $modulo['label'] }}
            </a>
        @endforeach

        <div class="mobile-title">Cuenta</div>

        @if(Route::has('profile.edit'))
            <a href="{{ route('profile.edit') }}" class="mobile-link"><span class="mobile-icon">👤</span>Mi perfil</a>
        @endif

        <button type="button" class="mobile-link" style="color:#dc3545;" @click="openLogout()"><span class="mobile-icon">↪</span>Cerrar sesión</button>
    </div>

    <form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>

    <div x-show="logout" x-cloak class="logout-backdrop" @click.self="logout=false">
        <div class="logout-modal">
            <div class="logout-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 17l5-5-5-5"/>
                    <path d="M15 12H3"/>
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                </svg>
            </div>

            <h3 class="logout-title">¿Deseas cerrar sesión?</h3>
            <p class="logout-text">Se cerrará tu sesión actual y tendrás que iniciar sesión nuevamente para acceder al sistema.</p>

            <div class="logout-actions">
                <button type="button" class="logout-cancel" @click="logout=false">No, cancelar</button>
                <button type="button" class="logout-confirm" @click="confirmLogout()">Sí, cerrar sesión</button>
            </div>
        </div>
    </div>

</nav>
