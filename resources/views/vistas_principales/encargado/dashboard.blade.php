<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'encargado'));
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(217, 173, 43, 0.18); }
            50% { box-shadow: 0 0 36px rgba(217, 173, 43, 0.35); }
        }

        @keyframes gradientPan {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes epicEnter {
            0% { opacity: 0; transform: scale(0.96) translateY(30px); filter: blur(8px); }
            100% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
        }

        .animate-epic {
            animation: epicEnter 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.74);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 25px 50px -12px rgba(25, 53, 64, 0.16);
        }

        .text-gradient {
            background: linear-gradient(135deg, #1d4d63 0%, #6f7b36 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-glow:hover {
            animation: pulseGlow 2s infinite;
            transform: translateY(-4px) scale(1.01);
        }

        .bg-animated-mesh {
            background: linear-gradient(-45deg, #f8f7ef, #f1eedf, #e7e4cf, #f8f7ef);
            background-size: 400% 400%;
            animation: gradientPan 15s ease infinite;
        }

        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .75rem;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 700;
        }
    </style>

    <div class="flex min-h-screen bg-animated-mesh relative overflow-hidden">
        <x-admin-sidebar />

        <div class="flex-1 relative"
             x-data="{
                time: '',
                greeting: '',
                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    setInterval(() => window.location.reload(), 15000);
                },
                updateClock() {
                    const d = new Date();
                    this.time = d.toLocaleTimeString('es-MX', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    let h = d.getHours();
                    this.greeting = h < 12 ? 'Buenos días' : (h < 19 ? 'Buenas tardes' : 'Buenas noches');
                }
             }">

            <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-[#d9ad2b] rounded-full mix-blend-multiply blur-[100px] opacity-20 animate-float pointer-events-none"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[30rem] h-[30rem] bg-[#6f7b36] rounded-full mix-blend-multiply blur-[120px] opacity-20 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

            <div class="py-12 px-6 relative z-10">
                <div class="max-w-7xl mx-auto">
                    <div class="glass-card overflow-hidden shadow-sm sm:rounded-[2rem] border-t-4 border-[#d9ad2b] animate-epic relative">

                        <div class="relative px-8 py-10 border-b border-white/50">
                            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                                <div>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#d9ad2b]/10 border border-[#d9ad2b]/30 text-[#1d4d63] text-xs font-black uppercase tracking-[0.2em] mb-4">
                                        <span class="w-2 h-2 rounded-full bg-[#d9ad2b] animate-pulse"></span>
                                        Dashboard encargado
                                    </div>

                                    <h3 class="text-3xl md:text-5xl font-black mb-2 tracking-tight text-gradient">
                                        <span x-text="greeting">Bienvenido</span>,<br>
                                        {{ $nombreCorto }}
                                    </h3>

                                    <p class="text-gray-600 text-base md:text-lg max-w-2xl">
                                        Da seguimiento operativo a lotes y frascos, registra evidencias y atiende alertas del sistema.
                                    </p>
                                </div>

                                <div class="bg-white/40 backdrop-blur-md border border-white/60 p-4 rounded-2xl shadow-xl flex items-center gap-4 hover-glow transition-all">
                                    <div class="p-3 bg-gradient-to-br from-[#6f7b36] to-[#d9ad2b] rounded-xl text-white shadow-inner">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tiempo local</p>
                                        <p class="text-2xl font-black text-[#1d4d63] font-mono tracking-tight" x-text="time">--:--:--</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-white/30 backdrop-blur-sm">

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seguimientos lote</p>
                                    <h3 class="text-3xl font-black text-[#1d4d63]">{{ $seguimientosLoteTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Registros generales de lote</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seguimientos frasco</p>
                                    <h3 class="text-3xl font-black text-[#1d4d63]">{{ $seguimientosFrascoTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Registros por frasco</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Evidencias</p>
                                    <h3 class="text-3xl font-black text-[#1d4d63]">{{ $evidenciasTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Archivos subidos al sistema</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alertas activas</p>
                                    <h3 class="text-3xl font-black text-[#1d4d63]">{{ $alertasActivas }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Pendientes o en revisión</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide">Mis seguimientos de lote</h3>
                                            <p class="text-sm text-gray-500">Últimos registros hechos por ti</p>
                                        </div>
                                        <a href="{{ route('encargado.seguimientos-lote.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#1d4d63] text-white text-sm font-semibold hover:bg-[#295f78] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Lote</th>
                                                <th class="py-2 pr-4">Usuario</th>
                                                <th class="py-2">Registro</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($misSeguimientosLote as $seguimiento)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#1d4d63]">
                                                        {{ $seguimiento->lote->codigo_lote ?? 'Sin lote' }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $seguimiento->usuario->name ?? 'Sin usuario' }}
                                                    </td>
                                                    <td class="py-3 text-gray-500">
                                                        {{ $seguimiento->created_at ? $seguimiento->created_at->format('d/m/Y H:i') : '—' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-6 text-center text-gray-500">No has registrado seguimientos de lote todavía.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide">Mis seguimientos de frasco</h3>
                                            <p class="text-sm text-gray-500">Últimos registros individuales</p>
                                        </div>
                                        <a href="{{ route('encargado.seguimientos-frasco.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#1d4d63] text-white text-sm font-semibold hover:bg-[#295f78] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Frasco</th>
                                                <th class="py-2 pr-4">Usuario</th>
                                                <th class="py-2">Registro</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($misSeguimientosFrasco as $seguimiento)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#1d4d63]">
                                                        {{ $seguimiento->frasco->codigo_frasco ?? ('Frasco #' . ($seguimiento->frasco_id ?? '')) }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $seguimiento->usuario->name ?? 'Sin usuario' }}
                                                    </td>
                                                    <td class="py-3 text-gray-500">
                                                        {{ $seguimiento->created_at ? $seguimiento->created_at->format('d/m/Y H:i') : '—' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-6 text-center text-gray-500">No has registrado seguimientos de frasco todavía.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide">Últimas evidencias</h3>
                                            <p class="text-sm text-gray-500">Archivos y soportes recientes</p>
                                        </div>
                                        <a href="{{ route('encargado.evidencias-lote.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#1d4d63] text-white text-sm font-semibold hover:bg-[#295f78] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Lote</th>
                                                <th class="py-2 pr-4">Archivo</th>
                                                <th class="py-2">Fecha</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($ultimasEvidencias as $evidencia)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#1d4d63]">
                                                        {{ $evidencia->seguimientoLote->lote->codigo_lote ?? 'Sin lote' }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $evidencia->archivo ?? $evidencia->nombre_archivo ?? 'Archivo' }}
                                                    </td>
                                                    <td class="py-3 text-gray-500">
                                                        {{ $evidencia->created_at ? $evidencia->created_at->format('d/m/Y H:i') : '—' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-6 text-center text-gray-500">No hay evidencias registradas.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide">Alertas recientes</h3>
                                            <p class="text-sm text-gray-500">Eventos que requieren atención</p>
                                        </div>
                                        <a href="{{ route('encargado.alertas.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#1d4d63] text-white text-sm font-semibold hover:bg-[#295f78] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Incubadora</th>
                                                <th class="py-2 pr-4">Tipo</th>
                                                <th class="py-2 pr-4">Estado</th>
                                                <th class="py-2">Fecha</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($alertasRecientes as $alerta)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#1d4d63]">
                                                        {{ $alerta->incubadora->nombre ?? 'Sin incubadora' }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $alerta->tipo->nombre ?? 'Sin tipo' }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $alerta->estado->nombre ?? 'Sin estado' }}
                                                    </td>
                                                    <td class="py-3 text-gray-500">
                                                        {{ isset($alerta->fecha_hora) ? \Carbon\Carbon::parse($alerta->fecha_hora)->format('d/m/Y H:i') : ($alerta->created_at ? $alerta->created_at->format('d/m/Y H:i') : '—') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-6 text-center text-gray-500">No hay alertas registradas.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide mb-4">Estado del sistema</h3>

                                    <div class="space-y-4">
                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nivel de acceso</p>
                                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#1d4d63] text-[#f4d03f] rounded-md text-sm font-bold uppercase tracking-wider shadow-inner">
                                                {{ $rolNombre }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Correo de sesión</p>
                                            <p class="text-lg font-black text-[#1d4d63] break-all">{{ $usuario->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <h3 class="font-black text-[#1d4d63] text-lg uppercase tracking-wide mb-4">Accesos rápidos</h3>

                                    <div class="grid grid-cols-1 gap-4">
                                        <a href="{{ route('encargado.seguimientos-lote.index') }}"
                                           class="rounded-xl bg-[#1d4d63] px-5 py-4 text-white font-bold hover:bg-[#295f78] transition">
                                            Ir a seguimientos de lote
                                        </a>

                                        <a href="{{ route('encargado.seguimientos-frasco.index') }}"
                                           class="rounded-xl bg-[#1d4d63] px-5 py-4 text-white font-bold hover:bg-[#295f78] transition">
                                            Ir a seguimientos de frasco
                                        </a>

                                        <a href="{{ route('encargado.evidencias-lote.index') }}"
                                           class="rounded-xl bg-[#1d4d63] px-5 py-4 text-white font-bold hover:bg-[#295f78] transition">
                                            Ir a evidencias de lote
                                        </a>

                                        <a href="{{ route('encargado.alertas.index') }}"
                                           class="rounded-xl bg-[#1d4d63] px-5 py-4 text-white font-bold hover:bg-[#295f78] transition">
                                            Ir a alertas
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
