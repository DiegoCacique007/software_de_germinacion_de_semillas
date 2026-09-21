@php
    $user = $user ?? auth()->user();
    $nombre = $user ? explode(' ', trim($user->name))[0] : 'Encargado';
    $rol = strtoupper($user?->rol_nombre ?? 'ENCARGADO');

    $totalIncubadoras = $totalIncubadoras ?? 0;
    $totalAlertasActivas = $totalAlertasActivas ?? 0;
    $lotesEnSeguimiento = $lotesEnSeguimiento ?? 0;
    $lecturasHoy = $lecturasHoy ?? 0;

    $incubadoras = $incubadoras ?? collect();
    $alertasPrioritarias = $alertasPrioritarias ?? collect();
    $alertasActivasPorIncubadora = $alertasActivasPorIncubadora ?? collect();
    $lecturasRecientes = $lecturasRecientes ?? collect();
    $nombresIncubadoras = $nombresIncubadoras ?? collect();
    $ultimaLectura = $ultimaLectura ?? null;
@endphp

<x-app-layout>

    <div class="container-fluid py-4 px-3 px-lg-4 dashboard-page"
         x-data="{
        time:'',
        date:'',
        greeting:'',
        init(){
            this.updateClock();
            setInterval(()=>this.updateClock(),1000);
        },
        updateClock(){
            const now=new Date();

            this.time=new Intl.DateTimeFormat('es-MX',{
                timeZone:'America/Mexico_City',
                hour:'2-digit',
                minute:'2-digit',
                second:'2-digit',
                hour12:false
            }).format(now);

            this.date=new Intl.DateTimeFormat('es-MX',{
                timeZone:'America/Mexico_City',
                weekday:'long',
                day:'2-digit',
                month:'long'
            }).format(now);

            const parts=new Intl.DateTimeFormat('es-MX',{
                timeZone:'America/Mexico_City',
                hour:'2-digit',
                hour12:false
            }).formatToParts(now);

            let hour=Number(parts.find(part=>part.type==='hour')?.value||0);

            if(hour===24)hour=0;

            this.greeting=hour<12
                ?'Buenos días'
                :(hour<19?'Buenas tardes':'Buenas noches');
        }
     }">

        {{-- ENCABEZADO --}}
        <section class="dashboard-hero mb-4">

            <div class="row align-items-center g-4">

                <div class="col-12 col-xl-8">

                    <h1 class="dashboard-title mb-2">
                        <span x-text="greeting">Bienvenido</span>, {{ $nombre }}
                    </h1>

                    <p class="text-secondary mb-0 dashboard-description">
                        Supervisa tus incubadoras asignadas, atiende alertas y consulta las condiciones del microclima.
                    </p>

                </div>

                <div class="col-12 col-xl-4">

                    <div class="dashboard-clock">

                        <div class="d-flex align-items-center gap-3">

                            <div class="dashboard-clock-icon">
                                <i class="bi bi-clock"></i>
                            </div>

                            <div class="flex-grow-1">

                            <span class="small text-secondary text-capitalize d-block"
                                  x-text="date"></span>

                                <strong class="dashboard-clock-time"
                                        x-text="time">
                                    --:--:--
                                </strong>

                            </div>

                            <div class="text-end">

                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                <span class="status-dot status-dot-success me-1"></span>
                                {{ $rol }}
                            </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {{-- INDICADORES PRINCIPALES --}}
        <section class="mb-4">

            <div class="row g-3">

                <div class="col-12 col-sm-6 col-xl-3">

                    <article class="dashboard-kpi h-100">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div>

                            <span class="dashboard-kpi-label">
                                Mis incubadoras
                            </span>

                                <div class="dashboard-kpi-value">
                                    {{ $totalIncubadoras }}
                                </div>

                                <span class="dashboard-kpi-caption">
                                Asignaciones vigentes
                            </span>

                            </div>

                            <div class="dashboard-kpi-icon bg-success-subtle text-success">
                                <i class="bi bi-cpu"></i>
                            </div>

                        </div>

                    </article>

                </div>

                <div class="col-12 col-sm-6 col-xl-3">

                    <article class="dashboard-kpi h-100">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div>

                            <span class="dashboard-kpi-label">
                                Alertas activas
                            </span>

                                <div class="dashboard-kpi-value">
                                    {{ $totalAlertasActivas }}
                                </div>

                                <span class="dashboard-kpi-caption">

                                @if($totalAlertasActivas > 0)

                                        <span class="text-danger fw-semibold">
                                        Requieren seguimiento
                                    </span>

                                    @else

                                        <span class="text-success fw-semibold">
                                        Sin incidencias
                                    </span>

                                    @endif

                            </span>

                            </div>

                            <div class="dashboard-kpi-icon bg-danger-subtle text-danger">
                                <i class="bi bi-bell"></i>
                            </div>

                        </div>

                    </article>

                </div>

                <div class="col-12 col-sm-6 col-xl-3">

                    <article class="dashboard-kpi h-100">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div>

                            <span class="dashboard-kpi-label">
                                Lotes
                            </span>

                                <div class="dashboard-kpi-value">
                                    {{ $lotesEnSeguimiento }}
                                </div>

                                <span class="dashboard-kpi-caption">
                                En incubadoras asignadas
                            </span>

                            </div>

                            <div class="dashboard-kpi-icon bg-primary-subtle text-primary">
                                <i class="bi bi-layers"></i>
                            </div>

                        </div>

                    </article>

                </div>

                <div class="col-12 col-sm-6 col-xl-3">

                    <article class="dashboard-kpi h-100">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div>

                            <span class="dashboard-kpi-label">
                                Lecturas hoy
                            </span>

                                <div class="dashboard-kpi-value">
                                    {{ $lecturasHoy }}
                                </div>

                                <span class="dashboard-kpi-caption">
                                Registros ambientales
                            </span>

                            </div>

                            <div class="dashboard-kpi-icon bg-warning-subtle text-warning-emphasis">
                                <i class="bi bi-activity"></i>
                            </div>

                        </div>

                    </article>

                </div>

            </div>

        </section>

        {{-- TRABAJO PRIORITARIO --}}
        <section class="mb-4">

            <div class="row g-4">

                {{-- ALERTAS --}}
                <div class="col-12 col-xl-7" id="alertas-prioritarias">

                    <div class="card dashboard-panel h-100">

                        <div class="card-header dashboard-panel-header">

                            <div>

                                <h5 class="fw-bold text-brand-dark mb-1">
                                    Alertas que requieren atención
                                </h5>

                                <span class="small text-secondary">
                                Incidencias pendientes o atendidas que todavía permanecen activas
                            </span>

                            </div>

                            @if($totalAlertasActivas > 0)

                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">
                                {{ $totalAlertasActivas }} activas
                            </span>

                            @else

                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                Sin incidencias
                            </span>

                            @endif

                        </div>

                        <div class="card-body p-3 p-lg-4">

                            @forelse($alertasPrioritarias as $alerta)

                                @php
                                    $estadoClave=$alerta->estado?->clave;

                                    $estadoClase=match($estadoClave){
                                        'pendiente'=>'bg-warning-subtle text-warning-emphasis',
                                        'atendida'=>'bg-primary-subtle text-primary-emphasis',
                                        default=>'bg-secondary-subtle text-secondary-emphasis',
                                    };

                                    $nivelClave=strtolower($alerta->nivel?->clave??'');

                                    $nivelClase=match($nivelClave){
                                        'alto','alta'=>'bg-danger-subtle text-danger-emphasis',
                                        'medio','media'=>'bg-warning-subtle text-warning-emphasis',
                                        'bajo','baja'=>'bg-success-subtle text-success-emphasis',
                                        default=>'bg-secondary-subtle text-secondary-emphasis',
                                    };
                                @endphp

                                <article class="incubator-summary mb-3">

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">

                                        <div class="flex-grow-1">

                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                                                <strong class="text-brand-dark">
                                                    {{ $alerta->incubadora?->nombre ?? 'Incubadora' }}
                                                </strong>

                                                <span class="badge {{ $nivelClase }} rounded-pill">
                                                {{ $alerta->nivel?->nombre ?? 'Sin nivel' }}
                                            </span>

                                                <span class="badge {{ $estadoClase }} rounded-pill">
                                                {{ $alerta->estado?->nombre ?? 'Sin estado' }}
                                            </span>

                                            </div>

                                            <div class="d-flex align-items-center gap-2 mb-2">

                                                @if($alerta->tipo?->clave === 'temperatura')
                                                    <i class="bi bi-thermometer-high text-danger"></i>
                                                @elseif($alerta->tipo?->clave === 'humedad')
                                                    <i class="bi bi-droplet-half text-info"></i>
                                                @else
                                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                                @endif

                                                <span class="fw-semibold text-brand-dark">
                                                {{ $alerta->tipo?->nombre ?? 'Alerta' }}
                                            </span>

                                                <span class="text-secondary small">
                                                · {{ $alerta->lectura_causante }}
                                            </span>

                                            </div>

                                            <p class="text-secondary small mb-2">
                                                {{ $alerta->mensaje }}
                                            </p>

                                            <div class="d-flex flex-wrap gap-3 text-secondary small">

                                                @if($alerta->lote)
                                                    <span>
                                                    <i class="bi bi-layers me-1"></i>
                                                    {{ $alerta->lote->codigo_lote }}
                                                </span>
                                                @endif

                                                <span>
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $alerta->fecha_hora?->format('d/m/Y H:i') }}
                                            </span>

                                                <span>
                                                <i class="bi bi-stopwatch me-1"></i>
                                                {{ $alerta->duracion_incidente }}
                                            </span>

                                            </div>

                                        </div>

                                    </div>

                                </article>

                            @empty

                                <div class="dashboard-empty-state py-5">

                                    <i class="bi bi-check-circle fs-2 text-success mb-2"></i>

                                    <strong class="text-brand-dark mb-1">
                                        Todo en orden
                                    </strong>

                                    <span>
                                    No tienes alertas activas en tus incubadoras.
                                </span>

                                </div>

                            @endforelse

                            @if($totalAlertasActivas > 0)

                                <div class="text-end mt-3">

                                    <a href="{{ route('encargado.alertas.index') }}"
                                       class="btn btn-brand-outline rounded-3 px-4">

                                        <i class="bi bi-bell me-2"></i>
                                        Gestionar mis alertas

                                    </a>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

                {{-- ESTADO AMBIENTAL --}}
                <div class="col-12 col-xl-5">

                    <div class="card dashboard-panel h-100">

                        <div class="card-header dashboard-panel-header">

                            <div>

                                <h5 class="fw-bold text-brand-dark mb-1">
                                    Estado ambiental reciente
                                </h5>

                                <span class="small text-secondary">
                                Última lectura de tus incubadoras
                            </span>

                            </div>

                            <i class="bi bi-broadcast-pin fs-4 text-success"></i>

                        </div>

                        <div class="card-body p-4">

                            @if($ultimaLectura)

                                <div class="sensor-live-value mb-3">

                                    <div class="sensor-live-icon bg-warning-subtle text-warning-emphasis">
                                        <i class="bi bi-thermometer-half"></i>
                                    </div>

                                    <div>

                                    <span class="small text-secondary d-block">
                                        Temperatura
                                    </span>

                                        <div>

                                            <strong class="sensor-live-number">
                                                {{ number_format((float)$ultimaLectura->temperatura,1) }}
                                            </strong>

                                            <span class="fw-bold text-secondary">
                                            °C
                                        </span>

                                        </div>

                                    </div>

                                </div>

                                <div class="sensor-live-value mb-4">

                                    <div class="sensor-live-icon bg-info-subtle text-info-emphasis">
                                        <i class="bi bi-droplet-half"></i>
                                    </div>

                                    <div>

                                    <span class="small text-secondary d-block">
                                        Humedad relativa
                                    </span>

                                        <div>

                                            <strong class="sensor-live-number">
                                                {{ number_format((float)$ultimaLectura->humedad,1) }}
                                            </strong>

                                            <span class="fw-bold text-secondary">
                                            %
                                        </span>

                                        </div>

                                    </div>

                                </div>

                                <div class="border-top pt-3">

                                    <div class="incubator-detail">

                                        <span>Incubadora</span>

                                        <strong>
                                            {{ $nombresIncubadoras->get($ultimaLectura->incubadora_id,'—') }}
                                        </strong>

                                    </div>

                                    <div class="incubator-detail">

                                        <span>Última actualización</span>

                                        <strong class="small">
                                            {{ $ultimaLectura->fecha_hora?->format('d/m/Y H:i') }}
                                        </strong>

                                    </div>

                                </div>

                            @else

                                <div class="dashboard-empty-state py-5">

                                    <i class="bi bi-activity fs-2 mb-2"></i>

                                    <span>
                                    No existen lecturas ambientales disponibles.
                                </span>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {{-- INCUBADORAS ASIGNADAS --}}
        <section class="mb-4" id="mis-incubadoras">

            <div class="card dashboard-panel">

                <div class="card-header dashboard-panel-header">

                    <div>

                        <h5 class="fw-bold text-brand-dark mb-1">
                            Mis incubadoras
                        </h5>

                        <span class="small text-secondary">
                        Estado actual de los equipos bajo tu responsabilidad
                    </span>

                    </div>

                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-2">
                    {{ $totalIncubadoras }}
                        {{ $totalIncubadoras === 1 ? 'asignada' : 'asignadas' }}
                </span>

                </div>

                <div class="card-body p-3 p-lg-4">

                    @if($incubadoras->isEmpty())

                        <div class="dashboard-empty-state py-5">

                            <i class="bi bi-inboxes fs-2 mb-2"></i>

                            <strong class="text-brand-dark mb-1">
                                Sin incubadoras asignadas
                            </strong>

                            <span>
                            Actualmente no cuentas con una asignación vigente.
                        </span>

                        </div>

                    @else

                        <div class="row g-3">

                            @foreach($incubadoras as $incubadora)

                                @php
                                    $lectura=$incubadora->ultimaLecturaMicroclima;
                                    $estado=$incubadora->estado?->nombre??'Sin estado';

                                    $alertasIncubadora=(int)$alertasActivasPorIncubadora->get($incubadora->id,0);

                                    $asignacion=($asignaciones??collect())
                                        ->firstWhere('incubadora_id',$incubadora->id);
                                @endphp

                                <div class="col-12 col-md-6 col-xl-4">

                                    <article class="incubator-summary h-100">

                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">

                                            <div>

                                                <h6 class="fw-bold text-brand-dark mb-1">
                                                    {{ $incubadora->nombre }}
                                                </h6>

                                                <span class="small text-secondary">
                                                {{ $incubadora->codigo ?? 'Sin código' }}
                                            </span>

                                            </div>

                                            @if($alertasIncubadora > 0)

                                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">
                                                {{ $alertasIncubadora }}
                                                    {{ $alertasIncubadora === 1 ? 'alerta' : 'alertas' }}
                                            </span>

                                            @else

                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                                Estable
                                            </span>

                                            @endif

                                        </div>

                                        <div class="incubator-detail">

                                            <span>Estado</span>

                                            <strong>
                                                {{ $estado }}
                                            </strong>

                                        </div>

                                        <div class="incubator-detail">

                                        <span>
                                            <i class="bi bi-geo-alt me-1"></i>
                                            Ubicación
                                        </span>

                                            <strong>
                                                {{ $incubadora->ubicacion ?: 'No registrada' }}
                                            </strong>

                                        </div>

                                        @if($lectura)

                                            <div class="incubator-detail">

                                                <span>Temperatura</span>

                                                <strong>
                                                    {{ number_format((float)$lectura->temperatura,1) }} °C
                                                </strong>

                                            </div>

                                            <div class="incubator-detail">

                                                <span>Humedad</span>

                                                <strong>
                                                    {{ number_format((float)$lectura->humedad,1) }} %
                                                </strong>

                                            </div>

                                            <div class="incubator-detail">

                                                <span>Última lectura</span>

                                                <strong class="small">
                                                    {{ $lectura->fecha_hora?->format('d/m/Y H:i') ?? '—' }}
                                                </strong>

                                            </div>

                                        @else

                                            <div class="small text-secondary fst-italic mt-3">
                                                Sin lecturas registradas.
                                            </div>

                                        @endif

                                        @if($asignacion?->fecha_inicio)

                                            <div class="border-top mt-3 pt-3">

                                            <span class="small text-secondary">

                                                <i class="bi bi-calendar-check me-1"></i>

                                                Asignada desde
                                                {{ $asignacion->fecha_inicio->format('d/m/Y') }}

                                            </span>

                                            </div>

                                        @endif

                                    </article>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>

            </div>

        </section>

        {{-- LECTURAS + ACCESOS --}}
        <section id="lecturas-recientes">

            <div class="row g-4">

                <div class="col-12 col-xl-8">

                    <div class="card dashboard-panel h-100">

                        <div class="card-header dashboard-panel-header">

                            <div>

                                <h5 class="fw-bold text-brand-dark mb-1">
                                    Lecturas recientes
                                </h5>

                                <span class="small text-secondary">
                                Últimos registros ambientales de tus incubadoras
                            </span>

                            </div>

                            <i class="bi bi-activity fs-4 text-brand-primary"></i>

                        </div>

                        <div class="card-body p-0">

                            @if($lecturasRecientes->isEmpty())

                                <div class="dashboard-empty-state py-5">
                                    <i class="bi bi-activity fs-2 mb-2"></i>
                                    <span>No existen lecturas registradas.</span>
                                </div>

                            @else

                                <div class="table-responsive">

                                    <table class="table align-middle mb-0">

                                        <thead class="table-light">

                                        <tr>

                                            <th class="px-4 py-3">
                                                Incubadora
                                            </th>

                                            <th class="py-3">
                                                Temperatura
                                            </th>

                                            <th class="py-3">
                                                Humedad
                                            </th>

                                            <th class="px-4 py-3">
                                                Fecha
                                            </th>

                                        </tr>

                                        </thead>

                                        <tbody>

                                        @foreach($lecturasRecientes as $lectura)

                                            <tr>

                                                <td class="px-4 py-3 fw-semibold text-brand-dark">

                                                    {{ $nombresIncubadoras->get(
                                                        $lectura->incubadora_id,
                                                        'Incubadora'
                                                    ) }}

                                                </td>

                                                <td class="py-3">

                                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">

                                                        <i class="bi bi-thermometer-half me-1"></i>

                                                        {{ number_format((float)$lectura->temperatura,1) }} °C

                                                    </span>

                                                </td>

                                                <td class="py-3">

                                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill">

                                                        <i class="bi bi-droplet-half me-1"></i>

                                                        {{ number_format((float)$lectura->humedad,1) }} %

                                                    </span>

                                                </td>

                                                <td class="px-4 py-3 text-secondary small">

                                                    {{ $lectura->fecha_hora?->format('d/m/Y H:i:s') }}

                                                </td>

                                            </tr>

                                        @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

                {{-- ACCESOS RÁPIDOS --}}
                <div class="col-12 col-xl-4">

                    <div class="card dashboard-panel h-100">

                        <div class="card-header dashboard-panel-header">

                            <div>

                                <h5 class="fw-bold text-brand-dark mb-1">
                                    Accesos rápidos
                                </h5>

                                <span class="small text-secondary">
                                Herramientas para tu trabajo diario
                            </span>

                            </div>

                            <i class="bi bi-grid fs-4 text-brand-primary"></i>

                        </div>

                        <div class="card-body p-4">

                            <div class="d-grid gap-3">

                                <a href="{{ route('encargado.alertas.index') }}"
                                   class="btn btn-brand d-flex align-items-center justify-content-between px-4 py-3 rounded-3">

                                <span>
                                    <i class="bi bi-bell me-2"></i>
                                    Mis alertas
                                </span>

                                    <i class="bi bi-chevron-right"></i>

                                </a>

                                <a href="#mis-incubadoras"
                                   class="btn btn-brand-outline d-flex align-items-center justify-content-between px-4 py-3 rounded-3">

                                <span>
                                    <i class="bi bi-cpu me-2"></i>
                                    Mis incubadoras
                                </span>

                                    <i class="bi bi-chevron-right"></i>

                                </a>

                                <a href="#lecturas-recientes"
                                   class="btn btn-brand-outline d-flex align-items-center justify-content-between px-4 py-3 rounded-3">

                                <span>
                                    <i class="bi bi-activity me-2"></i>
                                    Lecturas recientes
                                </span>

                                    <i class="bi bi-chevron-right"></i>

                                </a>

                                <a href="#alertas-prioritarias"
                                   class="btn btn-brand-outline d-flex align-items-center justify-content-between px-4 py-3 rounded-3">

                                <span>
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Incidencias activas
                                </span>

                                    <i class="bi bi-chevron-right"></i>

                                </a>

                            </div>

                            <div class="dashboard-control-info mt-4">

                                <i class="bi bi-info-circle me-2"></i>

                                Este panel muestra únicamente información de las incubadoras que tienes asignadas actualmente.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

</x-app-layout>
