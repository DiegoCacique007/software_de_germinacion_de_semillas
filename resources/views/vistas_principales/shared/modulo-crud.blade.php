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

    $emptyForm = collect($fields)->mapWithKeys(fn($field) => [$field['name'] => ''])->toArray();
    $oldForm = collect($fields)->mapWithKeys(fn($field) => [$field['name'] => old($field['name'], '')])->toArray();

    $longFields = collect($fields)
        ->filter(fn($field) => ($field['type'] ?? 'text') === 'textarea')
        ->pluck('name')
        ->toArray();
@endphp

<x-app-layout>

    <style>
        [x-cloak] { display: none !important; }

        .crud-btn {
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
            box-shadow: 0 10px 24px rgba(28, 96, 122, .16);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .crud-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(28, 96, 122, .22);
        }

        .crud-input {
            width: 100%;
            border: 1px solid #d8e5e8;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .crud-input:focus {
            outline: none;
            border-color: #3bb49c;
            box-shadow: 0 0 0 4px rgba(59, 180, 156, .12);
        }

        .crud-overlay {
            background: rgba(39, 52, 65, .46);
            backdrop-filter: blur(3px);
        }

        .crud-modal-card {
            border: 1px solid rgba(59, 180, 156, .20);
            box-shadow: 0 25px 65px rgba(20, 43, 57, .23);
        }

        .crud-modal-icon {
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
            border: 1px solid rgba(59, 180, 156, .25);
        }

        .crud-secondary-btn {
            background: #fff;
            box-shadow: 0 9px 28px rgba(28, 96, 122, .13);
            transition: transform .18s ease, box-shadow .18s ease, color .18s ease;
        }

        .crud-secondary-btn:hover {
            transform: translateY(-1px);
            color: #1c607a;
            box-shadow: 0 12px 30px rgba(28, 96, 122, .18);
        }

        .crud-readonly {
            border: 1px solid #dce8e9;
            background: #f7fbfa;
        }

        .crud-scroll::-webkit-scrollbar { width: 7px; height: 7px; }
        .crud-scroll::-webkit-scrollbar-track { background: #eef4f4; border-radius: 10px; }
        .crud-scroll::-webkit-scrollbar-thumb { background: #b5d6d2; border-radius: 10px; }
    </style>


    <div class="min-h-screen bg-[#f0f6f6] p-5 lg:p-8" x-data="crudModule()" x-init="init()" x-cloak>

        {{-- ========================================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================================= --}}

        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-3xl font-extrabold text-[#1c607a]">{{ $title }}</h1>
                <p class="mt-1 text-gray-500">{{ $subtitle }}</p>
            </div>

            @if($canStore)
                <button type="button" @click="openCreateModal()" class="crud-btn inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 font-bold text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>

                    Nuevo {{ $entitySingular }}
                </button>
            @endif

        </div>


        {{-- ========================================================= --}}
        {{-- ERRORES DE VALIDACIÓN --}}
        {{-- ========================================================= --}}

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <p class="mb-2 font-bold text-red-700">Corrige los siguientes errores:</p>

                <ul class="list-disc space-y-1 pl-5 text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- CONTENEDOR --}}
        {{-- ========================================================= --}}

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_15px_40px_-15px_rgba(28,96,122,.15)]">

            {{-- FILTROS --}}
            <div class="border-b border-gray-100 bg-gradient-to-r from-[#f4f9f9] to-white p-5">

                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                    <div class="flex flex-wrap items-center gap-3">

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-500">Mostrar</span>

                            <select x-model="perPage" class="crud-input w-auto rounded-xl px-3 py-2 text-sm font-bold text-[#1c607a]">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                            <span class="text-sm font-bold text-gray-500">registros</span>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-full border border-[#3bb49c]/20 bg-[#3bb49c]/10 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-[#1c607a]">
                            <span class="h-2 w-2 rounded-full bg-[#3bb49c]"></span>
                            <span x-text="filteredRows.length"></span>
                            resultados
                        </div>

                    </div>


                    {{-- BUSCADOR --}}
                    <div class="relative w-full xl:w-[400px]">

                        <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#1c607a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>

                        <input type="search" x-model="search" placeholder="Buscar {{ strtolower($entityPlural) }}..." class="crud-input rounded-xl py-3 pl-12 pr-12 text-sm font-semibold text-gray-700">

                        <button type="button" x-show="search.length > 0" x-cloak @click="clearSearch()" title="Limpiar búsqueda" class="absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                            ✕
                        </button>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- TABLA --}}
            {{-- ===================================================== --}}

            <div class="p-4 sm:p-5">

                <div class="crud-scroll overflow-x-auto rounded-xl border border-gray-200">

                    <table class="w-full min-w-[1050px] text-left text-sm">

                        <thead class="bg-gradient-to-r from-[#1c607a] to-[#3bb49c] text-white">

                        <tr>
                            @foreach($columns as $column)
                                <th class="px-5 py-4 font-bold">{{ $column['label'] }}</th>
                            @endforeach

                            <th class="px-5 py-4 text-center font-bold">Acciones</th>
                        </tr>

                        </thead>


                        <tbody x-ref="recordsContainer" class="divide-y divide-gray-100 bg-white">

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


                                $searchText = collect($columns)
                                    ->map(fn($column) => data_get($item, $column['key']))
                                    ->filter()
                                    ->implode(' ');
                            @endphp


                            <tr class="item-row align-top transition-colors hover:bg-[#f2faf8]" data-search="{{ $searchText }}">

                                @foreach($columns as $column)

                                    @php
                                        $value = data_get($item, $column['key']);
                                        $isLong = in_array($column['key'], $longFields, true);
                                    @endphp

                                    <td class="px-5 py-4 text-gray-700 {{ $isLong ? 'min-w-[260px] max-w-[400px]' : 'min-w-[155px]' }}">
                                        <div class="whitespace-normal break-words leading-6">
                                            {{ blank($value) ? '—' : $value }}
                                        </div>
                                    </td>

                                @endforeach


                                {{-- ACCIONES --}}
                                <td class="min-w-[150px] px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- VER --}}
                                        <button type="button" @click="openShowModal(@js($showPayload))" title="Ver datos" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#1c607a]/10 text-[#1c607a] transition hover:bg-[#1c607a] hover:text-white">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>

                                        </button>


                                        {{-- EDITAR --}}
                                        @if($canUpdate)

                                            <button type="button" @click="openEditModal(@js($editPayload))" title="Editar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#3bb49c]/10 text-[#2a9d8f] transition hover:bg-[#3bb49c] hover:text-white">

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>

                                            </button>

                                        @endif


                                        {{-- ELIMINAR --}}
                                        @if($canDestroy)

                                            <form action="{{ route($routeBase . '.destroy', $item) }}" method="POST" @submit.prevent="askDeleteConfirmation($event)">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Eliminar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#1c607a]/5 text-[#1c607a] transition hover:bg-[#1c607a] hover:text-white">

                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16"/>
                                                    </svg>

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="px-6 py-12 text-center text-gray-500">
                                    No hay {{ strtolower($entityPlural) }} registrados.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>


                <div x-show="filteredRows.length === 0 && rows.length > 0" x-cloak class="py-10 text-center text-gray-500">
                    No se encontraron resultados para la búsqueda.
                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- PAGINACIÓN --}}
            {{-- ===================================================== --}}

            <div x-show="totalPages > 1" x-cloak class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 bg-[#f7fbfa] p-4 sm:flex-row">

                <span class="text-sm font-medium text-gray-600">
                    Página
                    <strong class="text-[#1c607a]" x-text="currentPage"></strong>
                    de
                    <strong class="text-[#1c607a]" x-text="totalPages"></strong>
                </span>


                <div class="flex gap-2">

                    <button type="button" @click="previousPage()" :disabled="currentPage === 1" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">
                        Anterior
                    </button>

                    <button type="button" @click="nextPage()" :disabled="currentPage === totalPages" class="rounded-lg bg-[#1c607a] px-4 py-2 text-sm font-semibold text-white hover:bg-[#164d62] disabled:cursor-not-allowed disabled:opacity-40">
                        Siguiente
                    </button>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MODAL VER --}}
        {{-- MISMO FORMATO QUE EDITAR --}}
        {{-- ========================================================= --}}

        <div x-show="showModalOpen" x-cloak @keydown.escape.window="closeShowModal()" @click.self="closeShowModal()" class="crud-overlay fixed inset-0 z-[90] flex items-center justify-center p-4">

            <div x-show="showModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="crud-modal-card w-full max-w-[700px] overflow-hidden rounded-[26px] bg-white">


                {{-- CABECERA --}}
                <div class="px-7 pb-5 pt-6 text-center">

                    <div class="crud-modal-icon mx-auto mb-4 flex h-[58px] w-[58px] items-center justify-center rounded-[19px] text-[#1c607a]">

                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>

                    </div>


                    <h2 class="text-[21px] font-extrabold tracking-tight text-slate-700">
                        Ver {{ $entitySingular }}
                    </h2>


                    <p class="mx-auto mt-2 max-w-[450px] text-sm font-medium leading-5 text-slate-500">
                        Consulta la información completa del registro seleccionado.
                    </p>

                </div>


                {{-- DATOS --}}
                <div class="crud-scroll grid max-h-[52vh] grid-cols-1 gap-4 overflow-y-auto border-y border-gray-100 px-7 py-5 md:grid-cols-2">

                    <template x-for="(field, index) in selectedShow" :key="index">

                        <div :class="field.value.length > 120 ? 'md:col-span-2' : ''">

                            <label class="mb-1.5 flex items-center gap-2 text-sm font-bold text-[#1c607a]">

                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#1c607a]/8 text-[9px] font-black text-[#1c607a]" x-text="String(index + 1).padStart(2, '0')"></span>

                                <span x-text="field.label"></span>

                            </label>


                            <div class="crud-readonly min-h-[45px] rounded-xl px-4 py-2.5">

                                <p class="whitespace-pre-line break-words text-sm font-medium leading-6 text-slate-600" x-text="field.value"></p>

                            </div>

                        </div>

                    </template>

                </div>


                {{-- BOTÓN --}}
                <div class="flex items-center justify-center px-7 py-5">

                    <button type="button" @click="closeShowModal()" class="crud-secondary-btn min-w-[140px] rounded-xl px-5 py-2.5 text-sm font-medium text-slate-700">
                        Cerrar
                    </button>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MODAL CREAR / EDITAR --}}
        {{-- ========================================================= --}}

        <div x-show="formModalOpen" x-cloak @keydown.escape.window="closeFormModal()" @click.self="closeFormModal()" class="crud-overlay fixed inset-0 z-[100] flex items-center justify-center p-4">

            <div x-show="formModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="crud-modal-card w-full max-w-[700px] overflow-hidden rounded-[26px] bg-white">


                {{-- CABECERA --}}
                <div class="px-7 pb-5 pt-6 text-center">

                    <div class="crud-modal-icon mx-auto mb-4 flex h-[58px] w-[58px] items-center justify-center rounded-[19px] text-[#1c607a]">

                        {{-- CREAR --}}
                        <svg x-show="formMode === 'create'" class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>


                        {{-- EDITAR --}}
                        <svg x-show="formMode === 'edit'" class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>

                    </div>


                    <h2 class="text-[21px] font-extrabold tracking-tight text-slate-700"
                        x-text="formMode === 'create' ? 'Nuevo {{ $entitySingular }}' : 'Editar {{ $entitySingular }}'">
                    </h2>


                    <p class="mx-auto mt-2 max-w-[450px] text-sm font-medium leading-5 text-slate-500"
                       x-text="formMode === 'create'
                            ? 'Ingresa la información necesaria para registrar un nuevo elemento.'
                            : 'Modifica la información del registro seleccionado.'">
                    </p>

                </div>


                {{-- FORMULARIO --}}
                <form :action="formAction" method="POST" @submit.prevent="askFormConfirmation($event)">
                    @csrf

                    <input type="hidden" name="_method" value="PUT" :disabled="formMode !== 'edit'">
                    <input type="hidden" name="_crud_mode" :value="formMode">
                    <input type="hidden" name="_crud_edit_action" :value="formMode === 'edit' ? formAction : ''">


                    <div class="crud-scroll grid max-h-[52vh] grid-cols-1 gap-4 overflow-y-auto border-y border-gray-100 px-7 py-5 md:grid-cols-2">

                        @foreach($fields as $field)

                            @php
                                $fieldName = $field['name'];
                                $fieldType = $field['type'] ?? 'text';
                                $required = $field['required'] ?? false;
                            @endphp


                            <div class="{{ $fieldType === 'textarea' ? 'md:col-span-2' : '' }}">

                                <label for="{{ $fieldName }}" class="mb-1.5 block text-sm font-bold text-[#1c607a]">

                                    {{ $field['label'] }}

                                    @if($required)
                                        <span class="text-red-500">*</span>
                                    @endif

                                </label>


                                {{-- TEXTAREA --}}
                                @if($fieldType === 'textarea')

                                    <textarea id="{{ $fieldName }}"
                                              name="{{ $fieldName }}"
                                              x-model="formData['{{ $fieldName }}']"
                                              rows="3"
                                              @if($required) required @endif
                                              class="crud-input rounded-xl px-4 py-2.5 text-sm text-gray-700"></textarea>


                                    {{-- SELECT --}}
                                @elseif($fieldType === 'select')

                                    <select id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            x-model="formData['{{ $fieldName }}']"
                                            @if($required) required @endif
                                            class="crud-input rounded-xl px-4 py-2.5 text-sm text-gray-700">

                                        <option value="">Seleccione una opción</option>

                                        @foreach(($field['options'] ?? []) as $option)

                                            <option value="{{ data_get($option, $field['option_value'] ?? 'id') }}">
                                                {{ data_get($option, $field['option_label'] ?? 'nombre') }}
                                            </option>

                                        @endforeach

                                    </select>


                                    {{-- INPUT --}}
                                @else

                                    <input id="{{ $fieldName }}"
                                           type="{{ $fieldType }}"
                                           name="{{ $fieldName }}"
                                           x-model="formData['{{ $fieldName }}']"
                                           @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                           @if($required) required @endif
                                           class="crud-input rounded-xl px-4 py-2.5 text-sm text-gray-700">

                                @endif


                                @error($fieldName)
                                <p class="mt-1 text-xs font-semibold text-red-500">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>

                        @endforeach

                    </div>


                    {{-- BOTONES --}}
                    <div class="flex items-center justify-center gap-4 px-7 py-5">

                        <button type="button" @click="closeFormModal()" class="min-w-[125px] rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
                            Cancelar
                        </button>


                        <button type="submit"
                                class="crud-secondary-btn min-w-[150px] rounded-xl px-5 py-2.5 text-sm font-medium text-slate-700"
                                x-text="formMode === 'create' ? 'Guardar registro' : 'Guardar cambios'">
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MODAL DE CONFIRMACIÓN --}}
        {{-- CREAR / EDITAR / ELIMINAR --}}
        {{-- ========================================================= --}}

        <div x-show="confirmationOpen" x-cloak @keydown.escape.window="closeConfirmation()" @click.self="closeConfirmation()" class="crud-overlay fixed inset-0 z-[120] flex items-center justify-center px-4">

            <div x-show="confirmationOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="crud-modal-card w-full max-w-[365px] rounded-[24px] bg-white px-7 py-6 text-center">


                {{-- ICONO --}}
                <div class="crud-modal-icon mx-auto mb-4 flex h-[54px] w-[54px] items-center justify-center rounded-[18px] text-[#1c607a]">


                    {{-- CREAR --}}
                    <svg x-show="confirmationType === 'create'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>


                    {{-- EDITAR --}}
                    <svg x-show="confirmationType === 'edit'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>


                    {{-- ELIMINAR --}}
                    <svg x-show="confirmationType === 'delete'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6M4 7h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                    </svg>

                </div>


                {{-- TEXTO --}}
                <h3 class="text-[19px] font-extrabold tracking-tight text-slate-700" x-text="confirmationTitle"></h3>

                <p class="mx-auto mt-2 max-w-[290px] text-[13.5px] font-medium leading-5 text-slate-500" x-text="confirmationText"></p>


                {{-- BOTONES --}}
                <div class="mt-6 flex items-center justify-center gap-4">

                    <button type="button" @click="closeConfirmation()" class="min-w-[120px] rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
                        No, cancelar
                    </button>


                    <button type="button"
                            @click="confirmPendingAction()"
                            class="crud-secondary-btn min-w-[135px] rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700"
                            x-text="confirmationButton">
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- ALPINE JS --}}
    {{-- ============================================================= --}}

    <script>
        function crudModule() {
            return {
                /* TABLA */
                search: '',
                perPage: 10,
                currentPage: 1,
                rows: [],
                filteredRows: [],

                /* VER */
                showModalOpen: false,
                selectedShow: [],

                /* CREAR / EDITAR */
                formModalOpen: false,
                formMode: 'create',
                formAction: '',
                formData: @js($emptyForm),

                /* CONFIRMACIÓN */
                confirmationOpen: false,
                confirmationType: '',
                confirmationTitle: '',
                confirmationText: '',
                confirmationButton: '',
                pendingForm: null,


                init() {
                    this.$nextTick(() => {
                        this.loadRows();
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


                    /* REABRIR FORMULARIO SI HAY ERROR DE VALIDACIÓN */
                    const hasErrors = @js($errors->any());

                    if (hasErrors) {
                        const oldMode = @js(old('_crud_mode', 'create'));

                        this.formMode = oldMode === 'edit'
                            ? 'edit'
                            : 'create';


                        this.formAction = this.formMode === 'edit'
                            ? @js(old('_crud_edit_action', ''))
                            : @js($canStore ? route($routeBase . '.store') : '');


                        this.formData = @js($oldForm);

                        this.formModalOpen = true;
                    }
                },


                /* =====================================================
                   TABLA
                ===================================================== */

                loadRows() {
                    if (!this.$refs.recordsContainer) {
                        this.rows = [];
                        return;
                    }

                    this.rows = Array.from(
                        this.$refs.recordsContainer.querySelectorAll('.item-row')
                    );
                },


                updateTable() {
                    const term = this.search.trim().toLowerCase();


                    this.filteredRows = this.rows.filter(row => {
                        const content = (row.dataset.search ?? '').toLowerCase();
                        return content.includes(term);
                    });


                    const amount = Number(this.perPage);

                    const pages = Math.max(
                        1,
                        Math.ceil(this.filteredRows.length / amount)
                    );


                    if (this.currentPage > pages) {
                        this.currentPage = pages;
                    }


                    const start = (this.currentPage - 1) * amount;
                    const end = start + amount;


                    this.rows.forEach(row => {
                        row.style.display = 'none';
                    });


                    this.filteredRows
                        .slice(start, end)
                        .forEach(row => {
                            row.style.display = '';
                        });
                },


                get totalPages() {
                    return Math.max(
                        1,
                        Math.ceil(
                            this.filteredRows.length /
                            Number(this.perPage)
                        )
                    );
                },


                previousPage() {
                    if (this.currentPage <= 1) return;

                    this.currentPage--;

                    this.updateTable();
                },


                nextPage() {
                    if (this.currentPage >= this.totalPages) return;

                    this.currentPage++;

                    this.updateTable();
                },


                clearSearch() {
                    this.search = '';
                    this.currentPage = 1;

                    this.updateTable();
                },


                /* =====================================================
                   VER
                ===================================================== */

                openShowModal(data) {
                    this.selectedShow = data ?? [];
                    this.showModalOpen = true;
                },


                closeShowModal() {
                    this.showModalOpen = false;
                    this.selectedShow = [];
                },


                /* =====================================================
                   CREAR
                ===================================================== */

                openCreateModal() {
                    this.formMode = 'create';

                    this.formAction = @js(
                        $canStore
                            ? route($routeBase . '.store')
                            : ''
                    );

                    this.formData = @js($emptyForm);

                    this.formModalOpen = true;
                },


                /* =====================================================
                   EDITAR
                ===================================================== */

                openEditModal(payload) {
                    this.formMode = 'edit';

                    this.formAction = payload.action ?? '';

                    this.formData = {
                        ...@js($emptyForm),
                        ...(payload.fields ?? {})
                    };

                    this.formModalOpen = true;
                },


                closeFormModal() {
                    this.formModalOpen = false;

                    this.formMode = 'create';

                    this.formAction = '';

                    this.formData = @js($emptyForm);
                },


                /* =====================================================
                   CONFIRMACIÓN CREAR / EDITAR
                ===================================================== */

                askFormConfirmation(event) {
                    event.preventDefault();

                    this.pendingForm = event.currentTarget;


                    if (this.formMode === 'edit') {
                        this.confirmationType = 'edit';

                        this.confirmationTitle =
                            '¿Deseas guardar los cambios?';

                        this.confirmationText =
                            'Se actualizará la información de este registro.';

                        this.confirmationButton =
                            'Sí, actualizar';
                    } else {
                        this.confirmationType = 'create';

                        this.confirmationTitle =
                            '¿Deseas registrar este elemento?';

                        this.confirmationText =
                            'Se guardará la información ingresada en el sistema.';

                        this.confirmationButton =
                            'Sí, registrar';
                    }


                    this.confirmationOpen = true;
                },


                /* =====================================================
                   CONFIRMACIÓN ELIMINAR
                ===================================================== */

                askDeleteConfirmation(event) {
                    event.preventDefault();

                    this.pendingForm = event.currentTarget;

                    this.confirmationType = 'delete';

                    this.confirmationTitle =
                        '¿Deseas eliminar este registro?';

                    this.confirmationText =
                        'Esta acción eliminará permanentemente la información y no podrá deshacerse.';

                    this.confirmationButton =
                        'Sí, eliminar';

                    this.confirmationOpen = true;
                },


                closeConfirmation() {
                    this.confirmationOpen = false;

                    this.pendingForm = null;

                    this.confirmationType = '';
                },


                confirmPendingAction() {
                    if (!this.pendingForm) return;

                    const form = this.pendingForm;

                    this.confirmationOpen = false;

                    this.pendingForm = null;

                    form.submit();
                }
            };
        }
    </script>

</x-app-layout>
