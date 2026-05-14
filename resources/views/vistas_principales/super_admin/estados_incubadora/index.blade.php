@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Estados de Incubadora',
    'subtitle' => 'Administra los estados disponibles para las incubadoras.',
    'items' => $estados ?? collect(),
    'routeBase' => 'super_admin.estados-incubadora',
    'entitySingular' => 'Estado',
    'entityPlural' => 'Estados',
    'columns' => [
        ['label' => 'Clave', 'key' => 'clave'],
        ['label' => 'Nombre', 'key' => 'nombre'],
        ['label' => 'Descripción', 'key' => 'descripcion'],
    ],
    'fields' => [
        ['name' => 'clave', 'label' => 'Clave', 'type' => 'text', 'required' => true],
        ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea'],
    ],
])
