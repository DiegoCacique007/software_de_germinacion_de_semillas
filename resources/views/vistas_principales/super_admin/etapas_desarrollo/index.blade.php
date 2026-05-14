@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Etapas de Desarrollo',
    'subtitle' => 'Administra las etapas de desarrollo del proceso.',
    'items' => $etapas ?? collect(),
    'routeBase' => 'super_admin.etapas-desarrollo',
    'entitySingular' => 'Etapa',
    'entityPlural' => 'Etapas',
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
