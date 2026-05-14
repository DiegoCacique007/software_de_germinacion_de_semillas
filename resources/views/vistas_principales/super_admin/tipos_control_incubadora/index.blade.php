@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Tipos de Control de Incubadora',
    'subtitle' => 'Administra los tipos de control disponibles.',
    'items' => $tipos ?? collect(),
    'routeBase' => 'super_admin.tipos-control-incubadora',
    'entitySingular' => 'Tipo',
    'entityPlural' => 'Tipos',
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
