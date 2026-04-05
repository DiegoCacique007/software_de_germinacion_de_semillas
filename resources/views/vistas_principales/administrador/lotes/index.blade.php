<x-app-layout>
    <style>
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes shine {
            0% { transform: translateX(-140%) skewX(-20deg); }
            100% { transform: translateX(220%) skewX(-20deg); }
        }

        .animate-fade-up {
            animation: fadeUp 0.8s ease-out forwards;
        }

        .btn-brand {
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(28, 96, 122, 0.15);
        }

        .btn-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(28, 96, 122, 0.25);
        }

        .btn-brand::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg);
        }

        .btn-brand:hover::before {
            animation: shine 0.8s ease;
        }

        .input-brand {
            border: 1px solid #d9e4e8;
            transition: all 0.3s ease;
        }

        .input-brand:focus {
            border-color: #3bb49c;
            box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.15);
            outline: none;
        }

        .glass-modal {
            backdrop-filter: blur(8px);
            background-color: rgba(15, 39, 46, 0.6);
        }

        [x-cloak] { display: none !important; }
    </style>

    <div class="flex min-h-screen bg-[#f0f6f6] font-sans">
        <x-admin-sidebar />

        <div class="flex-1 w-full">
            <div
                x-data="{
                    openCreate: false,
                    openEdit: false,
                    loteToEdit: {
                        id: '',
                        posicion_incubadora_id: '',
                        especie_id: '',
                        codigo_lote: '',
                        fecha_siembra: '',
                        fecha_inicio: '',
                        fecha_fin: '',
                        estado_lote_id: '',
                        observaciones: ''
                    },
                    editAction: '',

                    loadEdit(data) {
                        this.loteToEdit = {
                            id: data.id || '',
                            posicion_incubadora_id: data.posicion || '',
                            especie_id: data.especie || '',
                            codigo_lote: data.codigo || '',
                            fecha_siembra: data.fechasiembra || '',
                            fecha_inicio: data.fechainicio || '',
                            fecha_fin: data.fechafin || '',
                            estado_lote_id: data.estado || '',
                            observaciones: data.observaciones || ''
                        };
                        this.editAction = data.action || '';
                        this.openEdit = true;
                    },

                    closeEdit() {
                        this.openEdit = false;
                        this.editAction = '';
                    }
                }"
                x-cloak
                class="p-6 lg:p-10"
            >
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 animate-fade-up">
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#1c607a]">Lotes</h2>
                        <p class="text-gray-500 mt-1">Administra los lotes sembrados dentro de las incubadoras.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate = true"
                        class="mt-4 sm:mt-0 btn-brand text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nuevo Lote
                    </button>
                </div>

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms
                         class="mb-6 p-4 bg-[#3bb49c]/10 border-l-4 border-[#3bb49c] rounded-r-lg flex justify-between items-center shadow-sm animate-fade-up">
                        <div class="flex items-center gap-3 text-[#1c607a] font-semibold">
                            {{ session('success') }}
                        </div>
                        <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">✕</button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms
                         class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex justify-between items-center shadow-sm animate-fade-up">
                        <div class="flex items-center gap-3 text-red-700 font-semibold">
                            {{ session('error') }}
                        </div>
                        <button type="button" @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">✕</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm animate-fade-up">
                        <p class="font-bold text-red-700 mb-2">Corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 text-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-[0_15px_40px_-15px_rgba(28,96,122,0.15)] border border-gray-100 overflow-hidden animate-fade-up">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-[#1c607a]/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Especie</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Posición</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Fecha siembra</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-[#1c607a] uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-50">
                            @forelse($lotes as $lote)
                                <tr class="hover:bg-[#3bb49c]/5 transition-colors duration-200 group">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $lote->codigo_lote }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $lote->especie->nombre_comun ?? 'Sin especie' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $lote->posicion->incubadora->nombre ?? 'Sin incubadora' }}
                                        - Pos. {{ $lote->posicion->numero_posicion ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $lote->fecha_siembra }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $lote->estado->nombre ?? 'Sin estado' }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <button
                                                type="button"
                                                data-id="{{ $lote->id }}"
                                                data-posicion="{{ $lote->posicion_incubadora_id }}"
                                                data-especie="{{ $lote->especie_id }}"
                                                data-codigo="{{ $lote->codigo_lote }}"
                                                data-fechasiembra="{{ $lote->fecha_siembra }}"
                                                data-fechainicio="{{ $lote->fecha_inicio }}"
                                                data-fechafin="{{ $lote->fecha_fin }}"
                                                data-estado="{{ $lote->estado_lote_id }}"
                                                data-observaciones="{{ $lote->observaciones }}"
                                                data-action="{{ route('administrador.lotes.update', $lote->id) }}"
                                                @click="loadEdit($event.currentTarget.dataset)"
                                                class="text-[#3bb49c] hover:text-[#1c607a] bg-[#3bb49c]/10 hover:bg-[#3bb49c]/20 px-3 py-1.5 rounded-lg transition-all"
                                            >
                                                Editar
                                            </button>

                                            <form action="{{ route('administrador.lotes.destroy', $lote->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('¿Deseas eliminar este lote?')"
                                                    class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all"
                                                >
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No hay lotes registrados.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- MODAL CREAR --}}
                <div x-show="openCreate"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.away="openCreate = false">
                        <div class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] p-6 text-white text-center">
                            <h3 class="text-2xl font-extrabold">Nuevo Lote</h3>
                            <p class="text-sm opacity-90 mt-1">Registra un lote nuevo.</p>
                        </div>

                        <form action="{{ route('administrador.lotes.store') }}" method="POST" class="p-8 space-y-5">
                            @csrf

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Posición de incubadora</label>
                                <select name="posicion_incubadora_id" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione una posición</option>
                                    @foreach($posiciones as $posicion)
                                        <option value="{{ $posicion->id }}">
                                            {{ $posicion->incubadora->nombre ?? 'Sin incubadora' }} - Pos. {{ $posicion->numero_posicion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Especie</label>
                                <select name="especie_id" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione una especie</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id }}">{{ $especie->nombre_comun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Código de lote</label>
                                <input type="text" name="codigo_lote" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de siembra</label>
                                <input type="date" name="fecha_siembra" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de inicio</label>
                                <input type="date" name="fecha_inicio" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de fin</label>
                                <input type="date" name="fecha_fin" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Estado</label>
                                <select name="estado_lote_id" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Observaciones</label>
                                <textarea name="observaciones" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">Cancelar</button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">Registrar Lote</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL EDITAR --}}
                <div x-show="openEdit"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.away="closeEdit()">
                        <div class="bg-[#f0f6f6] p-6 border-b border-gray-200">
                            <h3 class="text-2xl font-extrabold text-[#1c607a]">Editar Lote</h3>
                            <p class="text-sm text-gray-500 mt-1">Modifica la información del lote.</p>
                        </div>

                        <form :action="editAction" method="POST" class="p-8 space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Posición de incubadora</label>
                                <select name="posicion_incubadora_id" x-model="loteToEdit.posicion_incubadora_id" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione una posición</option>
                                    @foreach($posiciones as $posicion)
                                        <option value="{{ $posicion->id }}">
                                            {{ $posicion->incubadora->nombre ?? 'Sin incubadora' }} - Pos. {{ $posicion->numero_posicion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Especie</label>
                                <select name="especie_id" x-model="loteToEdit.especie_id" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione una especie</option>
                                    @foreach($especies as $especie)
                                        <option value="{{ $especie->id }}">{{ $especie->nombre_comun }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Código de lote</label>
                                <input type="text" name="codigo_lote" x-model="loteToEdit.codigo_lote" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de siembra</label>
                                <input type="date" name="fecha_siembra" x-model="loteToEdit.fecha_siembra" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de inicio</label>
                                <input type="date" name="fecha_inicio" x-model="loteToEdit.fecha_inicio" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Fecha de fin</label>
                                <input type="date" name="fecha_fin" x-model="loteToEdit.fecha_fin" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Estado</label>
                                <select name="estado_lote_id" x-model="loteToEdit.estado_lote_id" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white">
                                    <option value="">Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Observaciones</label>
                                <textarea name="observaciones" x-model="loteToEdit.observaciones" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="closeEdit()" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">Cancelar</button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
