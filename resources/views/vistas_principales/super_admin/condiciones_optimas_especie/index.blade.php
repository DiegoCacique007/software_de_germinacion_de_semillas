@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Condiciones Óptimas por Especie',
    'subtitle' => 'Administra las condiciones óptimas registradas para cada especie.',
    'items' => $condiciones ?? collect(),
    'routeBase' => 'super_admin.condiciones-optimas-especie',
    'entitySingular' => 'Condición',
    'entityPlural' => 'Condiciones',
    'columns' => [
        ['label' => 'Especie', 'key' => 'especie.nombre_comun'],
        ['label' => 'Temp. mín', 'key' => 'temperatura_min'],
        ['label' => 'Temp. máx', 'key' => 'temperatura_max'],
        ['label' => 'Humedad mín', 'key' => 'humedad_min'],
        ['label' => 'Humedad máx', 'key' => 'humedad_max'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],
    'fields' => [
        ['name' => 'especie_id', 'label' => 'Especie', 'type' => 'select', 'required' => true, 'options' => $especies ?? [], 'option_value' => 'id', 'option_label' => 'nombre_comun', 'edit_key' => 'especie_id'],
        ['name' => 'temperatura_min', 'label' => 'Temperatura mínima', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'temperatura_max', 'label' => 'Temperatura máxima', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'humedad_min', 'label' => 'Humedad mínima', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'humedad_max', 'label' => 'Humedad máxima', 'type' => 'number', 'required' => true, 'step' => '0.01'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
