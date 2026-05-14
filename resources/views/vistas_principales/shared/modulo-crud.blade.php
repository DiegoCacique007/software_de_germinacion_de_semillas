@php
    $title = $title ?? 'Módulo';
    $subtitle = $subtitle ?? 'Administración del módulo';
    $items = collect($items ?? []);
    $routeBase = $routeBase ?? '';
    $entitySingular = $entitySingular ?? 'Registro';
    $entityPlural = $entityPlural ?? 'Registros';
    $columns = $columns ?? [];
    $fields = $fields ?? [];
@endphp

<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <div class="flex-1 w-full relative">
            <div
                x-data="crudModule()"
                x-cloak
                class="p-6 lg:p-10"
            >
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 animate-fade-up">
                    <div>
                        <h2 class="text-3xl font-extrabold text-[#1c607a]">{{ $title }}</h2>
                        <p class="text-gray-500 mt-1">{{ $subtitle }}</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate = true"
                        class="mt-4 sm:mt-0 btn-brand text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nuevo {{ $entitySingular }}
                    </button>
                </div>

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: `{!! addslashes(session('success')) !!}`,
                                confirmButtonColor: '#3bb49c',
                                width: '18em',
                                customClass: { popup: 'text-sm' },
                                confirmButtonText: 'Aceptar'
                            });
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Error!',
                                text: `{!! addslashes(session('error')) !!}`,
                                confirmButtonColor: '#1c607a',
                                width: '18em',
                                customClass: { popup: 'text-sm' },
                                confirmButtonText: 'Aceptar'
                            });
                        });
                    </script>
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
                    
                    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <span class="text-sm text-gray-500 font-medium">Mostrar</span>
                            <select x-model="perPage" class="input-brand rounded-lg text-sm py-1.5 px-3 bg-white">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                            </select>
                            <span class="text-sm text-gray-500 font-medium">registros</span>
                        </div>

                        <div class="relative w-full sm:w-72">
                            <input type="text" x-model="search" placeholder="Buscar..." class="input-brand w-full rounded-lg py-2 pl-10 pr-4 text-sm bg-white">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100" x-ref="table">
                            <thead class="bg-[#1c607a]/5">
                            <tr>
                                @foreach($columns as $column)
                                    <th class="px-6 py-4 text-left text-xs font-black text-[#1c607a] uppercase tracking-wider">
                                        {{ $column['label'] }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-4 text-center text-xs font-black text-[#1c607a] uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-50">
                            @forelse($items as $item)
                                <tr class="item-row hover:bg-[#3bb49c]/5 transition-colors duration-200 group">
                                    @foreach($columns as $column)
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ data_get($item, $column['key']) ?? '—' }}
                                        </td>
                                    @endforeach

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <button
                                                type="button"
                                                data-action="{{ route($routeBase . '.update', $item) }}"
                                                @foreach($fields as $field)
                                                    data-{{ $field['name'] }}="{{ data_get($item, $field['edit_key'] ?? $field['name']) }}"
                                                @endforeach
                                                @click="loadEdit($event.currentTarget.dataset)"
                                                class="text-[#3bb49c] hover:text-[#1c607a] bg-[#3bb49c]/10 hover:bg-[#3bb49c]/20 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1"
                                            >
                                                Editar
                                            </button>

                                            <form action="{{ route($routeBase . '.destroy', $item) }}" method="POST" class="inline m-0" @submit.prevent="confirmDelete($event)">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
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
                                    <td colspan="{{ count($columns) + 1 }}" class="px-6 py-8 text-center text-gray-500">
                                        No hay {{ strtolower($entityPlural) }} registrados.
                                    </td>
                                </tr>
                            @endforelse
                            <tr x-show="filteredRows.length === 0 && rows.length > 0" x-cloak>
                                <td colspan="{{ count($columns) + 1 }}" class="px-6 py-8 text-center text-gray-500">
                                    No se encontraron resultados para la búsqueda.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50" x-show="totalPages > 1" x-cloak>
                        <span class="text-sm text-gray-600 font-medium">
                            Mostrando página <span x-text="currentPage"></span> de <span x-text="totalPages"></span>
                        </span>
                        <div class="flex gap-2">
                            <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'">Anterior</button>
                            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'">Siguiente</button>
                        </div>
                    </div>
                </div>

                {{-- OFFCANVAS CREAR --}}
                <div x-show="openCreate"
                     class="fixed inset-0 z-50 flex justify-end glass-modal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-cloak>

                    <div class="bg-white w-full max-w-md h-full shadow-2xl overflow-y-auto flex flex-col"
                         @click.away="openCreate = false"
                         x-transition:enter="transition transform ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition transform ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">

                        <div class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] p-6 text-white flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-extrabold">Nuevo {{ $entitySingular }}</h3>
                                <p class="text-sm opacity-90 mt-1">Registra un nuevo {{ strtolower($entitySingular) }}.</p>
                            </div>
                            <button @click="openCreate = false" class="text-white hover:text-gray-200 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form action="{{ route($routeBase . '.store') }}" method="POST" class="p-8 space-y-5 flex-1 flex flex-col" @submit="confirmSubmit($event, 'registrar')">
                            @csrf
                            
                            <div class="flex-1 space-y-5">
                            @foreach($fields as $field)
                                <div>
                                    <label class="block text-sm font-bold text-[#1c607a] mb-1">{{ $field['label'] }}</label>

                                    @if(($field['type'] ?? 'text') === 'textarea')
                                        <textarea
                                            name="{{ $field['name'] }}"
                                            @if(!empty($field['required'])) required @endif
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                            rows="4"
                                        ></textarea>

                                    @elseif(($field['type'] ?? 'text') === 'select')
                                        <select
                                            name="{{ $field['name'] }}"
                                            @if(!empty($field['required'])) required @endif
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white"
                                        >
                                            <option value="">Seleccione una opción</option>
                                            @foreach(($field['options'] ?? []) as $option)
                                                <option value="{{ data_get($option, $field['option_value'] ?? 'id') }}">
                                                    {{ data_get($option, $field['option_label'] ?? 'nombre') }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @else
                                        <input
                                            type="{{ $field['type'] ?? 'text' }}"
                                            name="{{ $field['name'] }}"
                                            @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                            @if(!empty($field['required'])) required @endif
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                        >
                                    @endif
                                </div>
                            @endforeach
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
                                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- OFFCANVAS EDITAR --}}
                <div x-show="openEdit"
                     class="fixed inset-0 z-50 flex justify-end glass-modal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     x-cloak>

                    <div class="bg-white w-full max-w-md h-full shadow-2xl overflow-y-auto flex flex-col"
                         @click.away="closeEdit()"
                         x-transition:enter="transition transform ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition transform ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">

                        <div class="bg-[#f0f6f6] p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-extrabold text-[#1c607a]">Editar {{ $entitySingular }}</h3>
                                <p class="text-sm text-gray-500 mt-1">Modifica la información del registro.</p>
                            </div>
                            <button @click="closeEdit()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form :action="editAction" method="POST" class="p-8 space-y-5 flex-1 flex flex-col" @submit="confirmSubmit($event, 'editar')">
                            @csrf
                            @method('PUT')
                            
                            <div class="flex-1 space-y-5">
                            @foreach($fields as $field)
                                <div>
                                    <label class="block text-sm font-bold text-[#1c607a] mb-1">{{ $field['label'] }}</label>

                                    @if(($field['type'] ?? 'text') === 'textarea')
                                        <textarea
                                            name="{{ $field['name'] }}"
                                            x-model="itemToEdit.{{ $field['name'] }}"
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                            rows="4"
                                        ></textarea>

                                    @elseif(($field['type'] ?? 'text') === 'select')
                                        <select
                                            name="{{ $field['name'] }}"
                                            x-model="itemToEdit.{{ $field['name'] }}"
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700 bg-white"
                                        >
                                            <option value="">Seleccione una opción</option>
                                            @foreach(($field['options'] ?? []) as $option)
                                                <option value="{{ data_get($option, $field['option_value'] ?? 'id') }}">
                                                    {{ data_get($option, $field['option_label'] ?? 'nombre') }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @else
                                        <input
                                            type="{{ $field['type'] ?? 'text' }}"
                                            name="{{ $field['name'] }}"
                                            x-model="itemToEdit.{{ $field['name'] }}"
                                            @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                            class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                        >
                                    @endif
                                </div>
                            @endforeach
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
                                <button type="button" @click="closeEdit()" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold">
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function crudModule() {
            return {
                openCreate: false,
                openEdit: false,
                itemToEdit: {
                    @foreach($fields as $field)
                        {{ $field["name"] }}: "",
                    @endforeach
                },
                editAction: "",
                
                search: '',
                perPage: 10,
                currentPage: 1,
                rows: [],
                filteredRows: [],

                init() {
                    this.$nextTick(() => {
                        this.rows = Array.from(this.$refs.table.querySelectorAll('tbody tr.item-row'));
                        this.updateTable();
                    });

                    this.$watch('search', () => { this.currentPage = 1; this.updateTable(); });
                    this.$watch('perPage', () => { this.currentPage = 1; this.updateTable(); });
                    this.$watch('currentPage', () => { this.updateTable(); });
                },

                updateTable() {
                    if(!this.rows) return;
                    let s = this.search.toLowerCase();
                    this.filteredRows = this.rows.filter(row => row.innerText.toLowerCase().includes(s));
                    let start = (this.currentPage - 1) * this.perPage;
                    let end = start + parseInt(this.perPage);
                    
                    this.rows.forEach(row => row.style.display = 'none');
                    this.filteredRows.slice(start, end).forEach(row => row.style.display = '');
                },

                get totalPages() {
                    return Math.ceil(this.filteredRows.length / this.perPage) || 1;
                },

                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                loadEdit(data) {
                    this.itemToEdit = {
                        @foreach($fields as $field)
                            {{ $field["name"] }}: data.{{ $field["name"] }} || "",
                        @endforeach
                    };
                    this.editAction = data.action || "";
                    this.openEdit = true;
                },

                closeEdit() {
                    this.openEdit = false;
                    this.itemToEdit = {
                        @foreach($fields as $field)
                            {{ $field["name"] }}: "",
                        @endforeach
                    };
                    this.editAction = "";
                },

                confirmDelete(event) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#1c607a',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        width: '18em',
                        customClass: { popup: 'text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            event.target.submit();
                        }
                    });
                },

                confirmSubmit(event, action) {
                    event.preventDefault();
                    let textAction = action === 'registrar' ? 'registrar este elemento' : 'guardar estos cambios';
                    
                    Swal.fire({
                        title: '¿Confirmar?',
                        text: `¿Deseas ${textAction}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3bb49c',
                        cancelButtonColor: '#1c607a',
                        confirmButtonText: 'Sí, confirmar',
                        cancelButtonText: 'Cancelar',
                        width: '18em',
                        customClass: { popup: 'text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            event.target.submit();
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
