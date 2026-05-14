<div class="flex flex-col w-80 bg-[#2a818c] text-white shadow-xl min-h-screen border-r border-[#1d626b] z-20 relative font-sans" x-data="{ showMicroclimaModal: false, showBiologicoModal: false }">
    @php
        $user = Auth::user();
        $role = $user->role ?? '';
        $incubadorasList = \App\Models\Incubadora::orderBy('id')->get();
        $lotesList = \App\Models\Lote::orderBy('id')->get();
    @endphp

    <style>
        .sidebar-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
    </style>

    <div class="flex items-center h-20 border-b border-white/10 px-6 bg-[#216a73]">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ffffff] to-[#eab308] flex items-center justify-center text-[#2a818c] font-extrabold shadow-md">

        </div>
        <div class="flex flex-col ml-3">
            <span class="text-sm font-bold uppercase tracking-widest text-white">Admin</span>
            <span class="text-[10px] text-[#c9e6e8] uppercase tracking-wider opacity-80">Microseed Control</span>
        </div>
    </div>

    <div class="flex flex-col flex-1 overflow-y-auto pt-5">
        <p class="px-6 text-[11px] font-semibold text-[#a6d8da] uppercase tracking-widest mb-3">
            Módulos del Sistema
        </p>

        <nav class="flex-1 px-3 space-y-1">

            @if($role === 'super_admin')

                {{-- GLOBAL --}}
                <p class="px-3 pt-2 pb-1 text-[10px] uppercase tracking-widest text-[#a6d8da] opacity-70">Global</p>

                <a href="{{ route('super_admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.dashboard') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z"/>
                    </svg>
                    <span>Dashboard Global</span>
                </a>

                <a href="{{ route('super_admin.usuarios.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 {{ request()->routeIs('super_admin.usuarios.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m8-6.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0zM6 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Usuarios</span>
                </a>

                {{-- ALERTAS --}}
                <div
                    x-data="{ open: {{ request()->routeIs('super_admin.alertas.*') || request()->routeIs('super_admin.tipos-alerta.*') || request()->routeIs('super_admin.niveles-alerta.*') || request()->routeIs('super_admin.estados-alerta.*') ? 'true' : 'false' }} }"
                    class="pt-2"
                >
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[10px] uppercase tracking-widest text-[#a6d8da] hover:text-white transition">
                        <span>Alertas</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="space-y-1 mt-1">
                        <a href="{{ route('super_admin.alertas.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.alertas.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Alertas</span>
                        </a>

                        <a href="{{ route('super_admin.tipos-alerta.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.tipos-alerta.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Tipos de alerta</span>
                        </a>

                        <a href="{{ route('super_admin.niveles-alerta.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.niveles-alerta.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Niveles de alerta</span>
                        </a>

                        <a href="{{ route('super_admin.estados-alerta.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.estados-alerta.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Estados de alerta</span>
                        </a>
                    </div>
                </div>

                {{-- INCUBADORAS --}}
                <div
                    x-data="{ open: {{ request()->routeIs('super_admin.incubadoras.*') || request()->routeIs('super_admin.estados-incubadora.*') || request()->routeIs('super_admin.posiciones-incubadora.*') || request()->routeIs('super_admin.asignaciones-incubadora.*') ? 'true' : 'false' }} }"
                    class="pt-2"
                >
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[10px] uppercase tracking-widest text-[#a6d8da] hover:text-white transition">
                        <span>Incubadoras</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="space-y-1 mt-1">
                        <a href="{{ route('super_admin.incubadoras.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.incubadoras.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Incubadoras</span>
                        </a>

                        <a href="{{ route('super_admin.estados-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.estados-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Estados de incubadora</span>
                        </a>

                        <a href="{{ route('super_admin.posiciones-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.posiciones-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Posiciones de incubadora</span>
                        </a>

                        <a href="{{ route('super_admin.asignaciones-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.asignaciones-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Asignaciones de incubadora</span>
                        </a>
                    </div>
                </div>

                {{-- MONITOREO Y CONTROL --}}
                <div
                    x-data="{ open: {{ request()->routeIs('super_admin.lecturas-microclima.*') || request()->routeIs('super_admin.controles-incubadora.*') || request()->routeIs('super_admin.tipos-control-incubadora.*') || request()->routeIs('super_admin.modos-control-incubadora.*') ? 'true' : 'false' }} }"
                    class="pt-2"
                >
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[10px] uppercase tracking-widest text-[#a6d8da] hover:text-white transition">
                        <span>Monitoreo y control</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="space-y-1 mt-1">
                        <a href="{{ route('super_admin.lecturas-microclima.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.lecturas-microclima.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Lecturas microclima</span>
                        </a>

                        <a href="{{ route('super_admin.controles-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.controles-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Controles</span>
                        </a>

                        <a href="{{ route('super_admin.tipos-control-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.tipos-control-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Tipos de control</span>
                        </a>

                        <a href="{{ route('super_admin.modos-control-incubadora.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.modos-control-incubadora.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Modos de control</span>
                        </a>
                    </div>
                </div>

                {{-- GERMINACIÓN --}}
                <div
                    x-data="{ open: {{ request()->routeIs('super_admin.especies.*') || request()->routeIs('super_admin.condiciones-optimas-especie.*') || request()->routeIs('super_admin.lotes.*') || request()->routeIs('super_admin.estados-lote.*') || request()->routeIs('super_admin.frascos.*') || request()->routeIs('super_admin.estados-frasco.*') || request()->routeIs('super_admin.etapas-desarrollo.*') ? 'true' : 'false' }} }"
                    class="pt-2"
                >
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[10px] uppercase tracking-widest text-[#a6d8da] hover:text-white transition">
                        <span>Germinación</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="space-y-1 mt-1">
                        <a href="{{ route('super_admin.especies.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.especies.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Especies</span>
                        </a>

                        <a href="{{ route('super_admin.condiciones-optimas-especie.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.condiciones-optimas-especie.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Condiciones óptimas</span>
                        </a>

                        <a href="{{ route('super_admin.lotes.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.lotes.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Lotes</span>
                        </a>

                        <a href="{{ route('super_admin.estados-lote.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.estados-lote.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Estados de lote</span>
                        </a>

                        <a href="{{ route('super_admin.frascos.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.frascos.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Frascos</span>
                        </a>

                        <a href="{{ route('super_admin.estados-frasco.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.estados-frasco.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Estados de frasco</span>
                        </a>

                        <a href="{{ route('super_admin.etapas-desarrollo.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.etapas-desarrollo.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Etapas de desarrollo</span>
                        </a>
                    </div>
                </div>

                {{-- SEGUIMIENTO BIOLÓGICO --}}
                <div
                    x-data="{ open: {{ request()->routeIs('super_admin.seguimientos-lote.*') || request()->routeIs('super_admin.seguimientos-frasco.*') || request()->routeIs('super_admin.evidencias-lote.*') || request()->routeIs('super_admin.registros-biologicos.*') ? 'true' : 'false' }} }"
                    class="pt-2"
                >
                    <button @click="open = !open"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 text-[10px] uppercase tracking-widest text-[#a6d8da] hover:text-white transition">
                        <span>Seguimiento biológico</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition class="space-y-1 mt-1">
                        <a href="{{ route('super_admin.seguimientos-lote.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.seguimientos-lote.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Seguimientos lote</span>
                        </a>

                        <a href="{{ route('super_admin.seguimientos-frasco.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.seguimientos-frasco.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Seguimientos frasco</span>
                        </a>

                        <a href="{{ route('super_admin.evidencias-lote.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.evidencias-lote.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Evidencias lote</span>
                        </a>

                        <a href="{{ route('super_admin.registros-biologicos.index') }}"
                           class="flex items-center gap-3 pl-10 pr-3 py-2 text-sm rounded-md border-l-4 transition-colors duration-200 {{ request()->routeIs('super_admin.registros-biologicos.*') ? 'bg-white/15 border-white text-white' : 'border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white' }}">
                            <span>Registros biológicos</span>
                        </a>
                    </div>
                </div>

                {{-- REPORTES --}}
                <div class="pt-3">
                    <p class="px-3 pb-1 text-[10px] uppercase tracking-widest text-[#a6d8da] opacity-70">Reportes</p>

                    <button @click="showMicroclimaModal = true"
                       class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        </svg>
                        <span>PDF Microclima</span>
                    </button>

                    <button @click="showBiologicoModal = true"
                       class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 border-l-4 border-transparent text-[#d1ecec] hover:bg-white/10 hover:text-white">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        </svg>
                        <span>PDF Biológico</span>
                    </button>
                </div>
            @endif

        </nav>

        <div class="mt-auto bg-[#216a73] border-t border-white/10 p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded bg-white/20 border border-white/30 flex items-center justify-center font-bold text-white text-sm">
                    {{ substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-[13px] font-semibold text-white truncate">{{ $user->name ?? 'Usuario' }}</p>
                    <p class="text-[11px] text-[#c9e6e8] font-medium tracking-wide truncate opacity-80">
                        {{ strtoupper(str_replace('_', ' ', $user->role ?? 'usuario')) }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-white/30 text-white hover:bg-red-500/40 hover:border-red-500/50 rounded-md transition-colors text-xs font-semibold tracking-wide uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Filtros PDF Microclima -->
    <div x-show="showMicroclimaModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 text-gray-800" @click.away="showMicroclimaModal = false">
            <div class="bg-[#1c607a] text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    Filtros: PDF Microclima
                </h3>
                <button @click="showMicroclimaModal = false" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('super_admin.reportes.microclima.pdf') }}" method="GET" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de Fin</label>
                        <input type="date" name="fecha_fin" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Incubadora</label>
                        <select name="incubadora_id" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c] bg-white">
                            <option value="">Todas las incubadoras</option>
                            @foreach($incubadorasList as $inc)
                                <option value="{{ $inc->id }}">{{ $inc->nombre ?? 'Incubadora #' . $inc->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex gap-3 justify-end">
                    <button type="button" @click="showMicroclimaModal = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" @click="showMicroclimaModal = false" class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Filtros PDF Biológico -->
    <div x-show="showBiologicoModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 text-gray-800" @click.away="showBiologicoModal = false">
            <div class="bg-[#1c607a] text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/></svg>
                    Filtros: PDF Biológico
                </h3>
                <button @click="showBiologicoModal = false" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('super_admin.reportes.biologico.pdf') }}" method="GET" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de Fin</label>
                        <input type="date" name="fecha_fin" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Lote</label>
                        <select name="lote_id" class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c] bg-white">
                            <option value="">Todos los lotes</option>
                            @foreach($lotesList as $lote)
                                <option value="{{ $lote->id }}">{{ $lote->codigo_lote ?? 'Lote #' . $lote->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex gap-3 justify-end">
                    <button type="button" @click="showBiologicoModal = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" @click="showBiologicoModal = false" class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
