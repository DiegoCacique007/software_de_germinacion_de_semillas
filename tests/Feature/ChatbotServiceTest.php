<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            $this->insertar('registros_biologicos', ['lote_id' => $id, 'fecha_registro' => now()->toDateString(), 'dias_estratificacion' => 3, 'tasa_germinacion' => 50]);
            $this->insertar('controles_incubadora', ['incubadora_id' => $id, 'tipo_control_incubadora_id' => 1, 'modo_control_incubadora_id' => 1, 'valor_aplicado' => 1, 'fecha_hora' => now()]);
        }
    }

    private function insertar(string $tabla, array $valores): void
    {
        DB::table($tabla)->insert($valores + ['created_at' => now(), 'updated_at' => now()]);
    }

    public static function preguntas(): array
    {
        $casos = [
            ['Hola', 'saludo'], ['Adiós', 'despedida'], ['Muchas gracias', 'agradecimiento'],
            ['Ayuda', 'ayuda'], ['¿Qué puedes hacer?', 'capacidades_chatbot'],
            ['¿Qué es MicroSeed Control?', 'informacion_sistema'],
            ['¿Qué es una alerta?', 'definicion_alerta'], ['que significa alerta', 'definicion_alerta'], ['para que sirven las alertas', 'definicion_alerta'],
            ['¿Hay alertas?', 'alertas_activas'], ['cuantas alertas activas tenemos', 'cantidad_alertas'], ['muéstrame las alertas', 'alertas_activas'],
            ['Muéstrame las últimas alertas', 'ultimas_alertas'], ['Última alerta', 'ultima_alerta'], ['¿La incubadora 1 tiene alertas?', 'alertas_por_incubadora'],
            ['Alertas críticas', 'alertas_criticas'], ['Qué significa el estado de una alerta', 'explicacion_estado_alerta'], ['Niveles de alerta', 'explicacion_nivel_alerta'], ['Tipos de alerta', 'explicacion_tipo_alerta'],
            ['Qué es una incubadora', 'definicion_incubadora'], ['Cuántas incubadoras hay?', 'cantidad_incubadoras'], ['cuantas incubadoras existen', 'cantidad_incubadoras'], ['¿Cuántas incubadoras tenemos?', 'cantidad_incubadoras'], ['numero de incubadoras', 'cantidad_incubadoras'], ['total incubadoras', 'cantidad_incubadoras'],
            ['Muéstrame las incubadoras', 'listar_incubadoras'], ['¿Cómo están las incubadoras?', 'estado_incubadoras'], ['¿Cómo está INC-006?', 'estado_incubadora_especifica'], ['Detalle de INC-006', 'detalle_incubadora'], ['Incubadoras activas', 'incubadoras_activas'], ['Incubadoras con alertas', 'incubadoras_con_alertas'], ['Mis incubadoras asignadas', 'incubadoras_asignadas'],
            ['¿Cuál fue la última lectura?', 'ultima_lectura'], ['¿Cuál fue la última lectura de INC-006?', 'ultima_lectura_incubadora'], ['¿Qué temperatura hay?', 'temperatura_actual'], ['Humedad actual', 'humedad_actual'], ['Temperatura de INC-006', 'temperatura_incubadora'], ['¿Cuál es la humedad de INC-008?', 'humedad_incubadora'], ['Lecturas recientes', 'lecturas_recientes'], ['Estado del microclima', 'estado_microclima'],
            ['Cuántos usuarios hay', 'cantidad_usuarios'], ['Cantidad de encargados', 'cantidad_encargados'], ['Usuarios activos', 'usuarios_activos'], ['Usuarios inactivos', 'usuarios_inactivos'], ['Información del usuario Juan', 'informacion_usuario'], ['Roles', 'roles'],
            ['Cantidad de asignaciones', 'cantidad_asignaciones'], ['¿Qué incubadoras tiene Juan?', 'incubadoras_por_encargado'], ['¿Quién tiene asignada INC-006?', 'encargado_de_incubadora'], ['Asignaciones activas', 'asignaciones_activas'],
            ['Qué es un lote', 'definicion_lote'], ['Cuántos lotes existen', 'cantidad_lotes'], ['¿Cuántos lotes están activos?', 'lotes_activos'], ['Lista los lotes', 'listar_lotes'], ['Detalle del lote 1', 'detalle_lote'], ['¿Qué lotes tiene INC-006?', 'lotes_por_incubadora'], ['Lotes de especie frijol', 'lotes_por_especie'], ['Estado del lote 1', 'estado_lote'],
            ['Qué es un frasco', 'definicion_frasco'], ['Cuántos frascos existen', 'cantidad_frascos'], ['Lista los frascos', 'listar_frascos'], ['Muéstrame los frascos del lote 1', 'frascos_por_lote'], ['Estado del frasco 1', 'estado_frasco'], ['Detalle del frasco 1', 'detalle_frasco'], ['Frascos activos', 'frascos_activos'],
            ['Qué es una especie', 'definicion_especie'], ['Cuántas especies hay', 'cantidad_especies'], ['Qué especies tenemos', 'listar_especies'], ['Detalle de especie frijol', 'detalle_especie'], ['Condiciones óptimas del frijol', 'condiciones_optimas'], ['Cuál es la temperatura óptima del frijol', 'temperatura_optima'], ['Cuál es la humedad recomendada para Phaseolus vulgaris', 'humedad_optima'],
            ['Posiciones de INC-006', 'posiciones_incubadora'], ['Estados de incubadora', 'estados_incubadora'], ['Estados de lote', 'estados_lote'], ['Estados de frasco', 'estados_frasco'], ['Modos de control', 'modos_control'],
            ['Etapas de desarrollo', 'etapas_desarrollo'], ['Etapa actual del lote 1', 'etapa_actual_lote'], ['Etapa actual del frasco 1', 'etapa_actual_frasco'],
            ['Cuál fue el último seguimiento del lote 1', 'ultimo_seguimiento_lote'], ['Último seguimiento del frasco 1', 'ultimo_seguimiento_frasco'], ['Seguimientos del lote 1', 'seguimientos_lote'], ['Seguimientos del frasco 1', 'seguimientos_frasco'], ['Lotes sin seguimiento reciente', 'lotes_sin_seguimiento_reciente'], ['Hay frascos sin seguimiento reciente', 'frascos_sin_seguimiento_reciente'],
            ['Cantidad de evidencias', 'cantidad_evidencias'], ['Evidencias del lote 1', 'evidencias_lote'], ['Última evidencia', 'ultima_evidencia'],
            ['Últimos registros biológicos', 'ultimos_registros_biologicos'], ['Registros biológicos del lote 1', 'registros_por_lote'], ['Registros biológicos del frasco 1', 'registros_por_frasco'],
            ['Qué es un control', 'definicion_control'], ['Último control', 'ultimo_control'], ['Controles recientes', 'controles_recientes'], ['Modo de control', 'modo_control'], ['Tipos de control', 'tipos_control'], ['Estado del control', 'estado_control'],
            ['Resumen del sistema', 'resumen_sistema'], ['Qué pasó hoy', 'resumen_sistema'], ['Hay algo que requiera atención', 'resumen_sistema'],
            ['Elimina la incubadora 4', 'solo_consulta'], ['Crea usuarios activos', 'solo_consulta'], ['Cambia el modo a manual', 'solo_consulta'], ['Ejecuta DROP TABLE users', 'solo_consulta'],
            ['Cuándo juega mi equipo', 'pregunta_desconocida'], ['¿Qué es una lectura de microclima?', 'definicion_lectura'], ['Qué significa humedad baja', 'definicion_humedad'], ['Qué es una condición óptima', 'definicion_condicion'],
            ['Alerta', 'pregunta_desconocida'], ['Me preocupa una alerta', 'pregunta_desconocida'], ['Activa incubadora 1', 'solo_consulta'],
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
        $respuesta = app(ChatbotService::class)->responder($pregunta, $rol === 'super_admin' ? $this->admin : $this->encargado);
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
        if (str_starts_with($intencion, 'definicion_')) {
            $this->assertStringNotContainsString('Hay 2 alertas', $respuesta['respuesta']);
            foreach ($consultas as $consulta) {
                $this->assertStringNotContainsString('"alertas"', $consulta['query']);
            }
        }
    }

    public function test_conteos_datos_reales_y_comparacion_de_rangos(): void
    {
        $s = app(ChatbotService::class);
        $this->assertStringContainsString('hay 2 incubadoras', $s->responder('Cuántas incubadoras hay', $this->admin)['respuesta']);
        $this->assertStringContainsString('hay 1 incubadoras', $s->responder('Cuántas incubadoras hay', $this->encargado)['respuesta']);
        $this->assertStringContainsString('31.2', $s->responder('Temperatura de INC-006', $this->encargado)['respuesta']);
        $this->assertStringContainsString('por encima', $s->responder('Cómo está INC-006', $this->encargado)['respuesta']);
        $this->assertStringContainsString('22.0–28.0', $s->responder('Temperatura óptima del frijol', $this->encargado)['respuesta']);
        $this->assertStringContainsString('60.0–80.0', $s->responder('Humedad recomendada para Phaseolus vulgaris', $this->encargado)['respuesta']);
    }

    public function test_ids_ajenos_no_se_sustituyen_por_recursos_propios(): void
    {
        foreach (['Temperatura de INC-008', 'Detalle del lote 2', 'Frascos del lote 2', 'Seguimientos del frasco 2', 'Evidencias del lote 2', 'Registros biológicos del lote 2', 'Último control de INC-008', 'Condiciones óptimas de Reservado', 'Temperatura de INC-999'] as $pregunta) {
            $r = app(ChatbotService::class)->responder($pregunta, $this->encargado);
            $this->assertStringContainsString('No encontré', $r['respuesta'], $pregunta);
            $this->assertStringNotContainsString('31.2', $r['respuesta']);
        }
    }

    public function test_asignaciones_vencidas_y_futuras_no_dan_acceso(): void
    {
        foreach ([['fecha_inicio' => now()->subDays(4), 'fecha_fin' => now()->subDay()], ['fecha_inicio' => now()->addDay(), 'fecha_fin' => null]] as $fechas) {
            DB::table('asignaciones_incubadora')->where('user_id', $this->encargado->id)->update($fechas);
            foreach (['Resumen del sistema', 'Lecturas recientes', 'Alertas activas', 'Lotes activos', 'Lista los frascos', 'Qué especies tenemos', 'Seguimientos de lote', 'Seguimientos de frasco', 'Cantidad de evidencias', 'Últimos registros biológicos', 'Controles recientes'] as $pregunta) {
                $r = app(ChatbotService::class)->responder($pregunta, $this->encargado);
                $this->assertStringNotContainsString('INC-006', $r['respuesta'], $pregunta);
                $this->assertStringNotContainsString('LOT-001', $r['respuesta'], $pregunta);
                $this->assertStringNotContainsString('31.2', $r['respuesta'], $pregunta);
            }
        }
    }

    public function test_temporal_y_sin_datos_y_ambiguedad(): void
    {
        DB::table('lecturas_microclima')->update(['fecha_hora' => now()->subDays(10)]);
        $s = app(ChatbotService::class);
        $this->assertStringContainsString('No encontré lecturas', $s->responder('Lecturas recientes hoy', $this->admin)['respuesta']);
        $this->assertStringContainsString('31.2', $s->responder('Última lectura', $this->encargado)['respuesta']);
        DB::table('alertas')->update(['estado_alerta_id' => 2]);
        $this->assertStringContainsString('No hay alertas', $s->responder('Hay alertas activas', $this->admin)['respuesta']);
        User::factory()->create(['name' => 'Juan Segundo']);
        $this->assertStringContainsString('más de un registro', $s->responder('Qué incubadoras tiene Juan', $this->admin)['respuesta']);
        $this->assertStringContainsString('Indica el nombre', $s->responder('Condiciones óptimas', $this->admin)['respuesta']);
    }

    public function test_endpoint_autenticado_y_usuario_anonimo(): void
    {
        $this->postJson(route('chatbot.mensaje'), ['mensaje' => 'Hola'])->assertUnauthorized();
        $this->actingAs($this->encargado)->postJson(route('chatbot.mensaje'), ['mensaje' => 'Qué significa una alerta'])
            ->assertOk()->assertJsonPath('intencion', 'definicion_alerta')->assertJsonPath('ok', true);
        $this->actingAs($this->admin)->postJson(route('chatbot.mensaje'), ['mensaje' => 'Última lectura de INC-008'])
            ->assertOk()->assertJsonPath('intencion', 'ultima_lectura_incubadora');
    }

    public function test_filtros_combinados_no_devuelven_datos_de_otra_incubadora(): void
    {
        $s = app(ChatbotService::class);
        foreach (['Frascos de INC-006', 'Seguimientos de lote de INC-006', 'Seguimientos de frasco de INC-006', 'Evidencias de INC-006', 'Registros biológicos de INC-006'] as $pregunta) {
            $r = $s->responder($pregunta, $this->admin);
            $this->assertStringContainsString('Encontré 1', $r['respuesta'], $pregunta);
            $this->assertStringNotContainsString('RESERVADO', $r['respuesta']);
        }
        $this->assertStringContainsString('Hay 1 alertas', $s->responder('Alertas del lote 1', $this->admin)['respuesta']);
        $this->assertStringContainsString('revisión 21/09/2026', $s->responder('Seguimientos del lote 1 hoy', $this->admin)['respuesta']);
    }

    public function test_cada_consulta_maneja_un_encargado_sin_asignaciones(): void
    {
        DB::table('asignaciones_incubadora')->where('user_id', $this->encargado->id)->delete();
        foreach (self::preguntas() as [$pregunta, $intencion, $rol]) {
            if ($rol !== 'encargado') {
                continue;
            }
            $r = app(ChatbotService::class)->responder($pregunta, $this->encargado);
            $this->assertSame($intencion, $r['intencion'], $pregunta);
            $this->assertStringNotContainsString('31.2', $r['respuesta']);
            $this->assertStringNotContainsString('99.9', $r['respuesta']);
            $this->assertStringNotContainsString('Temperatura alta visible', $r['respuesta']);
        }
    }

    public function test_cuenta_inactiva_y_rol_ajeno_no_reciben_datos(): void
    {
        $this->encargado->activo = false;
        $this->assertSame('acceso_denegado', app(ChatbotService::class)->responder('Resumen del sistema', $this->encargado)['intencion']);
        $this->encargado->activo = true;
        $rol = clone $this->encargado->rol;
        $rol->clave = 'otro';
        $this->encargado->setRelation('rol', $rol);
        $this->assertSame('acceso_denegado', app(ChatbotService::class)->responder('Resumen del sistema', $this->encargado)['intencion']);
    }

    public function test_numero_de_frasco_local_y_condiciones_ambiguas(): void
    {
        $s = app(ChatbotService::class);
        $r = $s->responder('Detalle del frasco 1 del lote 2', $this->admin);
        $this->assertStringContainsString('Frasco ID 2 / número 1', $r['respuesta']);
        $this->insertar('condiciones_optimas_especie', ['especie_id' => 1, 'temperatura_min' => 10, 'temperatura_max' => 12, 'humedad_min' => 30, 'humedad_max' => 40]);
        $this->assertStringContainsString('varias configuraciones', $s->responder('Cómo está INC-006', $this->admin)['respuesta']);
        $this->assertStringNotContainsString('por encima', $s->responder('Cómo está INC-006', $this->admin)['respuesta']);
    }
}
