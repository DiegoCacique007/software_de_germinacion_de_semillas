@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Asignaciones de Incubadora',
    'subtitle' => 'Administra las asignaciones de incubadoras a usuarios.',
    'items' => $asignaciones ?? collect(),
    'routeBase' => 'super_admin.asignaciones-incubadora',
    'entitySingular' => 'Asignación',
    'entityPlural' => 'Asignaciones',
    'columns' => [
        ['label' => 'Incubadora', 'key' => 'incubadora.nombre'],
        ['label' => 'Usuario', 'key' => 'user.name'],
        ['label' => 'Fecha inicio', 'key' => 'fecha_inicio'],
        ['label' => 'Fecha fin', 'key' => 'fecha_fin'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],
    'fields' => [
        ['name' => 'incubadora_id', 'label' => 'Incubadora', 'type' => 'select', 'required' => true, 'options' => $incubadoras ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'incubadora_id'],
        ['name' => 'user_id', 'label' => 'Usuario', 'type' => 'select', 'required' => true, 'options' => $usuarios ?? [], 'option_value' => 'id', 'option_label' => 'name', 'edit_key' => 'user_id'],
        ['name' => 'fecha_inicio', 'label' => 'Fecha inicio', 'type' => 'date', 'required' => true],
        ['name' => 'fecha_fin', 'label' => 'Fecha fin', 'type' => 'date'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
