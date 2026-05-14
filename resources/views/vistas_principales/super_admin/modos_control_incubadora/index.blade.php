@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Modos de Control de Incubadora',
    'subtitle' => 'Administra los modos de control disponibles.',
    'items' => $modos ?? collect(),
    'routeBase' => 'super_admin.modos-control-incubadora',
    'entitySingular' => 'Modo',
    'entityPlural' => 'Modos',
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
