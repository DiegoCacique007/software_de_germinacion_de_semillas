<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'super_admin'));
        $incubadoraTiempoRealId = $incubadoraActualId ?? optional($incubadoras->first())->id ?? 106;
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }

        .bg-animated-mesh {
            background:
                radial-gradient(
                    circle at 90% 10%,
                    rgba(59, 180, 156, 0.14),
                    transparent 32%
                ),
                linear-gradient(
                    180deg,
                    #f8fafc 0%,
                    #eef7f5 55%,
                    #e4f4ef 100%
                );
            background-size: cover;
            animation: none !important;
        }

        .microseed-dashboard-top-accent {
            height: 30px;
            width: 100%;
            background:
                radial-gradient(
                    circle at 72% -80%,
                    rgba(59, 180, 156, 0.55),
                    transparent 36%
                ),
                linear-gradient(
                    90deg,
                    #0f5568 0%,
                    #1f7b86 52%,
                    #39b39f 100%
                );
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.16);
        }

        .animate-epic,
        .animate-float,
        .status-pulse,
        .dato-real-actualizado,
        .animate-pulse {
            animation: none !important;
        }

        .animate-float {
            display: none !important;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 18px 42px -28px rgba(20, 66, 85, 0.42);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .text-gradient {
            color: #144255;
            background: linear-gradient(135deg, #144255 0%, #3bb49c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-glow:hover {
            animation: none !important;
            transform: none !important;
            box-shadow: inherit !important;
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

        .toggle-switch {
            position: relative;
            width: 52px;
            height: 28px;
            background: #d1d5db;
            border-radius: 9999px;
            cursor: pointer;
            flex-shrink: 0;
            transition: none !important;
        }

        .toggle-switch.active {
            background: #3bb49c;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .18);
            transition: none !important;
        }

        .toggle-switch.active::after {
            transform: translateX(24px);
        }

        .toggle-switch-lg {
            width: 60px;
            height: 32px;
        }

        .toggle-switch-lg::after {
            width: 26px;
            height: 26px;
        }

        .toggle-switch-lg.active::after {
            transform: translateX(28px);
        }

        .kpi-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, .9);
            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 16px 34px -24px rgba(20, 66, 85, .38);
            transition: none !important;
        }

        .kpi-card:hover {
            transform: none !important;
            box-shadow: 0 16px 34px -24px rgba(20, 66, 85, .38);
        }

        .kpi-card::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: -2rem;
            width: 7rem;
            height: 7rem;
            border-radius: 9999px;
            transition: none !important;
        }

        .kpi-card:hover::after {
            transform: none !important;
        }

        .kpi-blue::after {
            background: rgba(28, 96, 122, .11);
        }

        .kpi-green::after {
            background: rgba(59, 180, 156, .13);
        }

        .kpi-amber::after {
            background: rgba(251, 191, 36, .16);
        }

        .kpi-red::after {
            background: rgba(239, 68, 68, .13);
        }

        .kpi-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }

        .control-hub {
            background: rgba(255, 255, 255, .9);
            border: 1px solid rgba(255, 255, 255, .9);
            box-shadow: 0 18px 42px -24px rgba(20, 66, 85, .28);
        }

        .control-header {
            background: linear-gradient(135deg, #144255 0%, #1c607a 45%, #3bb49c 100%);
            position: relative;
            overflow: hidden;
        }

        .control-header::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .12);
            top: -90px;
            right: -70px;
            pointer-events: none;
        }

        .control-card {
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(255, 255, 255, .9);
            box-shadow: 0 16px 32px -22px rgba(20, 66, 85, .34);
            transition: none !important;
        }

        .control-card:hover {
            transform: none !important;
            box-shadow: 0 16px 32px -22px rgba(20, 66, 85, .34);
        }

        .sensor-box {
            border-radius: 1.25rem;
            padding: 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .sensor-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, .48), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .sensor-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .actuator-row {
            background: rgba(255, 255, 255, .86);
            border: 1px solid rgba(229, 231, 235, .95);
            border-radius: 1.25rem;
            padding: 1rem;
            transition: none !important;
        }

        .actuator-row:hover {
            border-color: rgba(229, 231, 235, .95);
            background: rgba(255, 255, 255, .86);
        }

        .actuator-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .control-help {
            font-size: .72rem;
            line-height: 1.15rem;
            color: #6b7280;
        }

        .locked-control {
            filter: grayscale(.2);
            opacity: .62;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .status-dot {
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
        }

        .backdrop-blur-sm,
        .backdrop-blur-md,
        .backdrop-blur-xl {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .transition-all,
        .duration-300 {
            transition: none !important;
        }
    </style>

    <div
        class="w-full min-h-screen bg-animated-mesh relative overflow-hidden"
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

                if (h === 24) {
                    h = 0;
                }

                this.greeting = h < 12
                    ? 'Buenos días'
                    : (h < 19 ? 'Buenas tardes' : 'Buenas noches');
            }
        }"
    >
        <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-[#3bb49c] rounded-full mix-blend-multiply blur-[100px] opacity-25 animate-float pointer-events-none"></div>

        <div
            class="absolute bottom-[-10%] right-[10%] w-[30rem] h-[30rem] bg-[#1c607a] rounded-full mix-blend-multiply blur-[120px] opacity-20 animate-float pointer-events-none"
            style="animation-delay: 2s;"
        ></div>

        <div class="py-12 px-6 relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="glass-card overflow-hidden shadow-sm sm:rounded-[2rem] relative">

                    <div class="microseed-dashboard-top-accent"></div>

                    <div class="relative px-8 py-10 border-b border-white/50">
                        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#3bb49c]/10 border border-[#3bb49c]/20 text-[#1c607a] text-xs font-black uppercase tracking-[0.2em] mb-4">
                                    <span class="w-2 h-2 rounded-full bg-[#3bb49c]"></span>
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

                            <div class="bg-white/40 border border-white/60 p-4 rounded-2xl shadow-xl flex items-center gap-4 hover-glow transition-all">
                                <div class="p-3 bg-gradient-to-br from-[#1c607a] to-[#3bb49c] rounded-xl text-white shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"
                                        />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        Tiempo local
                                    </p>

                                    <p class="text-2xl font-black text-[#144255] font-mono tracking-tight" x-text="time">
                                        --:--:--
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-white/30">

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
                                <p class="font-bold text-red-700 mb-2">
                                    Corrige los siguientes errores:
                                </p>

                                <ul class="list-disc pl-5 text-red-600 text-sm space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                            <div class="kpi-card kpi-blue">
                                <div class="relative z-10 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Usuarios
                                        </p>

                                        <h3 id="metricUsuariosTotal" class="text-4xl font-black text-[#144255] leading-none">
                                            {{ $usuariosTotal }}
                                        </h3>

                                        <p class="text-sm text-gray-600 mt-3">
                                            Cuentas registradas
                                        </p>
                                    </div>

                                    <div class="kpi-icon bg-gradient-to-br from-[#144255] to-[#1c607a] shadow-lg shadow-[#144255]/20">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-4.13a4 4 0 11-8 0 4 4 0 018 0zm-8 0a4 4 0 11-8 0 4 4 0 018 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative z-10 mt-5 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#3bb49c]"></span>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                        Gestión activa
                                    </span>
                                </div>
                            </div>

                            <div class="kpi-card kpi-green">
                                <div class="relative z-10 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Incubadoras
                                        </p>

                                        <h3 id="metricIncubadorasTotal" class="text-4xl font-black text-[#144255] leading-none">
                                            {{ $incubadorasTotal }}
                                        </h3>

                                        <p class="text-sm text-gray-600 mt-3">
                                            Dispositivos gestionados
                                        </p>
                                    </div>

                                    <div class="kpi-icon bg-gradient-to-br from-[#1c607a] to-[#3bb49c] shadow-lg shadow-[#3bb49c]/20">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 7h18M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M9 13h6"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative z-10 mt-5 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#3bb49c]"></span>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                        En operación
                                    </span>
                                </div>
                            </div>

                            <div class="kpi-card kpi-amber">
                                <div class="relative z-10 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Lecturas hoy
                                        </p>

                                        <h3 id="metricLecturasHoy" class="text-4xl font-black text-[#144255] leading-none">
                                            {{ $lecturasHoy }}
                                        </h3>

                                        <p class="text-sm text-gray-600 mt-3">
                                            Datos recibidos de sensores
                                        </p>
                                    </div>

                                    <div class="kpi-icon bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-500/20">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative z-10 mt-5 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                        Monitoreo diario
                                    </span>
                                </div>
                            </div>

                            <div class="kpi-card kpi-red">
                                <div class="relative z-10 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Alertas activas
                                        </p>

                                        <h3 id="metricAlertasActivas" class="text-4xl font-black text-[#144255] leading-none">
                                            {{ $alertasActivas }}
                                        </h3>

                                        <p class="text-sm text-gray-600 mt-3">
                                            Pendientes o en revisión
                                        </p>
                                    </div>

                                    <div class="kpi-icon bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-500/20">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div id="metricAlertasEstado" class="relative z-10 mt-5 flex items-center gap-2">
                                    @if($alertasActivas > 0)
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-xs font-bold text-red-500 uppercase tracking-wide">
                                            Requiere atención
                                        </span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                            Sin incidencias
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-2">
                                Monitoreo Ambiental en Tiempo Real
                            </h3>

                            <p class="text-sm text-gray-600 mb-6">
                                Visualización continua del microclima registrada por el sensor DHT22 conectado al ESP32.
                            </p>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-black text-[#eab308] text-md uppercase tracking-wide">
                                            Temperatura (°C)
                                        </h4>

                                        <span class="badge-soft bg-green-100 text-green-700">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                            En vivo
                                        </span>
                                    </div>

                                    <div class="relative h-64 w-full">
                                        <canvas id="temperaturaChart"></canvas>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-black text-[#3bb49c] text-md uppercase tracking-wide">
                                            Humedad Relativa (%)
                                        </h4>

                                        <span class="badge-soft bg-green-100 text-green-700">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                            En vivo
                                        </span>
                                    </div>

                                    <div class="relative h-64 w-full">
                                        <canvas id="humedadChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8 control-hub rounded-[2rem] overflow-hidden">
                            <div class="control-header px-6 py-6 text-white">
                                <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
                                    <div>
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-white text-xs font-black uppercase tracking-[0.18em] mb-3">
                                            <span class="w-2 h-2 rounded-full bg-[#3bb49c]"></span>
                                            Sistema operativo
                                        </div>

                                        <h3 class="text-2xl md:text-3xl font-black tracking-tight">
                                            Panel Microseed Control
                                        </h3>

                                        <p class="text-sm text-white/80 mt-1 max-w-2xl">
                                            Visualiza el microclima en tiempo real y manipula los actuadores principales de la incubadora.
                                        </p>
                                    </div>

                                    <div class="bg-white/15 border border-white/20 rounded-2xl px-5 py-4">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-white/65">
                                            Estado de conexión
                                        </p>

                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="w-3 h-3 rounded-full bg-[#3bb49c]"></span>
                                            <span class="text-lg font-black">
                                                En línea
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-white/40">
                                <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                                    <div class="xl:col-span-2 control-card rounded-3xl p-6">
                                        <div class="flex items-center justify-between gap-4 mb-5">
                                            <div class="flex items-center gap-3">
                                                <div class="sensor-icon bg-[#144255] text-white">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 3v10m0 0a4 4 0 11-4 4h8a4 4 0 01-4-4z"
                                                        />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <h4 class="font-black text-[#144255] text-lg">
                                                        Sensor DHT22
                                                    </h4>

                                                    <p class="text-sm text-gray-500">
                                                        Temperatura y humedad ambiental
                                                    </p>
                                                </div>
                                            </div>

                                            <span class="status-pill bg-emerald-100 text-emerald-700">
                                                <span class="status-dot bg-emerald-500"></span>
                                                En vivo
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="sensor-box bg-gradient-to-br from-amber-50 to-orange-100 border border-amber-100">
                                                <p class="relative text-[10px] font-black text-amber-700 uppercase tracking-widest">
                                                    Temperatura
                                                </p>

                                                <div class="relative flex items-end justify-center gap-1 mt-2">
                                                    <p class="text-5xl font-black text-amber-500 leading-none" id="dht22Temp">
                                                        --
                                                    </p>

                                                    <span class="text-lg font-black text-amber-500 mb-1">
                                                        °C
                                                    </span>
                                                </div>

                                                <p class="relative text-xs text-amber-700/70 font-semibold mt-2">
                                                    Lectura térmica actual
                                                </p>
                                            </div>

                                            <div class="sensor-box bg-gradient-to-br from-sky-50 to-cyan-100 border border-sky-100">
                                                <p class="relative text-[10px] font-black text-sky-700 uppercase tracking-widest">
                                                    Humedad
                                                </p>

                                                <div class="relative flex items-end justify-center gap-1 mt-2">
                                                    <p class="text-5xl font-black text-sky-500 leading-none" id="dht22Hum">
                                                        --
                                                    </p>

                                                    <span class="text-lg font-black text-sky-500 mb-1">
                                                        %
                                                    </span>
                                                </div>

                                                <p class="relative text-xs text-sky-700/70 font-semibold mt-2">
                                                    Humedad relativa
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-5 rounded-2xl bg-[#144255]/5 border border-[#144255]/10 px-4 py-3 flex items-center justify-between">
                                            <span class="text-xs font-bold text-gray-500">
                                                Última actualización
                                            </span>

                                            <span class="text-sm font-black text-[#144255] font-mono" id="dht22Time">
                                                --:--:--
                                            </span>
                                        </div>
                                    </div>

                                    <div class="xl:col-span-3 control-card rounded-3xl p-6">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                                            <div>
                                                <h4 class="font-black text-[#144255] text-lg">
                                                    Control de Actuadores
                                                </h4>

                                                <p class="text-sm text-gray-500">
                                                    Activa el modo manual para manipular niebla e iluminación.
                                                </p>
                                            </div>

                                            <span class="status-pill bg-emerald-100 text-emerald-700" id="modoBadge">
                                                ⚙️ AUTOMÁTICO
                                            </span>
                                        </div>

                                        <div class="rounded-3xl bg-gradient-to-br from-[#144255] to-[#1c607a] p-5 text-white mb-5 shadow-xl">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                                            />

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                            />
                                                        </svg>
                                                    </div>

                                                    <div>
                                                        <h5 class="text-lg font-black">
                                                            Modo de Operación
                                                        </h5>

                                                        <p class="text-sm text-white/75" id="modoLabel">
                                                            Modo Automático activo
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs font-black text-white/60 uppercase tracking-widest">
                                                        Auto
                                                    </span>

                                                    <div
                                                        class="toggle-switch-lg toggle-switch border border-white/20"
                                                        id="modoSwitch"
                                                        onclick="toggleModo()"
                                                    ></div>

                                                    <span class="text-xs font-black text-white/60 uppercase tracking-widest">
                                                        Manual
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <div class="actuator-row">
                                                <div class="flex items-start justify-between gap-4 mb-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="actuator-icon bg-emerald-100 text-emerald-600">
                                                            🌫️
                                                        </div>

                                                        <div>
                                                            <h5 class="font-black text-[#144255]">
                                                                Generador de Niebla
                                                            </h5>

                                                            <p class="control-help">
                                                                Relé del módulo ultrasónico de humedad.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <span class="badge-soft bg-gray-100 text-gray-500" id="nieblaBadge">
                                                        APAGADO
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                                    <div>
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                            Estado
                                                        </p>

                                                        <p class="text-sm font-black text-gray-700" id="nieblaLabel">
                                                            Apagado
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="toggle-switch-lg toggle-switch actuador-toggle locked-control"
                                                        id="nieblaSwitch"
                                                        onclick="toggleNiebla()"
                                                        style="pointer-events: none; opacity: .55;"
                                                    ></div>
                                                </div>
                                            </div>

                                            <div class="actuator-row">
                                                <div class="flex items-start justify-between gap-4 mb-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="actuator-icon bg-amber-100 text-amber-600">
                                                            💡
                                                        </div>

                                                        <div>
                                                            <h5 class="font-black text-[#144255]">
                                                                Tira LED Blanca
                                                            </h5>

                                                            <p class="control-help">
                                                                Iluminación para fotoperiodo del cultivo.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <span class="badge-soft bg-gray-100 text-gray-500" id="ledBadge">
                                                        APAGADO
                                                    </span>
                                                </div>

                                                <div class="flex items-center justify-between rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                                    <div>
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                            Estado
                                                        </p>

                                                        <p class="text-sm font-black text-gray-700" id="ledLabel">
                                                            Apagado
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="toggle-switch-lg toggle-switch actuador-toggle locked-control"
                                                        id="ledSwitch"
                                                        onclick="toggleLed()"
                                                        style="pointer-events: none; opacity: .55;"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 rounded-2xl bg-amber-50 border border-amber-100 px-4 py-3">
                                            <p class="text-xs text-amber-700 font-semibold">
                                                Nota: los actuadores permanecen bloqueados mientras el sistema esté en modo automático. Activa modo manual para encender o apagar niebla y luz.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                            <div class="xl:col-span-2 bg-white/70 rounded-2xl p-6 border border-white">
                                <div class="mb-5">
                                    <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">
                                        Estado actual por incubadora
                                    </h3>

                                    <p class="text-sm text-gray-500">
                                        Resumen en tiempo real del microclima
                                    </p>
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
                                                    <h4 class="text-base font-black text-[#144255]">
                                                        {{ $inc->nombre }}
                                                    </h4>

                                                    <p class="text-xs text-gray-500">
                                                        {{ $inc->codigo }}
                                                    </p>
                                                </div>

                                                @if($alertas > 0)
                                                    <span class="badge-soft bg-red-100 text-red-700">
                                                        Alerta activa
                                                    </span>
                                                @else
                                                    <span class="badge-soft bg-emerald-100 text-emerald-700">
                                                        Estable
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">
                                                        Estado incubadora
                                                    </span>

                                                    <span class="font-semibold text-gray-700">
                                                        {{ $inc->estado->nombre ?? 'Sin estado' }}
                                                    </span>
                                                </div>

                                                @if($lectura)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">
                                                            Temperatura
                                                        </span>

                                                        <span class="font-semibold text-[#144255]">
                                                            {{ $lectura->temperatura }} °C
                                                        </span>
                                                    </div>

                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">
                                                            Humedad
                                                        </span>

                                                        <span class="font-semibold text-[#144255]">
                                                            {{ $lectura->humedad }} %
                                                        </span>
                                                    </div>

                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">
                                                            Última lectura
                                                        </span>

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
                                <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-4">
                                    Estado del sistema
                                </h3>

                                <div class="space-y-4">
                                    <div class="rounded-xl bg-white border border-gray-200 p-4">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Nivel de acceso
                                        </p>

                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#144255] text-[#3bb49c] rounded-md text-sm font-bold uppercase tracking-wider shadow-inner">
                                            {{ $rolNombre }}
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-white border border-gray-200 p-4">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Lotes registrados
                                        </p>

                                        <p id="metricLotesTotal" class="text-2xl font-black text-[#144255]">
                                            {{ $lotesTotal }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-white border border-gray-200 p-4">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                            Frascos registrados
                                        </p>

                                        <p id="metricFrascosTotal" class="text-2xl font-black text-[#144255]">
                                            {{ $frascosTotal }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

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

            const modoSwitch = document.getElementById('modoSwitch');
            const modoLabel = document.getElementById('modoLabel');
            const modoBadge = document.getElementById('modoBadge');

            const nieblaSwitch = document.getElementById('nieblaSwitch');
            const nieblaLabel = document.getElementById('nieblaLabel');
            const nieblaBadge = document.getElementById('nieblaBadge');

            const ledSwitch = document.getElementById('ledSwitch');
            const ledLabel = document.getElementById('ledLabel');
            const ledBadge = document.getElementById('ledBadge');

            let temperaturaChart = null;
            let humedadChart = null;
            let peticionActiva = false;

            let modoManual = false;
            let nieblaActiva = false;
            let ledActivo = false;

            function normalizarArray(valor) {
                if (!valor) {
                    return [];
                }

                if (Array.isArray(valor)) {
                    return valor;
                }

                return Object.values(valor);
            }

            function marcarActualizacion(elemento) {
                return;
            }

            function setText(id, value) {
                const el = document.getElementById(id);

                if (el) {
                    el.textContent = value;
                }
            }

            function actualizarEstadoAlertas(activas) {
                const contenedor = document.getElementById('metricAlertasEstado');

                if (!contenedor) {
                    return;
                }

                const total = Number(activas || 0);

                if (total > 0) {
                    contenedor.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-xs font-bold text-red-500 uppercase tracking-wide">
                            Requiere atención
                        </span>
                    `;
                } else {
                    contenedor.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                            Sin incidencias
                        </span>
                    `;
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
                    animation: false,
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
                            datasets: [
                                {
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
                                }
                            ]
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
                            datasets: [
                                {
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
                                }
                            ]
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

                if (!contenedor) {
                    return;
                }

                if (!items || items.length === 0) {
                    contenedor.innerHTML = `
                        <div class="col-span-full text-center text-gray-500 py-8">
                            No hay incubadoras registradas.
                        </div>
                    `;
                    return;
                }

                contenedor.innerHTML = items.map(function (item) {
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
                if (peticionActiva) {
                    return;
                }

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

                    actualizarEstadoAlertas(data.metricas.alertas_activas);

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
                    if (!sw) {
                        return;
                    }

                    sw.style.pointerEvents = habilitar ? 'auto' : 'none';
                    sw.style.opacity = habilitar ? '1' : '.55';
                    sw.classList.toggle('locked-control', !habilitar);
                });
            }

            function actualizarModoVisual() {
                if (modoSwitch) {
                    modoSwitch.classList.toggle('active', modoManual);
                }

                if (modoLabel) {
                    modoLabel.textContent = modoManual
                        ? 'Modo Manual activo'
                        : 'Modo Automático activo';
                }

                if (modoBadge) {
                    if (modoManual) {
                        modoBadge.textContent = '✋ MANUAL';
                        modoBadge.className = 'status-pill bg-amber-100 text-amber-700';
                    } else {
                        modoBadge.textContent = '⚙️ AUTOMÁTICO';
                        modoBadge.className = 'status-pill bg-emerald-100 text-emerald-700';
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

            iniciarGraficas();
            actualizarDashboardTiempoReal();
            actualizarModoVisual();
            cambiarEstadoVisualActuador('niebla', false);
            cambiarEstadoVisualActuador('luz', false);

            setInterval(actualizarDashboardTiempoReal, 2000);
        });
    </script>
</x-app-layout>
