@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Posiciones de Incubadora',
    'subtitle' => 'Administra las posiciones disponibles dentro de las incubadoras.',
    'items' => $posiciones ?? collect(),
    'routeBase' => 'super_admin.posiciones-incubadora',
    'entitySingular' => 'Posición',
    'entityPlural' => 'Posiciones',
    'columns' => [
        ['label' => 'Incubadora', 'key' => 'incubadora.nombre'],
        ['label' => 'Número de posición', 'key' => 'numero_posicion'],
        ['label' => 'Descripción', 'key' => 'descripcion'],
    ],
    'fields' => [
        ['name' => 'incubadora_id', 'label' => 'Incubadora', 'type' => 'select', 'required' => true, 'options' => $incubadoras ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'incubadora_id'],
        ['name' => 'numero_posicion', 'label' => 'Número de posición', 'type' => 'number', 'required' => true],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea'],
    ],
])
