@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis lotes',
    'subtitle' => 'Consulta los lotes correspondientes a las incubadoras que tienes asignadas.',
    'items' => $lotes ?? collect(),
    'routeBase' => 'encargado.lotes',
    'entitySingular' => 'Lote',
    'entityPlural' => 'Lotes',

    'columns' => [
        ['label' => 'Código', 'key' => 'codigo_lote', 'size' => 'short'],
        ['label' => 'Incubadora', 'key' => 'posicion.incubadora.nombre'],
        ['label' => 'Posición', 'key' => 'posicion.numero_posicion', 'size' => 'short'],
        ['label' => 'Especie', 'key' => 'especie.nombre_comun'],
        ['label' => 'Estado', 'key' => 'estado.nombre', 'size' => 'short'],
        ['label' => 'Fecha de siembra', 'key' => 'fecha_siembra'],
        ['label' => 'Inicio', 'key' => 'fecha_inicio'],
        ['label' => 'Fin', 'key' => 'fecha_fin'],
        ['label' => 'Frascos', 'key' => 'frascos_count', 'size' => 'short'],
        ['label' => 'Observaciones', 'key' => 'observaciones', 'wrap' => true],
    ],

    'fields' => [],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => false,
    'showAsPage' => false,
])
