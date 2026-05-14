@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Especies',
    'subtitle' => 'Administra las especies registradas en el sistema.',
    'items' => $especies ?? collect(),
    'routeBase' => 'super_admin.especies',
    'entitySingular' => 'Especie',
    'entityPlural' => 'Especies',
    'columns' => [
        ['label' => 'Nombre común', 'key' => 'nombre_comun'],
        ['label' => 'Nombre científico', 'key' => 'nombre_cientifico'],
        ['label' => 'Familia', 'key' => 'familia'],
        ['label' => 'Descripción', 'key' => 'descripcion'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],
    'fields' => [
        ['name' => 'nombre_comun', 'label' => 'Nombre común', 'type' => 'text', 'required' => true],
        ['name' => 'nombre_cientifico', 'label' => 'Nombre científico', 'type' => 'text'],
        ['name' => 'familia', 'label' => 'Familia', 'type' => 'text'],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea'],
        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea'],
    ],
])
