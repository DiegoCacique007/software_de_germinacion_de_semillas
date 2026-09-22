@php
    $user=auth()->user();
    $isSuperAdmin=$user?->isSuperAdmin()??false;
@endphp

@if($isSuperAdmin)
    @php
        $incubadorasList=\App\Models\Incubadora::select('id','codigo','nombre')->orderBy('id')->get();
        $lotesList=\App\Models\Lote::select('id','codigo_lote')->orderBy('id')->get();
    @endphp

    {{-- MODAL REPORTE MICROCLIMA --}}
    <div
        x-show.important="showMicroclimaModal"
        x-cloak
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        style="background:rgba(15,23,42,.55);backdrop-filter:blur(4px);"
        @click.self="showMicroclimaModal=false"
        @keydown.escape.window="showMicroclimaModal=false">

        <div
            class="modal-dialog modal-dialog-centered"
            @click.outside="showMicroclimaModal=false">

            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                <div
                    class="modal-header border-0 text-white p-4"
                    style="background:linear-gradient(135deg,#1c607a 0%,#3bb49c 100%);">

                    <div>
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-bar-chart-line fs-4"></i>
                            Reporte de Microclima
                        </h5>

                        <p class="small mb-0 text-white-50">
                            Selecciona el periodo y la incubadora que deseas consultar.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        @click="showMicroclimaModal=false"
                        aria-label="Cerrar">
                    </button>
                </div>

                <form method="GET">

                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de inicio
                            </label>

                            <input
                                type="date"
                                name="fecha_inicio"
                                class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de fin
                            </label>

                            <input
                                type="date"
                                name="fecha_fin"
                                class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Incubadora
                            </label>

                            <select
                                name="incubadora_id"
                                class="form-select rounded-3 py-2">

                                <option value="">
                                    Todas las incubadoras
                                </option>

                                @foreach($incubadorasList as $incubadora)
                                    <option value="{{ $incubadora->id }}">
                                        {{ $incubadora->codigo ? $incubadora->codigo.' — ' : '' }}
                                        {{ $incubadora->nombre??('Incubadora #'.$incubadora->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div
                            class="rounded-3 p-3 mt-4"
                            style="background:#f0f9ff;border:1px solid #bae6fd;">

                            <div class="d-flex gap-2">
                                <i
                                    class="bi bi-info-circle-fill mt-1"
                                    style="color:#1c607a;">
                                </i>

                                <div class="small text-secondary">
                                    <strong class="d-block mb-1 text-dark">
                                        ¿Qué opción debo utilizar?
                                    </strong>

                                    <div class="mb-2">
                                        <strong>PDF:</strong>
                                        incluye las estadísticas calculadas con todos los registros
                                        encontrados y muestra las 300 lecturas más recientes.
                                    </div>

                                    <div>
                                        <strong>Datos completos:</strong>
                                        descarga en CSV todas las lecturas que cumplan los filtros,
                                        aunque existan miles de registros.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">

                        <div class="w-100 d-flex flex-column flex-sm-row gap-2">

                            <button
                                type="button"
                                @click="showMicroclimaModal=false"
                                class="btn btn-light rounded-3 px-3 py-2 fw-semibold text-secondary order-sm-1">
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                formaction="{{ route('super_admin.reportes.microclima.csv') }}"
                                formtarget="_self"
                                @click="showMicroclimaModal=false"
                                class="btn rounded-3 px-3 py-2 fw-bold text-white shadow-sm d-flex justify-content-center align-items-center gap-2 flex-grow-1 order-sm-2"
                                style="background:#2f8f83;border:none;">

                                <i class="bi bi-file-earmark-spreadsheet"></i>
                                Exportar datos completos
                            </button>

                            <button
                                type="submit"
                                formaction="{{ route('super_admin.reportes.microclima.pdf') }}"
                                formtarget="_blank"
                                @click="showMicroclimaModal=false"
                                class="btn rounded-3 px-3 py-2 fw-bold text-white shadow-sm d-flex justify-content-center align-items-center gap-2 flex-grow-1 order-sm-3"
                                style="background:linear-gradient(135deg,#1c607a,#3bb49c);border:none;">

                                <i class="bi bi-file-earmark-pdf"></i>
                                Generar PDF
                            </button>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- MODAL REPORTE BIOLÓGICO --}}
    <div
        x-show.important="showBiologicoModal"
        x-cloak
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        style="background:rgba(15,23,42,.55);backdrop-filter:blur(4px);"
        @click.self="showBiologicoModal=false"
        @keydown.escape.window="showBiologicoModal=false">

        <div
            class="modal-dialog modal-dialog-centered"
            @click.outside="showBiologicoModal=false">

            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                <div
                    class="modal-header border-0 text-white p-4"
                    style="background:linear-gradient(135deg,#1c607a 0%,#3bb49c 100%);">

                    <div>
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-flower1 fs-4"></i>
                            Reporte de Seguimiento Biológico
                        </h5>

                        <p class="small mb-0 text-white-50">
                            Selecciona el periodo y el lote que deseas consultar.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        @click="showBiologicoModal=false"
                        aria-label="Cerrar">
                    </button>
                </div>

                <form method="GET">

                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de inicio
                            </label>

                            <input
                                type="date"
                                name="fecha_inicio"
                                class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de fin
                            </label>

                            <input
                                type="date"
                                name="fecha_fin"
                                class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Lote
                            </label>

                            <select
                                name="lote_id"
                                class="form-select rounded-3 py-2">

                                <option value="">
                                    Todos los lotes
                                </option>

                                @foreach($lotesList as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->codigo_lote??('Lote #'.$lote->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div
                            class="rounded-3 p-3 mt-4"
                            style="background:#f0f9ff;border:1px solid #bae6fd;">

                            <div class="d-flex gap-2">
                                <i
                                    class="bi bi-info-circle-fill mt-1"
                                    style="color:#1c607a;">
                                </i>

                                <div class="small text-secondary">

                                    <strong class="d-block mb-1 text-dark">
                                        ¿Qué opción debo utilizar?
                                    </strong>

                                    <div class="mb-2">
                                        <strong>PDF:</strong>
                                        presenta el resumen del seguimiento y los 300 registros
                                        más recientes que cumplan los filtros.
                                    </div>

                                    <div>
                                        <strong>Datos completos:</strong>
                                        descarga en CSV todo el historial biológico correspondiente
                                        al lote y periodo seleccionado.
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">

                        <div class="w-100 d-flex flex-column flex-sm-row gap-2">

                            <button
                                type="button"
                                @click="showBiologicoModal=false"
                                class="btn btn-light rounded-3 px-3 py-2 fw-semibold text-secondary order-sm-1">
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                formaction="{{ route('super_admin.reportes.biologico.csv') }}"
                                formtarget="_self"
                                @click="showBiologicoModal=false"
                                class="btn rounded-3 px-3 py-2 fw-bold text-white shadow-sm d-flex justify-content-center align-items-center gap-2 flex-grow-1 order-sm-2"
                                style="background:#2f8f83;border:none;">

                                <i class="bi bi-file-earmark-spreadsheet"></i>
                                Exportar datos completos
                            </button>

                            <button
                                type="submit"
                                formaction="{{ route('super_admin.reportes.biologico.pdf') }}"
                                formtarget="_blank"
                                @click="showBiologicoModal=false"
                                class="btn rounded-3 px-3 py-2 fw-bold text-white shadow-sm d-flex justify-content-center align-items-center gap-2 flex-grow-1 order-sm-3"
                                style="background:linear-gradient(135deg,#1c607a,#3bb49c);border:none;">

                                <i class="bi bi-file-earmark-pdf"></i>
                                Generar PDF
                            </button>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
@endif
