@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis incubadoras',
    'subtitle' => 'Consulta las incubadoras que tienes asignadas actualmente.',
    'items' => $incubadoras ?? collect(),
    'routeBase' => 'encargado.incubadoras',
    'entitySingular' => 'Incubadora',
    'entityPlural' => 'Incubadoras',

    'columns' => [
        ['label' => 'Código', 'key' => 'codigo'],
        ['label' => 'Nombre', 'key' => 'nombre'],
        ['label' => 'Ubicación', 'key' => 'ubicacion'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
        ['label' => 'Temperatura', 'key' => 'ultimaLecturaMicroclima.temperatura'],
        ['label' => 'Humedad', 'key' => 'ultimaLecturaMicroclima.humedad'],
        ['label' => 'Última lectura', 'key' => 'ultimaLecturaMicroclima.fecha_hora'],
    ],

    'fields' => [],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => true,
    'showAsPage' => true,
])
