<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'administrador'));
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 180, 156, 0.18); }
            50% { box-shadow: 0 0 40px rgba(59, 180, 156, 0.35); }
        }

        @keyframes gradientPan {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes epicEnter {
            0% { opacity: 0; transform: scale(0.95) translateY(30px); filter: blur(8px); }
            100% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
        }

        .animate-epic {
            animation: epicEnter 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 50px -12px rgba(28, 96, 122, 0.15);
        }

        .text-gradient {
            background: linear-gradient(135deg, #144255 0%, #3bb49c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-glow:hover {
            animation: pulseGlow 2s infinite;
            transform: translateY(-4px) scale(1.01);
        }

        .bg-animated-mesh {
            background: linear-gradient(-45deg, #f0f6f6, #e0efec, #d1e8e4, #f0f6f6);
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

            <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-[#3bb49c] rounded-full mix-blend-multiply blur-[100px] opacity-25 animate-float pointer-events-none"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[30rem] h-[30rem] bg-[#1c607a] rounded-full mix-blend-multiply blur-[120px] opacity-20 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

            <div class="py-12 px-6 relative z-10">
                <div class="max-w-7xl mx-auto">
                    <div class="glass-card overflow-hidden shadow-sm sm:rounded-[2rem] border-t-4 border-[#3bb49c] animate-epic relative">

                        <div class="relative px-8 py-10 border-b border-white/50">
                            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                                <div>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3bb49c]/10 border border-[#3bb49c]/20 text-[#1c607a] text-xs font-black uppercase tracking-[0.2em] mb-4">
                                        <span class="w-2 h-2 rounded-full bg-[#3bb49c] animate-pulse"></span>
                                        Monitoreo de microclima
                                    </div>

                                    <h3 class="text-3xl md:text-5xl font-black mb-2 tracking-tight text-gradient">
                                        <span x-text="greeting">Bienvenido</span>,<br>
                                        {{ $nombreCorto }}
                                    </h3>

                                    <p class="text-gray-600 text-base md:text-lg max-w-2xl">
                                        Aquí puedes ver el comportamiento actual de temperatura, humedad, alertas y controles aplicados en las incubadoras.
                                    </p>
                                </div>

                                <div class="bg-white/40 backdrop-blur-md border border-white/60 p-4 rounded-2xl shadow-xl flex items-center gap-4 hover-glow transition-all">
                                    <div class="p-3 bg-gradient-to-br from-[#1c607a] to-[#3bb49c] rounded-xl text-white shadow-inner">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tiempo local</p>
                                        <p class="text-2xl font-black text-[#144255] font-mono tracking-tight" x-text="time">--:--:--</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-white/30 backdrop-blur-sm">

                            {{-- KPIs --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Incubadoras</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $incubadoras->count() }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Incubadoras monitoreadas</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Lecturas de hoy</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $lecturasHoy }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Registros recibidos hoy</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alertas activas</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $alertasActivas }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Pendientes o en revisión</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Controles hoy</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $controlesHoy }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Acciones de control registradas</p>
                                </div>
                            </div>

                            {{-- estado actual --}}
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                                <div class="xl:col-span-2 bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-5">
                                        <div>
                                            <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Estado actual por incubadora</h3>
                                            <p class="text-sm text-gray-500">Última lectura registrada por incubadora</p>
                                        </div>
                                        <a href="{{ route('administrador.lecturas-microclima.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#144255] text-white text-sm font-semibold hover:bg-[#1c607a] transition">
                                            Ver lecturas
                                        </a>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @forelse($resumenIncubadoras as $item)
                                            @php
                                                $inc = $item['incubadora'];
                                                $lectura = $item['ultima_lectura'];
                                                $alertas = $item['alertas_abiertas'];
                                            @endphp

                                            <div class="rounded-2xl border border-gray-200 bg-white/80 p-5">
                                                <div class="flex items-start justify-between gap-3 mb-3">
                                                    <div>
                                                        <h4 class="text-base font-black text-[#144255]">{{ $inc->nombre }}</h4>
                                                        <p class="text-xs text-gray-500">{{ $inc->codigo }}</p>
                                                    </div>

                                                    @if($alertas > 0)
                                                        <span class="badge-soft bg-red-100 text-red-700">
                                                            🚨 {{ $alertas }} alerta(s)
                                                        </span>
                                                    @else
                                                        <span class="badge-soft bg-emerald-100 text-emerald-700">
                                                            ✅ Estable
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Estado incubadora</span>
                                                        <span class="font-semibold text-gray-700">{{ $inc->estado->nombre ?? 'Sin estado' }}</span>
                                                    </div>

                                                    @if($lectura)
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Temperatura</span>
                                                            <span class="font-semibold text-[#144255]">{{ $lectura->temperatura }} °C</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Humedad</span>
                                                            <span class="font-semibold text-[#144255]">{{ $lectura->humedad }} %</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Última lectura</span>
                                                            <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i') }}</span>
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-500 italic pt-2">
                                                            Sin lecturas registradas todavía.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-full text-center text-gray-500 py-8">
                                                No hay incubadoras registradas.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-4">Resumen actual</h3>

                                    <div class="space-y-4">
                                        <div class="rounded-xl bg-[#144255] text-white p-4">
                                            <p class="text-xs uppercase tracking-widest text-gray-300 mb-1">Temperatura promedio actual</p>
                                            <p class="text-3xl font-black">
                                                {{ $temperaturaPromedioActual !== null ? number_format($temperaturaPromedioActual, 2) . ' °C' : '—' }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-[#1c607a] text-white p-4">
                                            <p class="text-xs uppercase tracking-widest text-gray-300 mb-1">Humedad promedio actual</p>
                                            <p class="text-3xl font-black">
                                                {{ $humedadPromedioActual !== null ? number_format($humedadPromedioActual, 2) . ' %' : '—' }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-xs uppercase tracking-widest text-gray-400 mb-1">Última lectura global</p>
                                            @if($ultimaLecturaGlobal)
                                                <p class="font-black text-[#144255]">
                                                    {{ $ultimaLecturaGlobal->incubadora->nombre ?? 'Incubadora' }}
                                                </p>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $ultimaLecturaGlobal->temperatura }} °C / {{ $ultimaLecturaGlobal->humedad }} %
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($ultimaLecturaGlobal->fecha_hora)->format('d/m/Y H:i:s') }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Sin lecturas todavía.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- tablas --}}
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Últimas lecturas</h3>
                                            <p class="text-sm text-gray-500">Registros más recientes del microclima</p>
                                        </div>
                                        <a href="{{ route('administrador.lecturas-microclima.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#144255] text-white text-sm font-semibold hover:bg-[#1c607a] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Incubadora</th>
                                                <th class="py-2 pr-4">Temp.</th>
                                                <th class="py-2 pr-4">Hum.</th>
                                                <th class="py-2">Fecha</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($ultimasLecturas as $lectura)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#144255]">{{ $lectura->incubadora->nombre ?? 'Sin incubadora' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $lectura->temperatura }} °C</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $lectura->humedad }} %</td>
                                                    <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-6 text-center text-gray-500">No hay lecturas registradas.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Últimos controles</h3>
                                            <p class="text-sm text-gray-500">Acciones recientes aplicadas a incubadoras</p>
                                        </div>
                                        <a href="{{ route('administrador.controles-incubadora.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#144255] text-white text-sm font-semibold hover:bg-[#1c607a] transition">
                                            Ver módulo
                                        </a>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Incubadora</th>
                                                <th class="py-2 pr-4">Tipo</th>
                                                <th class="py-2 pr-4">Modo</th>
                                                <th class="py-2">Valor</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($ultimosControles as $control)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#144255]">{{ $control->incubadora->nombre ?? 'Sin incubadora' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $control->tipo->nombre ?? 'Sin tipo' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $control->modo->nombre ?? 'Sin modo' }}</td>
                                                    <td class="py-3 text-gray-500">{{ $control->valor_aplicado ?? '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-6 text-center text-gray-500">No hay controles registrados.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- alertas recientes --}}
                            <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-4">Alertas recientes</h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                        <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                            <th class="py-2 pr-4">Incubadora</th>
                                            <th class="py-2 pr-4">Tipo</th>
                                            <th class="py-2 pr-4">Nivel</th>
                                            <th class="py-2 pr-4">Estado</th>
                                            <th class="py-2 pr-4">Mensaje</th>
                                            <th class="py-2">Fecha</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($ultimasAlertas as $alerta)
                                            <tr class="border-b border-gray-100">
                                                <td class="py-3 pr-4 font-semibold text-[#144255]">{{ $alerta->incubadora->nombre ?? 'Sin incubadora' }}</td>
                                                <td class="py-3 pr-4 text-gray-700">{{ $alerta->tipo->nombre ?? 'Sin tipo' }}</td>
                                                <td class="py-3 pr-4 text-gray-700">{{ $alerta->nivel->nombre ?? 'Sin nivel' }}</td>
                                                <td class="py-3 pr-4 text-gray-700">{{ $alerta->estado->nombre ?? 'Sin estado' }}</td>
                                                <td class="py-3 pr-4 text-gray-500">{{ \Illuminate\Support\Str::limit($alerta->mensaje, 70) }}</td>
                                                <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($alerta->fecha_hora)->format('d/m H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-gray-500">No hay alertas registradas.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
