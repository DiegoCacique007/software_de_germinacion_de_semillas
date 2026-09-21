@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis alertas',
    'subtitle' => 'Consulta, atiende y da seguimiento a las alertas de tus incubadoras asignadas.',
    'items' => $alertas ?? collect(),
    'routeBase' => 'encargado.alertas',
    'entitySingular' => 'Alerta',
    'entityPlural' => 'Alertas',

    'columns' => [
        ['label' => 'Incubadora', 'key' => 'incubadora.nombre'],
        ['label' => 'Lote', 'key' => 'lote.codigo_lote'],
        ['label' => 'Origen', 'key' => 'origen', 'size' => 'short'],
        ['label' => 'Tipo', 'key' => 'tipo.nombre', 'size' => 'short'],
        ['label' => 'Nivel', 'key' => 'nivel.nombre', 'size' => 'short'],
        ['label' => 'Estado', 'key' => 'estado.nombre', 'size' => 'short'],
        ['label' => 'Lectura causante', 'key' => 'lectura_causante'],
        ['label' => 'Detectada', 'key' => 'fecha_hora'],
        ['label' => 'Atendida', 'key' => 'fecha_atencion'],
        ['label' => 'Resuelta', 'key' => 'fecha_resolucion'],
        ['label' => 'Duración', 'key' => 'duracion_incidente'],
        ['label' => 'Atendida por', 'key' => 'atendidaPor.name'],
        ['label' => 'Mensaje', 'key' => 'mensaje', 'wrap' => true],
        ['label' => 'Observaciones', 'key' => 'observaciones', 'wrap' => true],
    ],

    'fields' => [],

    'quickActions' => [
        [
            'label' => 'Marcar como atendida',
            'route' => 'encargado.alertas.atender',
            'method' => 'PATCH',
            'icon' => 'bi-person-check',
            'class' => 'btn-outline-primary',
            'when' => [
                'key' => 'estado.clave',
                'values' => ['pendiente'],
            ],
        ],
        [
            'label' => 'Resolver alerta',
            'route' => 'encargado.alertas.resolver',
            'method' => 'PATCH',
            'icon' => 'bi-check-circle',
            'class' => 'btn-outline-success',
            'when' => [
                'key' => 'estado.clave',
                'values' => ['atendida'],
            ],
        ],
    ],

    'canCreate' => false,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => true,
    'showAsPage' => false,
])
