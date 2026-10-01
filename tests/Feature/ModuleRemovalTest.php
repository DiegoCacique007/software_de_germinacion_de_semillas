<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModuleRemovalTest extends TestCase
{
    private User $admin;
    private User $encargado;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
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

    private function insertar(string $tabla, array $valores): void
    {
        DB::table($tabla)->insert($valores + ['created_at' => now(), 'updated_at' => now()]);
    }

    public function test_chatbot_conserva_consultas_y_contexto_en_ambos_roles(): void
    {
        foreach ([$this->admin, $this->encargado] as $user) {
            $this->actingAs($user);
            $chatId = (string) Str::uuid();
            $preguntas = [
                ['Resumen del sistema', 'resumen'],
                ['Muéstrame los lotes', 'lotes'],
                ['Muéstrame los seguimientos del lote LOT-001', 'seguimientos'],
                ['¿Cuál es su porcentaje de germinación?', 'seguimientos'],
                ['¿Cuál es su etapa actual?', 'seguimientos'],
                ['¿Cuántas semillas germinaron?', 'seguimientos'],
                ['¿Cuántas semillas germinaron en ese lote?', 'seguimientos'],
                ['Muéstrame sus evidencias', 'evidencias'],
                ['Dame la última lectura', 'lecturas'],
                ['Muéstrame las alertas pendientes', 'alertas'],
                ['¿Qué hace el nebulizador?', 'conocimiento'],
                ['Hola', 'conversacion'],
                ['¿Qué puedes hacer?', 'conversacion'],
            ];
            foreach ($preguntas as [$mensaje, $recurso]) {
                $respuesta = $this->postJson(route('chatbot.mensaje'), ['mensaje' => $mensaje, 'chat_id' => $chatId]);
                $respuesta->assertOk()->assertJsonPath('ok', true);
                $this->assertStringContainsString('consulta_estructurada_'.$recurso, $respuesta->json('intencion'), $mensaje);
                $this->assertStringNotContainsString('error', $respuesta->json('intencion'), $mensaje);
                if (str_contains($mensaje, 'porcentaje')) $this->assertStringContainsString('50', $respuesta->json('respuesta'));
                if (str_contains($mensaje, 'etapa')) $this->assertStringContainsString('Germinacion', $respuesta->json('respuesta'));
                if ($mensaje === '¿Cuántas semillas germinaron?') $this->assertStringContainsString('Indica si deseas consultar seguimientos de lote o seguimientos de frasco', $respuesta->json('respuesta'));
                if (str_contains($mensaje, 'en ese lote')) $this->assertStringContainsString('5', $respuesta->json('respuesta'));
            }
            $this->postJson(route('chatbot.mensaje'), ['mensaje' => '¿Cuál es el porcentaje de carbono del lote?', 'chat_id' => $chatId])
                ->assertOk()->assertJsonPath('intencion', 'consulta_no_soportada');
        }
    }

    public function test_login_y_paginas_conservadas(): void
    {
        $this->get('/login')->assertOk();
        $this->post('/login', ['email' => $this->admin->email, 'password' => 'password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->admin);
        foreach (['dashboard', 'incubadoras.index', 'lotes.index', 'frascos.index', 'seguimientos-lote.index', 'seguimientos-frasco.index', 'evidencias-lote.index'] as $ruta) {
            $this->get(route('super_admin.'.$ruta))->assertOk();
        }
    }

    public function test_reportes_conservan_datos_de_seguimiento_y_filtros(): void
    {
        $this->actingAs($this->admin);
        $csv = $this->get(route('super_admin.reportes.biologico.csv', ['lote_id' => 1]));
        $csv->assertOk();
        $texto = $csv->streamedContent();
        $this->assertStringContainsString('Porcentaje de germinación', $texto);
        $this->assertStringContainsString('LOT-001', $texto);
        $this->assertStringContainsString('50', $texto);
        $this->assertStringNotContainsString('LOT-RESERVADO', $texto);
        $this->assertStringNotContainsString('estratificación', $texto);
        $pdf = $this->get(route('super_admin.reportes.biologico.pdf', ['lote_id' => 1]));
        $pdf->assertOk();
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
        $this->get(route('super_admin.reportes.biologico.csv', ['fecha_inicio' => '2030-01-01']))->assertOk();
    }

    public function test_migracion_puede_reconstruir_solo_la_tabla_retirada(): void
    {
        $antes = Schema::getTableListing();
        $archivos = glob(database_path('migrations/*_drop_*_table.php'));
        $archivo = collect($archivos)->first(fn ($p) => str_contains(basename($p), '2026_09_30'));
        $this->assertNotNull($archivo);
        $migracion = require $archivo;
        $migracion->down();
        $nuevas = array_values(array_diff(Schema::getTableListing(), $antes));
        $this->assertCount(1, $nuevas);
        $this->assertCount(12, Schema::getColumnListing($nuevas[0]));
        $this->assertCount(2, Schema::getForeignKeys($nuevas[0]));
        $migracion->up();
        $this->assertEqualsCanonicalizing($antes, Schema::getTableListing());
    }
}
