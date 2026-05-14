@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Evidencias de Lote',
    'subtitle' => 'Administra las evidencias asociadas a seguimientos de lote.',
    'items' => $evidencias ?? collect(),
    'routeBase' => 'super_admin.evidencias-lote',
    'entitySingular' => 'Evidencia',
    'entityPlural' => 'Evidencias',
    'columns' => [
        ['label' => 'Seguimiento', 'key' => 'seguimiento.id'],
        ['label' => 'Archivo', 'key' => 'archivo'],
        ['label' => 'Descripción', 'key' => 'descripcion'],
    ],
    'fields' => [
        ['name' => 'seguimiento_lote_id', 'label' => 'Seguimiento de lote', 'type' => 'select', 'required' => true, 'options' => $seguimientos ?? [], 'option_value' => 'id', 'option_label' => 'id', 'edit_key' => 'seguimiento_lote_id'],
        ['name' => 'archivo', 'label' => 'Archivo', 'type' => 'text', 'required' => true],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea'],
    ],
])
