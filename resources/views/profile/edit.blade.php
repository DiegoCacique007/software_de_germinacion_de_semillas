@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $rolClave = $user?->rol_clave;
    $rolVisible = strtoupper($user?->rol_nombre ?? 'Sin rol');
    $isSuperAdmin = $user?->isSuperAdmin() ?? false;
    $fotoUsuario = $user && $user->foto_perfil ? asset('storage/' . $user->foto_perfil) : null;

    $rutaInicio = match ($rolClave) {
        'super_admin' => Route::has('super_admin.dashboard') ? route('super_admin.dashboard') : route('dashboard'),
        'encargado' => Route::has('encargado.dashboard') ? route('encargado.dashboard') : route('dashboard'),
        default => Route::has('dashboard') ? route('dashboard') : url('/'),
    };

    $estadoVisible = $user?->activo ? 'Activo' : 'Inactivo';
    $ultimoAcceso = $user?->ultimo_acceso_at ? $user->ultimo_acceso_at->format('d/m/Y H:i') : 'Sin acceso registrado';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="microseed-profile-header-slot">
            <div>
                <p class="microseed-profile-header-kicker">MicroSeed Control</p>
                <h2 class="microseed-profile-header-title">Perfil de usuario</h2>
            </div>

            <a href="{{ $rutaInicio }}" class="microseed-profile-header-link">Volver al panel</a>
        </div>
    </x-slot>

    <style>
        .microseed-profile-page,.microseed-profile-page *,.microseed-profile-header-slot,.microseed-profile-header-slot *{font-family:'Instrument Sans',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;box-sizing:border-box}
        .microseed-profile-header-slot{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .microseed-profile-header-kicker{margin:0 0 4px;color:#3bb49c;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        .microseed-profile-header-title{margin:0;color:#334155;font-size:22px;font-weight:800;line-height:1.1;letter-spacing:-.035em}
        .microseed-profile-header-link{min-height:38px;display:inline-flex;align-items:center;justify-content:center;padding:0 14px;border-radius:13px;color:#1c607a;background:#ecfeff;font-size:12px;font-weight:800;text-decoration:none;transition:background .16s ease,transform .16s ease}
        .microseed-profile-header-link:hover{background:#d9fbf5;transform:translateY(-1px)}

        .microseed-profile-page{min-height:calc(100vh - 82px);background:radial-gradient(circle at top left,rgba(59,180,156,.12),transparent 34%),linear-gradient(180deg,#f8fafc 0%,#eef7f5 100%);color:#334155}
        .microseed-profile-container{width:min(1180px,calc(100% - 32px));margin:0 auto;padding:30px 0 46px}

        .microseed-profile-hero{position:relative;overflow:hidden;margin-bottom:24px;border:1px solid rgba(59,180,156,.18);border-radius:30px;background:radial-gradient(circle at 88% 10%,rgba(101,224,195,.28),transparent 42%),linear-gradient(135deg,#123f54 0%,#176475 50%,#2b9691 100%);box-shadow:0 24px 60px rgba(15,23,42,.12)}
        .microseed-profile-hero::before{content:'';position:absolute;top:-90px;right:-65px;width:230px;height:230px;border-radius:9999px;background:rgba(255,255,255,.07);pointer-events:none}
        .microseed-profile-hero-content{position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px}
        .microseed-profile-identity{min-width:0;display:flex;align-items:center;gap:24px}

        .microseed-main-profile-avatar-wrap{position:relative;width:160px;height:160px;flex-shrink:0}
        .microseed-main-profile-avatar{width:160px;height:160px;display:flex;align-items:center;justify-content:center;overflow:hidden;border:3px solid rgba(255,255,255,.28);border-radius:42px;color:#fff;background:linear-gradient(135deg,rgba(255,255,255,.22),rgba(255,255,255,.08));box-shadow:0 18px 36px rgba(7,37,49,.28),inset 0 1px 0 rgba(255,255,255,.24);font-size:46px;font-weight:800}
        .microseed-main-profile-avatar img{width:100%;height:100%;display:block;object-fit:cover;object-position:center}
        .microseed-main-profile-photo-button{position:absolute;right:-10px;bottom:-10px;width:44px;height:44px;display:flex;align-items:center;justify-content:center;border:3px solid #fff;border-radius:9999px;color:#fff;background:#3bb49c;box-shadow:0 10px 22px rgba(15,23,42,.2);cursor:pointer;transition:transform .16s ease,background .16s ease}
        .microseed-main-profile-photo-button:hover{background:#2fa58e;transform:translateY(-1px) scale(1.05)}

        .microseed-profile-title-area{min-width:0}
        .microseed-profile-kicker{display:flex;align-items:center;gap:8px;margin-bottom:8px;color:rgba(224,255,249,.78);font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        .microseed-profile-kicker::before{content:'';width:7px;height:7px;border-radius:9999px;background:#65e0c3;box-shadow:0 0 0 5px rgba(101,224,195,.12)}
        .microseed-profile-title{overflow:hidden;color:#fff;font-size:clamp(24px,3vw,34px);font-weight:800;line-height:1.05;letter-spacing:-.04em;text-overflow:ellipsis;white-space:nowrap;text-shadow:0 4px 14px rgba(8,39,52,.28)}
        .microseed-profile-subtitle{margin-top:8px;color:rgba(224,255,249,.76);font-size:13px;font-weight:500}
        .microseed-profile-role-badge{display:inline-flex;align-items:center;gap:8px;flex-shrink:0;padding:11px 14px;border:1px solid rgba(255,255,255,.2);border-radius:9999px;color:#fff;background:rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.14);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}

        .microseed-profile-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(300px,.85fr);gap:22px}
        .microseed-profile-column{display:flex;flex-direction:column;gap:22px}
        .microseed-profile-card{overflow:hidden;border:1px solid rgba(203,213,225,.76);border-radius:26px;background:rgba(255,255,255,.94);box-shadow:0 18px 42px rgba(15,23,42,.07),inset 0 1px 0 rgba(255,255,255,.9);backdrop-filter:blur(16px)}
        .microseed-profile-card-danger{border-color:rgba(248,113,113,.34)}

        .microseed-profile-card-header{display:flex;align-items:flex-start;gap:14px;padding:22px 24px 18px;border-bottom:1px solid #eef2f6;background:linear-gradient(135deg,rgba(236,254,255,.7),rgba(248,250,252,.92))}
        .microseed-profile-card-danger .microseed-profile-card-header{background:linear-gradient(135deg,#fff1f2,#fff)}
        .microseed-profile-card-icon{width:44px;height:44px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:15px;color:#1c607a;background:linear-gradient(135deg,rgba(33,106,115,.1),rgba(59,180,156,.16));box-shadow:inset 0 0 0 1px rgba(59,180,156,.14)}
        .microseed-profile-card-danger .microseed-profile-card-icon{color:#e11d48;background:#fff1f2;box-shadow:inset 0 0 0 1px rgba(244,63,94,.13)}
        .microseed-profile-card-title{margin:0;color:#334155;font-size:16px;font-weight:800;line-height:1.2;letter-spacing:-.02em}
        .microseed-profile-card-description{margin-top:5px;color:#94a3b8;font-size:12px;font-weight:500;line-height:1.45}
        .microseed-profile-card-body{padding:24px}

        .microseed-account-list{display:flex;flex-direction:column;gap:12px}
        .microseed-account-item{display:flex;align-items:center;gap:12px;padding:14px;border:1px solid #eef2f6;border-radius:17px;background:#f8fafc}
        .microseed-account-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:13px;color:#1c607a;background:#ecfeff}
        .microseed-account-label{margin:0;color:#94a3b8;font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
        .microseed-account-value{margin:3px 0 0;overflow:hidden;color:#334155;font-size:12px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}
        .microseed-account-status{display:inline-flex;align-items:center;gap:6px}
        .microseed-account-status::before{content:'';width:7px;height:7px;border-radius:999px;background:#3bb49c;box-shadow:0 0 0 3px rgba(59,180,156,.12)}
        .microseed-account-status.inactive::before{background:#94a3b8;box-shadow:0 0 0 3px rgba(148,163,184,.15)}

        .microseed-profile-card section{width:100%}
        .microseed-profile-card section>header{display:none}
        .microseed-profile-card form{margin:0}
        .microseed-profile-card label{color:#475569!important;font-size:11px!important;font-weight:800!important;letter-spacing:.07em!important;text-transform:uppercase!important}
        .microseed-profile-card input[type="text"],.microseed-profile-card input[type="email"],.microseed-profile-card input[type="password"]{width:100%!important;height:46px!important;border:1px solid #e2e8f0!important;border-radius:14px!important;outline:none!important;color:#334155!important;background:#f8fafc!important;padding:0 14px!important;font-size:13px!important;font-weight:600!important;box-shadow:none!important;transition:border-color .16s ease,background .16s ease,box-shadow .16s ease!important}
        .microseed-profile-card input[type="text"]:focus,.microseed-profile-card input[type="email"]:focus,.microseed-profile-card input[type="password"]:focus{border-color:rgba(59,180,156,.55)!important;background:#fff!important;box-shadow:0 0 0 4px rgba(59,180,156,.1)!important}
        .microseed-profile-card p{color:#64748b;font-size:13px;line-height:1.55}

        .microseed-profile-protected{padding:16px;border:1px solid rgba(59,180,156,.2);border-radius:16px;background:#effaf8;color:#475569;font-size:12px;font-weight:600;line-height:1.5}
        .microseed-profile-protected strong{color:#1c607a}

        @media(max-width:920px){
            .microseed-profile-grid{grid-template-columns:1fr}
            .microseed-profile-hero-content{align-items:flex-start;flex-direction:column}
            .microseed-profile-role-badge{width:fit-content}
        }

        @media(max-width:620px){
            .microseed-profile-container{width:min(100% - 20px,1180px);padding-top:18px}
            .microseed-profile-header-slot{align-items:flex-start;flex-direction:column}
            .microseed-profile-hero-content{padding:22px}
            .microseed-profile-identity{align-items:flex-start;flex-direction:column}
            .microseed-main-profile-avatar-wrap{width:130px;height:130px}
            .microseed-main-profile-avatar{width:130px;height:130px;border-radius:34px;font-size:38px}
            .microseed-main-profile-photo-button{right:-8px;bottom:-8px;width:40px;height:40px}
            .microseed-profile-title{white-space:normal}
            .microseed-profile-card-body{padding:20px}
        }
    </style>

    <div class="microseed-profile-page">
        <div class="microseed-profile-container">

            <section class="microseed-profile-hero">
                <div class="microseed-profile-hero-content">
                    <div class="microseed-profile-identity">

                        <div class="microseed-main-profile-avatar-wrap">
                            <div class="microseed-main-profile-avatar">
                                @if($fotoUsuario)
                                    <img src="{{ $fotoUsuario }}" alt="Foto de {{ $user->name }}">
                                @else
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>

                            @if(Route::has('perfil.foto.update'))
                                <form id="profile-photo-form" method="POST" action="{{ route('perfil.foto.update') }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <label for="profile_foto_perfil" class="microseed-main-profile-photo-button" title="Cambiar fotografía">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.2 5.2 18.8 8.8M9 13l6.6-6.6a2 2 0 112.8 2.8L11.8 15.8a2 2 0 01-.9.5L7 17l.7-4a2 2 0 01.5-.8L9 13z"/>
                                        </svg>
                                    </label>

                                    <input id="profile_foto_perfil" type="file" name="foto_perfil" accept="image/png,image/jpeg,image/jpg,image/webp" class="d-none" style="display:none;" onchange="document.getElementById('profile-photo-form').submit()">
                                </form>
                            @endif
                        </div>

                        <div class="microseed-profile-title-area">
                            <p class="microseed-profile-kicker">Perfil de usuario</p>
                            <h1 class="microseed-profile-title">{{ $user->name ?? 'Usuario' }}</h1>
                            <p class="microseed-profile-subtitle">Administra tu información personal, seguridad y acceso dentro de MicroSeed Control.</p>
                        </div>

                    </div>

                    <span class="microseed-profile-role-badge">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z"/>
                        </svg>
                        {{ $rolVisible }}
                    </span>
                </div>
            </section>

            <div class="microseed-profile-grid">

                <div class="microseed-profile-column">

                    <section class="microseed-profile-card">
                        <div class="microseed-profile-card-header">
                            <span class="microseed-profile-card-icon">
                                <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"/>
                                </svg>
                            </span>

                            <div>
                                <h3 class="microseed-profile-card-title">Información del perfil</h3>
                                <p class="microseed-profile-card-description">Actualiza tu nombre y correo electrónico.</p>
                            </div>
                        </div>

                        <div class="microseed-profile-card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </section>

                    <section class="microseed-profile-card">
                        <div class="microseed-profile-card-header">
                            <span class="microseed-profile-card-icon">
                                <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V7a3 3 0 016 0v4"/>
                                </svg>
                            </span>

                            <div>
                                <h3 class="microseed-profile-card-title">Seguridad de la cuenta</h3>
                                <p class="microseed-profile-card-description">Cambia tu contraseña para mantener tu cuenta protegida.</p>
                            </div>
                        </div>

                        <div class="microseed-profile-card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </section>

                </div>

                <div class="microseed-profile-column">

                    <section class="microseed-profile-card">
                        <div class="microseed-profile-card-header">
                            <span class="microseed-profile-card-icon">
                                <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                                </svg>
                            </span>

                            <div>
                                <h3 class="microseed-profile-card-title">Resumen de cuenta</h3>
                                <p class="microseed-profile-card-description">Información general del usuario autenticado.</p>
                            </div>
                        </div>

                        <div class="microseed-profile-card-body">
                            <div class="microseed-account-list">

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Usuario</p>
                                        <p class="microseed-account-value">{{ $user->name ?? 'Usuario' }}</p>
                                    </div>
                                </div>

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4V4zm0 4 8 5 8-5"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Correo</p>
                                        <p class="microseed-account-value">{{ $user->email ?? 'Sin correo registrado' }}</p>
                                    </div>
                                </div>

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Rol</p>
                                        <p class="microseed-account-value">{{ $rolVisible }}</p>
                                    </div>
                                </div>

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l4 4L19 6"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Estado</p>
                                        <p class="microseed-account-value">
                                            <span class="microseed-account-status {{ $user?->activo ? '' : 'inactive' }}">{{ $estadoVisible }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Último acceso</p>
                                        <p class="microseed-account-value">{{ $ultimoAcceso }}</p>
                                    </div>
                                </div>

                                <div class="microseed-account-item">
                                    <span class="microseed-account-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="microseed-account-label">Registro</p>
                                        <p class="microseed-account-value">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Sin fecha' }}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <section class="microseed-profile-card microseed-profile-card-danger">
                        <div class="microseed-profile-card-header">
                            <span class="microseed-profile-card-icon">
                                <svg width="21" height="21" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.3 3.9 1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>
                                </svg>
                            </span>

                            <div>
                                <h3 class="microseed-profile-card-title">Zona de riesgo</h3>

                                @if($isSuperAdmin)
                                    <p class="microseed-profile-card-description">La cuenta de superadministrador está protegida contra desactivación desde el perfil.</p>
                                @else
                                    <p class="microseed-profile-card-description">Puedes desactivar tu cuenta si ya no deseas utilizar el sistema.</p>
                                @endif
                            </div>
                        </div>

                        <div class="microseed-profile-card-body">
                            @if($isSuperAdmin)
                                <div class="microseed-profile-protected">
                                    <strong>Cuenta protegida.</strong> Un superadministrador no puede desactivar su propia cuenta desde esta sección.
                                </div>
                            @else
                                @include('profile.partials.delete-user-form')
                            @endif
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
