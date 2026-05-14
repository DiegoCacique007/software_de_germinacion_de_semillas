@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Lecturas de Microclima',
    'subtitle' => 'Administra las lecturas registradas del microclima.',
    'items' => $lecturas ?? collect(),
    'routeBase' => 'super_admin.lecturas-microclima',
    'entitySingular' => 'Lectura',
    'entityPlural' => 'Lecturas',
    'columns' => [
        ['label' => 'Incubadora', 'key' => 'incubadora.nombre'],
        ['label' => 'Fecha/Hora', 'key' => 'fecha_hora'],
        ['label' => 'Temperatura', 'key' => 'temperatura'],
        ['label' => 'Humedad', 'key' => 'humedad'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],
    'fields' => [
        ['name' => 'incubadora_id', 'label' => 'Incubadora', 'type' => 'select', 'required' => true, 'options' => $incubadoras ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'incubadora_id'],
        ['name' => 'fecha_hora', 'label' => 'Fecha y hora', 'type' => 'text', 'required' => true],
        ['name' => 'temperatura', 'label' => 'Temperatura', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'humedad', 'label' => 'Humedad', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
