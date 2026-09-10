@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis lotes',
    'subtitle' => 'Consulta los lotes correspondientes a las incubadoras que tienes asignadas.',
    'items' => $lotes ?? collect(),
    'routeBase' => 'encargado.lotes',
    'entitySingular' => 'Lote',
    'entityPlural' => 'Lotes',

    'columns' => [
        ['label' => 'Código', 'key' => 'codigo_lote'],
        ['label' => 'Incubadora', 'key' => 'posicion.incubadora.nombre'],
        ['label' => 'Posición', 'key' => 'posicion.numero_posicion'],
        ['label' => 'Especie', 'key' => 'especie.nombre_comun'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
        ['label' => 'Fecha de siembra', 'key' => 'fecha_siembra'],
        ['label' => 'Inicio', 'key' => 'fecha_inicio'],
        ['label' => 'Fin', 'key' => 'fecha_fin'],
        ['label' => 'Frascos', 'key' => 'frascos_count'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],

    'fields' => [],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => true,
    'showAsPage' => false,
])
