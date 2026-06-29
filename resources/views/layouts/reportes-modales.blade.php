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
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
         style="display: none;">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 text-gray-800"
             @click.away="showMicroclimaModal = false">

            <div class="bg-[#1c607a] text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                    </svg>
                    Filtros: PDF Microclima
                </h3>

                <button type="button"
                        @click="showMicroclimaModal = false"
                        class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('super_admin.reportes.microclima.pdf') }}" method="GET" class="p-6">
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Fecha de inicio
                        </label>

                        <input type="date"
                               name="fecha_inicio"
                               class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Fecha de fin
                        </label>

                        <input type="date"
                               name="fecha_fin"
                               class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Incubadora
                        </label>

                        <select name="incubadora_id"
                                class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c] bg-white">
                            <option value="">Todas las incubadoras</option>

                            @foreach($incubadorasList as $incubadora)
                                <option value="{{ $incubadora->id }}">
                                    {{ $incubadora->nombre ?? 'Incubadora #' . $incubadora->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-8 flex gap-3 justify-end">
                    <button type="button"
                            @click="showMicroclimaModal = false"
                            class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                        Cancelar
                    </button>

                    <button type="submit"
                            @click="showMicroclimaModal = false"
                            class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PDF BIOLÓGICO --}}
    <div x-show="showBiologicoModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
         style="display: none;">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 text-gray-800"
             @click.away="showBiologicoModal = false">

            <div class="bg-[#1c607a] text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M7 8h10M7 12h10M7 16h6M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                    </svg>
                    Filtros: PDF Biológico
                </h3>

                <button type="button"
                        @click="showBiologicoModal = false"
                        class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('super_admin.reportes.biologico.pdf') }}" method="GET" class="p-6">
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Fecha de inicio
                        </label>

                        <input type="date"
                               name="fecha_inicio"
                               class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Fecha de fin
                        </label>

                        <input type="date"
                               name="fecha_fin"
                               class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                            Lote
                        </label>

                        <select name="lote_id"
                                class="w-full rounded-xl border border-gray-300 py-2 px-3 focus:border-[#3bb49c] focus:ring-1 focus:ring-[#3bb49c] bg-white">
                            <option value="">Todos los lotes</option>

                            @foreach($lotesList as $lote)
                                <option value="{{ $lote->id }}">
                                    {{ $lote->codigo_lote ?? 'Lote #' . $lote->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-8 flex gap-3 justify-end">
                    <button type="button"
                            @click="showBiologicoModal = false"
                            class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                        Cancelar
                    </button>

                    <button type="submit"
                            @click="showBiologicoModal = false"
                            class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </button>
                </div>
            </form>
        </div>
    </div>

@endif
