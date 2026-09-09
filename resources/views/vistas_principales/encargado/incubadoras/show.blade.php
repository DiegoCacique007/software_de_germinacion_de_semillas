@php
    $lecturaActual = $incubadora->ultimaLecturaMicroclima;
    $asignacion = $incubadora->asignaciones->first();
@endphp

<x-app-layout>
    <div class="container-fluid py-4 px-3 px-lg-4">

        {{-- ENCABEZADO --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a href="{{ route('encargado.incubadoras.index') }}"
                       class="btn btn-sm btn-outline-secondary rounded-3 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        Volver
                    </a>

                    <span class="badge rounded-pill px-3 py-2"
                          style="background:#e8f7f4;color:#277c70;">
                        {{ $incubadora->codigo ?? 'Sin código' }}
                    </span>
                </div>

                <h1 class="h2 fw-bold mb-1" style="color:#1c607a;">
                    {{ $incubadora->nombre }}
                </h1>

                <p class="text-secondary mb-0">
                    Consulta la información general y el historial de microclima de la incubadora asignada.
                </p>
            </div>

            <div>
                <span class="badge rounded-pill px-3 py-2"
                      style="background:#e9f3f7;color:#1c607a;">
                    <i class="bi bi-circle-fill me-1" style="font-size:7px;color:#3bb49c;"></i>
                    {{ $incubadora->estado?->nombre ?? 'Sin estado' }}
                </span>
            </div>
        </div>

        {{-- INFORMACIÓN GENERAL --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="width:48px;height:48px;background:#e8f7f4;color:#3bb49c;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>

                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">
                            Información de la incubadora
                        </h4>

                        <p class="text-secondary small mb-0">
                            Datos generales de la incubadora actualmente asignada.
                        </p>
                    </div>
                </div>

                <div class="row g-3">

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="rounded-3 border p-3 h-100">
                            <span class="text-secondary small d-block mb-1">
                                Código
                            </span>

                            <span class="fw-bold" style="color:#1c607a;">
                                {{ $incubadora->codigo ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="rounded-3 border p-3 h-100">
                            <span class="text-secondary small d-block mb-1">
                                Nombre
                            </span>

                            <span class="fw-bold" style="color:#1c607a;">
                                {{ $incubadora->nombre ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="rounded-3 border p-3 h-100">
                            <span class="text-secondary small d-block mb-1">
                                Ubicación
                            </span>

                            <span class="fw-semibold text-dark">
                                {{ $incubadora->ubicacion ?: 'No registrada' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="rounded-3 border p-3 h-100">
                            <span class="text-secondary small d-block mb-1">
                                Estado
                            </span>

                            <span class="badge rounded-pill px-3 py-2"
                                  style="background:#e8f7f4;color:#277c70;">
                                {{ $incubadora->estado?->nombre ?? 'Sin estado' }}
                            </span>
                        </div>
                    </div>

                    @if($incubadora->descripcion)
                        <div class="col-12">
                            <div class="rounded-3 border p-3">
                                <span class="text-secondary small d-block mb-1">
                                    Descripción
                                </span>

                                <p class="mb-0 text-dark">
                                    {{ $incubadora->descripcion }}
                                </p>
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
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="width:48px;height:48px;background:#e9f3f7;color:#1c607a;">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>

                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">
                            Mi asignación
                        </h4>

                        <p class="text-secondary small mb-0">
                            Periodo durante el cual tienes acceso operativo a esta incubadora.
                        </p>
                    </div>
                </div>

                @if($asignacion)

                    <div class="row g-3">

                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">
                                    Fecha de inicio
                                </span>

                                <span class="fw-semibold text-dark">
                                    {{ $asignacion->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">
                                    Fecha de finalización
                                </span>

                                <span class="fw-semibold text-dark">
                                    {{ $asignacion->fecha_fin?->format('d/m/Y') ?? 'Sin fecha definida' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="rounded-3 border p-3 h-100">
                                <span class="text-secondary small d-block mb-1">
                                    Estado de asignación
                                </span>

                                <span class="badge rounded-pill px-3 py-2"
                                      style="background:#e8f7f4;color:#277c70;">
                                    Vigente
                                </span>
                            </div>
                        </div>

                        @if($asignacion->observaciones)
                            <div class="col-12">
                                <div class="rounded-3 border p-3">
                                    <span class="text-secondary small d-block mb-1">
                                        Observaciones
                                    </span>

                                    <p class="mb-0 text-dark">
                                        {{ $asignacion->observaciones }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>

                @else

                    <div class="alert alert-light border mb-0 rounded-3">
                        No se encontró información de la asignación vigente.
                    </div>

                @endif

            </div>
        </div>

        {{-- MICROCLIMA ACTUAL --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">
                            Microclima actual
                        </h4>

                        <p class="text-secondary small mb-0">
                            Última lectura ambiental registrada para esta incubadora.
                        </p>
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

                        {{-- TEMPERATURA --}}
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-4 h-100"
                                 style="background:#fff5f2;">

                                <div class="d-flex align-items-center justify-content-between gap-3">

                                    <div>
                                        <span class="small fw-semibold d-block mb-2"
                                              style="color:#c9674c;">
                                            Temperatura
                                        </span>

                                        <h2 class="fw-bold mb-0"
                                            style="color:#334155;">
                                            {{ number_format((float) $lecturaActual->temperatura, 2) }}
                                            <small class="fs-5">°C</small>
                                        </h2>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center rounded-3"
                                         style="width:54px;height:54px;background:#ffffff;color:#c9674c;">
                                        <i class="bi bi-thermometer-half fs-3"></i>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- HUMEDAD --}}
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-4 h-100"
                                 style="background:#eef8fb;">

                                <div class="d-flex align-items-center justify-content-between gap-3">

                                    <div>
                                        <span class="small fw-semibold d-block mb-2"
                                              style="color:#337c99;">
                                            Humedad relativa
                                        </span>

                                        <h2 class="fw-bold mb-0"
                                            style="color:#334155;">
                                            {{ number_format((float) $lecturaActual->humedad, 2) }}
                                            <small class="fs-5">%</small>
                                        </h2>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center rounded-3"
                                         style="width:54px;height:54px;background:#ffffff;color:#337c99;">
                                        <i class="bi bi-droplet-half fs-3"></i>
                                    </div>

                                </div>
                            </div>
                        </div>

                        @if($lecturaActual->observaciones)
                            <div class="col-12">
                                <div class="rounded-3 border p-3">
                                    <span class="text-secondary small d-block mb-1">
                                        Observaciones de la última lectura
                                    </span>

                                    <p class="mb-0 text-dark">
                                        {{ $lecturaActual->observaciones }}
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>

                @else

                    <div class="text-center py-5">
                        <div class="d-flex align-items-center justify-content-center rounded-4 mx-auto mb-3"
                             style="width:64px;height:64px;background:#eef7f5;color:#3bb49c;">
                            <i class="bi bi-activity fs-2"></i>
                        </div>

                        <h5 class="fw-bold mb-2" style="color:#1c607a;">
                            Sin lecturas ambientales
                        </h5>

                        <p class="text-secondary small mb-0">
                            Esta incubadora todavía no cuenta con información de microclima registrada.
                        </p>
                    </div>

                @endif

            </div>
        </div>

        {{-- HISTORIAL --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-header bg-white border-0 px-4 pt-4 pb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                    <div>
                        <h4 class="fw-bold mb-1" style="color:#1c607a;">
                            Historial reciente
                        </h4>

                        <p class="text-secondary small mb-0">
                            Últimas lecturas ambientales registradas para la incubadora.
                        </p>
                    </div>

                    <span class="badge rounded-pill px-3 py-2"
                          style="background:#e9f8f4;color:#277c70;">
                        {{ $lecturas->count() }}
                        {{ $lecturas->count() === 1 ? 'lectura' : 'lecturas' }}
                    </span>

                </div>
            </div>

            <div class="card-body p-0">

                @if($lecturas->isEmpty())

                    <div class="text-center py-5">
                        <i class="bi bi-database-x fs-2 text-secondary d-block mb-2"></i>

                        <h6 class="fw-bold mb-1" style="color:#1c607a;">
                            Sin historial
                        </h6>

                        <p class="text-secondary small mb-0">
                            No existen lecturas registradas para esta incubadora.
                        </p>
                    </div>

                @else

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead style="background:#f5f9f8;">
                            <tr>
                                <th class="px-4 py-3 text-nowrap">
                                    Fecha y hora
                                </th>

                                <th class="px-4 py-3 text-nowrap">
                                    Temperatura
                                </th>

                                <th class="px-4 py-3 text-nowrap">
                                    Humedad
                                </th>

                                <th class="px-4 py-3">
                                    Observaciones
                                </th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($lecturas as $lectura)

                                <tr>
                                    <td class="px-4 py-3 text-nowrap">
                                        <div class="fw-semibold text-dark">
                                            {{ $lectura->fecha_hora?->format('d/m/Y') ?? '—' }}
                                        </div>

                                        <small class="text-secondary">
                                            {{ $lectura->fecha_hora?->format('H:i:s') ?? '' }}
                                        </small>
                                    </td>

                                    <td class="px-4 py-3 text-nowrap">
                                            <span class="d-inline-flex align-items-center gap-2">
                                                <i class="bi bi-thermometer-half"
                                                   style="color:#c9674c;"></i>

                                                <strong>
                                                    {{ number_format((float) $lectura->temperatura, 2) }} °C
                                                </strong>
                                            </span>
                                    </td>

                                    <td class="px-4 py-3 text-nowrap">
                                            <span class="d-inline-flex align-items-center gap-2">
                                                <i class="bi bi-droplet-half"
                                                   style="color:#337c99;"></i>

                                                <strong>
                                                    {{ number_format((float) $lectura->humedad, 2) }} %
                                                </strong>
                                            </span>
                                    </td>

                                    <td class="px-4 py-3 text-secondary">
                                        {{ $lectura->observaciones ?: '—' }}
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
</x-app-layout>
