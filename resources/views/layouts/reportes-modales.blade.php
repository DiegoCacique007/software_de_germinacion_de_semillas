@php
    $user = Auth::user();
    $role = $user->role ?? null;
@endphp

@if($role === 'super_admin')

    @php
        $incubadorasList = \App\Models\Incubadora::select('id', 'nombre')
            ->orderBy('id')
            ->get();

        $lotesList = \App\Models\Lote::select('id', 'codigo_lote')
            ->orderBy('id')
            ->get();
    @endphp

    {{-- MODAL PDF MICROCLIMA --}}
    <div x-show="showMicroclimaModal"
         x-cloak
         class="modal fade show d-block"
         tabindex="-1"
         role="dialog"
         aria-modal="true"
         style="background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px);">

        <div class="modal-dialog modal-dialog-centered" @click.outside="showMicroclimaModal = false">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-file-earmark-pdf fs-4"></i>
                        Filtros: PDF Microclima
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="showMicroclimaModal = false" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('super_admin.reportes.microclima.pdf') }}" method="GET">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de inicio
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de fin
                            </label>
                            <input type="date" name="fecha_fin" class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Incubadora
                            </label>
                            <select name="incubadora_id" class="form-select rounded-3 py-2">
                                <option value="">Todas las incubadoras</option>
                                @foreach($incubadorasList as $incubadora)
                                    <option value="{{ $incubadora->id }}">
                                        {{ $incubadora->nombre ?? 'Incubadora #' . $incubadora->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" @click="showMicroclimaModal = false" class="btn btn-light rounded-3 px-4 py-2 fw-semibold text-secondary">
                            Cancelar
                        </button>
                        <button type="submit" @click="showMicroclimaModal = false" class="btn rounded-3 px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                            <i class="bi bi-download"></i>
                            Descargar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PDF BIOLÓGICO --}}
    <div x-show="showBiologicoModal"
         x-cloak
         class="modal fade show d-block"
         tabindex="-1"
         role="dialog"
         aria-modal="true"
         style="background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px);">

        <div class="modal-dialog modal-dialog-centered" @click.outside="showBiologicoModal = false">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-file-earmark-pdf fs-4"></i>
                        Filtros: PDF Biológico
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="showBiologicoModal = false" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('super_admin.reportes.biologico.pdf') }}" method="GET">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de inicio
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Fecha de fin
                            </label>
                            <input type="date" name="fecha_fin" class="form-control rounded-3 py-2">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">
                                Lote
                            </label>
                            <select name="lote_id" class="form-select rounded-3 py-2">
                                <option value="">Todos los lotes</option>
                                @foreach($lotesList as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->codigo_lote ?? 'Lote #' . $lote->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" @click="showBiologicoModal = false" class="btn btn-light rounded-3 px-4 py-2 fw-semibold text-secondary">
                            Cancelar
                        </button>
                        <button type="submit" @click="showBiologicoModal = false" class="btn rounded-3 px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                            <i class="bi bi-download"></i>
                            Descargar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endif
