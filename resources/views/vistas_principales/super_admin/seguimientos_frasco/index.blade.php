@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Seguimientos de Frasco',
    'subtitle' => 'Administra los seguimientos realizados a los frascos.',
    'items' => $seguimientos ?? collect(),
    'routeBase' => 'super_admin.seguimientos-frasco',
    'entitySingular' => 'Seguimiento',
    'entityPlural' => 'Seguimientos',
    'columns' => [
        ['label' => 'Frasco', 'key' => 'frasco.numero_frasco'],
        ['label' => 'Fecha revisión', 'key' => 'fecha_revision'],
        ['label' => 'Semillas germinadas', 'key' => 'semillas_germinadas'],
        ['label' => 'Altura promedio', 'key' => 'altura_promedio_cm'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
    ],
    'fields' => [
        ['name' => 'frasco_id', 'label' => 'Frasco', 'type' => 'select', 'required' => true, 'options' => $frascos ?? [], 'option_value' => 'id', 'option_label' => 'numero_frasco', 'edit_key' => 'frasco_id'],
        ['name' => 'fecha_revision', 'label' => 'Fecha revisión', 'type' => 'date', 'required' => true],
        ['name' => 'semillas_germinadas', 'label' => 'Semillas germinadas', 'type' => 'number', 'required' => true],
        ['name' => 'altura_promedio_cm', 'label' => 'Altura promedio (cm)', 'type' => 'number', 'step' => '0.01'],
        ['name' => 'estado_frasco_id', 'label' => 'Estado del frasco', 'type' => 'select', 'required' => true, 'options' => $estados ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'estado_frasco_id'],
        ['name' => 'user_id', 'label' => 'Usuario', 'type' => 'select', 'required' => true, 'options' => $usuarios ?? [], 'option_value' => 'id', 'option_label' => 'name', 'edit_key' => 'user_id'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
