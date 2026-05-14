@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Frascos',
    'subtitle' => 'Administra los frascos registrados en el sistema.',
    'items' => $frascos ?? collect(),
    'routeBase' => 'super_admin.frascos',
    'entitySingular' => 'Frasco',
    'entityPlural' => 'Frascos',
    'columns' => [
        ['label' => 'Lote', 'key' => 'lote.codigo_lote'],
        ['label' => 'Número de frasco', 'key' => 'numero_frasco'],
        ['label' => 'Cantidad de semillas', 'key' => 'cantidad_semillas'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
    ],
    'fields' => [
        ['name' => 'lote_id', 'label' => 'Lote', 'type' => 'select', 'required' => true, 'options' => $lotes ?? [], 'option_value' => 'id', 'option_label' => 'codigo_lote', 'edit_key' => 'lote_id'],
        ['name' => 'numero_frasco', 'label' => 'Número de frasco', 'type' => 'number', 'required' => true],
        ['name' => 'cantidad_semillas', 'label' => 'Cantidad de semillas', 'type' => 'number', 'required' => true],
        ['name' => 'estado_frasco_id', 'label' => 'Estado del frasco', 'type' => 'select', 'required' => true, 'options' => $estados ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'estado_frasco_id'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
