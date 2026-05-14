@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Incubadoras',
    'subtitle' => 'Administra las incubadoras registradas en el sistema.',
    'items' => $incubadoras ?? collect(),
    'routeBase' => 'super_admin.incubadoras',
    'entitySingular' => 'Incubadora',
    'entityPlural' => 'Incubadoras',
    'columns' => [
        ['label' => 'Código', 'key' => 'codigo'],
        ['label' => 'Nombre', 'key' => 'nombre'],
        ['label' => 'Ubicación', 'key' => 'ubicacion'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
    ],
    'fields' => [
        ['name' => 'codigo', 'label' => 'Código', 'type' => 'text', 'required' => true],
        ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
        ['name' => 'ubicacion', 'label' => 'Ubicación', 'type' => 'text'],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea'],
        ['name' => 'estado_incubadora_id', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => $estados ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'estado_incubadora_id'],
    ],
])
