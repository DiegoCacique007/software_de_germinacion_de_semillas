<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\AsignacionIncubadora;
use App\Models\CondicionOptimaEspecie;
use App\Models\ControlIncubadora;
use App\Models\Especie;
use App\Models\EstadoAlerta;
use App\Models\EstadoFrasco;
use App\Models\EstadoIncubadora;
use App\Models\EstadoLote;
use App\Models\EtapaDesarrollo;
use App\Models\EvidenciaLote;
use App\Models\Frasco;
use App\Models\Incubadora;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use App\Models\ModoControlIncubadora;
use App\Models\NivelAlerta;
use App\Models\PosicionIncubadora;
use App\Models\RegistroBiologico;
use App\Models\Role;
use App\Models\SeguimientoFrasco;
use App\Models\SeguimientoLote;
use App\Models\TipoAlerta;
use App\Models\TipoControlIncubadora;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Asistente determinista de consulta. Nunca ejecuta instrucciones ni escribe datos. */
class ChatbotService
{
    private const LIMITE = 5;

    private const DIAS_RECIENTES = 7;

    private const ABIERTAS = ['pendiente', 'atendida'];

    private const LOTES_ACTIVOS = ['activo', 'seguimiento'];

    private const FRASCOS_ACTIVOS = ['preparado', 'imbibicion', 'germinacion', 'germinado'];

    private const DEFINICIONES = [
        'alerta' => 'Una alerta en MicroSeed Control indica una condición que requiere atención, por ejemplo temperatura o humedad fuera del rango configurado. Consultar su significado no implica que existan alertas activas.',
        'incubadora' => 'Una incubadora es el equipo donde se alojan los cultivos y se monitorean las condiciones de microclima. Sus posiciones contienen lotes y puede tener encargados asignados.',
        'lectura' => 'Una lectura de microclima registra temperatura, humedad, incubadora y fecha/hora. La última lectura registrada puede no reflejar el estado actual si el sensor dejó de enviar datos.',
        'temperatura' => 'La temperatura se registra en grados Celsius. Alta o baja significa por encima o por debajo del rango configurado para la especie; no existe un único rango válido para todas las semillas.',
        'humedad' => 'La humedad relativa se registra como porcentaje. Alta o baja significa por encima o por debajo del rango configurado para la especie.',
        'lote' => 'Un lote agrupa un cultivo de una especie, ubicado en una posición de incubadora, con fechas, estado, frascos y seguimientos asociados.',
        'frasco' => 'Un frasco pertenece a un lote y registra su número dentro de ese lote, cantidad de semillas, estado y seguimientos. Su número no es necesariamente su ID.',
        'especie' => 'Una especie identifica la planta por su nombre común o científico. Puede tener condiciones óptimas configuradas y lotes asociados.',
        'usuario' => 'Un usuario es una cuenta de acceso con un rol. El Super Admin puede consultar globalmente y el encargado solo los recursos de sus asignaciones vigentes.',
        'seguimiento' => 'Un seguimiento documenta una revisión biológica de un lote o frasco: fecha, germinación, altura y observaciones. Los seguimientos de lote también registran una etapa de desarrollo.',
        'control' => 'Un control de incubadora registra tipo, modo, valor aplicado y fecha/hora. Un registro histórico no confirma el estado físico actual del actuador. El chatbot solo consulta: no activa dispositivos.',
        'asignacion' => 'Una asignación vincula un encargado con una incubadora durante un periodo. Es vigente cuando ya comenzó y no ha terminado.',
        'condicion' => 'Las condiciones óptimas son los límites de temperatura y humedad configurados para una especie. El chatbot solo informa los valores registrados, sin inventar rangos.',
        'microclima' => 'El microclima es el conjunto de condiciones ambientales de la incubadora; este sistema registra principalmente temperatura y humedad relativa.',
    ];

    public function responder(string $mensaje, User $user): array
    {
        // También protege llamadas directas al servicio, fuera del controlador.
        if (! $user->activo || (! $user->isSuperAdmin() && ! $user->isEncargado())) {
            return $this->respuesta('acceso_denegado', 'Tu cuenta no tiene acceso a estas consultas.', false);
        }

        $texto = $this->normalizarMensaje($mensaje);
        $parametros = $this->extraerParametros($texto);
        $intencion = $this->detectarIntencion($texto, $parametros);

        return $this->resolverIntencion($intencion, $parametros, $user);
    }

    private function normalizarMensaje(string $mensaje): string
    {
        $texto = Str::lower(Str::ascii($mensaje));

        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9\s_\-]/', ' ', $texto)));
    }

    private function coincide(string $texto, string $patron): bool
    {
        return preg_match('~(?:'.$patron.')~u', $texto) === 1;
    }

    /** Parámetros explícitos nunca se sustituyen por otro registro al no encontrarlos. */
    private function extraerParametros(string $texto): array
    {
        $p = ['texto' => $texto, 'periodo' => null];
        foreach (['incubadora', 'lote', 'frasco', 'usuario'] as $entidad) {
            if (preg_match('/\b'.$entidad.'\s+(?:id\s+|numero\s+)?(\d+)\b/', $texto, $m)) {
                $p[$entidad] = ['campo' => 'id', 'valor' => (int) $m[1]];
            }
        }
        foreach (['incubadora' => 'inc', 'lote' => 'lot(?:e)?'] as $entidad => $prefijo) {
            if (preg_match('/\b('.$prefijo.'[-_][a-z0-9_-]+)\b/', $texto, $m)) {
                $p[$entidad] = ['campo' => $entidad === 'lote' ? 'codigo_lote' : 'codigo', 'valor' => Str::upper($m[1])];
            }
        }
        if ($this->coincide($texto, '\bhoy\b')) {
            $p['periodo'] = [now()->startOfDay(), now()->endOfDay()];
        } elseif ($this->coincide($texto, '\besta semana\b')) {
            $p['periodo'] = [now()->startOfWeek(), now()];
        } elseif ($this->coincide($texto, '\brecientes?\b') && ! $this->coincide($texto, '\bsin seguimiento\b')) {
            $p['periodo'] = [now()->subDays(self::DIAS_RECIENTES)->startOfDay(), now()];
        }
        // Un número de frasco con lote es el número local; sin lote se interpreta como ID.
        if (isset($p['frasco'], $p['lote'])) {
            $p['frasco']['campo'] = 'numero_frasco';
        }
        if (preg_match('/\b(?:especie|condiciones optimas|temperatura optima|humedad (?:optima|recomendada))\s+(?:de |del |para )?(.+)$/', $texto, $m)
            && ! $this->coincide($m[1], '^(?:hay|tenemos|existen|registradas|actuales)$')) {
            $p['especie'] = ['campo' => 'nombre', 'valor' => preg_replace('/^(?:la especie |el |la )/', '', $m[1])];
        }
        if (! isset($p['usuario']) && preg_match('/(?:incubadoras? (?:tiene|de)|(?:informacion|detalle) (?:del? )?usuario|encargado llamado)\s+(.+)$/', $texto, $m)) {
            $p['usuario'] = ['campo' => 'name', 'valor' => $m[1]];
        }
        if (! isset($p['especie']) && ! isset($p['incubadora']) && preg_match('/\blotes (?:de|del|para) (?!incubadora\b|inc[-_])(.+)$/', $texto, $m)) {
            $p['especie'] = ['campo' => 'nombre', 'valor' => preg_replace('/^la especie /', '', $m[1])];
        }
        if (! isset($p['incubadora']) && preg_match('/\bincubadora (?:llamada|con nombre)\s+(.+)$/', $texto, $m)) {
            $p['incubadora'] = ['campo' => 'nombre', 'valor' => $m[1]];
        }

        return $p;
    }

    /** Prioridad: acciones prohibidas, lenguaje explicativo, consultas específicas, generales. */
    private function detectarIntencion(string $t, array $p): string
    {
        if ($this->coincide($t, '\b(?:elimina(?:r|me)?|borra(?:r|me)?|crea(?:r|me)?|modifica(?:r)?|cambia(?:r)?|activar|desactiva(?:r)?|enciende|encender|apaga(?:r)?|ejecuta(?:r)?|asigna(?:r)?|actualiza(?:r)?)\b|\bactiva (?:el|la|los|las)\b|^activa\b')) {
            return 'solo_consulta';
        }
        if ($this->coincide($t, '^(?:hola|buenos dias|buenas tardes|buenas noches|hey)(?: microseed)?$')) {
            return 'saludo';
        }
        if ($this->coincide($t, '^(?:adios|hasta luego|nos vemos|hasta pronto)$')) {
            return 'despedida';
        }
        if ($this->coincide($t, '^(?:muchas )?gracias(?: por .*)?$')) {
            return 'agradecimiento';
        }
        if ($this->coincide($t, '^(?:ayuda|opciones)$')) {
            return 'ayuda';
        }
        if ($this->coincide($t, 'que (?:puedes hacer|sabes hacer|puedo preguntar)|como me puedes ayudar')) {
            return 'capacidades_chatbot';
        }
        if ($this->coincide($t, 'que (?:es|hace) (?:el )?(?:microseed|sistema)|para que sirve (?:el )?(?:microseed|sistema)')) {
            return 'informacion_sistema';
        }
        if ($this->coincide($t, 'resumen|como esta el sistema|que (?:esta pasando|paso|requiere atencion)|hay algo|algo importante')) {
            return 'resumen_sistema';
        }

        if ($this->coincide($t, '\b(?:que (?:es|son|significa|significan)|para que (?:sirve|sirven)|explica\w*|definicion|como funciona)\b')) {
            foreach (['estado', 'nivel', 'tipo'] as $catalogo) {
                if ($this->coincide($t, '\b'.$catalogo.'(?:es|s)?\b') && $this->coincide($t, '\balertas?\b')) {
                    return 'explicacion_'.$catalogo.'_alerta';
                }
            }
            if ($this->coincide($t, '\bcondicion(?:es)? optima')) {
                return 'definicion_condicion';
            }
            foreach (self::DEFINICIONES as $tema => $definicion) {
                $raiz = $tema === 'condicion' ? 'condicion(?:es)?' : ($tema === 'asignacion' ? 'asignacion(?:es)?' : $tema.'s?');
                if ($this->coincide($t, '\b'.$raiz.'\b')) {
                    return 'definicion_'.$tema;
                }
            }

            return 'explicacion_sistema';
        }

        // Mencionar un módulo no basta para inferir una consulta de datos.
        $operacionExplicita = $this->coincide($t, '\b(?:cuant[oa]s?|cantidad|numero de|total|hay|existen?|tenemos|tiene|muestra\w*|lista\w*|ver|consulta\w*|estado(?:s)?|detalle|informacion|ultim[oa]s?|recientes?|actual|activ[oa]s?|inactivos|asignad[oa]s?|critic[oa]s?|nivel(?:es)?|tipos?|modos?|etapas?|posicion(?:es)?|roles|optimas?|recomendada)\b|como esta|quien tiene')
            || $this->coincide($t, '^cual(?:es)? (?:es|son|fue|fueron)\b|^(?:alertas?|incubadoras?|lotes?|frascos?|especies?|seguimientos?|evidencias?|registros biologicos|temperatura|humedad) (?:de|del|por|con|sin)\b');
        if (! $operacionExplicita) {
            return 'pregunta_desconocida';
        }

        $cantidad = $this->coincide($t, '\b(?:cuant[oa]s?|cantidad|numero de|total)\b');
        $ultima = $this->coincide($t, '\bultim[oa]\b');
        $recientes = $this->coincide($t, '\b(?:ultim[oa]s|recientes?)\b');
        $activas = $this->coincide($t, '\bactiv[oa]s?\b');
        $estado = $this->coincide($t, '\bestado\b|como esta');
        $inc = isset($p['incubadora']);
        $lote = isset($p['lote']);
        $frasco = isset($p['frasco']);

        foreach (['incubadora', 'lote', 'frasco'] as $catalogo) {
            if ($this->coincide($t, '\bestados (?:de |del? )?'.$catalogo.'s?\b')) {
                return 'estados_'.$catalogo;
            }
        }
        if ($this->coincide($t, '\bposicion(?:es)?\b')) {
            return 'posiciones_incubadora';
        }
        if ($this->coincide($t, '\bmodos de control\b')) {
            return 'modos_control';
        }

        if ($this->coincide($t, '\balertas?\b')) {
            foreach (['estado', 'nivel', 'tipo'] as $catalogo) {
                if ($this->coincide($t, '\b'.$catalogo.'(?:es|s)?\b')) {
                    return 'explicacion_'.$catalogo.'_alerta';
                }
            }
            if ($this->coincide($t, '\bincubadoras\b') && ! $inc) {
                return 'incubadoras_con_alertas';
            }
            if ($this->coincide($t, '\bcritic[oa]s?\b')) {
                return 'alertas_criticas';
            }
            if ($inc) {
                return 'alertas_por_incubadora';
            }
            if ($cantidad) {
                return 'cantidad_alertas';
            }
            if ($ultima) {
                return 'ultima_alerta';
            }
            if ($recientes) {
                return 'ultimas_alertas';
            }

            return 'alertas_activas';
        }
        if ($this->coincide($t, '\b(?:condiciones? optimas?|optima|optimo|recomendada)\b')) {
            if ($this->coincide($t, '\btemperatura\b')) {
                return 'temperatura_optima';
            }
            if ($this->coincide($t, '\bhumedad\b')) {
                return 'humedad_optima';
            }

            return 'condiciones_optimas';
        }
        if ($this->coincide($t, '\b(?:seguimientos?|revision(?:es)?)\b')) {
            $tipo = $this->coincide($t, '\bfrascos?\b') ? 'frasco' : 'lote';
            if ($this->coincide($t, '\bsin\b')) {
                return $tipo.'s_sin_seguimiento_reciente';
            }

            return ($ultima ? 'ultimo_seguimiento_' : 'seguimientos_').$tipo;
        }
        if ($this->coincide($t, '\betapas?\b')) {
            return $frasco ? 'etapa_actual_frasco' : ($lote ? 'etapa_actual_lote' : 'etapas_desarrollo');
        }
        if ($this->coincide($t, '\bevidencias?\b')) {
            return $cantidad ? 'cantidad_evidencias' : ($ultima ? 'ultima_evidencia' : 'evidencias_lote');
        }
        if ($this->coincide($t, '\bregistros? biologicos?\b')) {
            return $frasco ? 'registros_por_frasco' : ($lote ? 'registros_por_lote' : 'ultimos_registros_biologicos');
        }
        if ($this->coincide($t, '\b(?:control(?:es)?|modo|actuador(?:es)?)\b')) {
            if ($this->coincide($t, '\btipo')) {
                return 'tipos_control';
            }
            if ($this->coincide($t, '\bmodo\b')) {
                return 'modo_control';
            }

            return $estado ? 'estado_control' : ($ultima ? 'ultimo_control' : 'controles_recientes');
        }
        if ($this->coincide($t, '\b(?:asignaciones?|asignad[oa]s?|encargado de|quien tiene)\b') || (isset($p['usuario']) && $this->coincide($t, '\bincubadoras?\b'))) {
            if ($inc) {
                return 'encargado_de_incubadora';
            }
            if (isset($p['usuario'])) {
                return 'incubadoras_por_encargado';
            }
            if ($cantidad) {
                return 'cantidad_asignaciones';
            }

            return $this->coincide($t, '\bincubadoras?\b') ? 'incubadoras_asignadas' : 'asignaciones_activas';
        }
        if ($this->coincide($t, '\b(?:usuarios?|encargados?|roles)\b')) {
            if ($this->coincide($t, '\broles\b')) {
                return 'roles';
            }
            if (isset($p['usuario']) || $this->coincide($t, '\b(?:informacion|detalle)\b')) {
                return 'informacion_usuario';
            }
            if ($this->coincide($t, '\binactivos\b')) {
                return 'usuarios_inactivos';
            }
            if ($activas) {
                return 'usuarios_activos';
            }

            return $this->coincide($t, '\bencargados\b') ? 'cantidad_encargados' : 'cantidad_usuarios';
        }
        foreach (['temperatura', 'humedad'] as $variable) {
            if ($this->coincide($t, '\b'.$variable.'\b')) {
                return $variable.($inc ? '_incubadora' : '_actual');
            }
        }
        if ($this->coincide($t, '\b(?:lecturas?|microclima)\b')) {
            if ($this->coincide($t, '\bmicroclima\b') && $estado) {
                return 'estado_microclima';
            }

            return $recientes ? 'lecturas_recientes' : ($inc ? 'ultima_lectura_incubadora' : 'ultima_lectura');
        }
        if ($this->coincide($t, '\bfrascos?\b')) {
            if ($frasco) {
                return $estado ? 'estado_frasco' : 'detalle_frasco';
            }
            if ($activas) {
                return 'frascos_activos';
            }
            if ($lote) {
                return 'frascos_por_lote';
            }

            return $cantidad ? 'cantidad_frascos' : 'listar_frascos';
        }
        if ($this->coincide($t, '\blotes?\b|\blot[-_]')) {
            if ($lote) {
                return $estado ? 'estado_lote' : 'detalle_lote';
            }
            if (isset($p['especie'])) {
                return 'lotes_por_especie';
            }
            if ($inc) {
                return 'lotes_por_incubadora';
            }
            if ($activas) {
                return 'lotes_activos';
            }

            return $cantidad ? 'cantidad_lotes' : 'listar_lotes';
        }
        if ($this->coincide($t, '\bespecies?\b')) {
            if (isset($p['especie'])) {
                return 'detalle_especie';
            }

            return $cantidad ? 'cantidad_especies' : 'listar_especies';
        }
        if ($inc || $this->coincide($t, '\bincubadoras?\b')) {
            if ($inc) {
                return $estado ? 'estado_incubadora_especifica' : 'detalle_incubadora';
            }
            if ($activas) {
                return 'incubadoras_activas';
            }
            if ($cantidad) {
                return 'cantidad_incubadoras';
            }

            return $estado ? 'estado_incubadoras' : 'listar_incubadoras';
        }
        if ($this->coincide($t, '\b(?:reportes?|pdf|csv)\b')) {
            return 'reportes';
        }

        return 'pregunta_desconocida';
    }

    private function resolverIntencion(string $i, array $p, User $user): array
    {
        if (str_starts_with($i, 'definicion_')) {
            return $this->respuesta($i, self::DEFINICIONES[substr($i, 11)], true);
        }
        $estaticas = [
            'solo_consulta' => 'Puedo consultar información de MicroSeed Control, pero actualmente no realizo modificaciones ni ejecuto comandos desde el chatbot.',
            'saludo' => 'Hola, '.$user->name.'. Soy el asistente de consulta de MicroSeed Control. ¿Qué deseas revisar?',
            'despedida' => 'Hasta luego. Cuando lo necesites, puedo ayudarte a consultar MicroSeed Control.',
            'agradecimiento' => 'De nada. Puedes continuar consultando información de MicroSeed Control.',
            'informacion_sistema' => 'MicroSeed Control permite monitorear el microclima de incubadoras y consultar cultivos, alertas, controles y seguimiento biológico de la germinación.',
            'ayuda' => 'Puedo explicar conceptos y consultar incubadoras, lecturas, alertas, lotes, frascos, especies, condiciones óptimas, asignaciones, seguimientos, evidencias y controles. Solo consulto información permitida para tu usuario. Puedes indicar un código como INC-006 o un ID como lote 3.',
            'capacidades_chatbot' => 'Puedo explicar conceptos y consultar datos de MicroSeed Control: incubadoras, lecturas, alertas, lotes, frascos, especies, condiciones óptimas, seguimientos, evidencias y controles. Las consultas se limitan a tu rol y asignaciones. No modifico datos.',
            'pregunta_desconocida' => 'No pude identificar exactamente la consulta. Puedo ayudarte con incubadoras, lecturas, alertas, lotes, frascos, especies, condiciones óptimas y seguimientos. Intenta indicar un módulo y qué deseas saber.',
            'reportes' => $user->isSuperAdmin() ? 'En Reportes puedes consultar Microclima y Seguimiento Biológico, generar PDF y exportar CSV usando los filtros disponibles. El chatbot no genera archivos.' : 'Puedes consultar aquí los datos de tus incubadoras asignadas. Los reportes globales corresponden al Super Admin.',
        ];
        if (isset($estaticas[$i])) {
            return $this->respuesta($i, $estaticas[$i], $i !== 'pregunta_desconocida');
        }
        if ($i === 'explicacion_sistema') {
            return $this->explicacionSistema($p['texto']);
        }

        $privadas = ['cantidad_usuarios', 'cantidad_encargados', 'usuarios_activos', 'usuarios_inactivos', 'informacion_usuario', 'incubadoras_por_encargado', 'roles'];
        if (! $user->isSuperAdmin() && in_array($i, $privadas, true)) {
            return $this->respuesta($i, 'Esta consulta global de usuarios está disponible para el Super Admin. Puedes consultar tus incubadoras asignadas.', false);
        }
        $catalogos = [
            'explicacion_estado_alerta' => [EstadoAlerta::class, 'estados de alerta'],
            'explicacion_nivel_alerta' => [NivelAlerta::class, 'niveles de alerta'],
            'explicacion_tipo_alerta' => [TipoAlerta::class, 'tipos de alerta'],
            'etapas_desarrollo' => [EtapaDesarrollo::class, 'etapas de desarrollo'],
            'tipos_control' => [TipoControlIncubadora::class, 'tipos de control'],
            'roles' => [Role::class, 'roles'],
            'estados_incubadora' => [EstadoIncubadora::class, 'estados de incubadora'],
            'estados_lote' => [EstadoLote::class, 'estados de lote'],
            'estados_frasco' => [EstadoFrasco::class, 'estados de frasco'],
            'modos_control' => [ModoControlIncubadora::class, 'modos de control'],
        ];
        if (isset($catalogos[$i])) {
            [$clase, $nombre] = $catalogos[$i];
            $items = $clase::query()->orderBy('id')->limit(20)->get(['clave', 'nombre', 'descripcion']);

            return $this->respuesta($i, $items->isEmpty() ? 'No hay '.$nombre.' registrados.' : 'Catálogo de '.$nombre.' (hasta 20):\n'.$items->map(fn ($r) => '• '.$r->nombre.' ('.$r->clave.'): '.($r->descripcion ?: 'Sin descripción registrada.'))->implode("\n"), true);
        }

        // Solo se buscan entidades dentro del conjunto autorizado.
        foreach (['incubadora', 'lote', 'frasco', 'especie', 'usuario'] as $tipo) {
            if (! isset($p[$tipo])) {
                continue;
            }
            $resultado = $this->resolverEntidad($tipo, $p[$tipo], $p, $user);
            if (is_string($resultado)) {
                return $this->respuesta($i, $resultado, true);
            }
            $p[$tipo.'_modelo'] = $resultado;
        }
        if ($i === 'resumen_sistema') {
            return $this->resumen($i, $p, $user);
        }
        if ($i === 'etapa_actual_frasco' || $i === 'registros_por_frasco') {
            return $this->respuesta($i, $i === 'etapa_actual_frasco'
                ? 'Los seguimientos de frasco registran estado, germinación y altura, pero no una etapa propia. La etapa de desarrollo se registra en el seguimiento del lote; puedes consultar la etapa de ese lote.'
                : 'Los registros biológicos están asociados a lotes, no a frascos individuales. Puedes consultar los registros del lote correspondiente.', true);
        }
        if (in_array($i, ['condiciones_optimas', 'temperatura_optima', 'humedad_optima'], true)) {
            return $this->condiciones($i, $p, $user);
        }
        if (str_contains($i, 'alerta') && $i !== 'incubadoras_con_alertas') {
            return $this->alertas($i, $p, $user);
        }
        if (str_contains($i, 'incubadora') && ! in_array($i, ['ultima_lectura_incubadora', 'temperatura_incubadora', 'humedad_incubadora', 'lotes_por_incubadora', 'encargado_de_incubadora', 'incubadoras_por_encargado', 'incubadoras_asignadas', 'posiciones_incubadora'], true)) {
            return $this->incubadoras($i, $p, $user);
        }
        if (in_array($i, ['ultima_lectura', 'ultima_lectura_incubadora', 'temperatura_actual', 'humedad_actual', 'temperatura_incubadora', 'humedad_incubadora', 'lecturas_recientes', 'estado_microclima'], true)) {
            return $this->lecturas($i, $p, $user);
        }
        if (str_contains($i, 'asignacion') || in_array($i, ['encargado_de_incubadora', 'incubadoras_por_encargado', 'incubadoras_asignadas'], true)) {
            return $this->asignaciones($i, $p, $user);
        }
        if (in_array($i, $privadas, true)) {
            return $this->usuarios($i, $p);
        }

        return $this->recursos($i, $p, $user);
    }

    /** Único punto de entrada a consultas operativas: filtra en SQL, antes de obtener filas. */
    private function consulta(string $tipo, User $user): Builder
    {
        $clases = [
            'incubadora' => Incubadora::class, 'alerta' => Alerta::class, 'lectura' => LecturaMicroclima::class,
            'lote' => Lote::class, 'frasco' => Frasco::class, 'especie' => Especie::class,
            'seguimiento_lote' => SeguimientoLote::class, 'seguimiento_frasco' => SeguimientoFrasco::class,
            'evidencia' => EvidenciaLote::class, 'registro' => RegistroBiologico::class,
            'control' => ControlIncubadora::class, 'asignacion' => AsignacionIncubadora::class, 'usuario' => User::class,
            'posicion' => PosicionIncubadora::class,
        ];
        $q = $clases[$tipo]::query();
        if ($user->isSuperAdmin()) {
            return $q;
        }
        if (! $user->isEncargado()) {
            return $q->whereRaw('1 = 0');
        }

        return match ($tipo) {
            'incubadora' => $q->asignadasA($user->id),
            'alerta', 'lote', 'frasco', 'seguimiento_lote', 'seguimiento_frasco', 'evidencia' => $q->deEncargado($user->id),
            'lectura', 'control', 'posicion' => $q->whereHas('incubadora', fn ($r) => $r->asignadasA($user->id)),
            'registro' => $q->whereHas('lote', fn ($r) => $r->deEncargado($user->id)),
            'especie' => $q->whereHas('lotes', fn ($r) => $r->deEncargado($user->id)),
            'asignacion' => $q->deUsuario($user->id)->vigentes(),
            default => $q->whereRaw('1 = 0'),
        };
    }

    private function resolverEntidad(string $tipo, array $referencia, array $p, User $user): Model|string
    {
        $q = $this->consulta($tipo, $user);
        if ($tipo === 'frasco' && isset($p['lote_modelo'])) {
            $q->where('lote_id', $p['lote_modelo']->id);
        }
        if ($referencia['campo'] === 'nombre' && $tipo === 'especie') {
            $q->where(fn ($r) => $r->whereRaw('LOWER(nombre_comun) = ?', [$referencia['valor']])->orWhereRaw('LOWER(nombre_cientifico) = ?', [$referencia['valor']]));
        } elseif (in_array($referencia['campo'], ['name', 'nombre'], true)) {
            $campo = $referencia['campo']; // Lista interna, nunca un nombre de columna proporcionado por el usuario.
            $q->whereRaw('LOWER('.$campo.') LIKE ?', [$referencia['valor'].'%']);
        } else {
            $q->where($referencia['campo'], $referencia['valor']);
        }
        $items = $q->limit(2)->get();
        if ($items->isEmpty()) {
            return 'No encontré '.$tipo.' identificado como '.$referencia['valor'].' dentro de la información que tienes permitido consultar.';
        }
        if ($items->count() > 1) {
            return 'Hay más de un registro que coincide con '.$referencia['valor'].'. Indica el ID de '.$tipo.' para precisar la consulta.';
        }

        return $items->first();
    }

    private function periodo(Builder $q, string $campo, array $p): Builder
    {
        if (! isset($p['periodo'])) {
            return $q;
        }
        if (in_array($campo, ['fecha_revision', 'fecha_siembra', 'fecha_registro'], true)) {
            return $q->whereDate($campo, '>=', $p['periodo'][0]->toDateString())->whereDate($campo, '<=', $p['periodo'][1]->toDateString());
        }

        return $q->whereBetween($campo, $p['periodo']);
    }

    private function abiertas(Builder $q): Builder
    {
        return $q->whereHas('estado', fn ($r) => $r->whereIn('clave', self::ABIERTAS));
    }

    private function alertas(string $i, array $p, User $user): array
    {
        $q = $this->periodo($this->consulta('alerta', $user), 'fecha_hora', $p);
        if (isset($p['incubadora_modelo'])) {
            $q->where('incubadora_id', $p['incubadora_modelo']->id);
        }
        if (isset($p['lote_modelo'])) {
            $q->where('lote_id', $p['lote_modelo']->id);
        }
        $abiertas = ! in_array($i, ['ultima_alerta', 'ultimas_alertas'], true) && ! $this->coincide($p['texto'], '\b(?:total|registradas|historicas|resueltas)\b');
        if ($abiertas) {
            $this->abiertas($q);
        }
        if ($this->coincide($p['texto'], '\bresueltas\b')) {
            $q->whereHas('estado', fn ($r) => $r->where('clave', 'resuelta'));
        }
        if ($i === 'alertas_criticas') {
            $q->whereHas('nivel', fn ($r) => $r->whereIn('clave', ['critica', 'critico']));
        }
        $total = (clone $q)->count();
        $items = $q->with(['incubadora', 'tipo', 'nivel', 'estado'])->orderByDesc('fecha_hora')->orderByDesc('id')->limit($i === 'ultima_alerta' ? 1 : 3)->get();
        $etiqueta = $abiertas ? 'alertas activas' : 'alertas registradas';
        $texto = $total === 0 ? 'No hay '.$etiqueta.' que coincidan con tu consulta.' : 'Hay '.$total.' '.$etiqueta.' que coinciden con tu consulta. '.($i === 'ultima_alerta' ? 'La última es:' : 'Las más recientes son:');
        foreach ($items as $r) {
            $texto .= "\n• ".$r->incubadora?->codigo.' — '.$r->tipo?->nombre.': '.$r->mensaje.' ['.$r->estado?->nombre.', '.$r->nivel?->nombre.'] — '.$this->fecha($r->fecha_hora);
        }
        if ($total > $items->count()) {
            $texto .= "\nHay ".($total - $items->count()).' alertas adicionales dentro de esta consulta.';
        }

        return $this->respuesta($i, $texto.$this->notaPeriodo($p), true, ['¿Qué significa una alerta?', 'Estado de incubadoras', 'Última lectura']);
    }

    private function incubadoras(string $i, array $p, User $user): array
    {
        $q = $this->consulta('incubadora', $user);
        if (isset($p['incubadora_modelo'])) {
            $q->whereKey($p['incubadora_modelo']->id);
        }
        if ($i === 'incubadoras_activas') {
            $q->whereHas('estado', fn ($r) => $r->where('clave', 'activa'));
        }
        if ($i === 'incubadoras_con_alertas') {
            $q->whereIn('id', $this->abiertas($this->consulta('alerta', $user))->select('incubadora_id'));
        }
        if ($i === 'cantidad_incubadoras') {
            return $this->respuesta($i, 'Actualmente hay '.$q->count().' incubadoras registradas'.$this->alcance($user).'.', true);
        }
        $total = (clone $q)->count();
        $items = $q->with(['estado', 'ultimaLecturaMicroclima'])->orderBy('codigo')->limit(self::LIMITE)->get();
        $conteos = $this->abiertas($this->consulta('alerta', $user))->whereIn('incubadora_id', $items->modelKeys())->selectRaw('incubadora_id, COUNT(*) as total')->groupBy('incubadora_id')->pluck('total', 'incubadora_id');
        $texto = $total ? 'Encontré '.$total.' incubadoras'.$this->alcance($user).':' : 'No encontré incubadoras para esta consulta.';
        foreach ($items as $r) {
            $texto .= "\n• ".$r->codigo.' — '.$r->nombre.'; estado: '.($r->estado?->nombre ?? 'sin registrar').'; alertas activas: '.($conteos[$r->id] ?? 0).'.';
            $texto .= ' '.($r->ultimaLecturaMicroclima ? $this->textoLectura($r->ultimaLecturaMicroclima) : 'Sin lecturas registradas.');
        }
        if (isset($p['incubadora_modelo']) && $items->first()?->ultimaLecturaMicroclima) {
            $texto .= $this->comparacionRangos($items->first()->ultimaLecturaMicroclima, $user);
        }

        return $this->respuesta($i, $texto.$this->restantes($total, $items->count()), true, isset($p['incubadora_modelo'])
            ? ['Última lectura de '.$p['incubadora_modelo']->codigo, 'Alertas de '.$p['incubadora_modelo']->codigo, 'Lotes de '.$p['incubadora_modelo']->codigo]
            : ['Incubadoras con alertas', 'Última lectura', 'Resumen del sistema']);
    }

    private function lecturas(string $i, array $p, User $user): array
    {
        $q = $this->periodo($this->consulta('lectura', $user), 'fecha_hora', $p);
        if (isset($p['incubadora_modelo'])) {
            $q->where('incubadora_id', $p['incubadora_modelo']->id);
        }
        $items = $q->with('incubadora')->orderByDesc('fecha_hora')->orderByDesc('id')->limit($i === 'lecturas_recientes' ? self::LIMITE : 1)->get();
        $texto = $items->isEmpty() ? 'No encontré lecturas registradas para esta consulta.' : 'Últimas lecturas registradas'.$this->alcance($user).':';
        foreach ($items as $r) {
            $texto .= "\n• ".$r->incubadora?->codigo.': '.$this->textoLectura($r);
        }
        if ($i === 'estado_microclima' && $items->isNotEmpty()) {
            $texto .= $this->comparacionRangos($items->first(), $user);
        }

        return $this->respuesta($i, $texto.$this->notaPeriodo($p), true, ['Lecturas recientes', 'Condiciones óptimas', 'Alertas activas']);
    }

    private function textoLectura(LecturaMicroclima $r): string
    {
        return 'Temperatura: '.$this->numero($r->temperatura).' °C; humedad: '.$this->numero($r->humedad).' %. Fecha: '.$this->fecha($r->fecha_hora).'. Son datos de esa lectura, no una confirmación del estado físico actual.';
    }

    private function comparacionRangos(LecturaMicroclima $lectura, User $user): string
    {
        $lotes = $this->consulta('lote', $user)->whereHas('posicion', fn ($q) => $q->where('incubadora_id', $lectura->incubadora_id))
            ->whereHas('estado', fn ($q) => $q->whereIn('clave', self::LOTES_ACTIVOS))->with('especie')->limit(self::LIMITE)->get();
        // Solo compara cuando hay una configuración inequívoca por especie.
        $condiciones = CondicionOptimaEspecie::whereIn('especie_id', $lotes->pluck('especie_id'))->whereIn('id',
            CondicionOptimaEspecie::selectRaw('MIN(id)')->groupBy('especie_id'))->get()->keyBy('especie_id');
        $multiples = CondicionOptimaEspecie::whereIn('especie_id', $lotes->pluck('especie_id'))->selectRaw('especie_id, COUNT(*) as total')->groupBy('especie_id')->pluck('total', 'especie_id');
        $texto = '';
        foreach ($lotes as $lote) {
            if (($multiples[$lote->especie_id] ?? 0) > 1) {
                $texto .= "\nEl lote ".$lote->codigo_lote.' tiene varias configuraciones de especie; no selecciono un rango automáticamente.';

                continue;
            }
            $c = $condiciones->get($lote->especie_id);
            if (! $c) {
                continue;
            }
            foreach (['temperatura' => '°C', 'humedad' => '%'] as $v => $unidad) {
                $min = $c->{$v.'_min'};
                $max = $c->{$v.'_max'};
                if ($min === null || $max === null || $min > $max || $lectura->$v === null) {
                    continue;
                }
                $estado = $min > $lectura->$v ? 'por debajo' : ($max < $lectura->$v ? 'por encima' : 'dentro');
                $texto .= "\n".$lote->codigo_lote.' ('.$lote->especie?->nombre_comun.'): '.$v.' '.$estado.' del rango configurado '.$this->numero($min).'–'.$this->numero($max).' '.$unidad.'.';
            }
        }

        return $texto;
    }

    private function condiciones(string $i, array $p, User $user): array
    {
        if (! isset($p['especie_modelo'])) {
            $opciones = $this->consulta('especie', $user)->orderBy('nombre_comun')->limit(self::LIMITE)->pluck('nombre_comun');

            return $this->respuesta($i, $opciones->isEmpty() ? 'No hay especies disponibles para consultar condiciones óptimas.' : 'Indica el nombre común o científico de la especie. Disponibles: '.$opciones->implode(', ').'.', true);
        }
        $especie = $p['especie_modelo'];
        $q = CondicionOptimaEspecie::where('especie_id', $especie->id);
        $total = (clone $q)->count();
        $items = $q->orderBy('id')->limit(self::LIMITE)->get();
        $texto = $items->isEmpty() ? 'No hay condiciones óptimas registradas para '.$especie->nombre_comun.'.' : 'Rangos registrados para '.$especie->nombre_comun.':';
        foreach ($items as $c) {
            $texto .= "\n• Configuración #".$c->id.': ';
            if ($i !== 'humedad_optima') {
                $texto .= 'temperatura '.$this->numero($c->temperatura_min).'–'.$this->numero($c->temperatura_max).' °C. ';
            }
            if ($i !== 'temperatura_optima') {
                $texto .= 'humedad '.$this->numero($c->humedad_min).'–'.$this->numero($c->humedad_max).' %.';
            }
        }
        if ($total > 1) {
            $texto .= ' Hay varias configuraciones; no se asume cuál está vigente.';
        }

        return $this->respuesta($i, $texto.$this->restantes($total, $items->count()), true, ['Detalle de especie '.$especie->nombre_comun, 'Lotes de especie '.$especie->nombre_comun, 'Última lectura']);
    }

    private function asignaciones(string $i, array $p, User $user): array
    {
        $q = $this->consulta('asignacion', $user)->vigentes();
        if (isset($p['incubadora_modelo'])) {
            $q->where('incubadora_id', $p['incubadora_modelo']->id);
        }
        if (isset($p['usuario_modelo'])) {
            $q->where('user_id', $p['usuario_modelo']->id);
        }
        $total = (clone $q)->count();
        if ($i === 'cantidad_asignaciones') {
            return $this->respuesta($i, 'Hay '.$total.' asignaciones vigentes'.$this->alcance($user).'.', true);
        }
        $items = $q->with(['incubadora', 'user'])->orderByDesc('fecha_inicio')->limit(self::LIMITE)->get();
        $texto = $total ? 'Hay '.$total.' asignaciones vigentes'.$this->alcance($user).':' : 'No encontré asignaciones vigentes para esta consulta.';
        foreach ($items as $r) {
            $texto .= "\n• ".$r->incubadora?->codigo.' — '.$r->user?->name.'; desde '.$this->fecha($r->fecha_inicio).' hasta '.($r->fecha_fin ? $this->fecha($r->fecha_fin) : 'sin fecha de fin').'.';
        }

        return $this->respuesta($i, $texto.$this->restantes($total, $items->count()), true, ['Incubadoras asignadas', 'Última lectura', 'Lotes activos']);
    }

    private function usuarios(string $i, array $p): array
    {
        $q = User::query();
        if (isset($p['usuario_modelo'])) {
            $q->whereKey($p['usuario_modelo']->id);
        }
        if ($i === 'cantidad_encargados') {
            $q->whereHas('rol', fn ($r) => $r->where('clave', 'encargado'));
        }
        if (in_array($i, ['usuarios_activos', 'usuarios_inactivos'], true)) {
            $q->where('activo', $i === 'usuarios_activos');
        }
        if ($i === 'informacion_usuario' && ! isset($p['usuario_modelo'])) {
            return $this->respuesta($i, 'Indica el ID o nombre del usuario que deseas consultar.', true);
        }
        $total = (clone $q)->count();
        $texto = 'Hay '.$total.' '.($i === 'cantidad_encargados' ? 'encargados' : 'usuarios').' que coinciden con tu consulta.';
        if (! str_starts_with($i, 'cantidad_')) {
            $items = $q->with('rol')->orderBy('id')->limit(self::LIMITE)->get(['id', 'name', 'role_id', 'activo']);
            foreach ($items as $r) {
                $texto .= "\n• Usuario #".$r->id.' — '.$r->name.'; rol: '.$r->rol?->nombre.'; '.($r->activo ? 'activo' : 'inactivo').'.';
            }
            $texto .= $this->restantes($total, $items->count());
        }

        return $this->respuesta($i, $texto, true, ['Cantidad de encargados', 'Asignaciones activas', 'Resumen del sistema']);
    }

    /** Metadatos fijos permiten reutilizar consultas sin construir SQL desde lenguaje natural. */
    private function recursos(string $i, array $p, User $user): array
    {
        $tipo = match (true) {
            $i === 'posiciones_incubadora' => 'posicion',
            str_contains($i, 'sin_seguimiento') => str_starts_with($i, 'frascos') ? 'frasco' : 'lote',
            str_contains($i, 'seguimiento_frasco'), $i === 'seguimientos_frasco' => 'seguimiento_frasco',
            str_contains($i, 'seguimiento_lote'), $i === 'seguimientos_lote', $i === 'etapa_actual_lote' => 'seguimiento_lote',
            str_contains($i, 'evidencia') => 'evidencia',
            str_contains($i, 'registro') => 'registro',
            str_contains($i, 'control') => 'control',
            str_contains($i, 'frasco') => 'frasco',
            str_contains($i, 'especie') && $i !== 'lotes_por_especie' => 'especie',
            default => 'lote',
        };
        $meta = [
            'posicion' => ['posiciones de incubadora', 'id', ['incubadora']],
            'lote' => ['lotes', 'fecha_siembra', ['estado', 'especie', 'posicion.incubadora']],
            'frasco' => ['frascos', 'created_at', ['estado', 'lote']],
            'especie' => ['especies', 'created_at', []],
            'seguimiento_lote' => ['seguimientos de lote', 'fecha_revision', ['lote', 'etapa']],
            'seguimiento_frasco' => ['seguimientos de frasco', 'fecha_revision', ['frasco.lote', 'estado']],
            'evidencia' => ['evidencias de lote', 'created_at', ['seguimiento.lote']],
            'registro' => ['registros biológicos', 'fecha_registro', ['lote']],
            'control' => ['controles de incubadora', 'fecha_hora', ['incubadora', 'tipo', 'modo']],
        ];
        [$nombre, $fecha, $relaciones] = $meta[$tipo];
        $q = $this->consulta($tipo, $user);
        if (isset($p[$tipo.'_modelo'])) {
            $q->whereKey($p[$tipo.'_modelo']->id);
        }
        if (isset($p['lote_modelo'])) {
            if (in_array($tipo, ['frasco', 'seguimiento_lote', 'registro'], true)) {
                $q->where('lote_id', $p['lote_modelo']->id);
            }
            if ($tipo === 'evidencia') {
                $q->whereHas('seguimiento', fn ($r) => $r->where('lote_id', $p['lote_modelo']->id));
            }
            if ($tipo === 'seguimiento_frasco') {
                $q->whereHas('frasco', fn ($r) => $r->where('lote_id', $p['lote_modelo']->id));
            }
        }
        if (isset($p['frasco_modelo']) && $tipo === 'seguimiento_frasco') {
            $q->where('frasco_id', $p['frasco_modelo']->id);
        }
        if (isset($p['incubadora_modelo'])) {
            $caminos = ['lote' => 'posicion', 'frasco' => 'lote.posicion', 'seguimiento_lote' => 'lote.posicion',
                'seguimiento_frasco' => 'frasco.lote.posicion', 'evidencia' => 'seguimiento.lote.posicion', 'registro' => 'lote.posicion', 'especie' => 'lotes.posicion'];
            if (isset($caminos[$tipo])) {
                $q->whereHas($caminos[$tipo], fn ($r) => $r->where('incubadora_id', $p['incubadora_modelo']->id));
            }
            if (in_array($tipo, ['control', 'posicion'], true)) {
                $q->where('incubadora_id', $p['incubadora_modelo']->id);
            }
        }
        if (isset($p['especie_modelo']) && $tipo === 'lote') {
            $q->where('especie_id', $p['especie_modelo']->id);
        }
        if (in_array($i, ['lotes_activos', 'frascos_activos'], true)) {
            $q->whereHas('estado', fn ($r) => $r->whereIn('clave', $tipo === 'lote' ? self::LOTES_ACTIVOS : self::FRASCOS_ACTIVOS));
        }
        if (str_contains($i, 'sin_seguimiento')) {
            $q->whereDoesntHave('seguimientos', fn ($r) => $r->whereDate('fecha_revision', '>=', now()->subDays(self::DIAS_RECIENTES)->toDateString()));
        } elseif ($tipo !== 'posicion') {
            $this->periodo($q, $fecha, $p);
        }
        if (str_starts_with($i, 'cantidad_')) {
            return $this->respuesta($i, 'Hay '.$q->count().' '.$nombre.$this->alcance($user).$this->notaPeriodo($p).'.', true);
        }
        $total = (clone $q)->count();
        $unico = str_starts_with($i, 'ultimo_') || str_starts_with($i, 'ultima_') || in_array($i, ['etapa_actual_lote', 'modo_control', 'estado_control'], true);
        $items = $q->with($relaciones)->orderByDesc($fecha)->orderByDesc('id')->limit($unico ? 1 : self::LIMITE)->get();
        $texto = $total ? 'Encontré '.$total.' '.$nombre.' para tu consulta:' : 'No encontré '.$nombre.' para esta consulta.';
        foreach ($items as $r) {
            $texto .= "\n• ".$this->describirRecurso($tipo, $r);
        }
        if (str_contains($i, 'sin_seguimiento')) {
            $texto .= "\nSe consideran sin seguimiento reciente los que no tienen revisión en los últimos ".self::DIAS_RECIENTES.' días, incluidos los que nunca han tenido una.';
        }
        if ($i === 'frascos_activos') {
            $texto .= "\nSe incluyen estados preparado, imbibición, germinación y germinado.";
        }
        if ($i === 'lotes_activos') {
            $texto .= "\nSe incluyen estados activo y en seguimiento.";
        }
        if ($tipo === 'control') {
            $texto .= "\nEstos son registros históricos. No confirman el modo ni el estado físico actual del equipo.";
        }
        $sugerencias = isset($p['lote_modelo']) ? ['Frascos del lote '.$p['lote_modelo']->id, 'Último seguimiento del lote '.$p['lote_modelo']->id, 'Registros biológicos del lote '.$p['lote_modelo']->id] : ['Lotes activos', 'Frascos activos', 'Resumen del sistema'];

        return $this->respuesta($i, $texto.$this->restantes($total, $items->count()).$this->notaPeriodo($p), true, $sugerencias);
    }

    private function describirRecurso(string $tipo, Model $r): string
    {
        return match ($tipo) {
            'posicion' => 'Posición '.$r->numero_posicion.' de '.$r->incubadora?->codigo.'; '.($r->descripcion ?: 'Sin descripción registrada.'),
            'lote' => $r->codigo_lote.' (ID '.$r->id.') — '.$r->especie?->nombre_comun.'; incubadora '.$r->posicion?->incubadora?->codigo.'; estado '.$r->estado?->nombre.'; siembra '.$this->fecha($r->fecha_siembra).'.',
            'frasco' => 'Frasco ID '.$r->id.' / número '.$r->numero_frasco.' del lote '.$r->lote?->codigo_lote.'; '.$r->cantidad_semillas.' semillas; estado '.$r->estado?->nombre.'.',
            'especie' => $r->nombre_comun.($r->nombre_cientifico ? ' ('.$r->nombre_cientifico.')' : '').'; '.($r->descripcion ?: 'Sin descripción registrada.'),
            'seguimiento_lote' => $r->lote?->codigo_lote.'; revisión '.$this->fecha($r->fecha_revision).'; etapa '.$r->etapa?->nombre.'; germinación '.$this->numero($r->porcentaje_germinacion).' %; altura '.$this->numero($r->altura_promedio_cm).' cm.',
            'seguimiento_frasco' => 'Frasco ID '.$r->frasco_id.' del lote '.$r->frasco?->lote?->codigo_lote.'; revisión '.$this->fecha($r->fecha_revision).'; '.$r->semillas_germinadas.' semillas germinadas; altura '.$this->numero($r->altura_promedio_cm).' cm; estado '.$r->estado?->nombre.'.',
            'evidencia' => 'Evidencia #'.$r->id.' del lote '.$r->seguimiento?->lote?->codigo_lote.'; '.$this->fecha($r->created_at).'; '.($r->descripcion ?: 'Sin descripción.').' Consulta el archivo desde el módulo de evidencias.',
            'registro' => $r->lote?->codigo_lote.'; registro '.$this->fecha($r->fecha_registro).'; germinación '.$this->numero($r->tasa_germinacion).' %; estratificación '.$r->dias_estratificacion.' días.',
            'control' => $r->incubadora?->codigo.'; '.$r->tipo?->nombre.'; modo registrado '.$r->modo?->nombre.'; valor '.$this->numero($r->valor_aplicado).'; '.$this->fecha($r->fecha_hora).'.',
        };
    }

    private function resumen(string $i, array $p, User $user): array
    {
        $incubadoras = $this->consulta('incubadora', $user)->count();
        $lotes = $this->consulta('lote', $user)->whereHas('estado', fn ($q) => $q->whereIn('clave', self::LOTES_ACTIVOS))->count();
        $frascos = $this->consulta('frasco', $user)->count();
        $alertas = $this->abiertas($this->consulta('alerta', $user))->count();
        $lectura = $this->periodo($this->consulta('lectura', $user), 'fecha_hora', $p)->with('incubadora')->orderByDesc('fecha_hora')->orderByDesc('id')->first();
        $texto = 'Resumen actual'.$this->alcance($user).":\n• ".$incubadoras." incubadoras.\n• ".$lotes." lotes activos o en seguimiento.\n• ".$frascos." frascos.\n• ".$alertas.' alertas activas (pendientes o atendidas).';
        if ($lectura) {
            $texto .= "\n• Última lectura ".$lectura->incubadora?->codigo.': '.$this->textoLectura($lectura);
        } else {
            $texto .= "\nNo hay lecturas para el periodo consultado.";
        }
        if ($alertas) {
            $texto .= "\nHay alertas abiertas que conviene revisar; esto no confirma que sus condiciones sigan presentes.";
        }
        if (isset($p['periodo'])) {
            $nuevas = $this->periodo($this->consulta('alerta', $user), 'fecha_hora', $p)->count();
            $texto .= "\nEn el periodo consultado se registraron ".$nuevas.' alertas. Los conteos de recursos y alertas abiertas describen el estado actual, no solo las altas del periodo.'.$this->notaPeriodo($p);
        }

        return $this->respuesta($i, $texto, true, ['Alertas activas', 'Incubadoras con alertas', 'Frascos sin seguimiento reciente']);
    }

    private function explicacionSistema(string $texto): array
    {
        // Reutiliza el conocimiento local existente solo para preguntas explicativas explícitas.
        $mejor = null;
        $longitud = 0;
        foreach (config('microseed_chatbot.conocimiento', []) as $tema) {
            foreach ($tema['palabras'] ?? [] as $frase) {
                $frase = $this->normalizarMensaje($frase);
                if (strlen($frase) > $longitud && $this->coincide($texto, '\b'.preg_quote($frase, '~').'\b')) {
                    $mejor = $tema['respuesta'];
                    $longitud = strlen($frase);
                }
            }
        }

        return $this->respuesta($mejor ? 'explicacion_sistema' : 'pregunta_desconocida', $mejor ?? 'No encontré una explicación precisa. Indica un concepto de MicroSeed Control como alerta, lectura, incubadora o lote.', $mejor !== null);
    }

    private function respuesta(string $intencion, string $texto, bool $reconocida, array $sugerencias = []): array
    {
        if (! $sugerencias && str_contains($intencion, 'alerta')) {
            $sugerencias = ['Alertas activas', 'Estado de incubadoras', 'Última lectura'];
        }

        return ['respuesta' => str_replace('\\n', "\n", $texto), 'intencion' => $intencion, 'reconocida' => $reconocida,
            'sugerencias' => $sugerencias ?: ['Estado de incubadoras', 'Última lectura', 'Alertas activas', 'Lotes activos', 'Resumen del sistema']];
    }

    private function alcance(User $user): string
    {
        return $user->isSuperAdmin() ? ' en MicroSeed Control' : ' dentro de tus asignaciones vigentes';
    }

    private function numero($valor): string
    {
        return $valor === null ? 'sin dato' : number_format((float) $valor, 1, '.', '');
    }

    private function fecha($valor): string
    {
        return $valor ? $valor->format('d/m/Y H:i') : 'sin fecha';
    }

    private function restantes(int $total, int $mostrados): string
    {
        return $total > $mostrados ? "\nSe muestran ".$mostrados.' de '.$total.' resultados; precisa un ID para consultar un registro.' : '';
    }

    private function notaPeriodo(array $p): string
    {
        return isset($p['periodo']) ? "\nPeriodo: ".$this->fecha($p['periodo'][0]).' a '.$this->fecha($p['periodo'][1]).'.' : '';
    }
}
