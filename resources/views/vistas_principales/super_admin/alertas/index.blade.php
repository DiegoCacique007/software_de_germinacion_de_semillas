@include('vistas_principales.shared.modulo-crud', [
    'title' => 'Alertas',
    'subtitle' => 'Supervisa las incidencias detectadas en las incubadoras y consulta su ciclo de atención.',
    'items' => $alertas ?? collect(),
    'routeBase' => 'super_admin.alertas',
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

    'fields' => [
        ['name' => 'incubadora_id', 'label' => 'Incubadora', 'type' => 'select', 'required' => true, 'options' => $incubadoras ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'incubadora_id'],

        ['name' => 'tipo_alerta_id', 'label' => 'Tipo de alerta', 'type' => 'select', 'required' => true, 'options' => $tipos ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'tipo_alerta_id'],

        ['name' => 'nivel_alerta_id', 'label' => 'Nivel de alerta', 'type' => 'select', 'required' => true, 'options' => $niveles ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'nivel_alerta_id'],

        ['name' => 'estado_alerta_id', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => $estados ?? [], 'option_value' => 'id', 'option_label' => 'nombre', 'edit_key' => 'estado_alerta_id'],

        ['name' => 'mensaje', 'label' => 'Mensaje', 'type' => 'text', 'required' => true],

        ['name' => 'fecha_hora', 'label' => 'Fecha y hora de detección', 'type' => 'datetime-local', 'required' => true],

        ['name' => 'atendida_por', 'label' => 'Atendida por', 'type' => 'select', 'options' => $usuarios ?? [], 'option_value' => 'id', 'option_label' => 'name', 'edit_key' => 'atendida_por'],

        ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea', 'rows' => 3],
    ],
])
