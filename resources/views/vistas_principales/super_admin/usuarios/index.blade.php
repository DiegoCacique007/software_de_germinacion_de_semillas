@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Usuarios',
    'subtitle' => 'Administra los usuarios registrados en el sistema.',
    'items' => $usuarios ?? collect(),
    'routeBase' => 'super_admin.usuarios',
    'entitySingular' => 'Usuario',
    'entityPlural' => 'Usuarios',
    'columns' => [
        ['label' => 'Nombre', 'key' => 'name'],
        ['label' => 'Correo', 'key' => 'email'],
        ['label' => 'Rol', 'key' => 'role'],
    ],
    'fields' => [
        ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
        ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'required' => true],
        ['name' => 'role', 'label' => 'Rol', 'type' => 'select', 'required' => true, 'options' => collect([['id' => 'super_admin', 'nombre' => 'Super Admin']]), 'option_value' => 'id', 'option_label' => 'nombre'],
        ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password'],
    ],
])
