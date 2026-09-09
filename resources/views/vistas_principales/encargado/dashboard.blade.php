@php
    $user = $user ?? auth()->user();
    $nombre = $user ? explode(' ', trim($user->name))[0] : 'Encargado';
    $rol = strtoupper($user?->rol_nombre ?? 'Encargado');
    $totalIncubadoras = $totalIncubadoras ?? 0;
    $incubadorasConLectura = $incubadorasConLectura ?? 0;
    $incubadoras = $incubadoras ?? collect();
    $ultimaLectura = $ultimaLectura ?? null;
@endphp

<x-app-layout>
    <div class="container-fluid py-4 px-3 px-lg-4">

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <p class="text-uppercase fw-bold mb-1" style="font-size:10px;letter-spacing:.12em;color:#3bb49c;">MicroSeed Control</p>
                <h1 class="fw-bold mb-2" style="color:#1c607a;">Bienvenido, {{ $nombre }}</h1>
                <p class="text-secondary mb-0">Monitorea las incubadoras que tienes asignadas y consulta sus condiciones ambientales.</p>
            </div>

            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:#e9f8f4;color:#277c70;width:fit-content;">
                <i class="bi bi-person-check-fill"></i>
                <span class="fw-semibold small">{{ $rol }}</span>
            </div>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <span class="text-secondary small">Incubadoras asignadas</span>
                                <h2 class="fw-bold mb-0 mt-1" style="color:#1c607a;">{{ $totalIncubadoras }}</h2>
                            </div>

                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:52px;height:52px;background:#e8f7f4;color:#3bb49c;">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <span class="text-secondary small">Con información ambiental</span>
                                <h2 class="fw-bold mb-0 mt-1" style="color:#1c607a;">{{ $incubadorasConLectura }}</h2>
                            </div>

                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:52px;height:52px;background:#e9f3f7;color:#1c607a;">
                                <i class="bi bi-activity fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <span class="text-secondary small">Última actualización</span>

                                @if($ultimaLectura?->fecha_hora)
                                    <h6 class="fw-bold mb-1 mt-1" style="color:#1c607a;">
                                        {{ $ultimaLectura->fecha_hora->format('d/m/Y') }}
                                    </h6>

                                    <small class="text-secondary">
                                        {{ $ultimaLectura->fecha_hora->format('H:i') }} hrs
                                    </small>
                                @else
                                    <h6 class="fw-bold mb-0 mt-1 text-secondary">Sin lecturas</h6>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:52px;height:52px;background:#f1f5f4;color:#64748b;">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-header border-0 bg-white px-4 pt-4 pb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">Mis incubadoras</h4>
                        <p class="text-secondary small mb-0">Solo se muestran las incubadoras correspondientes a tus asignaciones vigentes.</p>
                    </div>

                    <span class="badge rounded-pill px-3 py-2" style="background:#e9f8f4;color:#277c70;">
                        {{ $totalIncubadoras }} {{ $totalIncubadoras === 1 ? 'asignada' : 'asignadas' }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4 pt-2">

                @if($incubadoras->isEmpty())

                    <div class="text-center py-5">
                        <div class="d-flex align-items-center justify-content-center rounded-4 mx-auto mb-3" style="width:64px;height:64px;background:#eef7f5;color:#3bb49c;">
                            <i class="bi bi-inboxes fs-2"></i>
                        </div>

                        <h5 class="fw-bold mb-2" style="color:#1c607a;">Sin incubadoras asignadas</h5>

                        <p class="text-secondary small mb-0">
                            Actualmente no cuentas con una asignación vigente de incubadora.
                        </p>
                    </div>

                @else

                    <div class="row g-3">

                        @foreach($incubadoras as $incubadora)
                            @php
                                $lectura = $incubadora->ultimaLecturaMicroclima;
                                $estado = $incubadora->estado?->nombre ?? 'Sin estado';
                                $asignacion = ($asignaciones ?? collect())->firstWhere('incubadora_id', $incubadora->id);
                            @endphp

                            <div class="col-12 col-xl-6">
                                <div class="border rounded-4 p-4 h-100" style="border-color:#e4eceb!important;background:linear-gradient(135deg,#ffffff 0%,#f9fcfb 100%);">

                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:48px;height:48px;background:#e8f7f4;color:#3bb49c;">
                                                <i class="bi bi-box-seam fs-4"></i>
                                            </div>

                                            <div>
                                                <span class="text-secondary d-block" style="font-size:11px;">{{ $incubadora->codigo ?? 'Sin código' }}</span>
                                                <h5 class="fw-bold mb-0" style="color:#1c607a;">{{ $incubadora->nombre }}</h5>
                                            </div>
                                        </div>

                                        <span class="badge rounded-pill px-3 py-2" style="background:#e9f8f4;color:#277c70;">
                                            {{ $estado }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2 text-secondary small">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $incubadora->ubicacion ?: 'Ubicación no registrada' }}</span>
                                        </div>
                                    </div>

                                    <div class="row g-3">

                                        <div class="col-6">
                                            <div class="rounded-3 p-3 h-100" style="background:#fff5f2;">
                                                <div class="d-flex align-items-center gap-2 mb-2" style="color:#c9674c;">
                                                    <i class="bi bi-thermometer-half"></i>
                                                    <span class="small fw-semibold">Temperatura</span>
                                                </div>

                                                @if($lectura)
                                                    <h4 class="fw-bold mb-0" style="color:#334155;">
                                                        {{ number_format((float) $lectura->temperatura, 2) }} °C
                                                    </h4>
                                                @else
                                                    <span class="text-secondary small">Sin datos</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="rounded-3 p-3 h-100" style="background:#eef8fb;">
                                                <div class="d-flex align-items-center gap-2 mb-2" style="color:#337c99;">
                                                    <i class="bi bi-droplet-half"></i>
                                                    <span class="small fw-semibold">Humedad</span>
                                                </div>

                                                @if($lectura)
                                                    <h4 class="fw-bold mb-0" style="color:#334155;">
                                                        {{ number_format((float) $lectura->humedad, 2) }} %
                                                    </h4>
                                                @else
                                                    <span class="text-secondary small">Sin datos</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4 pt-3 border-top">
                                        <div class="text-secondary small">
                                            <i class="bi bi-clock me-1"></i>

                                            @if($lectura?->fecha_hora)
                                                Última lectura: {{ $lectura->fecha_hora->format('d/m/Y H:i') }}
                                            @else
                                                Sin lecturas registradas
                                            @endif
                                        </div>

                                        @if($asignacion?->fecha_inicio)
                                            <div class="text-secondary small">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                Asignada desde {{ $asignacion->fecha_inicio->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>

                @endif

            </div>
        </div>

    </div>
</x-app-layout>
