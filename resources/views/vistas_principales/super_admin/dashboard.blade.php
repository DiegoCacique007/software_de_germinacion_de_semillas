<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'super_admin'));
        $incubadoraTiempoRealId = $incubadoraActualId ?? optional($incubadoras->first())->id ?? 106;
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 180, 156, 0.2); }
            50% { box-shadow: 0 0 40px rgba(59, 180, 156, 0.45); }
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

        @keyframes statusPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59,180,156,.5); }
            50% { box-shadow: 0 0 0 6px rgba(59,180,156,0); }
        }

        @keyframes dataFlash {
            0% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.08); filter: brightness(1.2); }
            100% { transform: scale(1); filter: brightness(1); }
        }

        .animate-epic { animation: epicEnter 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-float { animation: float 6s ease-in-out infinite; }

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

        .microseed-sensor-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .microseed-setpoint {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .microseed-range {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 9999px;
            background: #e5e7eb;
            outline: none;
            cursor: pointer;
        }

        .microseed-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #3bb49c;
            border: 3px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.2);
            cursor: pointer;
        }

        .microseed-range.range-warning::-webkit-slider-thumb { background: #eab308; }

        .toggle-switch {
            position: relative;
            width: 52px;
            height: 28px;
            background: #d1d5db;
            border-radius: 9999px;
            cursor: pointer;
            transition: background .3s;
            flex-shrink: 0;
        }

        .toggle-switch.active { background: #3bb49c; }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,.2);
            transition: transform .3s;
        }

        .toggle-switch.active::after { transform: translateX(24px); }

        .toggle-switch-lg {
            width: 60px;
            height: 32px;
        }

        .toggle-switch-lg::after {
            width: 26px;
            height: 26px;
        }

        .toggle-switch-lg.active::after { transform: translateX(28px); }

        .btn-adjust {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid;
            background: transparent;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-adjust:hover { transform: scale(1.1); }

        .status-pulse { animation: statusPulse 2s infinite; }

        .dato-real-actualizado { animation: dataFlash .45s ease-in-out; }
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

                    this.time = new Intl.DateTimeFormat('es-MX', {
                        timeZone: 'America/Mexico_City',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    }).format(d);

                    const parts = new Intl.DateTimeFormat('es-MX', {
                        timeZone: 'America/Mexico_City',
                        hour: '2-digit',
                        hour12: false
                    }).formatToParts(d);

                    let h = Number(parts.find(part => part.type === 'hour')?.value || 0);
                    if (h === 24) h = 0;

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
                                        Control global del sistema
                                    </div>

                                    <h3 class="text-3xl md:text-5xl font-black mb-2 tracking-tight text-gradient">
                                        <span x-text="greeting">Bienvenido</span>,<br>
                                        {{ $nombreCorto }}
                                    </h3>

                                    <p class="text-gray-600 text-base md:text-lg max-w-2xl">
                                        Desde aquí supervisas usuarios, sensores, lecturas, alertas, controles y el flujo completo de incubación.
                                    </p>
                                </div>

                                <div class="bg-white/40 backdrop-blur-md border border-white/60 p-4 rounded-2xl shadow-xl flex items-center gap-4 hover-glow transition-all">
                                    <div class="p-3 bg-gradient-to-br from-[#1c607a] to-[#3bb49c] rounded-xl text-white shadow-inner">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"></path>
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

                            @if (session('success'))
                                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 font-semibold">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                                    <p class="font-bold text-red-700 mb-2">Corrige los siguientes errores:</p>
                                    <ul class="list-disc pl-5 text-red-600 text-sm space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Usuarios</p>
                                    <h3 id="metricUsuariosTotal" class="text-3xl font-black text-[#144255]">{{ $usuariosTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Cuentas registradas</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Incubadoras</p>
                                    <h3 id="metricIncubadorasTotal" class="text-3xl font-black text-[#144255]">{{ $incubadorasTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Dispositivos gestionados</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Lecturas hoy</p>
                                    <h3 id="metricLecturasHoy" class="text-3xl font-black text-[#144255]">{{ $lecturasHoy }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Datos recibidos de sensores</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alertas activas</p>
                                    <h3 id="metricAlertasActivas" class="text-3xl font-black text-[#144255]">{{ $alertasActivas }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Pendientes o en revisión</p>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-2">Monitoreo Ambiental en Tiempo Real</h3>
                                <p class="text-sm text-gray-600 mb-6">Visualización continua del microclima registrada por el sensor DHT22 conectado al ESP32.</p>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-black text-[#eab308] text-md uppercase tracking-wide">Temperatura (°C)</h4>
                                            <span class="badge-soft bg-green-100 text-green-700 animate-pulse">
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span> En vivo
                                            </span>
                                        </div>

                                        <div class="relative h-64 w-full">
                                            <canvas id="temperaturaChart"></canvas>
                                        </div>
                                    </div>

                                    <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-black text-[#3bb49c] text-md uppercase tracking-wide">Humedad Relativa (%)</h4>
                                            <span class="badge-soft bg-green-100 text-green-700 animate-pulse">
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span> En vivo
                                            </span>
                                        </div>

                                        <div class="relative h-64 w-full">
                                            <canvas id="humedadChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Panel Microseed Control</h3>
                                        <p class="text-sm text-gray-600">Sensores en tiempo real, configuración de microclima y control manual de actuadores.</p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#3bb49c] status-pulse"></span>
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">En línea</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-5">
                                    <div class="space-y-4">
                                        <p class="text-xs font-black text-[#3bb49c] uppercase tracking-[0.15em] mb-1">🔴 Monitoreo en Tiempo Real</p>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-bold text-[#144255] text-sm">Sensor DHT22</h4>
                                                <span class="badge-soft bg-blue-100 text-blue-700">Aire</span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="bg-amber-50 rounded-xl p-3 text-center">
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Temperatura</p>
                                                    <p class="microseed-sensor-value text-amber-500 mt-1" id="dht22Temp">--</p>
                                                    <p class="text-xs text-gray-500 font-semibold">°C</p>
                                                </div>

                                                <div class="bg-sky-50 rounded-xl p-3 text-center">
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Humedad</p>
                                                    <p class="microseed-sensor-value text-sky-500 mt-1" id="dht22Hum">--</p>
                                                    <p class="text-xs text-gray-500 font-semibold">% HR</p>
                                                </div>
                                            </div>

                                            <p class="text-[10px] text-gray-400 text-right mt-2">
                                                Últ: <span id="dht22Time">--:--:--</span>
                                            </p>
                                        </div>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-bold text-[#144255] text-sm">Sensor DS18B20</h4>
                                                <span class="badge-soft bg-emerald-100 text-emerald-700">Sustrato</span>
                                            </div>

                                            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Temperatura</p>
                                                <p class="microseed-sensor-value text-emerald-600 mt-1" id="ds18Temp">--</p>
                                                <p class="text-xs text-gray-500 font-semibold">°C</p>
                                            </div>

                                            <p class="text-[10px] text-gray-400 text-right mt-2">
                                                Últ: <span id="ds18Time">--:--:--</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <p class="text-xs font-black text-[#eab308] uppercase tracking-[0.15em] mb-1">⚙️ Configuración de Microclima</p>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-1">
                                                <h4 class="font-bold text-[#144255] text-sm">Humedad Deseada</h4>
                                                <span class="badge-soft bg-sky-100 text-sky-700">Setpoint</span>
                                            </div>

                                            <div class="text-center my-3">
                                                <span class="microseed-setpoint text-sky-500" id="humSetpointDisplay">70</span>
                                                <span class="text-sm text-gray-500 font-bold">% HR</span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <button type="button" class="btn-adjust border-sky-400 text-sky-500 hover:bg-sky-50" onclick="ajustarSlider('humSetpoint', -1)">−</button>
                                                <input type="range" class="microseed-range flex-1" id="humSetpoint" min="40" max="100" step="1" value="70">
                                                <button type="button" class="btn-adjust border-sky-400 text-sky-500 hover:bg-sky-50" onclick="ajustarSlider('humSetpoint', 1)">+</button>
                                            </div>

                                            <div class="flex justify-between mt-1">
                                                <span class="text-[10px] text-gray-400">40%</span>
                                                <span class="text-[10px] text-gray-400">100%</span>
                                            </div>
                                        </div>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-1">
                                                <h4 class="font-bold text-[#144255] text-sm">Temperatura Deseada</h4>
                                                <span class="badge-soft bg-amber-100 text-amber-700">Setpoint</span>
                                            </div>

                                            <div class="text-center my-3">
                                                <span class="microseed-setpoint text-amber-500" id="tempSetpointDisplay">25</span>
                                                <span class="text-sm text-gray-500 font-bold">°C</span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <button type="button" class="btn-adjust border-amber-400 text-amber-500 hover:bg-amber-50" onclick="ajustarSlider('tempSetpoint', -1)">−</button>
                                                <input type="range" class="microseed-range range-warning flex-1" id="tempSetpoint" min="15" max="40" step="1" value="25">
                                                <button type="button" class="btn-adjust border-amber-400 text-amber-500 hover:bg-amber-50" onclick="ajustarSlider('tempSetpoint', 1)">+</button>
                                            </div>

                                            <div class="flex justify-between mt-1">
                                                <span class="text-[10px] text-gray-400">15°C</span>
                                                <span class="text-[10px] text-gray-400">40°C</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <p class="text-xs font-black text-red-500 uppercase tracking-[0.15em] mb-1">🎛️ Control de Actuadores</p>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <h4 class="font-bold text-[#144255] text-sm">Modo de Operación</h4>
                                                    <p class="text-xs text-gray-500 mt-1" id="modoLabel">Modo Automático activo</p>
                                                </div>

                                                <div class="toggle-switch-lg toggle-switch" id="modoSwitch" onclick="toggleModo()"></div>
                                            </div>

                                            <div class="mt-2">
                                                <span class="badge-soft bg-emerald-100 text-emerald-700" id="modoBadge">⚙️ AUTOMÁTICO</span>
                                            </div>
                                        </div>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-2">
                                                <h4 class="font-bold text-[#144255] text-sm">🌫️ Generador de Niebla</h4>
                                                <span class="badge-soft bg-gray-100 text-gray-500" id="nieblaBadge">APAGADO</span>
                                            </div>

                                            <p class="text-xs text-gray-400 mb-3">Relé del módulo ultrasónico de niebla.</p>

                                            <div class="flex justify-between items-center bg-white/60 rounded-xl p-3 border border-gray-100">
                                                <span class="text-sm font-semibold text-gray-600" id="nieblaLabel">Apagado</span>
                                                <div class="toggle-switch-lg toggle-switch actuador-toggle" id="nieblaSwitch" onclick="toggleNiebla()" style="pointer-events: none; opacity: .5;"></div>
                                            </div>
                                        </div>

                                        <div class="bg-white/70 rounded-2xl p-5 border border-white hover-glow transition-all duration-300">
                                            <div class="flex justify-between items-center mb-2">
                                                <h4 class="font-bold text-[#144255] text-sm">💡 Tira LED Blanca</h4>
                                                <span class="badge-soft bg-gray-100 text-gray-500" id="ledBadge">APAGADO</span>
                                            </div>

                                            <p class="text-xs text-gray-400 mb-3">Relé de iluminación LED para fotoperiodo.</p>

                                            <div class="flex justify-between items-center bg-white/60 rounded-xl p-3 border border-gray-100">
                                                <span class="text-sm font-semibold text-gray-600" id="ledLabel">Apagado</span>
                                                <div class="toggle-switch-lg toggle-switch actuador-toggle" id="ledSwitch" onclick="toggleLed()" style="pointer-events: none; opacity: .5;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                                <div class="xl:col-span-2 bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="mb-5">
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Estado actual por incubadora</h3>
                                        <p class="text-sm text-gray-500">Resumen en tiempo real del microclima</p>
                                    </div>

                                    <div id="resumenIncubadorasLive" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                                        <span class="badge-soft bg-red-100 text-red-700">Alerta activa</span>
                                                    @else
                                                        <span class="badge-soft bg-emerald-100 text-emerald-700">Estable</span>
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
                                                            <span class="font-semibold text-gray-700">
                                                                {{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i') }}
                                                            </span>
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
                                    <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-4">Estado del sistema</h3>

                                    <div class="space-y-4">
                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nivel de acceso</p>

                                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#144255] text-[#3bb49c] rounded-md text-sm font-bold uppercase tracking-wider shadow-inner">
                                                {{ $rolNombre }}
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lotes registrados</p>
                                            <p id="metricLotesTotal" class="text-2xl font-black text-[#144255]">{{ $lotesTotal }}</p>
                                        </div>

                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Frascos registrados</p>
                                            <p id="metricFrascosTotal" class="text-2xl font-black text-[#144255]">{{ $frascosTotal }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const INCUBADORA_ID = @json($incubadoraTiempoRealId ?? 106);
            const URL_TIEMPO_REAL = `{{ route('super_admin.dashboard.tiempo-real') }}?incubadora_id=${INCUBADORA_ID}`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const URL_ACTUADORES = {
                niebla: @json(route('super_admin.microclima.actuadores.update', 'niebla')),
                luz: @json(route('super_admin.microclima.actuadores.update', 'luz')),
            };

            const dhtTemp = document.getElementById('dht22Temp');
            const dhtHum = document.getElementById('dht22Hum');
            const dhtTime = document.getElementById('dht22Time');

            const ds18Temp = document.getElementById('ds18Temp');
            const ds18Time = document.getElementById('ds18Time');

            const modoSwitch = document.getElementById('modoSwitch');
            const modoLabel = document.getElementById('modoLabel');
            const modoBadge = document.getElementById('modoBadge');

            const nieblaSwitch = document.getElementById('nieblaSwitch');
            const nieblaLabel = document.getElementById('nieblaLabel');
            const nieblaBadge = document.getElementById('nieblaBadge');

            const ledSwitch = document.getElementById('ledSwitch');
            const ledLabel = document.getElementById('ledLabel');
            const ledBadge = document.getElementById('ledBadge');

            if (ds18Temp) ds18Temp.textContent = '--';
            if (ds18Time) ds18Time.textContent = '--:--:--';

            let temperaturaChart = null;
            let humedadChart = null;
            let peticionActiva = false;

            let modoManual = false;
            let nieblaActiva = false;
            let ledActivo = false;

            function normalizarArray(valor) {
                if (!valor) return [];
                if (Array.isArray(valor)) return valor;
                return Object.values(valor);
            }

            function marcarActualizacion(elemento) {
                if (!elemento) return;

                elemento.classList.remove('dato-real-actualizado');
                void elemento.offsetWidth;
                elemento.classList.add('dato-real-actualizado');
            }

            function setText(id, value) {
                const el = document.getElementById(id);

                if (el) {
                    el.textContent = value;
                }
            }

            function iniciarGraficas() {
                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js no cargó.');
                    return;
                }

                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 250
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#e5e7eb'
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            },
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#144255',
                            titleFont: {
                                size: 13
                            },
                            bodyFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    }
                };

                const canvasTemp = document.getElementById('temperaturaChart');

                if (canvasTemp) {
                    const ctxTemp = canvasTemp.getContext('2d');
                    const gradientTemp = ctxTemp.createLinearGradient(0, 0, 0, 400);

                    gradientTemp.addColorStop(0, 'rgba(234, 179, 8, 0.4)');
                    gradientTemp.addColorStop(1, 'rgba(234, 179, 8, 0.0)');

                    temperaturaChart = new Chart(ctxTemp, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Temperatura (°C)',
                                data: [],
                                borderColor: '#eab308',
                                backgroundColor: gradientTemp,
                                borderWidth: 3,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#eab308',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: commonOptions
                    });
                }

                const canvasHum = document.getElementById('humedadChart');

                if (canvasHum) {
                    const ctxHum = canvasHum.getContext('2d');
                    const gradientHum = ctxHum.createLinearGradient(0, 0, 0, 400);

                    gradientHum.addColorStop(0, 'rgba(59, 180, 156, 0.4)');
                    gradientHum.addColorStop(1, 'rgba(59, 180, 156, 0.0)');

                    humedadChart = new Chart(ctxHum, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Humedad (%)',
                                data: [],
                                borderColor: '#3bb49c',
                                backgroundColor: gradientHum,
                                borderWidth: 3,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#3bb49c',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: commonOptions
                    });
                }
            }

            function actualizarGraficas(grafica) {
                const labels = normalizarArray(grafica?.labels);
                const temperaturas = normalizarArray(grafica?.temperaturas).map(Number);
                const humedades = normalizarArray(grafica?.humedades).map(Number);

                if (temperaturaChart) {
                    temperaturaChart.data.labels = labels;
                    temperaturaChart.data.datasets[0].data = temperaturas;
                    temperaturaChart.update('none');
                }

                if (humedadChart) {
                    humedadChart.data.labels = labels;
                    humedadChart.data.datasets[0].data = humedades;
                    humedadChart.update('none');
                }
            }

            function renderResumenIncubadoras(items) {
                const contenedor = document.getElementById('resumenIncubadorasLive');

                if (!contenedor) return;

                if (!items || items.length === 0) {
                    contenedor.innerHTML = `
                <div class="col-span-full text-center text-gray-500 py-8">
                    No hay incubadoras registradas.
                </div>
            `;
                    return;
                }

                contenedor.innerHTML = items.map(item => {
                    const alertaHtml = item.alertas_abiertas > 0
                        ? `<span class="badge-soft bg-red-100 text-red-700">Alerta activa</span>`
                        : `<span class="badge-soft bg-emerald-100 text-emerald-700">Estable</span>`;

                    const lecturaHtml = item.temperatura !== null
                        ? `
                    <div class="flex justify-between">
                        <span class="text-gray-500">Temperatura</span>
                        <span class="font-semibold text-[#144255]">${item.temperatura} °C</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Humedad</span>
                        <span class="font-semibold text-[#144255]">${item.humedad} %</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Última lectura</span>
                        <span class="font-semibold text-gray-700">${item.fecha}</span>
                    </div>
                `
                        : `
                    <div class="text-sm text-gray-500 italic pt-2">
                        Sin lecturas registradas todavía.
                    </div>
                `;

                    return `
                <div class="rounded-2xl border border-gray-200 bg-white/80 p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h4 class="text-base font-black text-[#144255]">${item.nombre}</h4>
                            <p class="text-xs text-gray-500">${item.codigo ?? ''}</p>
                        </div>
                        ${alertaHtml}
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Estado incubadora</span>
                            <span class="font-semibold text-gray-700">${item.estado}</span>
                        </div>
                        ${lecturaHtml}
                    </div>
                </div>
            `;
                }).join('');
            }

            async function actualizarDashboardTiempoReal() {
                if (peticionActiva) return;

                peticionActiva = true;

                try {
                    const response = await fetch(`${URL_TIEMPO_REAL}&t=${Date.now()}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache',
                            'Pragma': 'no-cache'
                        },
                        cache: 'no-store'
                    });

                    const data = await response.json();

                    if (!data.ok) {
                        console.warn('No se pudo actualizar el dashboard.');
                        return;
                    }

                    setText('metricUsuariosTotal', data.metricas.usuarios_total);
                    setText('metricIncubadorasTotal', data.metricas.incubadoras_total);
                    setText('metricLecturasHoy', data.metricas.lecturas_hoy);
                    setText('metricAlertasActivas', data.metricas.alertas_activas);
                    setText('metricLotesTotal', data.metricas.lotes_total);
                    setText('metricFrascosTotal', data.metricas.frascos_total);

                    if (data.dht22 && data.dht22.temperatura !== null) {
                        const temperatura = parseFloat(data.dht22.temperatura);
                        const humedad = parseFloat(data.dht22.humedad);

                        if (dhtTemp && !Number.isNaN(temperatura)) {
                            dhtTemp.textContent = temperatura.toFixed(1);
                            marcarActualizacion(dhtTemp);
                        }

                        if (dhtHum && !Number.isNaN(humedad)) {
                            dhtHum.textContent = humedad.toFixed(1);
                            marcarActualizacion(dhtHum);
                        }

                        if (dhtTime) {
                            dhtTime.textContent = data.dht22.fecha_hora;
                        }
                    }

                    actualizarGraficas(data.grafica);
                    renderResumenIncubadoras(data.resumen_incubadoras);

                } catch (error) {
                    console.error('Error al actualizar dashboard en tiempo real:', error);
                } finally {
                    peticionActiva = false;
                }
            }

            function cambiarEstadoVisualActuador(actuador, activo) {
                let switchElement = null;
                let labelElement = null;
                let badgeElement = null;

                if (actuador === 'niebla') {
                    switchElement = nieblaSwitch;
                    labelElement = nieblaLabel;
                    badgeElement = nieblaBadge;
                    nieblaActiva = activo;
                }

                if (actuador === 'luz') {
                    switchElement = ledSwitch;
                    labelElement = ledLabel;
                    badgeElement = ledBadge;
                    ledActivo = activo;
                }

                if (switchElement) {
                    switchElement.classList.toggle('active', activo);
                }

                if (labelElement) {
                    labelElement.textContent = activo ? 'Encendido' : 'Apagado';
                }

                if (badgeElement) {
                    if (activo) {
                        badgeElement.textContent = 'ENCENDIDO';
                        badgeElement.className = actuador === 'luz'
                            ? 'badge-soft bg-amber-100 text-amber-700'
                            : 'badge-soft bg-emerald-100 text-emerald-700';
                    } else {
                        badgeElement.textContent = 'APAGADO';
                        badgeElement.className = 'badge-soft bg-gray-100 text-gray-500';
                    }
                }
            }

            function habilitarActuadores(habilitar) {
                [nieblaSwitch, ledSwitch].forEach(function (sw) {
                    if (!sw) return;

                    sw.style.pointerEvents = habilitar ? 'auto' : 'none';
                    sw.style.opacity = habilitar ? '1' : '.5';
                });
            }

            function actualizarModoVisual() {
                if (modoSwitch) {
                    modoSwitch.classList.toggle('active', modoManual);
                }

                if (modoLabel) {
                    modoLabel.textContent = modoManual ? 'Modo Manual activo' : 'Modo Automático activo';
                }

                if (modoBadge) {
                    if (modoManual) {
                        modoBadge.textContent = '✋ MANUAL';
                        modoBadge.className = 'badge-soft bg-amber-100 text-amber-700';
                    } else {
                        modoBadge.textContent = '⚙️ AUTOMÁTICO';
                        modoBadge.className = 'badge-soft bg-emerald-100 text-emerald-700';
                    }
                }

                habilitarActuadores(modoManual);
            }

            async function enviarOrdenActuador(actuador, accion) {
                if (!URL_ACTUADORES[actuador]) {
                    alert('Actuador no válido.');
                    return false;
                }

                try {
                    const response = await fetch(URL_ACTUADORES[actuador], {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            accion: accion
                        })
                    });

                    const data = await response.json();

                    if (!response.ok || !data.ok) {
                        console.error('Error al enviar orden:', data);
                        alert('No se pudo enviar la orden al actuador.');
                        return false;
                    }

                    console.log('Orden enviada:', data);
                    return true;

                } catch (error) {
                    console.error('Error de conexión al enviar orden:', error);
                    alert('Error de conexión al enviar la orden.');
                    return false;
                }
            }

            window.toggleModo = async function () {
                modoManual = !modoManual;
                actualizarModoVisual();

                if (!modoManual) {
                    cambiarEstadoVisualActuador('niebla', false);
                    cambiarEstadoVisualActuador('luz', false);

                    await enviarOrdenActuador('niebla', 'apagar');
                    await enviarOrdenActuador('luz', 'apagar');
                }
            };

            window.toggleNiebla = async function () {
                if (!modoManual) {
                    alert('Activa primero el modo manual para controlar la niebla.');
                    return;
                }

                const nuevoEstado = !nieblaActiva;
                const accion = nuevoEstado ? 'encender' : 'apagar';

                const ok = await enviarOrdenActuador('niebla', accion);

                if (ok) {
                    cambiarEstadoVisualActuador('niebla', nuevoEstado);
                }
            };

            window.toggleLed = async function () {
                if (!modoManual) {
                    alert('Activa primero el modo manual para controlar la luz.');
                    return;
                }

                const nuevoEstado = !ledActivo;
                const accion = nuevoEstado ? 'encender' : 'apagar';

                const ok = await enviarOrdenActuador('luz', accion);

                if (ok) {
                    cambiarEstadoVisualActuador('luz', nuevoEstado);
                }
            };

            const humSlider = document.getElementById('humSetpoint');
            const humDisplay = document.getElementById('humSetpointDisplay');
            const tempSlider = document.getElementById('tempSetpoint');
            const tempDisplay = document.getElementById('tempSetpointDisplay');

            if (humSlider && humDisplay) {
                humSlider.addEventListener('input', function () {
                    humDisplay.textContent = this.value;
                });
            }

            if (tempSlider && tempDisplay) {
                tempSlider.addEventListener('input', function () {
                    tempDisplay.textContent = this.value;
                });
            }

            window.ajustarSlider = function (sliderId, delta) {
                const slider = document.getElementById(sliderId);

                if (!slider) return;

                let value = parseInt(slider.value) + delta;

                value = Math.max(parseInt(slider.min), Math.min(parseInt(slider.max), value));

                slider.value = value;
                slider.dispatchEvent(new Event('input'));
            };

            iniciarGraficas();
            actualizarDashboardTiempoReal();
            actualizarModoVisual();
            cambiarEstadoVisualActuador('niebla', false);
            cambiarEstadoVisualActuador('luz', false);

            setInterval(actualizarDashboardTiempoReal, 2000);
        });
    </script>
</x-app-layout>
