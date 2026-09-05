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

    <div class="container-fluid py-4 px-3 px-lg-4" x-data="crudModule()" x-init="init()" x-cloak>

        {{-- ========================================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================================= --}}

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-brand-primary mb-1">{{ $title }}</h1>
                <p class="text-secondary mb-0">{{ $subtitle }}</p>
            </div>

            @if($canStore)
                <div>
                    <button type="button" @click="openCreateModal()" class="btn btn-brand d-inline-flex align-items-center justify-content-center gap-2 px-4 py-2 rounded-3">
                        <i class="bi bi-plus-lg fs-5"></i>
                        <span>Nuevo {{ $entitySingular }}</span>
                    </button>
                </div>
            @endif
        </div>


        {{-- ========================================================= --}}
        {{-- ERRORES DE VALIDACIÓN --}}
        {{-- ========================================================= --}}

        @if($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <span>Corrige los siguientes errores:</span>
                </div>

                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- TARJETA PRINCIPAL --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

            {{-- TOOLBAR / FILTROS Y BUSCADOR --}}
            <div class="card-header bg-light border-bottom border-light-subtle py-3 px-3 px-sm-4">
                <div class="row g-3 align-items-center justify-content-between">

                    {{-- SELECTOR DE PAGINADO Y CONTADOR --}}
                    <div class="col-12 col-md-auto d-flex flex-wrap align-items-center gap-2 gap-sm-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 small fw-bold text-secondary">Mostrar</label>

                            <select x-model="perPage" class="form-select form-select-sm w-auto fw-bold text-brand-primary rounded-3 border-secondary-subtle">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                            <span class="small fw-bold text-secondary">registros</span>
                        </div>

                        <span class="badge rounded-pill bg-brand-soft text-brand-primary px-3 py-2 border border-brand-subtle d-inline-flex align-items-center gap-2">
                            <span class="badge-dot bg-brand-accent"></span>
                            <span><span x-text="filteredRows.length"></span> resultados</span>
                        </span>
                    </div>

                    {{-- BUSCADOR --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-brand-primary ps-3 rounded-start-3">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="search"
                                   x-model="search"
                                   placeholder="Buscar {{ strtolower($entityPlural) }}..."
                                   class="form-control border-start-0 ps-1 rounded-end-3 py-2">

                            <button type="button"
                                    x-show="search.length > 0"
                                    x-cloak
                                    @click="clearSearch()"
                                    title="Limpiar búsqueda"
                                    class="btn btn-outline-secondary border-start-0 border-end-0">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>


            {{-- ===================================================== --}}
            {{-- TABLA DE REGISTROS --}}
            {{-- ===================================================== --}}

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="crud-table-header">
                            <tr>
                                @foreach($columns as $column)
                                    <th class="px-3 px-sm-4 py-3 text-nowrap">{{ $column['label'] }}</th>
                                @endforeach
                                <th class="px-3 px-sm-4 py-3 text-center text-nowrap" style="width: 140px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody x-ref="recordsContainer">

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

                            <tr class="item-row crud-table-row" data-search="{{ $searchText }}">

                                @foreach($columns as $column)

                                    @php
                                        $value = data_get($item, $column['key']);
                                        $isLong = in_array($column['key'], $longFields, true);
                                    @endphp

                                    <td class="px-3 px-sm-4 py-3 text-secondary {{ $isLong ? 'min-w-250 text-wrap text-break' : '' }}">
                                        <div class="text-wrap text-break lh-sm">
                                            {{ blank($value) ? '—' : $value }}
                                        </div>
                                    </td>

                                @endforeach

                                {{-- ACCIONES --}}
                                <td class="px-3 px-sm-4 py-3 text-center text-nowrap">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1">

                                        {{-- VER --}}
                                        <button type="button"
                                                @click="openShowModal(@js($showPayload))"
                                                title="Ver datos"
                                                class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        {{-- EDITAR --}}
                                        @if($canUpdate)
                                            <button type="button"
                                                    @click="openEditModal(@js($editPayload))"
                                                    title="Editar"
                                                    class="btn btn-sm btn-outline-success rounded-2 px-2 py-1">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif

                                        {{-- ELIMINAR --}}
                                        @if($canDestroy)
                                            <form action="{{ route($routeBase . '.destroy', $item) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  @submit.prevent="askDeleteConfirmation($event)">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Eliminar"
                                                        class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="px-4 py-5 text-center text-muted">
                                    No hay {{ strtolower($entityPlural) }} registrados.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>
                </div>

                {{-- SIN RESULTADOS DE BÚSQUEDA --}}
                <div x-show="filteredRows.length === 0 && rows.length > 0" x-cloak class="py-5 text-center text-muted">
                    <i class="bi bi-search fs-3 text-secondary mb-2 d-block"></i>
                    No se encontraron resultados para la búsqueda.
                </div>
            </div>


            {{-- ===================================================== --}}
            {{-- PAGINACIÓN --}}
            {{-- ===================================================== --}}

            <div x-show="totalPages > 1" x-cloak class="card-footer bg-light border-top border-light-subtle d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 py-3 px-3 px-sm-4">

                <span class="small text-secondary">
                    Página
                    <strong class="text-brand-primary" x-text="currentPage"></strong>
                    de
                    <strong class="text-brand-primary" x-text="totalPages"></strong>
                </span>

                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                        <button type="button" class="page-link rounded-start-2" @click="previousPage()" :disabled="currentPage === 1">
                            <i class="bi bi-chevron-left me-1"></i> Anterior
                        </button>
                    </li>

                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                        <button type="button" class="page-link rounded-end-2" @click="nextPage()" :disabled="currentPage === totalPages">
                            Siguiente <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </li>
                </ul>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MODAL VER --}}
        {{-- ========================================================= --}}

        <div x-show="showModalOpen"
             x-cloak
             class="modal fade show d-block crud-modal-backdrop"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="closeShowModal()"
             @click.self="closeShowModal()">

            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                    {{-- CABECERA --}}
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 text-center d-flex flex-column align-items-center">
                        <div class="crud-modal-icon mb-3">
                            <i class="bi bi-eye fs-3"></i>
                        </div>

                        <h4 class="modal-title fw-bold text-brand-dark">
                            Ver {{ $entitySingular }}
                        </h4>

                        <p class="text-secondary small mb-0 mt-1">
                            Consulta la información completa del registro seleccionado.
                        </p>
                    </div>

                    {{-- DATOS --}}
                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">
                            <template x-for="(field, index) in selectedShow" :key="index">
                                <div :class="field.value && field.value.length > 120 ? 'col-12' : 'col-12 col-md-6'">
                                    <div class="crud-field-box h-100">
                                        <label class="d-flex align-items-center gap-2 small fw-bold text-brand-primary mb-1">
                                            <span class="badge bg-brand-primary text-white rounded-2 px-1 py-0" style="font-size: 0.65rem;" x-text="String(index + 1).padStart(2, '0')"></span>
                                            <span x-text="field.label"></span>
                                        </label>

                                        <p class="mb-0 text-dark small fw-medium text-break" style="white-space: pre-line;" x-text="field.value"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- BOTÓN CERRAR --}}
                    <div class="modal-footer border-top-0 justify-content-center pb-4 pt-0">
                        <button type="button" @click="closeShowModal()" class="btn btn-brand-outline px-4 py-2 rounded-3">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MODAL CREAR / EDITAR --}}
        {{-- ========================================================= --}}

        <div x-show="formModalOpen"
             x-cloak
             class="modal fade show d-block crud-modal-backdrop"
             tabindex="-1"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="closeFormModal()"
             @click.self="closeFormModal()">

            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                    {{-- CABECERA --}}
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 text-center d-flex flex-column align-items-center">
                        <div class="crud-modal-icon mb-3">
                            <i class="bi fs-3" :class="formMode === 'create' ? 'bi-plus-circle' : 'bi-pencil-square'"></i>
                        </div>

                        <h4 class="modal-title fw-bold text-brand-dark"
                            x-text="formMode === 'create' ? 'Nuevo {{ $entitySingular }}' : 'Editar {{ $entitySingular }}'">
                        </h4>

                        <p class="text-secondary small mb-0 mt-1"
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

                        <div class="modal-body px-4 py-3">
                            <div class="row g-3">

                                @foreach($fields as $field)

                                    @php
                                        $fieldName = $field['name'];
                                        $fieldType = $field['type'] ?? 'text';
                                        $required = $field['required'] ?? false;
                                    @endphp

                                    <div class="{{ $fieldType === 'textarea' ? 'col-12' : 'col-12 col-md-6' }}">
                                        <label for="{{ $fieldName }}" class="form-label small fw-bold text-brand-primary mb-1">
                                            {{ $field['label'] }}
                                            @if($required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        {{-- TEXTAREA --}}
                                        @if($fieldType === 'textarea')
                                            <textarea id="{{ $fieldName }}"
                                                      name="{{ $fieldName }}"
                                                      x-model="formData['{{ $fieldName }}']"
                                                      rows="3"
                                                      @if($required) required @endif
                                                      class="form-control rounded-3 @error($fieldName) is-invalid @enderror"></textarea>

                                        {{-- SELECT --}}
                                        @elseif($fieldType === 'select')
                                            <select id="{{ $fieldName }}"
                                                    name="{{ $fieldName }}"
                                                    x-model="formData['{{ $fieldName }}']"
                                                    @if($required) required @endif
                                                    class="form-select rounded-3 @error($fieldName) is-invalid @enderror">
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
                                                   class="form-control rounded-3 @error($fieldName) is-invalid @enderror">
                                        @endif

                                        @error($fieldName)
                                            <div class="invalid-feedback d-block small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                @endforeach

                            </div>
                        </div>

                        {{-- BOTONES --}}
                        <div class="modal-footer border-top-0 justify-content-center gap-3 pb-4 pt-2">
                            <button type="button" @click="closeFormModal()" class="btn btn-secondary px-4 py-2 rounded-3 fw-semibold">
                                Cancelar
                            </button>

                            <button type="submit"
                                    class="btn btn-brand px-4 py-2 rounded-3"
                                    x-text="formMode === 'create' ? 'Guardar registro' : 'Guardar cambios'">
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- CONTROLADOR REACTIVO (ALPINE JS) --}}
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
                    this.formAction = @js($canStore ? route($routeBase . '.store') : '');
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
                   CONFIRMACIÓN CREAR / EDITAR (SWEETALERT2)
                ===================================================== */

                async askFormConfirmation(event) {
                    event.preventDefault();
                    const form = event.currentTarget;

                    if (this.formMode === 'edit') {
                        if (typeof window.microseedConfirmEdit === 'function') {
                            const result = await window.microseedConfirmEdit();
                            if (result && result.isConfirmed) {
                                form.submit();
                            }
                        } else {
                            form.submit();
                        }
                    } else {
                        if (typeof window.microseedConfirmCreate === 'function') {
                            const result = await window.microseedConfirmCreate();
                            if (result && result.isConfirmed) {
                                form.submit();
                            }
                        } else {
                            form.submit();
                        }
                    }
                },

                /* =====================================================
                   CONFIRMACIÓN ELIMINAR (SWEETALERT2)
                ===================================================== */

                async askDeleteConfirmation(event) {
                    event.preventDefault();
                    const form = event.currentTarget;

                    if (typeof window.microseedConfirmDelete === 'function') {
                        const result = await window.microseedConfirmDelete();
                        if (result && result.isConfirmed) {
                            form.submit();
                        }
                    } else {
                        form.submit();
                    }
                }
            };
        }
    </script>

</x-app-layout>
