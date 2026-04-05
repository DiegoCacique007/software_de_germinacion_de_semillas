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

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="flex min-h-screen bg-[#f0f6f6] font-sans">
        <x-admin-sidebar />

        <div class="flex-1 w-full">
            <div
                x-data="{
                    openCreate: false,
                    openEdit: false,
                    especieToEdit: { id: '', nombre_comun: '', nombre_cientifico: '', familia: '', descripcion: '', observaciones: '' },
                    editAction: '',

                    loadEdit(data) {
                        this.especieToEdit = {
                            id: data.id || '',
                            nombre_comun: data.nombrecomun || '',
                            nombre_cientifico: data.nombrecientifico || '',
                            familia: data.familia || '',
                            descripcion: data.descripcion || '',
                            observaciones: data.observaciones || ''
                        };
                        this.editAction = data.action || '';
                        this.openEdit = true;
                    },

                    closeEdit() {
                        this.openEdit = false;
                        this.especieToEdit = { id: '', nombre_comun: '', nombre_cientifico: '', familia: '', descripcion: '', observaciones: '' };
                        this.editAction = '';
                    }
                }"
                x-cloak
                class="p-6 lg:p-10"
            >
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 animate-fade-up">
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#1c607a]">Especies</h2>
                        <p class="text-gray-500 mt-1">Administra las especies registradas en el sistema.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate = true"
                        class="mt-4 sm:mt-0 btn-brand text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nueva Especie
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
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Nombre común</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Nombre científico</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">Familia</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-[#1c607a] uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-50">
                            @forelse($especies as $especie)
                                <tr class="hover:bg-[#3bb49c]/5 transition-colors duration-200 group">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $especie->nombre_comun }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $especie->nombre_cientifico }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $especie->familia }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <button
                                                type="button"
                                                data-id="{{ $especie->id }}"
                                                data-nombrecomun="{{ $especie->nombre_comun }}"
                                                data-nombrecientifico="{{ $especie->nombre_cientifico }}"
                                                data-familia="{{ $especie->familia }}"
                                                data-descripcion="{{ $especie->descripcion }}"
                                                data-observaciones="{{ $especie->observaciones }}"
                                                data-action="{{ route('administrador.especies.update', $especie->id) }}"
                                                @click="loadEdit($event.currentTarget.dataset)"
                                                class="text-[#3bb49c] hover:text-[#1c607a] bg-[#3bb49c]/10 hover:bg-[#3bb49c]/20 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
                                            >
                                                Editar
                                            </button>

                                            <form action="{{ route('administrador.especies.destroy', $especie->id) }}" method="POST" class="inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('¿Deseas eliminar esta especie?')"
                                                    class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
                                                >
                                                    Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No hay especies registradas.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- MODAL CREAR --}}
                <div x-show="openCreate"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                         @click.away="openCreate = false"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <div class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] p-6 text-white text-center">
                            <h3 class="text-2xl font-extrabold">Nueva Especie</h3>
                            <p class="text-sm opacity-90 mt-1">Registra una nueva especie.</p>
                        </div>

                        <form action="{{ route('administrador.especies.store') }}" method="POST" class="p-8 space-y-5">
                            @csrf

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre común</label>
                                <input type="text" name="nombre_comun" required class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre científico</label>
                                <input type="text" name="nombre_cientifico" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Familia</label>
                                <input type="text" name="familia" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Descripción</label>
                                <textarea name="descripcion" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Observaciones</label>
                                <textarea name="observaciones" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Registrar Especie
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- MODAL EDITAR --}}
                <div x-show="openEdit"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center glass-modal p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                         @click.away="closeEdit()"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                        <div class="bg-[#f0f6f6] p-6 border-b border-gray-200">
                            <h3 class="text-2xl font-extrabold text-[#1c607a]">Editar Especie</h3>
                            <p class="text-sm text-gray-500 mt-1">Modifica la información de la especie.</p>
                        </div>

                        <form :action="editAction" method="POST" class="p-8 space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre común</label>
                                <input type="text" name="nombre_comun" x-model="especieToEdit.nombre_comun" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Nombre científico</label>
                                <input type="text" name="nombre_cientifico" x-model="especieToEdit.nombre_cientifico" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Familia</label>
                                <input type="text" name="familia" x-model="especieToEdit.familia" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Descripción</label>
                                <textarea name="descripcion" x-model="especieToEdit.descripcion" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#1c607a] mb-1">Observaciones</label>
                                <textarea name="observaciones" x-model="especieToEdit.observaciones" class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"></textarea>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-2">
                                <button type="button" @click="closeEdit()" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
