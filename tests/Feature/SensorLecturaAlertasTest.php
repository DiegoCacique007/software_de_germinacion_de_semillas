<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\CondicionOptimaEspecie;
use App\Models\EstadoAlerta;
use App\Models\LecturaMicroclima;
use App\Models\Lote;
use Database\Seeders\MicroseedConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SensorLecturaAlertasTest extends TestCase
{
    use RefreshDatabase;

    private int $incubadoraId;
    private int $loteId;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.sensor.token' => 'sensor-test-only']);
        $this->seed(MicroseedConfigSeeder::class);
        $this->incubadoraId = DB::table('incubadoras')->value('id');
        $posicion = DB::table('posiciones_incubadora')->insertGetId(['incubadora_id' => $this->incubadoraId, 'numero_posicion' => 1]);
        $especie = DB::table('especies')->insertGetId(['nombre_comun' => 'Especie de prueba']);
        $estado = DB::table('estados_lote')->insertGetId(['clave' => 'prueba', 'nombre' => 'Prueba']);
        $this->loteId = DB::table('lotes')->insertGetId([
            'posicion_incubadora_id' => $posicion, 'especie_id' => $especie,
            'estado_lote_id' => $estado, 'codigo_lote' => 'L-1', 'fecha_siembra' => '2026-09-01',
        ]);
        CondicionOptimaEspecie::create(['especie_id' => $especie, 'temperatura_min' => 20,
            'temperatura_max' => 30, 'humedad_min' => 60, 'humedad_max' => 80]);
    }

    private function lectura(array $datos = [])
    {
        return $this->withHeader('X-SENSOR-TOKEN', 'sensor-test-only')->postJson('/api/sensores/lecturas', array_replace([
            'incubadora_id' => $this->incubadoraId, 'temperatura' => 25, 'humedad' => 70,
        ], $datos));
    }

    public static function mediciones(): array
    {
        return [
            'normal' => [25, 70, []],
            'limites' => [20, 80, []],
            'temperatura alta' => [35, 70, ['temperatura']],
            'temperatura baja' => [19, 70, ['temperatura']],
            'humedad baja' => [25, 50, ['humedad']],
            'humedad alta' => [25, 90, ['humedad']],
            'ambas' => [35, 50, ['temperatura', 'humedad']],
        ];
    }

    #[DataProvider('mediciones')]
    public function test_lectura_genera_alertas_relacionadas(float $temperatura, float $humedad, array $tipos): void
    {
        $r = $this->lectura(compact('temperatura', 'humedad'));
        $r->assertCreated()->assertJsonPath('ok', true)->assertJsonPath('alertas.evaluadas', true)
            ->assertJsonPath('alertas.generadas', count($tipos));
        $this->assertDatabaseCount('lecturas_microclima', 1);
        $this->assertDatabaseCount('alertas', count($tipos));
        $this->assertEqualsCanonicalizing($tipos, Alerta::with('tipo')->get()->pluck('tipo.clave')->all());
        foreach (Alerta::all() as $alerta) {
            $this->assertSame($this->loteId, $alerta->lote->id);
            $this->assertSame($r->json('data.id'), $alerta->lecturaMicroclima->id);
            $this->assertSame($this->incubadoraId, $alerta->incubadora_id);
            $this->assertNull($alerta->atendida_por);
        }
    }

    public function test_no_duplica_y_no_cierra_alertas_al_volver_al_rango(): void
    {
        $id = $this->lectura(['temperatura' => 35])->assertCreated()->json('data.id');
        $this->lectura(['temperatura' => 36])->assertJsonPath('alertas.generadas', 0);
        $this->lectura()->assertJsonPath('alertas.generadas', 0);
        $this->assertDatabaseCount('alertas', 1);
        $this->assertSame($id, Alerta::first()->lectura_microclima_id);
        $this->assertSame('pendiente', Alerta::first()->estado->clave);
    }

    public function test_lotes_con_codigos_similares_son_independientes(): void
    {
        $lote = Lote::findOrFail($this->loteId)->replicate();
        $lote->codigo_lote = 'L-10';
        $lote->save();
        $this->lectura(['temperatura' => 35])->assertJsonPath('alertas.generadas', 2);
        $this->lectura(['temperatura' => 36])->assertJsonPath('alertas.generadas', 0);
        $this->assertDatabaseCount('alertas', 2);
    }

    public function test_otra_incubadora_no_evalua_lotes_ajenos(): void
    {
        $otra = DB::table('incubadoras')->insertGetId(['codigo' => 'OTRA', 'nombre' => 'Otra',
            'estado_incubadora_id' => DB::table('estados_incubadora')->value('id')]);
        $this->lectura(['incubadora_id' => $otra, 'temperatura' => 35])->assertJsonPath('alertas.generadas', 0);
    }

    public function test_incubadora_es_obligatoria_y_debe_existir(): void
    {
        $this->withHeader('X-SENSOR-TOKEN', 'sensor-test-only')->postJson('/api/sensores/lecturas', [
            'temperatura' => 25, 'humedad' => 70,
        ])->assertUnprocessable()->assertJsonValidationErrors('incubadora_id');
        $this->lectura(['incubadora_id' => 999999])->assertUnprocessable();
        $this->assertDatabaseCount('lecturas_microclima', 0);
    }

    public function test_token_incorrecto_ausente_o_sin_configurar(): void
    {
        $this->postJson('/api/sensores/lecturas', [])->assertUnauthorized();
        $this->withHeader('X-SENSOR-TOKEN', 'incorrecto')->postJson('/api/sensores/lecturas', [])->assertUnauthorized();
        config(['services.sensor.token' => null]);
        $this->lectura()->assertUnauthorized();
        $this->assertDatabaseCount('lecturas_microclima', 0);
    }

    public function test_fallo_de_configuracion_conserva_lectura_sin_exponer_excepcion(): void
    {
        Log::spy();
        EstadoAlerta::where('clave', 'pendiente')->delete();
        $this->lectura(['temperatura' => 35])->assertCreated()->assertJsonPath('ok', true)
            ->assertJsonPath('alertas.evaluadas', false)->assertJsonPath('alertas.generadas', 0)
            ->assertJsonMissingPath('error');
        $this->assertDatabaseCount('lecturas_microclima', 1);
        $this->assertDatabaseCount('alertas', 0);
        Log::shouldHaveReceived('error')->once();
    }

    public function test_fallo_en_otro_lote_revierte_solo_alertas(): void
    {
        Log::spy();
        $lote = Lote::findOrFail($this->loteId)->replicate();
        $lote->codigo_lote = 'SIN-CONDICION';
        $lote->especie_id = DB::table('especies')->insertGetId(['nombre_comun' => 'Sin condición']);
        $lote->save();
        $this->lectura(['temperatura' => 35])->assertCreated()->assertJsonPath('alertas.evaluadas', false);
        $this->assertDatabaseCount('lecturas_microclima', 1);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_niveles_conservan_umbrales_y_alertas_atendidas_no_bloquean_nuevas(): void
    {
        $cerrada = EstadoAlerta::create(['clave' => 'atendida', 'nombre' => 'Atendida']);
        foreach ([31 => 'bajo', 32 => 'medio', 35 => 'alto'] as $temperatura => $nivel) {
            $this->lectura(['temperatura' => $temperatura])->assertJsonPath('alertas.generadas', 1);
            $alerta = Alerta::latest('id')->first();
            $this->assertSame($nivel, $alerta->nivel->clave);
            $alerta->update(['estado_alerta_id' => $cerrada->id]);
        }
    }

    public function test_relaciones_nullable_preservan_alertas_historicas(): void
    {
        $this->lectura(['temperatura' => 35])->assertCreated();
        $alerta = Alerta::first();
        $manual = $alerta->replicate();
        $manual->lote_id = null;
        $manual->lectura_microclima_id = null;
        $manual->save();
        LecturaMicroclima::first()->delete();
        Lote::find($this->loteId)->delete();
        $this->assertDatabaseCount('alertas', 2);
        $this->assertNull($alerta->fresh()->lote_id);
        $this->assertNull($alerta->fresh()->lectura_microclima_id);
    }

    public function test_error_al_guardar_no_expone_detalles_ni_payload(): void
    {
        Log::spy();
        LecturaMicroclima::creating(function () {
            throw new \RuntimeException('Detalle interno de prueba');
        });
        try {
            $this->lectura()->assertStatus(500)->assertExactJson([
                'ok' => false, 'message' => 'Error interno al registrar la lectura.',
            ]);
            $this->assertDatabaseCount('lecturas_microclima', 0);
            Log::shouldHaveReceived('error')->withArgs(function ($message, $context) {
                return !array_key_exists('payload', $context)
                    && !str_contains(json_encode($context), 'sensor-test-only');
            })->once();
        } finally {
            LecturaMicroclima::flushEventListeners();
        }
    }
}
