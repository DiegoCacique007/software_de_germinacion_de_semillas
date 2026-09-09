@php
    $user = auth()->user();
    $nombre = $user ? explode(' ', trim($user->name))[0] : 'Administrador';
    $rol = strtoupper($user?->rol_nombre ?? 'Administrador');
@endphp

<x-app-layout>
    <div class="container-fluid py-4 px-3 px-lg-4">
        <div class="mb-4">
            <p class="text-uppercase fw-bold mb-1" style="font-size:10px;letter-spacing:.12em;color:#3bb49c;">MicroSeed Control</p>
            <h1 class="fw-bold mb-2" style="color:#1c607a;">Bienvenido, {{ $nombre }}</h1>
            <p class="text-secondary mb-0">Panel de gestión operativa del sistema.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:50px;height:50px;background:#e8f7f4;color:#3bb49c;">
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>

                            <div>
                                <span class="text-secondary small">Rol actual</span>
                                <h5 class="fw-bold mb-0" style="color:#1c607a;">{{ $rol }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:50px;height:50px;background:#e9f3f7;color:#1c607a;">
                                <i class="bi bi-person-check fs-4"></i>
                            </div>

                            <div>
                                <span class="text-secondary small">Cuenta</span>
                                <h5 class="fw-bold mb-0" style="color:#1c607a;">Activa</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-2" style="color:#1c607a;">Panel del Administrador</h4>
                        <p class="text-secondary mb-0">
                            Desde este panel se integrarán las funciones de administración operativa de incubadoras, especies, lotes, frascos, alertas y gestión de encargados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
