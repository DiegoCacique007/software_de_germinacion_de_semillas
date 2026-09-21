@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis frascos',
    'subtitle' => 'Consulta los frascos correspondientes a los lotes de tus incubadoras asignadas.',
    'items' => $frascos ?? collect(),
    'routeBase' => 'encargado.frascos',
    'entitySingular' => 'Frasco',
    'entityPlural' => 'Frascos',

    'columns' => [
        ['label' => 'Incubadora', 'key' => 'lote.posicion.incubadora.nombre'],
        ['label' => 'Posición', 'key' => 'lote.posicion.numero_posicion', 'size' => 'short'],
        ['label' => 'Lote', 'key' => 'lote.codigo_lote'],
        ['label' => 'Especie', 'key' => 'lote.especie.nombre_comun'],
        ['label' => 'N.º frasco', 'key' => 'numero_frasco', 'size' => 'short'],
        ['label' => 'Semillas', 'key' => 'cantidad_semillas', 'size' => 'short'],
        ['label' => 'Estado', 'key' => 'estado.nombre', 'size' => 'short'],
        ['label' => 'Seguimientos', 'key' => 'seguimientos_count', 'size' => 'short'],
        ['label' => 'Observaciones', 'key' => 'observaciones', 'wrap' => true],
    ],

    'fields' => [],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => false,
    'showAsPage' => false,
])
