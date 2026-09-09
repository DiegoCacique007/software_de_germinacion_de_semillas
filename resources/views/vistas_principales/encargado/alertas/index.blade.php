@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Mis alertas',
    'subtitle' => 'Consulta y atiende las alertas correspondientes a tus incubadoras asignadas.',
    'items' => $alertas ?? collect(),
    'routeBase' => 'encargado.alertas',
    'entitySingular' => 'Alerta',
    'entityPlural' => 'Alertas',

    'columns' => [
        ['label' => 'Incubadora', 'key' => 'incubadora.nombre'],
        ['label' => 'Tipo', 'key' => 'tipo.nombre'],
        ['label' => 'Nivel', 'key' => 'nivel.nombre'],
        ['label' => 'Estado', 'key' => 'estado.nombre'],
        ['label' => 'Mensaje', 'key' => 'mensaje'],
        ['label' => 'Fecha/Hora', 'key' => 'fecha_hora'],
        ['label' => 'Atendida por', 'key' => 'atendidaPor.name'],
        ['label' => 'Observaciones', 'key' => 'observaciones'],
    ],

    'fields' => [
        [
            'name' => 'estado_alerta_id',
            'label' => 'Estado de la alerta',
            'type' => 'select',
            'required' => true,
            'options' => $estados ?? [],
            'option_value' => 'id',
            'option_label' => 'nombre',
            'edit_key' => 'estado_alerta_id',
        ],
        [
            'name' => 'observaciones',
            'label' => 'Observaciones',
            'type' => 'textarea',
        ],
    ],

    'canCreate' => false,
    'canEdit' => true,
    'canDelete' => false,
    'canShow' => true,
    'showAsPage' => false,
])
