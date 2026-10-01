<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ChatbotServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $encargado;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->travelTo(now()->setDate(2026, 9, 21)->setTime(12, 0));
        $this->admin = User::factory()->superAdmin()->create(['name' => 'Admin']);
        $this->encargado = User::factory()->create(['name' => 'Juan']);
        $otro = User::factory()->create(['name' => 'Encargado Reservado']);
        foreach (['estados_incubadora' => 'activa', 'estados_lote' => 'activo', 'estados_frasco' => 'germinacion', 'etapas_desarrollo' => 'germinacion', 'tipos_alerta' => 'temperatura', 'niveles_alerta' => 'critica', 'estados_alerta' => 'pendiente', 'tipos_control_incubadora' => 'niebla', 'modos_control_incubadora' => 'manual'] as $tabla => $clave) {
            $this->insertar($tabla, ['id' => 1, 'clave' => $clave, 'nombre' => ucfirst($clave), 'descripcion' => 'Descripción de prueba']);
        }
        $this->insertar('estados_alerta', ['id' => 2, 'clave' => 'resuelta', 'nombre' => 'Resuelta']);
        foreach ([1, 2] as $id) {
            $this->insertar('incubadoras', ['id' => $id, 'codigo' => $id === 1 ? 'INC-006' : 'INC-008', 'nombre' => $id === 1 ? 'Incubadora visible' : 'Reservado', 'estado_incubadora_id' => 1]);
            $this->insertar('asignaciones_incubadora', ['incubadora_id' => $id, 'user_id' => $id === 1 ? $this->encargado->id : $otro->id, 'fecha_inicio' => now()->subDay()->toDateString()]);
            $this->insertar('posiciones_incubadora', ['id' => $id, 'incubadora_id' => $id, 'numero_posicion' => 1]);
            $this->insertar('especies', ['id' => $id, 'nombre_comun' => $id === 1 ? 'Frijol' : 'Reservado', 'nombre_cientifico' => $id === 1 ? 'Phaseolus vulgaris' : 'Planta reservada']);
            $this->insertar('condiciones_optimas_especie', ['especie_id' => $id, 'temperatura_min' => 22, 'temperatura_max' => 28, 'humedad_min' => 60, 'humedad_max' => 80]);
            $this->insertar('lotes', ['id' => $id, 'posicion_incubadora_id' => $id, 'especie_id' => $id, 'codigo_lote' => $id === 1 ? 'LOT-001' : 'LOT-RESERVADO', 'fecha_siembra' => now()->subDays(4)->toDateString(), 'estado_lote_id' => 1]);
            $this->insertar('frascos', ['id' => $id, 'lote_id' => $id, 'numero_frasco' => 1, 'cantidad_semillas' => 10, 'estado_frasco_id' => 1]);
            $this->insertar('lecturas_microclima', ['incubadora_id' => $id, 'fecha_hora' => now()->subMinutes($id), 'temperatura' => $id === 1 ? 31.2 : 99.9, 'humedad' => 70.4]);
            $this->insertar('alertas', ['incubadora_id' => $id, 'lote_id' => $id, 'tipo_alerta_id' => 1, 'nivel_alerta_id' => 1, 'estado_alerta_id' => 1, 'mensaje' => $id === 1 ? 'Temperatura alta visible' : 'Alerta Reservado', 'fecha_hora' => now()]);
            $this->insertar('seguimientos_lote', ['id' => $id, 'lote_id' => $id, 'fecha_revision' => now()->toDateString(), 'frascos_activos' => 1, 'semillas_germinadas' => 5, 'porcentaje_germinacion' => 50, 'altura_promedio_cm' => 2, 'etapa_desarrollo_id' => 1, 'user_id' => $this->admin->id]);
            $this->insertar('seguimientos_frasco', ['frasco_id' => $id, 'fecha_revision' => now()->toDateString(), 'semillas_germinadas' => 5, 'estado_frasco_id' => 1, 'user_id' => $this->admin->id]);
            $this->insertar('evidencias_lote', ['seguimiento_lote_id' => $id, 'archivo' => 'privado.jpg', 'descripcion' => $id === 1 ? 'Evidencia visible' : 'Reservado']);
            $this->insertar('controles_incubadora', ['incubadora_id' => $id, 'tipo_control_incubadora_id' => 1, 'modo_control_incubadora_id' => 1, 'valor_aplicado' => 1, 'fecha_hora' => now()]);
        }
    }

    private function responderChat(string $pregunta, User $user, ?string $chatId = null): array
    {
        return app(ChatbotService::class)->responder(
            $pregunta,
            $user,
            $chatId ?? uniqid('test-chat-', true)
        );
    }

    private function insertar(string $tabla, array $valores): void
    {
        DB::table($tabla)->insert($valores + ['created_at' => now(), 'updated_at' => now()]);
    }

    public static function preguntas(): array
    {
        $casos = [
            ['Hola', 'consulta_estructurada_conversacion_saludo'], ['Adiós', 'consulta_estructurada_conversacion_despedida'], ['Muchas gracias', 'consulta_estructurada_conversacion_agradecimiento'],
            ['Ayuda', 'consulta_estructurada_conversacion_ayuda'], ['¿Qué puedes hacer?', 'consulta_estructurada_conversacion_capacidades'],
            ['¿Qué es MicroSeed Control?', 'consulta_estructurada_conocimiento_explicar'],
            ['¿Qué es una alerta?', 'consulta_estructurada_conocimiento_explicar'], ['que significa alerta', 'consulta_estructurada_conocimiento_explicar'], ['para que sirven las alertas', 'consulta_estructurada_conocimiento_explicar'],
            ['¿Hay alertas?', 'consulta_estructurada_alertas_listar'], ['cuantas alertas activas tenemos', 'consulta_estructurada_alertas_contar'], ['muéstrame las alertas', 'consulta_estructurada_alertas_listar'],
            ['Muéstrame las últimas alertas', 'consulta_estructurada_alertas_listar'], ['Última alerta', 'consulta_estructurada_alertas_ultimo'], ['¿La incubadora 1 tiene alertas?', 'consulta_estructurada_alertas_listar'],
            ['Alertas críticas', 'consulta_estructurada_alertas_listar'], ['Qué significa el estado de una alerta', 'consulta_estructurada_conocimiento_explicar'], ['Niveles de alerta', 'consulta_estructurada_catalogos_listar'], ['Tipos de alerta', 'consulta_estructurada_catalogos_listar'],
            ['Qué es una incubadora', 'consulta_estructurada_conocimiento_explicar'], ['Cuántas incubadoras hay?', 'consulta_estructurada_incubadoras_contar'], ['cuantas incubadoras existen', 'consulta_estructurada_incubadoras_contar'], ['¿Cuántas incubadoras tenemos?', 'consulta_estructurada_incubadoras_contar'], ['numero de incubadoras', 'consulta_estructurada_incubadoras_contar'], ['total incubadoras', 'consulta_estructurada_incubadoras_contar'],
            ['Muéstrame las incubadoras', 'consulta_estructurada_incubadoras_listar'], ['¿Cómo están las incubadoras?', 'consulta_estructurada_incubadoras_listar'], ['¿Cómo está INC-006?', 'consulta_estructurada_lecturas_estado'], ['Detalle de INC-006', 'consulta_estructurada_incubadoras_estado'], ['Incubadoras activas', 'consulta_estructurada_incubadoras_listar'], ['Incubadoras con alertas', 'consulta_estructurada_incubadoras_listar'], ['Mis incubadoras asignadas', 'consulta_estructurada_asignaciones_listar'],
            ['¿Cuál fue la última lectura?', 'consulta_estructurada_lecturas_ultimo'], ['¿Cuál fue la última lectura de INC-006?', 'consulta_estructurada_lecturas_ultimo'], ['¿Qué temperatura hay?', 'consulta_estructurada_lecturas_ultimo_temperatura'], ['Humedad actual', 'consulta_estructurada_lecturas_ultimo_humedad'], ['Temperatura de INC-006', 'consulta_estructurada_lecturas_listar_temperatura'], ['¿Cuál es la humedad de INC-008?', 'consulta_estructurada_lecturas_listar_humedad'], ['Lecturas recientes', 'consulta_estructurada_lecturas_listar'], ['Estado del microclima', 'consulta_estructurada_lecturas_estado'],
            ['Cuántos usuarios hay', 'consulta_estructurada_usuarios_contar'], ['Cantidad de encargados', 'consulta_estructurada_usuarios_contar'], ['Usuarios activos', 'consulta_estructurada_usuarios_listar'], ['Usuarios inactivos', 'consulta_estructurada_usuarios_listar'], ['Información del usuario Juan', 'consulta_estructurada_usuarios_listar'], ['Roles', 'consulta_estructurada_catalogos_listar'],
            ['Cantidad de asignaciones', 'consulta_estructurada_asignaciones_contar'], ['¿Qué incubadoras tiene Juan?', 'consulta_estructurada_asignaciones_listar'], ['¿Quién tiene asignada INC-006?', 'consulta_estructurada_asignaciones_listar'], ['Asignaciones activas', 'consulta_estructurada_asignaciones_listar'],
            ['Qué es un lote', 'consulta_estructurada_conocimiento_explicar'], ['Cuántos lotes existen', 'consulta_estructurada_lotes_contar'], ['¿Cuántos lotes están activos?', 'consulta_estructurada_lotes_contar'], ['Lista los lotes', 'consulta_estructurada_lotes_listar'], ['Detalle del lote 1', 'consulta_estructurada_lotes_listar'], ['¿Qué lotes tiene INC-006?', 'consulta_estructurada_lotes_listar'], ['Lotes de especie frijol', 'consulta_estructurada_lotes_listar'], ['Estado del lote 1', 'consulta_estructurada_lotes_estado'],
            ['Qué es un frasco', 'consulta_estructurada_conocimiento_explicar'], ['Cuántos frascos existen', 'consulta_estructurada_frascos_contar'], ['Lista los frascos', 'consulta_estructurada_frascos_listar'], ['Muéstrame los frascos del lote 1', 'consulta_estructurada_frascos_listar'], ['Estado del frasco 1', 'consulta_estructurada_frascos_estado'], ['Detalle del frasco 1', 'consulta_estructurada_frascos_listar'], ['Frascos activos', 'consulta_estructurada_frascos_listar'],
            ['Qué es una especie', 'consulta_estructurada_conocimiento_explicar'], ['Cuántas especies hay', 'consulta_estructurada_especies_contar'], ['Qué especies tenemos', 'consulta_estructurada_especies_listar'], ['Detalle de especie frijol', 'consulta_estructurada_especies_listar'], ['Condiciones óptimas del frijol', 'consulta_estructurada_condiciones_optimas_listar'], ['Cuál es la temperatura óptima del frijol', 'consulta_estructurada_condiciones_optimas_listar_temperatura'], ['Cuál es la humedad recomendada para Phaseolus vulgaris', 'consulta_estructurada_condiciones_optimas_listar_humedad'],
            ['Posiciones de INC-006', 'consulta_estructurada_posiciones_listar'], ['Estados de incubadora', 'consulta_estructurada_catalogos_listar'], ['Estados de lote', 'consulta_estructurada_catalogos_listar'], ['Estados de frasco', 'consulta_estructurada_catalogos_listar'], ['Modos de control', 'consulta_estructurada_catalogos_listar'],
            ['Etapas de desarrollo', 'consulta_estructurada_catalogos_listar'], ['Etapa actual del lote 1', 'consulta_estructurada_seguimientos_ultimo_etapa'], ['Etapa actual del frasco 1', 'consulta_estructurada_seguimientos_ultimo_etapa'],
            ['Cuál fue el último seguimiento del lote 1', 'consulta_estructurada_seguimientos_ultimo'], ['Último seguimiento del frasco 1', 'consulta_estructurada_seguimientos_ultimo'], ['Seguimientos del lote 1', 'consulta_estructurada_seguimientos_listar'], ['Seguimientos del frasco 1', 'consulta_estructurada_seguimientos_listar'], ['Lotes sin seguimiento reciente', 'consulta_estructurada_lotes_listar'], ['Hay frascos sin seguimiento reciente', 'consulta_estructurada_frascos_listar'],
            ['Cantidad de evidencias', 'consulta_estructurada_evidencias_contar'], ['Evidencias del lote 1', 'consulta_estructurada_evidencias_listar'], ['Última evidencia', 'consulta_estructurada_evidencias_ultimo'],
            ['Qué es un control', 'consulta_estructurada_conocimiento_explicar'], ['Último control', 'consulta_estructurada_controles_ultimo'], ['Controles recientes', 'consulta_estructurada_controles_listar'], ['Modo de control', 'consulta_estructurada_controles_ultimo_modo_control'], ['Tipos de control', 'consulta_estructurada_catalogos_listar'], ['Estado del control', 'consulta_estructurada_controles_estado'],
            ['Resumen del sistema', 'consulta_estructurada_resumen_resumen'], ['Qué pasó hoy', 'consulta_estructurada_resumen_resumen'], ['Hay algo que requiera atención', 'consulta_estructurada_resumen_resumen'],
            ['Elimina la incubadora 4', 'accion_no_permitida'], ['Crea usuarios activos', 'accion_no_permitida'], ['Cambia el modo a manual', 'accion_no_permitida'], ['Ejecuta DROP TABLE users', 'accion_no_permitida'],
            ['Cuándo juega mi equipo', 'consulta_estructurada_conversacion_desconocida'], ['¿Qué es una lectura de microclima?', 'consulta_estructurada_conocimiento_explicar'], ['Qué significa humedad baja', 'consulta_estructurada_conocimiento_explicar_humedad'], ['Qué es una condición óptima', 'consulta_estructurada_conocimiento_explicar'],
            ['Alerta', 'consulta_estructurada_conocimiento_explicar'], ['Me preocupa una alerta', 'consulta_estructurada_alertas_listar'], ['Activa incubadora 1', 'accion_no_permitida'],
        ];
        $resultado = [];
        foreach ($casos as [$pregunta, $intencion]) {
            foreach (['super_admin', 'encargado'] as $rol) {
                $resultado[$pregunta.' | '.$rol] = [$pregunta, $intencion, $rol];
            }
        }

        return $resultado;
    }

    #[DataProvider('preguntas')]
    public function test_preguntas_y_contrato_sin_escrituras(string $pregunta, string $intencion, string $rol): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $respuesta = $this->responderChat($pregunta, $rol === 'super_admin' ? $this->admin : $this->encargado);
        $consultas = DB::getQueryLog();
        DB::disableQueryLog();
        $this->assertSame($intencion, $respuesta['intencion']);
        $this->assertNotEmpty($respuesta['respuesta']);
        $this->assertIsBool($respuesta['reconocida']);
        $this->assertNotEmpty($respuesta['sugerencias']);
        $this->assertStringNotContainsString('privado.jpg', $respuesta['respuesta']);
        if ($rol === 'encargado') {
            $this->assertStringNotContainsString('Reservado', $respuesta['respuesta']);
            $this->assertStringNotContainsString('99.9', $respuesta['respuesta']);
        }
        foreach ($consultas as $consulta) {
            $this->assertMatchesRegularExpression('/^select\b/i', $consulta['query']);
        }
        $this->assertLessThanOrEqual(30, count($consultas), 'Consulta demasiado costosa: '.$pregunta);
        if (str_starts_with($intencion, 'consulta_estructurada_conocimiento_explicar')) {
            $this->assertStringNotContainsString('Hay 2 alertas', $respuesta['respuesta']);
            foreach ($consultas as $consulta) {
                $this->assertStringNotContainsString('"alertas"', $consulta['query']);
            }
        }
    }

    public function test_conteos_datos_reales_y_comparacion_de_rangos(): void
    {
        $this->assertStringContainsString('2 incubadoras', $this->responderChat('Cuántas incubadoras hay', $this->admin)['respuesta']);
        $this->assertMatchesRegularExpression('/\b1 incubadoras?\b/iu', $this->responderChat('Cuántas incubadoras hay', $this->encargado)['respuesta']);
        $this->assertStringContainsString('31.2', $this->responderChat('Temperatura de INC-006', $this->encargado)['respuesta']);
        $this->assertStringContainsString('por encima', $this->responderChat('Cómo está INC-006', $this->encargado)['respuesta']);
        $this->assertMatchesRegularExpression('/(?<![\d.])22(?:\.0+)?\s*–\s*28(?:\.0+)?(?![\d.])\s*°C/u', $this->responderChat('Temperatura óptima del frijol', $this->encargado)['respuesta']);
        $this->assertMatchesRegularExpression('/(?<![\d.])60(?:\.0+)?\s*–\s*80(?:\.0+)?(?![\d.])\s*%/u', $this->responderChat('Humedad recomendada para Phaseolus vulgaris', $this->encargado)['respuesta']);
    }

    public function test_ids_ajenos_no_se_sustituyen_por_recursos_propios(): void
    {
        foreach (['Temperatura de INC-008', 'Detalle del lote 2', 'Frascos del lote 2', 'Seguimientos del frasco 2', 'Evidencias del lote 2', 'Último control de INC-008', 'Condiciones óptimas de Reservado', 'Temperatura de INC-999'] as $pregunta) {
            $r = $this->responderChat($pregunta, $this->encargado);
            $this->assertStringContainsString('No encontré', $r['respuesta'], $pregunta);
            $this->assertStringNotContainsString('31.2', $r['respuesta']);
        }
    }

    public function test_asignaciones_vencidas_y_futuras_no_dan_acceso(): void
    {
        foreach ([['fecha_inicio' => now()->subDays(4), 'fecha_fin' => now()->subDay()], ['fecha_inicio' => now()->addDay(), 'fecha_fin' => null]] as $fechas) {
            DB::table('asignaciones_incubadora')->where('user_id', $this->encargado->id)->update($fechas);
            foreach (['Resumen del sistema', 'Lecturas recientes', 'Alertas activas', 'Lotes activos', 'Lista los frascos', 'Qué especies tenemos', 'Seguimientos de lote', 'Seguimientos de frasco', 'Cantidad de evidencias', 'Controles recientes'] as $pregunta) {
                $r = $this->responderChat($pregunta, $this->encargado);
                $this->assertStringNotContainsString('INC-006', $r['respuesta'], $pregunta);
                $this->assertStringNotContainsString('LOT-001', $r['respuesta'], $pregunta);
                $this->assertStringNotContainsString('31.2', $r['respuesta'], $pregunta);
            }
        }
    }

    public function test_temporal_y_sin_datos_y_ambiguedad(): void
    {
        DB::table('lecturas_microclima')->update(['fecha_hora' => now()->subDays(10)]);
        $this->assertMatchesRegularExpression('/\bno (?:encontré|se encontraron) lecturas\b/iu', $this->responderChat('Lecturas recientes hoy', $this->admin)['respuesta']);
        $this->assertStringContainsString('31.2', $this->responderChat('Última lectura', $this->encargado)['respuesta']);
        DB::table('alertas')->update(['estado_alerta_id' => 2]);
        $this->assertMatchesRegularExpression('/\bno (?:hay|encontré|se encontraron) alertas\b/iu', $this->responderChat('Hay alertas activas', $this->admin)['respuesta']);
        User::factory()->create(['name' => 'Juan Segundo']);
        $ambigua = $this->responderChat('Qué incubadoras tiene Juan', $this->admin);
        $this->assertSame('consulta_estructurada_asignaciones_listar', $ambigua['intencion']);
        $this->assertEqualsCanonicalizing(['Juan', 'Juan Segundo'], $ambigua['sugerencias']);
        $this->assertMatchesRegularExpression('/\b(?:varias coincidencias|más de un registro)\b/iu', $ambigua['respuesta']);
        $this->assertMatchesRegularExpression('/^\s*\d+\.\s*Juan\s*$/mu', $ambigua['respuesta']);
        $this->assertStringContainsString('Juan Segundo', $ambigua['respuesta']);
        $this->assertStringNotContainsString('@', json_encode($ambigua, JSON_THROW_ON_ERROR));
        $this->assertStringContainsString('Indica la especie', $this->responderChat('Condiciones óptimas', $this->admin)['respuesta']);
    }

    public function test_endpoint_autenticado_y_usuario_anonimo(): void
    {
        $this->postJson(route('chatbot.mensaje'), ['chat_id' => (string) Str::uuid(), 'mensaje' => 'Hola'])->assertUnauthorized();
        $this->actingAs($this->encargado)->postJson(route('chatbot.mensaje'), ['chat_id' => (string) Str::uuid(), 'mensaje' => 'Qué significa una alerta'])
            ->assertOk()->assertJsonPath('intencion', 'consulta_estructurada_conocimiento_explicar')->assertJsonPath('ok', true);
        $this->actingAs($this->admin)->postJson(route('chatbot.mensaje'), ['chat_id' => (string) Str::uuid(), 'mensaje' => 'Última lectura de INC-008'])
            ->assertOk()->assertJsonPath('intencion', 'consulta_estructurada_lecturas_ultimo');
    }

    public function test_filtros_combinados_no_devuelven_datos_de_otra_incubadora(): void
    {
        foreach (['Frascos de INC-006', 'Seguimientos de lote de INC-006', 'Seguimientos de frasco de INC-006', 'Evidencias de INC-006', ] as $pregunta) {
            $r = $this->responderChat($pregunta, $this->admin);
            $this->assertStringContainsString('Encontré 1', $r['respuesta'], $pregunta);
            $this->assertStringNotContainsString('RESERVADO', $r['respuesta']);
        }
        $alertas = $this->responderChat('Alertas del lote 1', $this->admin);
        $this->assertMatchesRegularExpression('/\b1 alertas?\b/iu', $alertas['respuesta']);
        $this->assertStringContainsString('LOT-001', $alertas['respuesta']);
        $this->assertStringNotContainsString('INC-008', $alertas['respuesta']);
        $this->assertStringContainsString('21/09/2026', $this->responderChat('Seguimientos del lote 1 hoy', $this->admin)['respuesta']);
    }

    public function test_cada_consulta_maneja_un_encargado_sin_asignaciones(): void
    {
        DB::table('asignaciones_incubadora')->where('user_id', $this->encargado->id)->delete();
        foreach (self::preguntas() as [$pregunta, $intencion, $rol]) {
            if ($rol !== 'encargado') {
                continue;
            }
            $r = $this->responderChat($pregunta, $this->encargado);
            $this->assertSame($intencion, $r['intencion'], $pregunta);
            $this->assertStringNotContainsString('31.2', $r['respuesta']);
            $this->assertStringNotContainsString('99.9', $r['respuesta']);
            $this->assertStringNotContainsString('Temperatura alta visible', $r['respuesta']);
        }
    }

    public function test_preocupacion_consulta_solo_alertas_abiertas_del_ambito_actual(): void
    {
        // La alerta propia está cerrada; solamente queda abierta la de otra incubadora.
        DB::table('alertas')->where('incubadora_id', 1)->update(['estado_alerta_id' => 2]);
        DB::flushQueryLog();
        DB::enableQueryLog();
        $admin = $this->responderChat('Me preocupa una alerta', $this->admin);
        $encargado = $this->responderChat('Me preocupa una alerta', $this->encargado);
        $consultas = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertSame('consulta_estructurada_alertas_listar', $admin['intencion']);
        $this->assertStringContainsString('INC-008', $admin['respuesta']);
        $this->assertStringNotContainsString('Temperatura alta visible', $admin['respuesta']);
        $this->assertSame('consulta_estructurada_alertas_listar', $encargado['intencion']);
        $this->assertMatchesRegularExpression('/\bno (?:hay|encontré|se encontraron) alertas\b/iu', $encargado['respuesta']);
        $this->assertStringNotContainsString('INC-008', $encargado['respuesta']);
        $this->assertStringNotContainsString('Reservado', $encargado['respuesta']);
        foreach ($consultas as $consulta) {
            $this->assertMatchesRegularExpression('/^select\b/i', $consulta['query']);
        }
    }

    public function test_cuenta_inactiva_y_rol_ajeno_no_reciben_datos(): void
    {
        $this->encargado->activo = false;
        $this->assertSame('acceso_denegado', $this->responderChat('Resumen del sistema', $this->encargado)['intencion']);
        $this->encargado->activo = true;
        $rol = clone $this->encargado->rol;
        $rol->clave = 'otro';
        $this->encargado->setRelation('rol', $rol);
        $this->assertSame('acceso_denegado', $this->responderChat('Resumen del sistema', $this->encargado)['intencion']);
    }

    public function test_numero_de_frasco_local_y_condiciones_ambiguas(): void
    {
        $r = $this->responderChat('Detalle del frasco 1 del lote 2', $this->admin);
        // El contrato público no expone IDs: vincular número y lote en la misma descripción.
        $this->assertSame('consulta_estructurada_frascos_listar', $r['intencion']);
        $this->assertMatchesRegularExpression('/\bFrasco\s+1\b[^\r\n]*\blote:\s*LOT-RESERVADO\b/iu', $r['respuesta']);
        $this->assertStringContainsString('INC-008', $r['respuesta']);
        $this->assertStringNotContainsString('LOT-001', $r['respuesta']);
        $this->assertStringNotContainsString('INC-006', $r['respuesta']);
        $this->insertar('condiciones_optimas_especie', ['especie_id' => 1, 'temperatura_min' => 10, 'temperatura_max' => 12, 'humedad_min' => 30, 'humedad_max' => 40]);
        $this->assertStringContainsString('varias configuraciones', $this->responderChat('Cómo está INC-006', $this->admin)['respuesta']);
        $this->assertStringNotContainsString('por encima', $this->responderChat('Cómo está INC-006', $this->admin)['respuesta']);
    }
}
