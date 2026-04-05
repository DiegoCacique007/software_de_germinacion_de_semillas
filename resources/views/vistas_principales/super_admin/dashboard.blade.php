<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'super_admin'));
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

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Usuarios</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $usuariosTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Cuentas registradas</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Incubadoras</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $incubadorasTotal }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Dispositivos gestionados</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Lecturas hoy</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $lecturasHoy }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Datos recibidos de sensores</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alertas activas</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $alertasActivas }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Pendientes o en revisión</p>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Órdenes pendientes</p>
                                    <h3 class="text-3xl font-black text-[#144255]">{{ $ordenesPendientes }}</h3>
                                    <p class="text-sm text-gray-600 mt-2">Esperando dispositivo</p>
                                </div>
                            </div>

                            <div class="bg-white/70 rounded-2xl p-6 border border-white mb-8">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                                    <div>
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Control manual de incubadora</h3>
                                        <p class="text-sm text-gray-500">Envía órdenes manuales al dispositivo y deja bitácora en el sistema.</p>
                                    </div>

                                    <div class="inline-flex items-center gap-2 rounded-full bg-[#144255] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white">
                                        Controles hoy: {{ $controlesHoy }}
                                    </div>
                                </div>

                                <form action="{{ route('super_admin.control-manual.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                                    @csrf

                                    <div>
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Incubadora</label>
                                        <select name="incubadora_id" class="w-full rounded-xl border-gray-300 focus:border-[#3bb49c] focus:ring-[#3bb49c]">
                                            @foreach($incubadoras as $incubadora)
                                                <option value="{{ $incubadora->id }}">{{ $incubadora->nombre }} ({{ $incubadora->codigo }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Tipo de control</label>
                                        <select name="tipo_control_incubadora_id" class="w-full rounded-xl border-gray-300 focus:border-[#3bb49c] focus:ring-[#3bb49c]">
                                            @foreach($tiposControl as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Modo</label>
                                        <select name="modo_control_incubadora_id" class="w-full rounded-xl border-gray-300 focus:border-[#3bb49c] focus:ring-[#3bb49c]">
                                            @foreach($modosControl as $modo)
                                                <option value="{{ $modo->id }}">{{ $modo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Acción</label>
                                        <select name="accion" class="w-full rounded-xl border-gray-300 focus:border-[#3bb49c] focus:ring-[#3bb49c]">
                                            <option value="encender">Encender</option>
                                            <option value="apagar">Apagar</option>
                                            <option value="ajustar">Ajustar</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">Valor aplicado</label>
                                        <input type="number" step="0.01" name="valor_aplicado" class="w-full rounded-xl border-gray-300 focus:border-[#3bb49c] focus:ring-[#3bb49c]" placeholder="Opcional">
                                    </div>

                                    <div class="md:col-span-2 xl:col-span-5">
                                        <button type="submit" class="rounded-xl bg-[#144255] px-6 py-3 text-white font-bold hover:bg-[#1c607a] transition">
                                            Enviar orden al dispositivo
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="mb-8">
                                <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide mb-4">Accesos rápidos completos</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                                    <a href="{{ route('super_admin.usuarios.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Global</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Usuarios</h3>
                                        <p class="text-sm text-gray-600">Administrar accesos y roles.</p>
                                    </a>

                                    <a href="{{ route('super_admin.incubadoras.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Infraestructura</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Incubadoras</h3>
                                        <p class="text-sm text-gray-600">Gestionar equipos y estados.</p>
                                    </a>

                                    <a href="{{ route('super_admin.especies.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Catálogo</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Especies</h3>
                                        <p class="text-sm text-gray-600">Administrar especies forestales.</p>
                                    </a>

                                    <a href="{{ route('super_admin.condiciones-optimas-especie.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Catálogo</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Condiciones</h3>
                                        <p class="text-sm text-gray-600">Configurar rangos ideales.</p>
                                    </a>

                                    <a href="{{ route('super_admin.posiciones-incubadora.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Estructura</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Posiciones</h3>
                                        <p class="text-sm text-gray-600">Distribuir lotes en incubadoras.</p>
                                    </a>

                                    <a href="{{ route('super_admin.lotes.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Producción</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Lotes</h3>
                                        <p class="text-sm text-gray-600">Gestionar siembra y seguimiento.</p>
                                    </a>

                                    <a href="{{ route('super_admin.frascos.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Producción</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Frascos</h3>
                                        <p class="text-sm text-gray-600">Gestionar unidades por lote.</p>
                                    </a>

                                    <a href="{{ route('super_admin.lecturas-microclima.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Monitoreo</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Lecturas</h3>
                                        <p class="text-sm text-gray-600">Ver temperatura y humedad.</p>
                                    </a>

                                    <a href="{{ route('super_admin.controles-incubadora.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Control</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Controles</h3>
                                        <p class="text-sm text-gray-600">Aplicar y auditar acciones.</p>
                                    </a>

                                    <a href="{{ route('super_admin.seguimientos-lote.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seguimiento</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Seguimientos Lote</h3>
                                        <p class="text-sm text-gray-600">Avance general por lote.</p>
                                    </a>

                                    <a href="{{ route('super_admin.seguimientos-frasco.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seguimiento</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Seguimientos Frasco</h3>
                                        <p class="text-sm text-gray-600">Avance individual por frasco.</p>
                                    </a>

                                    <a href="{{ route('super_admin.evidencias-lote.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seguimiento</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Evidencias</h3>
                                        <p class="text-sm text-gray-600">Archivos y soportes visuales.</p>
                                    </a>

                                    <a href="{{ route('super_admin.alertas.index') }}" class="block bg-white/70 rounded-2xl p-6 border border-white hover-glow transition-all duration-300">
                                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alerta</p>
                                        <h3 class="font-black text-[#144255] text-xl mb-2">Alertas</h3>
                                        <p class="text-sm text-gray-600">Incidencias del microclima.</p>
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                                <div class="xl:col-span-2 bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-5">
                                        <div>
                                            <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Estado actual por incubadora</h3>
                                            <p class="text-sm text-gray-500">Resumen en tiempo real del microclima</p>
                                        </div>
                                        <a href="{{ route('super_admin.lecturas-microclima.index') }}"
                                           class="px-4 py-2 rounded-lg bg-[#144255] text-white text-sm font-semibold hover:bg-[#1c607a] transition">
                                            Ir a lecturas
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
                                            <p class="text-2xl font-black text-[#144255]">{{ $lotesTotal }}</p>
                                        </div>

                                        <div class="rounded-xl bg-white border border-gray-200 p-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Frascos registrados</p>
                                            <p class="text-2xl font-black text-[#144255]">{{ $frascosTotal }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Últimas lecturas</h3>
                                        <a href="{{ route('super_admin.lecturas-microclima.index') }}"
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
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Últimos controles</h3>
                                        <a href="{{ route('super_admin.controles-incubadora.index') }}"
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
                                                <th class="py-2">Usuario</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($ultimosControles as $control)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#144255]">{{ $control->incubadora->nombre ?? 'Sin incubadora' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $control->tipo->nombre ?? 'Sin tipo' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $control->modo->nombre ?? 'Sin modo' }}</td>
                                                    <td class="py-3 text-gray-500">{{ $control->usuario->name ?? 'Sin usuario' }}</td>
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

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Órdenes recientes</h3>
                                        <div class="inline-flex items-center rounded-full bg-[#144255] px-4 py-2 text-xs font-bold uppercase tracking-wider text-white">
                                            Control manual
                                        </div>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                            <tr class="border-b border-gray-200 text-left text-gray-500 uppercase text-xs">
                                                <th class="py-2 pr-4">Incubadora</th>
                                                <th class="py-2 pr-4">Tipo</th>
                                                <th class="py-2 pr-4">Acción</th>
                                                <th class="py-2 pr-4">Estado</th>
                                                <th class="py-2">Fecha</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($ordenesRecientes as $orden)
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 pr-4 font-semibold text-[#144255]">{{ $orden->incubadora->nombre ?? 'Sin incubadora' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700">{{ $orden->tipo->nombre ?? 'Sin tipo' }}</td>
                                                    <td class="py-3 pr-4 text-gray-700 capitalize">{{ $orden->accion }}</td>
                                                    <td class="py-3 pr-4 text-gray-700 capitalize">{{ $orden->estado_orden }}</td>
                                                    <td class="py-3 text-gray-500">{{ optional($orden->fecha_solicitud)->format('d/m H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-6 text-center text-gray-500">No hay órdenes registradas.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="bg-white/70 rounded-2xl p-6 border border-white">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-black text-[#144255] text-lg uppercase tracking-wide">Alertas recientes</h3>
                                        <a href="{{ route('super_admin.alertas.index') }}"
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
                                                <th class="py-2 pr-4">Nivel</th>
                                                <th class="py-2 pr-4">Estado</th>
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
                                                    <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($alerta->fecha_hora)->format('d/m H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-6 text-center text-gray-500">No hay alertas registradas.</td>
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
    </div>
</x-app-layout>
