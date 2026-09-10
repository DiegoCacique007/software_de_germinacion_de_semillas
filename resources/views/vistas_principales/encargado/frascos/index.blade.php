@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis frascos',
    'subtitle' => 'Consulta los frascos correspondientes a los lotes de tus incubadoras asignadas.',
    'items' => $frascos ?? collect(),
    'routeBase' => 'encargado.frascos',
    'entitySingular' => 'Frasco',
    'entityPlural' => 'Frascos',

    'columns' => [
        ['label' => 'Incubadora', 'key' => 'lote.posicion.incubadora.nombre'],
        ['label' => 'Lote', 'key' => 'lote.codigo_lote'],
        ['label' => 'Especie', 'key' => 'lote.especie.nombre_comun'],
        ['label' => 'N.º frasco', 'key' => 'numero_frasco'],
        ['label' => 'Semillas', 'key' => 'cantidad_semillas'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
        ['label' => 'Seguimientos', 'key' => 'seguimientos_count'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],

    'fields' => [],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => true,
    'showAsPage' => false,
])
