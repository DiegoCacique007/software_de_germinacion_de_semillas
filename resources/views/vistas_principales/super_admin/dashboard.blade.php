<x-app-layout>
    @php
        $usuario = Auth::user();
        $nombreCorto = $usuario ? explode(' ', $usuario->name)[0] : 'Usuario';
        $rolNombre = strtoupper(str_replace('_', ' ', $usuario->role ?? 'super_admin'));
        $incubadoraTiempoRealId = $incubadoraActualId ?? optional($incubadoras->first())->id ?? 106;
    @endphp

    <div
        class="container-fluid py-4 px-3 px-lg-4"
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
        {{-- ========================================================= --}}
        {{-- BANNER DE BIENVENIDA Y RELOJ --}}
        {{-- ========================================================= --}}
        <div class="card dashboard-card rounded-4 overflow-hidden mb-4">
            <div class="dashboard-top-accent"></div>

            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-brand-soft border border-brand-subtle text-brand-primary small fw-bold text-uppercase mb-3">
                            <span class="badge-dot bg-brand-accent"></span>
                            Control global del sistema
                        </div>

                        <h2 class="display-6 fw-bold text-brand-dark mb-2">
                            <span x-text="greeting">Bienvenido</span>, {{ $nombreCorto }}
                        </h2>

                        <p class="text-secondary mb-0" style="max-width: 650px;">
                            Desde aquí supervisas usuarios, sensores, lecturas, alertas, controles y el flujo completo de incubación.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border border-light-subtle shadow-sm">
                        <div class="rounded-3 p-3 btn-brand text-white shadow-sm">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>

                        <div>
                            <span class="small fw-bold text-uppercase text-secondary d-block" style="font-size: 0.75rem;">
                                Tiempo local
                            </span>
                            <span class="h4 fw-bold text-brand-dark font-monospace mb-0" x-text="time">
                                --:--:--
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- ALERTAS DE SESIÓN / ERRORES --}}
        {{-- ========================================================= --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4" role="alert">
                <div class="fw-bold mb-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Corrige los siguientes errores:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- MÉTRICAS PRINCIPALES (KPIS) --}}
        {{-- ========================================================= --}}
        <div class="row g-3 g-xl-4 mb-4">
            {{-- USUARIOS --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card rounded-4 h-100 p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.75rem;">
                                Usuarios
                            </span>
                            <h3 id="metricUsuariosTotal" class="display-6 fw-bold text-brand-dark mb-0">
                                {{ $usuariosTotal }}
                            </h3>
                            <span class="small text-secondary mt-2 d-block">
                                Cuentas registradas
                            </span>
                        </div>

                        <div class="kpi-icon-box bg-brand-primary shadow-sm">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>

                    <div class="mt-4 d-flex align-items-center gap-2">
                        <span class="badge-dot bg-brand-accent"></span>
                        <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
                            Gestión activa
                        </span>
                    </div>
                </div>
            </div>

            {{-- INCUBADORAS --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card rounded-4 h-100 p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.75rem;">
                                Incubadoras
                            </span>
                            <h3 id="metricIncubadorasTotal" class="display-6 fw-bold text-brand-dark mb-0">
                                {{ $incubadorasTotal }}
                            </h3>
                            <span class="small text-secondary mt-2 d-block">
                                Dispositivos gestionados
                            </span>
                        </div>

                        <div class="kpi-icon-box bg-brand-accent shadow-sm">
                            <i class="bi bi-cpu fs-4"></i>
                        </div>
                    </div>

                    <div class="mt-4 d-flex align-items-center gap-2">
                        <span class="badge-dot bg-brand-accent"></span>
                        <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
                            En operación
                        </span>
                    </div>
                </div>
            </div>

            {{-- LECTURAS HOY --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card rounded-4 h-100 p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.75rem;">
                                Lecturas hoy
                            </span>
                            <h3 id="metricLecturasHoy" class="display-6 fw-bold text-brand-dark mb-0">
                                {{ $lecturasHoy }}
                            </h3>
                            <span class="small text-secondary mt-2 d-block">
                                Datos de sensores
                            </span>
                        </div>

                        <div class="kpi-icon-box bg-warning text-dark shadow-sm">
                            <i class="bi bi-activity fs-4 text-white"></i>
                        </div>
                    </div>

                    <div class="mt-4 d-flex align-items-center gap-2">
                        <span class="badge-dot bg-warning"></span>
                        <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
                            Monitoreo diario
                        </span>
                    </div>
                </div>
            </div>

            {{-- ALERTAS ACTIVAS --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card rounded-4 h-100 p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.75rem;">
                                Alertas activas
                            </span>
                            <h3 id="metricAlertasActivas" class="display-6 fw-bold text-brand-dark mb-0">
                                {{ $alertasActivas }}
                            </h3>
                            <span class="small text-secondary mt-2 d-block">
                                Pendientes o revisión
                            </span>
                        </div>

                        <div class="kpi-icon-box bg-danger shadow-sm">
                            <i class="bi bi-bell fs-4"></i>
                        </div>
                    </div>

                    <div id="metricAlertasEstado" class="mt-4 d-flex align-items-center gap-2">
                        @if($alertasActivas > 0)
                            <span class="badge-dot bg-danger"></span>
                            <span class="small fw-bold text-danger text-uppercase" style="font-size: 0.7rem;">
                                Requiere atención
                            </span>
                        @else
                            <span class="badge-dot bg-success"></span>
                            <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
                                Sin incidencias
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MONITOREO AMBIENTAL EN TIEMPO REAL (GRÁFICOS) --}}
        {{-- ========================================================= --}}
        <div class="mb-4">
            <div class="mb-3">
                <h4 class="fw-bold text-brand-dark mb-1">
                    Monitoreo Ambiental en Tiempo Real
                </h4>
                <p class="text-secondary small mb-0">
                    Visualización continua del microclima registrada por el sensor DHT22 conectado al ESP32.
                </p>
            </div>

            <div class="row g-3 g-xl-4">
                {{-- GRÁFICA TEMPERATURA --}}
                <div class="col-12 col-lg-6">
                    <div class="card dashboard-card rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-warning text-uppercase mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-thermometer-half fs-5"></i>
                                <span>Temperatura (°C)</span>
                            </h6>

                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 small fw-bold">
                                <span class="badge-dot bg-success"></span>
                                En vivo
                            </span>
                        </div>

                        <div class="dashboard-chart-container">
                            <canvas id="temperaturaChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- GRÁFICA HUMEDAD --}}
                <div class="col-12 col-lg-6">
                    <div class="card dashboard-card rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-brand-accent text-uppercase mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-moisture fs-5"></i>
                                <span>Humedad Relativa (%)</span>
                            </h6>

                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 small fw-bold">
                                <span class="badge-dot bg-success"></span>
                                En vivo
                            </span>
                        </div>

                        <div class="dashboard-chart-container">
                            <canvas id="humedadChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PANEL MICROSEED CONTROL Y ACTUADORES --}}
        {{-- ========================================================= --}}
        <div class="card dashboard-card rounded-4 overflow-hidden mb-4">
            {{-- CABECERA DEL PANEL --}}
            <div class="control-header-gradient p-4 text-white">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-15 border border-white border-opacity-25 text-white small fw-bold text-uppercase mb-2">
                            <span class="badge-dot bg-brand-accent"></span>
                            Sistema operativo
                        </div>

                        <h4 class="fw-bold mb-1">
                            Panel Microseed Control
                        </h4>

                        <p class="text-white text-opacity-75 small mb-0" style="max-width: 650px;">
                            Visualiza el microclima en tiempo real y manipula los actuadores principales de la incubadora.
                        </p>
                    </div>

                    <div class="bg-white bg-opacity-15 border border-white border-opacity-25 rounded-4 px-4 py-3">
                        <span class="small fw-bold text-uppercase text-white text-opacity-75 d-block" style="font-size: 0.7rem;">
                            Estado de conexión
                        </span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge-dot bg-brand-accent"></span>
                            <span class="h6 fw-bold mb-0">En línea</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CONTENIDO DEL PANEL --}}
            <div class="card-body p-4 bg-light bg-opacity-50">
                <div class="row g-4">
                    {{-- SENSOR DHT22 --}}
                    <div class="col-12 col-xl-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 bg-brand-dark text-white p-2.5 d-flex align-items-center justify-center">
                                        <i class="bi bi-broadcast-pin fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-brand-dark mb-0">Sensor DHT22</h5>
                                        <span class="small text-secondary">Temperatura y humedad ambiental</span>
                                    </div>
                                </div>

                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 small fw-bold">
                                    <span class="badge-dot bg-success"></span>
                                    En vivo
                                </span>
                            </div>

                            <div class="row g-3">
                                {{-- TEMPERATURA --}}
                                <div class="col-12 col-sm-6">
                                    <div class="sensor-metric-box bg-warning-subtle border border-warning-subtle">
                                        <span class="small fw-bold text-warning-emphasis text-uppercase d-block mb-2" style="font-size: 0.75rem;">
                                            Temperatura
                                        </span>

                                        <div class="d-flex align-items-baseline justify-content-center gap-1">
                                            <span class="display-5 fw-bold text-warning-emphasis" id="dht22Temp">--</span>
                                            <span class="h5 fw-bold text-warning-emphasis">°C</span>
                                        </div>

                                        <span class="small text-warning-emphasis text-opacity-75 d-block mt-2">
                                            Lectura térmica actual
                                        </span>
                                    </div>
                                </div>

                                {{-- HUMEDAD --}}
                                <div class="col-12 col-sm-6">
                                    <div class="sensor-metric-box bg-info-subtle border border-info-subtle">
                                        <span class="small fw-bold text-info-emphasis text-uppercase d-block mb-2" style="font-size: 0.75rem;">
                                            Humedad
                                        </span>

                                        <div class="d-flex align-items-baseline justify-content-center gap-1">
                                            <span class="display-5 fw-bold text-info-emphasis" id="dht22Hum">--</span>
                                            <span class="h5 fw-bold text-info-emphasis">%</span>
                                        </div>

                                        <span class="small text-info-emphasis text-opacity-75 d-block mt-2">
                                            Humedad relativa
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-3 bg-light border border-light-subtle px-3 py-2.5 d-flex align-items-center justify-content-between">
                                <span class="small fw-bold text-secondary">Última actualización</span>
                                <span class="small fw-bold text-brand-dark font-monospace" id="dht22Time">--:--:--</span>
                            </div>
                        </div>
                    </div>

                    {{-- CONTROL DE ACTUADORES --}}
                    <div class="col-12 col-xl-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
                                <div>
                                    <h5 class="fw-bold text-brand-dark mb-1">Control de Actuadores</h5>
                                    <span class="small text-secondary">Activa el modo manual para manipular niebla e iluminación.</span>
                                </div>

                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5 small fw-bold" id="modoBadge">
                                    ⚙️ AUTOMÁTICO
                                </span>
                            </div>

                            {{-- MODO SWITCH CARD --}}
                            <div class="rounded-4 btn-brand p-4 text-white mb-4 shadow-sm">
                                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 bg-white bg-opacity-15 border border-white border-opacity-25 p-2.5 d-flex align-items-center justify-center">
                                            <i class="bi bi-sliders fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">Modo de Operación</h6>
                                            <span class="small text-white text-opacity-75" id="modoLabel">Modo Automático activo</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small fw-bold text-white text-opacity-75 text-uppercase">Auto</span>
                                        <div class="toggle-switch-lg toggle-switch border border-white border-opacity-25" id="modoSwitch" onclick="toggleModo()"></div>
                                        <span class="small fw-bold text-white text-opacity-75 text-uppercase">Manual</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ACTUADORES ROWS --}}
                            <div class="row g-3">
                                {{-- GENERADOR DE NIEBLA --}}
                                <div class="col-12 col-md-6">
                                    <div class="actuator-card h-100">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="actuator-icon-box bg-success-subtle text-success">
                                                    🌫️
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-brand-dark mb-0">Generador de Niebla</h6>
                                                    <span class="small text-secondary" style="font-size: 0.72rem;">Módulo ultrasónico</span>
                                                </div>
                                            </div>

                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 small fw-bold" id="nieblaBadge">
                                                APAGADO
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between rounded-3 bg-light border border-light-subtle px-3 py-2">
                                            <div>
                                                <span class="small text-secondary d-block" style="font-size: 0.7rem;">Estado</span>
                                                <span class="small fw-bold text-brand-dark" id="nieblaLabel">Apagado</span>
                                            </div>

                                            <div class="toggle-switch-lg toggle-switch locked-control" id="nieblaSwitch" onclick="toggleNiebla()" style="pointer-events: none; opacity: 0.55;"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TIRA LED BLANCA --}}
                                <div class="col-12 col-md-6">
                                    <div class="actuator-card h-100">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="actuator-icon-box bg-warning-subtle text-warning">
                                                    💡
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-brand-dark mb-0">Tira LED Blanca</h6>
                                                    <span class="small text-secondary" style="font-size: 0.72rem;">Fotoperiodo del cultivo</span>
                                                </div>
                                            </div>

                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 small fw-bold" id="ledBadge">
                                                APAGADO
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between rounded-3 bg-light border border-light-subtle px-3 py-2">
                                            <div>
                                                <span class="small text-secondary d-block" style="font-size: 0.7rem;">Estado</span>
                                                <span class="small fw-bold text-brand-dark" id="ledLabel">Apagado</span>
                                            </div>

                                            <div class="toggle-switch-lg toggle-switch locked-control" id="ledSwitch" onclick="toggleLed()" style="pointer-events: none; opacity: 0.55;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-3 bg-warning-subtle border border-warning-subtle p-3">
                                <span class="small text-warning-emphasis fw-medium">
                                    <i class="bi bi-info-circle me-1"></i> Nota: los actuadores permanecen bloqueados mientras el sistema esté en modo automático. Activa modo manual para encender o apagar niebla y luz.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- ESTADO POR INCUBADORA Y ESTADO DEL SISTEMA --}}
        {{-- ========================================================= --}}
        <div class="row g-3 g-xl-4 mb-4">
            {{-- RESUMEN INCUBADORAS LIVE --}}
            <div class="col-12 col-xl-8">
                <div class="card dashboard-card rounded-4 p-4 h-100">
                    <div class="mb-4">
                        <h5 class="fw-bold text-brand-dark mb-1">Estado actual por incubadora</h5>
                        <span class="small text-secondary">Resumen en tiempo real del microclima</span>
                    </div>

                    <div id="resumenIncubadorasLive" class="row g-3">
                        @forelse($resumenIncubadoras as $item)
                            @php
                                $inc = $item['incubadora'];
                                $lectura = $item['ultima_lectura'];
                                $alertas = $item['alertas_abiertas'];
                            @endphp

                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border border-light-subtle">
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                        <div>
                                            <h6 class="fw-bold text-brand-dark mb-0">{{ $inc->nombre }}</h6>
                                            <span class="small text-secondary">{{ $inc->codigo }}</span>
                                        </div>

                                        @if($alertas > 0)
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                                Alerta activa
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                                Estable
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-column gap-2 small">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-secondary">Estado incubadora</span>
                                            <span class="fw-semibold text-brand-dark">{{ $inc->estado->nombre ?? 'Sin estado' }}</span>
                                        </div>

                                        @if($lectura)
                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">Temperatura</span>
                                                <span class="fw-bold text-brand-dark">{{ $lectura->temperatura }} °C</span>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">Humedad</span>
                                                <span class="fw-bold text-brand-dark">{{ $lectura->humedad }} %</span>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span class="text-secondary">Última lectura</span>
                                                <span class="fw-semibold text-secondary">{{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @else
                                            <div class="text-secondary fst-italic pt-1">
                                                Sin lecturas registradas todavía.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-secondary py-5">
                                No hay incubadoras registradas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ESTADO DEL SISTEMA --}}
            <div class="col-12 col-xl-4">
                <div class="card dashboard-card rounded-4 p-4 h-100">
                    <h5 class="fw-bold text-brand-dark mb-4">Estado del sistema</h5>

                    <div class="d-flex flex-column gap-3">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem;">
                                Nivel de acceso
                            </span>
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-brand-dark text-brand-accent rounded-3 small fw-bold text-uppercase">
                                {{ $rolNombre }}
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem;">
                                Lotes registrados
                            </span>
                            <span id="metricLotesTotal" class="h3 fw-bold text-brand-dark mb-0">
                                {{ $lotesTotal }}
                            </span>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                            <span class="small fw-bold text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem;">
                                Frascos registrados
                            </span>
                            <span id="metricFrascosTotal" class="h3 fw-bold text-brand-dark mb-0">
                                {{ $frascosTotal }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- TELEMETRÍA, CHART.JS Y CONTROL DE ACTUADORES --}}
    {{-- ============================================================= --}}
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
                        <span class="badge-dot bg-danger"></span>
                        <span class="small fw-bold text-danger text-uppercase" style="font-size: 0.7rem;">
                            Requiere atención
                        </span>
                    `;
                } else {
                    contenedor.innerHTML = `
                        <span class="badge-dot bg-success"></span>
                        <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
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
                                color: '#6c757d',
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#e9ecef'
                            },
                            ticks: {
                                color: '#6c757d',
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
                    const gradientTemp = ctxTemp.createLinearGradient(0, 0, 0, 300);
                    gradientTemp.addColorStop(0, 'rgba(234, 179, 8, 0.35)');
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
                                    borderWidth: 2.5,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#eab308',
                                    pointBorderWidth: 2,
                                    pointRadius: 3.5,
                                    pointHoverRadius: 5.5,
                                    tension: 0.35,
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
                    const gradientHum = ctxHum.createLinearGradient(0, 0, 0, 300);
                    gradientHum.addColorStop(0, 'rgba(59, 180, 156, 0.35)');
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
                                    borderWidth: 2.5,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#3bb49c',
                                    pointBorderWidth: 2,
                                    pointRadius: 3.5,
                                    pointHoverRadius: 5.5,
                                    tension: 0.35,
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
                        <div class="col-12 text-center text-secondary py-4">
                            No hay incubadoras registradas.
                        </div>
                    `;
                    return;
                }

                contenedor.innerHTML = items.map(function (item) {
                    const alertaHtml = item.alertas_abiertas > 0
                        ? `<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2.5 py-1 small fw-bold">Alerta activa</span>`
                        : `<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">Estable</span>`;

                    const lecturaHtml = item.temperatura !== null
                        ? `
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Temperatura</span>
                                <span class="fw-bold text-brand-dark">${item.temperatura} °C</span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Humedad</span>
                                <span class="fw-bold text-brand-dark">${item.humedad} %</span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Última lectura</span>
                                <span class="fw-semibold text-secondary">${item.fecha}</span>
                            </div>
                        `
                        : `
                            <div class="text-secondary fst-italic pt-1">
                                Sin lecturas registradas todavía.
                            </div>
                        `;

                    return `
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border border-light-subtle">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                    <div>
                                        <h6 class="fw-bold text-brand-dark mb-0">${item.nombre}</h6>
                                        <span class="small text-secondary">${item.codigo ?? ''}</span>
                                    </div>
                                    ${alertaHtml}
                                </div>

                                <div class="d-flex flex-column gap-2 small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary">Estado incubadora</span>
                                        <span class="fw-semibold text-brand-dark">${item.estado}</span>
                                    </div>
                                    ${lecturaHtml}
                                </div>
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
                            ? 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-bold'
                            : 'badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold';
                    } else {
                        badgeElement.textContent = 'APAGADO';
                        badgeElement.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 small fw-bold';
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
                        modoBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 small fw-bold';
                    } else {
                        modoBadge.textContent = '⚙️ AUTOMÁTICO';
                        modoBadge.className = 'badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5 small fw-bold';
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
