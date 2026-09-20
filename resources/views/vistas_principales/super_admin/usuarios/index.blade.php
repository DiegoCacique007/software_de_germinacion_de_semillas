@php
    $titulo = 'Usuarios';
    $subtitulo = 'Administra los usuarios registrados en el sistema.';
    $entidadSingular = 'Usuario';
    $entidadPlural = 'Usuarios';
    $rutaBaseUsuarios = $routeBase ?? 'super_admin.usuarios';
@endphp

@include('vistas_principales.shared.modulo-crud', [
    'title' => $titulo,
    'subtitle' => $subtitulo,
    'items' => $usuarios ?? collect(),
    'routeBase' => $rutaBaseUsuarios,
    'entitySingular' => $entidadSingular,
    'entityPlural' => $entidadPlural,

    'columns' => [
        ['label' => 'Nombre', 'key' => 'name'],
        ['label' => 'Correo', 'key' => 'email'],
        ['label' => 'Rol', 'key' => 'rol.nombre'],
        ['label' => 'Estado', 'key' => 'activo'],
        ['label' => 'Último acceso', 'key' => 'ultimo_acceso_at'],
    ],

    'fields' => [
        ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'required' => true],
        ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'required' => true],
        [
            'name' => 'role_id',
            'label' => 'Rol',
            'type' => 'select',
            'required' => true,
            'options' => $roles ?? collect(),
            'option_value' => 'id',
            'option_label' => 'nombre',
        ],
        [
            'name' => 'activo',
            'label' => 'Estado',
            'type' => 'select',
            'required' => true,
            'options' => $estadosUsuario ?? collect(),
            'option_value' => 'id',
            'option_label' => 'nombre',
        ],
        ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password', 'required_create' => true],
        ['name' => 'password_confirmation', 'label' => 'Confirmar contraseña', 'type' => 'password', 'required_create' => true],
    ],
])
