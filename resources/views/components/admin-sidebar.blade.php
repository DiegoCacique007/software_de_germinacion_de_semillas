<div class="flex flex-col w-80 bg-[#144255] text-white shadow-xl min-h-screen border-r border-[#0e303e] z-20 relative font-sans">
    @php
        $user = Auth::user();
        $role = $user->role ?? '';
    @endphp

    <style>
        .sidebar-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
    </style>

    <div class="flex items-center h-20 border-b border-white/10 px-6 bg-[#113849]">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#3bb49c] to-[#eab308] flex items-center justify-center text-[#144255] font-extrabold shadow-md">
            G
        </div>
        <div class="flex flex-col ml-3">
            <span class="text-sm font-bold uppercase tracking-widest text-gray-100">Germinación</span>
            <span class="text-[10px] text-[#3bb49c] uppercase tracking-wider">Control System</span>
        </div>
    </div>

    <div class="flex flex-col flex-1 overflow-y-auto pt-5">
        <p class="px-6 text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3">
            Módulos del Sistema
        </p>

        <nav class="flex-1 px-3 space-y-1">

            @if($role === 'super_admin')
                <p class="px-3 pt-2 pb-1 text-[10px] uppercase tracking-widest text-gray-400">Global</p>

                <a href="{{ route('super_admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.dashboard') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z"/></svg>
                    <span>Dashboard Global</span>
                </a>

                <a href="{{ route('super_admin.usuarios.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.usuarios.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m8-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0zM6 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Usuarios</span>
                </a>

                <a href="{{ route('super_admin.estados-incubadora.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.estados-incubadora.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Estados Incubadora</span>
                </a>

                <a href="{{ route('super_admin.incubadoras.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.incubadoras.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2" ry="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6M9 11h6M9 15h3"/></svg>
                    <span>Incubadoras</span>
                </a>

                <p class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-widest text-gray-400">Monitoreo y control</p>

                <a href="{{ route('super_admin.especies.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.especies.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c4-4 6-7.5 6-11a6 6 0 10-12 0c0 3.5 2 7 6 11z"/></svg>
                    <span>Especies</span>
                </a>

                <a href="{{ route('super_admin.condiciones-optimas-especie.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.condiciones-optimas-especie.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 10-5 0v11.26a4 4 0 105 0z"/></svg>
                    <span>Condiciones Óptimas</span>
                </a>

                <a href="{{ route('super_admin.posiciones-incubadora.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.posiciones-incubadora.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2" stroke-width="2"/></svg>
                    <span>Posiciones</span>
                </a>

                <a href="{{ route('super_admin.lotes.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.lotes.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span>Lotes</span>
                </a>

                <a href="{{ route('super_admin.frascos.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.frascos.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M10 3v4l-4.5 7.5A4 4 0 009 21h6a4 4 0 003.5-6.5L14 7V3"/></svg>
                    <span>Frascos</span>
                </a>

                <a href="{{ route('super_admin.lecturas-microclima.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.lecturas-microclima.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6 4 4 7-7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 8h6v6"/></svg>
                    <span>Lecturas Microclima</span>
                </a>

                <a href="{{ route('super_admin.controles-incubadora.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.controles-incubadora.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317a1 1 0 011.35-.936l1.95.78a1 1 0 00.76 0l1.95-.78a1 1 0 011.35.936v2.024a1 1 0 00.293.707l1.432 1.432a1 1 0 010 1.414l-1.432 1.432a1 1 0 00-.293.707v2.024a1 1 0 01-1.35.936l-1.95-.78a1 1 0 00-.76 0l-1.95.78a1 1 0 01-1.35-.936v-2.024a1 1 0 00-.293-.707L8.893 9.894a1 1 0 010-1.414l1.432-1.432a1 1 0 00.293-.707V4.317z"/><circle cx="14" cy="9" r="2" stroke-width="2"/></svg>
                    <span>Controles</span>
                </a>

                <p class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-widest text-gray-400">Seguimiento</p>

                <a href="{{ route('super_admin.seguimientos-lote.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.seguimientos-lote.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-4m3 8H6a2 2 0 01-2-2V5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2z"/></svg>
                    <span>Seguimientos Lote</span>
                </a>

                <a href="{{ route('super_admin.seguimientos-frasco.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.seguimientos-frasco.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M10 3v4l-4.5 7.5A4 4 0 009 21h6a4 4 0 003.5-6.5L14 7V3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6"/></svg>
                    <span>Seguimientos Frasco</span>
                </a>

                <a href="{{ route('super_admin.evidencias-lote.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.evidencias-lote.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" ry="2" stroke-width="2"/><circle cx="8.5" cy="10.5" r="1.5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15l-5-5L5 21"/></svg>
                    <span>Evidencias Lote</span>
                </a>

                <a href="{{ route('super_admin.alertas.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.alertas.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8 14A1 1 0 003.17 19h17.66a1 1 0 00.88-1.14l-8-14a1 1 0 00-1.74 0z"/></svg>
                    <span>Alertas</span>
                </a>

                <a href="{{ route('super_admin.registros-biologicos.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.registros-biologicos.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 4h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    <span>Registros Biológicos</span>
                </a>

                <p class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-widest text-gray-400">Reportes</p>

                <a href="{{ route('super_admin.reportes.microclima.pdf') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-gray-300 hover:bg-white/5 hover:text-white">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    <span>PDF Microclima</span>
                </a>

                <a href="{{ route('super_admin.reportes.biologico.pdf') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-gray-300 hover:bg-white/5 hover:text-white">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    <span>PDF Biológico</span>
                </a>
            @endif

            @if($role === 'administrador')
                <a href="{{ route('administrador.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.dashboard') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z"/></svg>
                    <span>Panel General</span>
                </a>

                <a href="{{ route('administrador.especies.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.especies.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c4-4 6-7.5 6-11a6 6 0 10-12 0c0 3.5 2 7 6 11z"/></svg>
                    <span>Especies</span>
                </a>

                <a href="{{ route('administrador.condiciones-optimas-especie.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.condiciones-optimas-especie.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14.76V3.5a2.5 2.5 0 10-5 0v11.26a4 4 0 105 0z"/></svg>
                    <span>Condiciones Óptimas</span>
                </a>

                <a href="{{ route('administrador.posiciones-incubadora.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.posiciones-incubadora.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2" stroke-width="2"/></svg>
                    <span>Posiciones</span>
                </a>

                <a href="{{ route('administrador.lotes.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.lotes.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span>Lotes</span>
                </a>

                <a href="{{ route('administrador.frascos.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.frascos.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M10 3v4l-4.5 7.5A4 4 0 009 21h6a4 4 0 003.5-6.5L14 7V3"/></svg>
                    <span>Frascos</span>
                </a>

                <a href="{{ route('administrador.lecturas-microclima.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.lecturas-microclima.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6 4 4 7-7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 8h6v6"/></svg>
                    <span>Lecturas Microclima</span>
                </a>

                <a href="{{ route('administrador.controles-incubadora.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('administrador.controles-incubadora.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317a1 1 0 011.35-.936l1.95.78a1 1 0 00.76 0l1.95-.78a1 1 0 011.35.936v2.024a1 1 0 00.293.707l1.432 1.432a1 1 0 010 1.414l-1.432 1.432a1 1 0 00-.293.707v2.024a1 1 0 01-1.35.936l-1.95-.78a1 1 0 00-.76 0l-1.95.78a1 1 0 01-1.35-.936v-2.024a1 1 0 00-.293-.707L8.893 9.894a1 1 0 010-1.414l1.432-1.432a1 1 0 00.293-.707V4.317z"/><circle cx="14" cy="9" r="2" stroke-width="2"/></svg>
                    <span>Controles</span>
                </a>

                <a href="{{ route('administrador.reportes.microclima.pdf') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-gray-300 hover:bg-white/5 hover:text-white">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    <span>PDF Microclima</span>
                </a>
            @endif

            @if($role === 'encargado')
                <a href="{{ route('encargado.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.dashboard') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z"/></svg>
                    <span>Panel General</span>
                </a>

                <a href="{{ route('encargado.seguimientos-lote.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.seguimientos-lote.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-4m3 8H6a2 2 0 01-2-2V5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2z"/></svg>
                    <span>Seguimientos Lote</span>
                </a>

                <a href="{{ route('encargado.seguimientos-frasco.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.seguimientos-frasco.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M10 3v4l-4.5 7.5A4 4 0 009 21h6a4 4 0 003.5-6.5L14 7V3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14h6"/></svg>
                    <span>Seguimientos Frasco</span>
                </a>

                <a href="{{ route('encargado.evidencias-lote.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.evidencias-lote.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" ry="2" stroke-width="2"/><circle cx="8.5" cy="10.5" r="1.5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15l-5-5L5 21"/></svg>
                    <span>Evidencias Lote</span>
                </a>

                <a href="{{ route('encargado.alertas.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.alertas.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8 14A1 1 0 003.17 19h17.66a1 1 0 00.88-1.14l-8-14a1 1 0 00-1.74 0z"/></svg>
                    <span>Alertas</span>
                </a>

                <a href="{{ route('encargado.registros-biologicos.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('encargado.registros-biologicos.*') ? 'bg-[#1c607a] border-[#3bb49c] text-white' : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 4h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    <span>Registros Biológicos</span>
                </a>

                <a href="{{ route('encargado.reportes.biologico.pdf') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-gray-300 hover:bg-white/5 hover:text-white">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    <span>PDF Biológico</span>
                </a>
            @endif
        </nav>

        <div class="mt-auto bg-[#113849] border-t border-white/5 p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded bg-[#1c607a] border border-[#3bb49c]/30 flex items-center justify-center font-bold text-white text-sm">
                    {{ substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-[13px] font-semibold text-gray-200 truncate">{{ $user->name ?? 'Usuario' }}</p>
                    <p class="text-[11px] text-[#3bb49c] font-medium tracking-wide truncate">
                        {{ strtoupper(str_replace('_', ' ', $user->role ?? 'usuario')) }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-600 text-gray-300 hover:bg-red-900/40 hover:text-red-400 hover:border-red-900/50 rounded-md transition-colors text-xs font-semibold tracking-wide uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
