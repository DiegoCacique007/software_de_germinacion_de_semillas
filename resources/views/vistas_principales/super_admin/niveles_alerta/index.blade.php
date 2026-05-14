@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Niveles de Alerta',
    'subtitle' => 'Administra los niveles de alerta del sistema.',
    'items' => $niveles ?? collect(),
    'routeBase' => 'super_admin.niveles-alerta',
    'entitySingular' => 'Nivel',
    'entityPlural' => 'Niveles',
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
