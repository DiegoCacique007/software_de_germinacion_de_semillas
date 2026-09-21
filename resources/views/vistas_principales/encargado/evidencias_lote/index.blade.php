@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Evidencias de lote',
    'subtitle' => 'Registra fotografías del proceso de germinación de los lotes bajo tu responsabilidad.',
    'items' => $evidencias ?? collect(),
    'routeBase' => 'encargado.evidencias-lote',
    'entitySingular' => 'Evidencia',
    'entityPlural' => 'Evidencias',

    'columns' => [
        ['label' => 'Incubadora', 'key' => 'seguimiento.lote.posicion.incubadora.nombre'],
        ['label' => 'Lote', 'key' => 'seguimiento.lote.codigo_lote'],
        ['label' => 'Especie', 'key' => 'seguimiento.lote.especie.nombre_comun'],
        ['label' => 'Fecha', 'key' => 'seguimiento.fecha_revision'],
        ['label' => 'Fotografía', 'key' => 'archivo', 'type' => 'image'],
        ['label' => 'Descripción', 'key' => 'descripcion', 'wrap' => true],
    ],

    'fields' => [
        [
            'name' => 'seguimiento_lote_id',
            'label' => 'Seguimiento del lote',
            'type' => 'select',
            'required' => true,
            'options' => $seguimientosOptions ?? [],
            'option_value' => 'id',
            'option_label' => 'etiqueta',
        ],
        [
            'name' => 'archivo',
            'label' => 'Fotografía',
            'type' => 'file',
            'required' => true,
            'accept' => 'image/jpeg,image/png,image/webp',
            'help' => 'Formatos permitidos: JPG, PNG y WEBP. Máximo 5 MB.',
        ],
        [
            'name' => 'descripcion',
            'label' => 'Descripción',
            'type' => 'textarea',
            'rows' => 4,
        ],
    ],

    'canCreate' => true,
    'canEdit' => false,
    'canDelete' => false,
    'canShow' => false,
    'showAsPage' => false,
])
