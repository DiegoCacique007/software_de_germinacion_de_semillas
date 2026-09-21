@php
    $lecturaActual=$incubadora->ultimaLecturaMicroclima;
    $asignacion=$incubadora->asignaciones->first();
@endphp

<x-app-layout>
    <div class="container-fluid py-4 px-3 px-lg-4">

        {{-- ENCABEZADO --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a href="{{ route('encargado.incubadoras.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                    <span class="badge rounded-pill px-3 py-2" style="background:#e8f7f4;color:#277c70;">
                    {{ $incubadora->codigo ?? 'Sin código' }}
                </span>
                </div>

                <h1 class="h2 fw-bold mb-1" style="color:#1c607a;">{{ $incubadora->nombre }}</h1>
                <p class="text-secondary mb-0">Consulta el estado, microclima, alertas, lotes e historial de tu incubadora asignada.</p>
            </div>

            <span class="badge rounded-pill px-3 py-2 align-self-start align-self-lg-center" style="background:#e9f3f7;color:#1c607a;">
            <i class="bi bi-circle-fill me-1" style="font-size:7px;color:#3bb49c;"></i>
            {{ $incubadora->estado?->nombre ?? 'Sin estado' }}
        </span>
        </div>

        {{-- INFORMACIÓN GENERAL --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#e8f7f4;color:#3bb49c;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">Información de la incubadora</h4>
                        <p class="text-secondary small mb-0">Datos generales del equipo asignado.</p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach([
                        ['Código',$incubadora->codigo ?? '—'],
                        ['Nombre',$incubadora->nombre ?? '—'],
                        ['Ubicación',$incubadora->ubicacion ?: 'No registrada'],
                        ['Estado',$incubadora->estado?->nombre ?? 'Sin estado'],
                    ] as [$titulo,$valor])
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">{{ $titulo }}</span>
                                <span class="fw-semibold" style="color:#1c607a;">{{ $valor }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($incubadora->descripcion)
                        <div class="col-12">
                            <div class="rounded-3 border p-3">
                                <span class="text-secondary small d-block mb-1">Descripción</span>
                                <p class="mb-0">{{ $incubadora->descripcion }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ASIGNACIÓN --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#e9f3f7;color:#1c607a;">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">Mi asignación</h4>
                        <p class="text-secondary small mb-0">Periodo durante el cual tienes acceso a esta incubadora.</p>
                    </div>
                </div>

                @if($asignacion)
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">Fecha de inicio</span>
                                <span class="fw-semibold">{{ $asignacion->fecha_inicio?->format('d/m/Y') ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">Fecha de finalización</span>
                                <span class="fw-semibold">{{ $asignacion->fecha_fin?->format('d/m/Y') ?? 'Sin fecha definida' }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">Estado</span>
                                <span class="badge rounded-pill px-3 py-2" style="background:#e8f7f4;color:#277c70;">Vigente</span>
                            </div>
                        </div>

                        @if($asignacion->observaciones)
                            <div class="col-12">
                                <div class="rounded-3 border p-3">
                                    <span class="text-secondary small d-block mb-1">Observaciones</span>
                                    <p class="mb-0">{{ $asignacion->observaciones }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-light border rounded-3 mb-0">No se encontró información de la asignación vigente.</div>
                @endif
            </div>
        </div>

        {{-- MICROCLIMA --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">Microclima actual</h4>
                        <p class="text-secondary small mb-0">Última lectura ambiental registrada.</p>
                    </div>

                    @if($lecturaActual?->fecha_hora)
                        <span class="text-secondary small">
                        <i class="bi bi-clock-history me-1"></i>
                        {{ $lecturaActual->fecha_hora->format('d/m/Y H:i') }}
                    </span>
                    @endif
                </div>

                @if($lecturaActual)
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-4 h-100" style="background:#fff5f2;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small fw-semibold d-block mb-2" style="color:#c9674c;">Temperatura</span>
                                        <h2 class="fw-bold mb-0 text-dark">{{ number_format((float)$lecturaActual->temperatura,2) }} <small class="fs-5">°C</small></h2>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-white rounded-3" style="width:54px;height:54px;color:#c9674c;">
                                        <i class="bi bi-thermometer-half fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-4 h-100" style="background:#eef8fb;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small fw-semibold d-block mb-2" style="color:#337c99;">Humedad relativa</span>
                                        <h2 class="fw-bold mb-0 text-dark">{{ number_format((float)$lecturaActual->humedad,2) }} <small class="fs-5">%</small></h2>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center bg-white rounded-3" style="width:54px;height:54px;color:#337c99;">
                                        <i class="bi bi-droplet-half fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($lecturaActual->observaciones)
                            <div class="col-12">
                                <div class="rounded-3 border p-3">
                                    <span class="text-secondary small d-block mb-1">Observaciones</span>
                                    <p class="mb-0">{{ $lecturaActual->observaciones }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-activity fs-2 d-block mb-2" style="color:#3bb49c;"></i>
                        <h6 class="fw-bold" style="color:#1c607a;">Sin lecturas ambientales</h6>
                        <p class="text-secondary small mb-0">Todavía no se ha registrado información de microclima.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ALERTAS + LOTES --}}
        <div class="row g-4 mb-4">

            {{-- ALERTAS --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-2">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h4 class="fw-bold mb-1" style="color:#1c607a;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Alertas activas
                                </h4>
                                <p class="text-secondary small mb-0">Incidencias pendientes o atendidas de esta incubadora.</p>
                            </div>
                            <span class="badge rounded-pill px-3 py-2" style="background:#fff1f2;color:#c92f40;">{{ $alertas->count() }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-3">
                        @forelse($alertas as $alerta)
                            @php $estadoClave=$alerta->estado?->clave; @endphp

                            <div class="border rounded-3 p-3 {{ !$loop->last?'mb-3':'' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div class="min-w-0">
                                        <span class="fw-bold d-block text-dark">{{ $alerta->tipo?->nombre ?? 'Alerta' }}</span>
                                        <small class="text-secondary">{{ $alerta->nivel?->nombre ?? 'Sin nivel' }}</small>
                                    </div>

                                    <span class="badge rounded-pill px-2 py-1"
                                          style="{{ $estadoClave==='pendiente'?'background:#fff3cd;color:#856404;':'background:#e8f7f4;color:#277c70;' }}">
                                    {{ $alerta->estado?->nombre ?? 'Sin estado' }}
                                </span>
                                </div>

                                <p class="small text-secondary mb-2">{{ $alerta->mensaje ?? 'Sin mensaje registrado.' }}</p>

                                <small class="text-secondary">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $alerta->fecha_hora?->format('d/m/Y H:i') ?? 'Sin fecha' }}
                                </small>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle fs-2 d-block mb-2" style="color:#3bb49c;"></i>
                                <h6 class="fw-bold mb-1" style="color:#1c607a;">Sin alertas activas</h6>
                                <p class="text-secondary small mb-0">Esta incubadora no presenta incidencias que requieran atención.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <a href="{{ route('encargado.alertas.index') }}" class="btn btn-sm rounded-3 w-100" style="background:#eef8f7;color:#216a73;">
                            Ver mis alertas <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- LOTES --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-2">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h4 class="fw-bold mb-1" style="color:#1c607a;">
                                    <i class="bi bi-layers me-2"></i>Lotes en la incubadora
                                </h4>
                                <p class="text-secondary small mb-0">Lotes ubicados actualmente dentro de este equipo.</p>
                            </div>
                            <span class="badge rounded-pill px-3 py-2" style="background:#e9f8f4;color:#277c70;">{{ $lotes->count() }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-3">
                        @forelse($lotes as $lote)
                            <div class="border rounded-3 p-3 {{ !$loop->last?'mb-3':'' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <span class="fw-bold d-block" style="color:#1c607a;">{{ $lote->codigo_lote }}</span>
                                        <small class="text-secondary">{{ $lote->especie?->nombre_comun ?? 'Sin especie' }}</small>
                                    </div>

                                    <span class="badge rounded-pill px-2 py-1" style="background:#f1f5f4;color:#475569;">
                                    {{ $lote->estado?->nombre ?? 'Sin estado' }}
                                </span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 small text-secondary">
                                    <span><i class="bi bi-box me-1"></i>{{ $lote->frascos_count }} {{ $lote->frascos_count===1?'frasco':'frascos' }}</span>

                                    @if($lote->fecha_siembra)
                                        <span><i class="bi bi-calendar3 me-1"></i>{{ $lote->fecha_siembra->format('d/m/Y') }}</span>
                                    @endif

                                    @if($lote->posicion)
                                        <span><i class="bi bi-geo-alt me-1"></i>Posición {{ $lote->posicion->numero_posicion ?? $lote->posicion->id }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-layers fs-2 text-secondary d-block mb-2"></i>
                                <h6 class="fw-bold mb-1" style="color:#1c607a;">Sin lotes registrados</h6>
                                <p class="text-secondary small mb-0">No existen lotes asociados actualmente a esta incubadora.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <a href="{{ route('encargado.lotes.index') }}" class="btn btn-sm rounded-3 w-100" style="background:#eef8f7;color:#216a73;">
                            Ver mis lotes <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- HISTORIAL --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">Historial reciente</h4>
                        <p class="text-secondary small mb-0">Últimas lecturas ambientales registradas para esta incubadora.</p>
                    </div>

                    <span class="badge rounded-pill px-3 py-2" style="background:#e9f8f4;color:#277c70;">
                    {{ $lecturas->count() }} {{ $lecturas->count()===1?'lectura':'lecturas' }}
                </span>
                </div>
            </div>

            <div class="card-body p-0">
                @if($lecturas->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-database-x fs-2 text-secondary d-block mb-2"></i>
                        <h6 class="fw-bold mb-1" style="color:#1c607a;">Sin historial</h6>
                        <p class="text-secondary small mb-0">No existen lecturas registradas para esta incubadora.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f5f9f8;">
                            <tr>
                                <th class="px-4 py-3 text-nowrap">Fecha y hora</th>
                                <th class="px-4 py-3 text-nowrap">Temperatura</th>
                                <th class="px-4 py-3 text-nowrap">Humedad</th>
                                <th class="px-4 py-3">Observaciones</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($lecturas as $lectura)
                                <tr>
                                    <td class="px-4 py-3 text-nowrap">
                                        <div class="fw-semibold">{{ $lectura->fecha_hora?->format('d/m/Y') ?? '—' }}</div>
                                        <small class="text-secondary">{{ $lectura->fecha_hora?->format('H:i:s') ?? '' }}</small>
                                    </td>

                                    <td class="px-4 py-3 text-nowrap">
                                        <i class="bi bi-thermometer-half me-1" style="color:#c9674c;"></i>
                                        <strong>{{ number_format((float)$lectura->temperatura,2) }} °C</strong>
                                    </td>

                                    <td class="px-4 py-3 text-nowrap">
                                        <i class="bi bi-droplet-half me-1" style="color:#337c99;"></i>
                                        <strong>{{ number_format((float)$lectura->humedad,2) }} %</strong>
                                    </td>

                                    <td class="px-4 py-3 text-secondary">{{ $lectura->observaciones ?: '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
