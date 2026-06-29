@php
    use Illuminate\Support\Facades\Route;

    $title = $title ?? 'Módulo';
    $subtitle = $subtitle ?? 'Administración del módulo';
    $items = collect($items ?? []);
    $routeBase = $routeBase ?? '';
    $entitySingular = $entitySingular ?? 'Registro';
    $entityPlural = $entityPlural ?? 'Registros';
    $columns = $columns ?? [];
    $fields = $fields ?? [];

    $canStore = $routeBase !== '' && Route::has($routeBase . '.store');
    $canUpdate = $routeBase !== '' && Route::has($routeBase . '.update');
    $canDestroy = $routeBase !== '' && Route::has($routeBase . '.destroy');
@endphp

<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .btn-brand {
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px rgba(28, 96, 122, 0.12);
        }

        .btn-brand:hover {
            box-shadow: 0 14px 24px rgba(28, 96, 122, 0.18);
        }

        .input-brand {
            border: 1px solid #d9e4e8;
            transition: all 0.2s ease;
        }

        .input-brand:focus {
            border-color: #3bb49c;
            box-shadow: 0 0 0 4px rgba(59, 180, 156, 0.12);
            outline: none;
        }

        .glass-modal {
            backdrop-filter: blur(6px);
            background-color: rgba(15, 39, 46, 0.58);
        }

        .filter-toolbar {
            background:
                radial-gradient(circle at top right, rgba(59, 180, 156, 0.11), transparent 36%),
                linear-gradient(135deg, rgba(240, 246, 246, 0.92), rgba(255, 255, 255, 0.96));
        }

        .search-shell {
            position: relative;
            width: 100%;
        }

        .search-input-enhanced {
            width: 100%;
            border: 1px solid rgba(28, 96, 122, 0.16);
            background: #ffffff;
            border-radius: 1rem;
            padding: 0.78rem 2.75rem 0.78rem 3.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #1f2937;
            box-shadow: 0 10px 24px rgba(28, 96, 122, 0.08);
            transition: all 0.22s ease;
        }

        .search-input-enhanced::placeholder {
            color: #7a8a91;
            font-weight: 600;
        }

        .search-input-enhanced:focus {
            outline: none;
            border-color: #3bb49c;
            box-shadow:
                0 0 0 4px rgba(59, 180, 156, 0.14),
                0 14px 28px rgba(28, 96, 122, 0.12);
        }

        .search-icon-box {
            position: absolute;
            left: 0.55rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 0.85rem;
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 18px rgba(28, 96, 122, 0.20);
            pointer-events: none;
        }

        .search-clear-btn {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.9rem;
            height: 1.9rem;
            border-radius: 0.7rem;
            background: rgba(28, 96, 122, 0.08);
            color: #1c607a;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s ease;
        }

        .search-clear-btn:hover {
            background: #1c607a;
            color: #ffffff;
        }

        .search-result-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.75rem;
            border-radius: 9999px;
            background: rgba(59, 180, 156, 0.10);
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, 0.22);
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="w-full relative min-h-screen bg-[#f0f6f6] font-sans">
        <div
            x-data="crudModule()"
            x-cloak
            class="p-6 lg:p-10"
        >
            {{-- ENCABEZADO --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-[#1c607a]">
                        {{ $title }}
                    </h2>

                    <p class="text-gray-500 mt-1">
                        {{ $subtitle }}
                    </p>
                </div>

                @if($canStore)
                    <button
                        type="button"
                        @click="openCreate = true"
                        class="mt-4 sm:mt-0 btn-brand text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>

                        Nuevo {{ $entitySingular }}
                    </button>
                @endif
            </div>

            {{-- MENSAJES --}}
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
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <p class="font-bold text-red-700 mb-2">
                        Corrige los siguientes errores:
                    </p>

                    <ul class="list-disc pl-5 text-red-600 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CONTENEDOR DE REGISTROS --}}
            <div class="bg-white rounded-2xl shadow-[0_15px_40px_-15px_rgba(28,96,122,0.15)] border border-gray-100 overflow-hidden">

                {{-- FILTROS --}}
                <div class="filter-toolbar p-5 border-b border-gray-100">
                    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full xl:w-auto">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500 font-bold">
                                    Mostrar
                                </span>

                                <select x-model="perPage" class="input-brand rounded-xl text-sm py-2 px-3 bg-white font-bold text-[#1c607a]">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                </select>

                                <span class="text-sm text-gray-500 font-bold">
                                    registros
                                </span>
                            </div>

                            <div class="search-result-pill">
                                <span class="w-2 h-2 rounded-full bg-[#3bb49c]"></span>
                                <span x-text="filteredRows.length"></span>
                                resultados
                            </div>
                        </div>

                        <div class="search-shell w-full xl:w-[390px]">
                            <input
                                type="text"
                                x-model="search"
                                placeholder="Buscar {{ strtolower($entityPlural) }}..."
                                class="search-input-enhanced"
                                aria-label="Buscar registros"
                            >

                            <div class="search-icon-box">
                                <svg class="w-5 h-5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2.4"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <button
                                type="button"
                                x-show="search.length > 0"
                                x-cloak
                                @click="search = ''; currentPage = 1; updateTable();"
                                class="search-clear-btn"
                                aria-label="Limpiar búsqueda"
                                title="Limpiar búsqueda"
                            >
                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2.4"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TARJETAS --}}
                <div class="p-6">
                    <div x-ref="recordsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($items as $item)
                            @php
                                $showPayload = collect($columns)->map(function ($column) use ($item) {
                                    $value = data_get($item, $column['key']);

                                    return [
                                        'label' => $column['label'],
                                        'value' => blank($value) ? '—' : (string) $value,
                                    ];
                                })->values()->all();

                                $editPayload = [
                                    'action' => $canUpdate ? route($routeBase . '.update', $item) : '',
                                    'fields' => collect($fields)->mapWithKeys(function ($field) use ($item) {
                                        $name = $field['name'];
                                        $key = $field['edit_key'] ?? $name;

                                        return [
                                            $name => (string) data_get($item, $key, ''),
                                        ];
                                    })->toArray(),
                                ];

                                $primaryColumn = $columns[0]['key'] ?? null;
                                $secondaryColumn = $columns[1]['key'] ?? null;

                                $primaryValue = $primaryColumn ? data_get($item, $primaryColumn) : null;
                                $secondaryValue = $secondaryColumn ? data_get($item, $secondaryColumn) : null;
                            @endphp

                            <div class="item-row rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                                {{-- TEXTO OCULTO PARA BUSCADOR --}}
                                <div class="hidden">
                                    @foreach($columns as $column)
                                        {{ data_get($item, $column['key']) }}
                                    @endforeach
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-black uppercase tracking-widest text-[#1c607a] mb-1">
                                                {{ $entitySingular }}
                                            </p>

                                            <h3 class="text-lg font-extrabold text-gray-800 truncate">
                                                {{ $primaryValue ?: 'Registro #' . $loop->iteration }}
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1 truncate">
                                                {{ $secondaryValue ?: 'Consulta los datos en ventana flotante' }}
                                            </p>
                                        </div>

                                        <div class="w-11 h-11 rounded-xl bg-[#1c607a]/10 text-[#1c607a] flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-center gap-3">

                                        {{-- VER DATOS --}}
                                        <button
                                            type="button"
                                            @click="openShowModal(@js($showPayload))"
                                            title="Ver datos"
                                            aria-label="Ver datos"
                                            class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[#1c607a]/10 text-[#1c607a] hover:bg-[#1c607a] hover:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        {{-- EDITAR --}}
                                        @if($canUpdate)
                                            <button
                                                type="button"
                                                @click="loadEdit(@js($editPayload))"
                                                title="Editar"
                                                aria-label="Editar"
                                                class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[#3bb49c]/10 text-[#2a9d8f] hover:bg-[#3bb49c] hover:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- BORRAR --}}
                                        @if($canDestroy)
                                            <form
                                                action="{{ route($routeBase . '.destroy', $item) }}"
                                                method="POST"
                                                class="inline-flex m-0"
                                                @submit.prevent="confirmDelete($event)"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    title="Eliminar"
                                                    aria-label="Eliminar"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M10 11v6m4-6v6"/>
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-span-full px-6 py-10 text-center text-gray-500">
                                No hay {{ strtolower($entityPlural) }} registrados.
                            </div>
                        @endforelse
                    </div>

                    <div x-show="filteredRows.length === 0 && rows.length > 0" x-cloak class="px-6 py-10 text-center text-gray-500">
                        No se encontraron resultados para la búsqueda.
                    </div>
                </div>

                {{-- PAGINACIÓN --}}
                <div
                    class="p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50"
                    x-show="totalPages > 1"
                    x-cloak
                >
                    <span class="text-sm text-gray-600 font-medium">
                        Mostrando página <span x-text="currentPage"></span> de <span x-text="totalPages"></span>
                    </span>

                    <div class="flex gap-2">
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                            :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                        >
                            Anterior
                        </button>

                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                            :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>

            {{-- MODAL VER DATOS CENTRADO --}}
            <div
                x-show="openShow"
                x-cloak
                class="fixed inset-0 z-[80] flex items-center justify-center glass-modal px-4"
            >
                <div
                    class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
                    @click.away="closeShowModal()"
                >
                    <div class="bg-[#1c607a] px-6 py-5 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-extrabold">
                                Datos de {{ $entitySingular }}
                            </h3>

                            <p class="text-sm text-white/80 mt-1">
                                Información detallada del registro seleccionado.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="closeShowModal()"
                            class="w-9 h-9 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="field in selectedShow" :key="field.label">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-[11px] font-black uppercase tracking-widest text-[#1c607a] mb-1"
                                       x-text="field.label"></p>

                                    <p class="text-sm font-semibold text-gray-700 break-words"
                                       x-text="field.value"></p>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 flex justify-end border-t border-gray-100 pt-5">
                            <button
                                type="button"
                                @click="closeShowModal()"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL CREAR CENTRADO --}}
            @if($canStore)
                <div
                    x-show="openCreate"
                    x-cloak
                    class="fixed inset-0 z-[90] flex items-center justify-center glass-modal px-4 py-6"
                >
                    <div
                        class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
                        @click.away="openCreate = false"
                    >
                        <div class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] px-6 py-5 text-white flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-extrabold">
                                    Nuevo {{ $entitySingular }}
                                </h3>

                                <p class="text-sm text-white/85 mt-1">
                                    Registra la información solicitada para crear un nuevo {{ strtolower($entitySingular) }}.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="openCreate = false"
                                class="w-9 h-9 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form
                            action="{{ route($routeBase . '.store') }}"
                            method="POST"
                            class="p-6"
                            @submit="confirmSubmit($event, 'registrar')"
                        >
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[62vh] overflow-y-auto pr-1">
                                @foreach($fields as $field)
                                    @php
                                        $fieldType = $field['type'] ?? 'text';
                                    @endphp

                                    <div class="{{ $fieldType === 'textarea' ? 'md:col-span-2' : '' }}">
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                                            {{ $field['label'] }}

                                            @if(!empty($field['required']))
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        @if($fieldType === 'textarea')
                                            <textarea
                                                name="{{ $field['name'] }}"
                                                @if(!empty($field['required'])) required @endif
                                                class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                                rows="4"
                                            ></textarea>

                                        @elseif($fieldType === 'select')
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
                                                type="{{ $fieldType }}"
                                                name="{{ $field['name'] }}"
                                                @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                                @if(!empty($field['required'])) required @endif
                                                class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                            >
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end gap-3 pt-5 border-t border-gray-100 mt-6">
                                <button
                                    type="button"
                                    @click="openCreate = false"
                                    class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors"
                                >
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold"
                                >
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- MODAL EDITAR HORIZONTAL --}}
            @if($canUpdate)
                <div
                    x-show="openEdit"
                    x-cloak
                    class="fixed inset-0 z-[90] flex items-center justify-center glass-modal px-4 py-6"
                >
                    <div
                        class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
                        @click.away="closeEdit()"
                    >
                        <div class="bg-[#f0f6f6] px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-extrabold text-[#1c607a]">
                                    Editar {{ $entitySingular }}
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Modifica la información del registro seleccionado.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="closeEdit()"
                                class="w-9 h-9 rounded-lg hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form
                            :action="editAction"
                            method="POST"
                            class="p-6"
                            @submit="confirmSubmit($event, 'editar')"
                        >
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 max-h-[62vh] overflow-y-auto pr-1">
                                @foreach($fields as $field)
                                    @php
                                        $fieldType = $field['type'] ?? 'text';
                                    @endphp

                                    <div class="{{ $fieldType === 'textarea' ? 'md:col-span-2 lg:col-span-3' : '' }}">
                                        <label class="block text-sm font-bold text-[#1c607a] mb-1">
                                            {{ $field['label'] }}

                                            @if(!empty($field['required']))
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        @if($fieldType === 'textarea')
                                            <textarea
                                                name="{{ $field['name'] }}"
                                                x-model="itemToEdit.{{ $field['name'] }}"
                                                @if(!empty($field['required'])) required @endif
                                                class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                                rows="4"
                                            ></textarea>

                                        @elseif($fieldType === 'select')
                                            <select
                                                name="{{ $field['name'] }}"
                                                x-model="itemToEdit.{{ $field['name'] }}"
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
                                                type="{{ $fieldType }}"
                                                name="{{ $field['name'] }}"
                                                x-model="itemToEdit.{{ $field['name'] }}"
                                                @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                                @if(!empty($field['required'])) required @endif
                                                class="input-brand w-full rounded-xl py-2.5 px-4 text-gray-700"
                                            >
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end gap-3 pt-5 border-t border-gray-100 mt-6">
                                <button
                                    type="button"
                                    @click="closeEdit()"
                                    class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition-colors"
                                >
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn-brand text-white px-6 py-2.5 rounded-xl font-bold"
                                >
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function crudModule() {
            return {
                openCreate: false,
                openEdit: false,

                openShow: false,
                selectedShow: [],

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
                        this.rows = Array.from(this.$refs.recordsContainer.querySelectorAll('.item-row'));
                        this.updateTable();
                    });

                    this.$watch('search', () => {
                        this.currentPage = 1;
                        this.updateTable();
                    });

                    this.$watch('perPage', () => {
                        this.currentPage = 1;
                        this.updateTable();
                    });

                    this.$watch('currentPage', () => {
                        this.updateTable();
                    });
                },

                updateTable() {
                    if (!this.rows) return;

                    let s = this.search.toLowerCase();

                    this.filteredRows = this.rows.filter(row => row.innerText.toLowerCase().includes(s));

                    let start = (this.currentPage - 1) * parseInt(this.perPage);
                    let end = start + parseInt(this.perPage);

                    this.rows.forEach(row => row.style.display = 'none');
                    this.filteredRows.slice(start, end).forEach(row => row.style.display = '');
                },

                get totalPages() {
                    return Math.ceil(this.filteredRows.length / parseInt(this.perPage)) || 1;
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },

                openShowModal(data) {
                    this.selectedShow = data || [];
                    this.openShow = true;
                },

                closeShowModal() {
                    this.openShow = false;
                    this.selectedShow = [];
                },

                loadEdit(data) {
                    this.itemToEdit = data.fields || {};
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
                        text: "No podrás revertir esta acción.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#1c607a',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        width: '18em',
                        customClass: {
                            popup: 'text-sm'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            event.target.submit();
                        }
                    });
                },

                confirmSubmit(event, action) {
                    event.preventDefault();

                    let textAction = action === 'registrar'
                        ? 'registrar este elemento'
                        : 'guardar estos cambios';

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
                        customClass: {
                            popup: 'text-sm'
                        }
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
