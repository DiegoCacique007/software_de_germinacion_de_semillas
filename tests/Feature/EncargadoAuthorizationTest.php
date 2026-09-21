<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EncargadoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $encargadoA;
    private User $encargadoB;
    private User $superAdmin;
    private User $inactivo;

    private int $incubadoraA;
    private int $incubadoraB;

    private int $loteA;
    private int $loteB;

    private int $frascoA;
    private int $frascoB;

    private int $seguimientoA;
    private int $seguimientoB;

    private int $etapaId;
    private int $estadoFrascoId;

    protected function setUp(): void
    {
        parent::setUp();

        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        */

        $this->encargadoA = User::factory()->create([
            'name' => 'Encargado A',
            'email' => 'encargado.a@microseed.test',
        ]);

        $this->encargadoB = User::factory()->create([
            'name' => 'Encargado B',
            'email' => 'encargado.b@microseed.test',
        ]);

        $this->superAdmin = User::factory()
            ->superAdmin()
            ->create([
                'name' => 'Super Admin',
                'email' => 'superadmin@microseed.test',
            ]);

        $this->inactivo = User::factory()->create([
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo@microseed.test',
            'activo' => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Catálogos
        |--------------------------------------------------------------------------
        */

        $estadoIncubadoraId = $this->catalogo(
            'estados_incubadora',
            'activa_test',
            'Activa'
        );

        $estadoLoteId = $this->catalogo(
            'estados_lote',
            'activo_test',
            'Activo'
        );

        $this->estadoFrascoId = $this->catalogo(
            'estados_frasco',
            'activo_test',
            'Activo'
        );

        $this->etapaId = $this->catalogo(
            'etapas_desarrollo',
            'germinacion_test',
            'Germinación'
        );

        /*
        |--------------------------------------------------------------------------
        | Especie
        |--------------------------------------------------------------------------
        */

        $especieId = DB::table('especies')->insertGetId([
            'nombre_comun' => 'Especie de seguridad',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Incubadoras
        |--------------------------------------------------------------------------
        */

        $this->incubadoraA = $this->crearIncubadora(
            'INC-SEG-A',
            'Incubadora A',
            $estadoIncubadoraId
        );

        $this->incubadoraB = $this->crearIncubadora(
            'INC-SEG-B',
            'Incubadora B',
            $estadoIncubadoraId
        );

        /*
        |--------------------------------------------------------------------------
        | Asignaciones
        |--------------------------------------------------------------------------
        */

        DB::table('asignaciones_incubadora')->insert([
            [
                'incubadora_id' => $this->incubadoraA,
                'user_id' => $this->encargadoA->id,
                'fecha_inicio' => now()->subDay()->toDateString(),
                'fecha_fin' => null,
                'observaciones' => 'Asignación de prueba A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incubadora_id' => $this->incubadoraB,
                'user_id' => $this->encargadoB->id,
                'fecha_inicio' => now()->subDay()->toDateString(),
                'fecha_fin' => null,
                'observaciones' => 'Asignación de prueba B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Posiciones
        |--------------------------------------------------------------------------
        */

        $posicionA = $this->crearPosicion($this->incubadoraA);
        $posicionB = $this->crearPosicion($this->incubadoraB);

        /*
        |--------------------------------------------------------------------------
        | Lotes
        |--------------------------------------------------------------------------
        */

        $this->loteA = $this->crearLote(
            $posicionA,
            $especieId,
            $estadoLoteId,
            'LOTE-SEG-A'
        );

        $this->loteB = $this->crearLote(
            $posicionB,
            $especieId,
            $estadoLoteId,
            'LOTE-SEG-B'
        );

        /*
        |--------------------------------------------------------------------------
        | Frascos
        |--------------------------------------------------------------------------
        */

        $this->frascoA = $this->crearFrasco(
            $this->loteA,
            1,
            $this->estadoFrascoId
        );

        $this->frascoB = $this->crearFrasco(
            $this->loteB,
            1,
            $this->estadoFrascoId
        );

        /*
        |--------------------------------------------------------------------------
        | Seguimientos
        |--------------------------------------------------------------------------
        */

        $this->seguimientoA = $this->crearSeguimientoLote(
            $this->loteA,
            $this->encargadoA->id
        );

        $this->seguimientoB = $this->crearSeguimientoLote(
            $this->loteB,
            $this->encargadoB->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function catalogo(
        string $tabla,
        string $clave,
        string $nombre
    ): int {
        $id = DB::table($tabla)
            ->where('clave', $clave)
            ->value('id');

        if ($id) {
            return (int) $id;
        }

        return DB::table($tabla)->insertGetId([
            'clave' => $clave,
            'nombre' => $nombre,
            'descripcion' => 'Registro creado para pruebas de autorización.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearIncubadora(
        string $codigo,
        string $nombre,
        int $estadoId
    ): int {
        return DB::table('incubadoras')->insertGetId([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'ubicacion' => 'Área de pruebas',
            'estado_incubadora_id' => $estadoId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearPosicion(int $incubadoraId): int
    {
        return DB::table('posiciones_incubadora')->insertGetId([
            'incubadora_id' => $incubadoraId,
            'numero_posicion' => 1,
            'descripcion' => 'Posición de prueba',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearLote(
        int $posicionId,
        int $especieId,
        int $estadoId,
        string $codigo
    ): int {
        return DB::table('lotes')->insertGetId([
            'posicion_incubadora_id' => $posicionId,
            'especie_id' => $especieId,
            'codigo_lote' => $codigo,
            'fecha_siembra' => now()->subDays(5)->toDateString(),
            'fecha_inicio' => now()->subDays(5)->toDateString(),
            'estado_lote_id' => $estadoId,
            'observaciones' => 'Lote para pruebas de autorización.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearFrasco(
        int $loteId,
        int $numero,
        int $estadoId
    ): int {
        return DB::table('frascos')->insertGetId([
            'lote_id' => $loteId,
            'numero_frasco' => $numero,
            'cantidad_semillas' => 10,
            'estado_frasco_id' => $estadoId,
            'observaciones' => 'Frasco para pruebas de autorización.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function crearSeguimientoLote(
        int $loteId,
        int $userId
    ): int {
        return DB::table('seguimientos_lote')->insertGetId([
            'lote_id' => $loteId,
            'fecha_revision' => now()->toDateString(),
            'frascos_activos' => 1,
            'semillas_germinadas' => 2,
            'porcentaje_germinacion' => 20,
            'altura_promedio_cm' => 1.5,
            'etapa_desarrollo_id' => $this->etapaId,
            'observaciones' => 'Seguimiento para pruebas.',
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Imagen de prueba sin GD
    |--------------------------------------------------------------------------
    */

    private function imagenPrueba(
        string $nombre = 'evidencia.png'
    ): UploadedFile {
        $contenido = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9ZQmcAAAAASUVORK5CYII='
        );

        return UploadedFile::fake()->createWithContent(
            $nombre,
            $contenido
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    public function test_encargado_puede_entrar_a_su_dashboard(): void
    {
        $this->actingAs($this->encargadoA)
            ->get(route('encargado.dashboard'))
            ->assertOk();
    }

    public function test_encargado_no_puede_entrar_al_area_super_admin(): void
    {
        $this->actingAs($this->encargadoA)
            ->get(route('super_admin.dashboard'))
            ->assertForbidden();
    }

    public function test_super_admin_no_puede_entrar_al_area_encargado(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('encargado.dashboard'))
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Usuario inactivo
    |--------------------------------------------------------------------------
    */

    public function test_usuario_inactivo_no_puede_entrar_al_sistema(): void
    {
        $this->actingAs($this->inactivo)
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs($this->inactivo)
            ->get(route('encargado.dashboard'))
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Incubadoras
    |--------------------------------------------------------------------------
    */

    public function test_encargado_puede_ver_su_incubadora(): void
    {
        $this->actingAs($this->encargadoA)
            ->get(
                route(
                    'encargado.incubadoras.show',
                    $this->incubadoraA
                )
            )
            ->assertOk();
    }

    public function test_encargado_no_puede_ver_incubadora_ajena(): void
    {
        $this->actingAs($this->encargadoA)
            ->get(
                route(
                    'encargado.incubadoras.show',
                    $this->incubadoraB
                )
            )
            ->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Seguimientos de lote
    |--------------------------------------------------------------------------
    */

    public function test_encargado_puede_registrar_seguimiento_en_su_lote(): void
    {
        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.seguimientos-lote.store'),
                [
                    'lote_id' => $this->loteA,
                    'fecha_revision' => now()->toDateString(),
                    'frascos_activos' => 1,
                    'semillas_germinadas' => 5,
                    'altura_promedio_cm' => 2.5,
                    'etapa_desarrollo_id' => $this->etapaId,
                    'observaciones' => 'Seguimiento autorizado.',
                ]
            )
            ->assertRedirect(
                route('encargado.seguimientos-lote.index')
            );

        $this->assertDatabaseHas('seguimientos_lote', [
            'lote_id' => $this->loteA,
            'user_id' => $this->encargadoA->id,
            'semillas_germinadas' => 5,
        ]);
    }

    public function test_encargado_no_puede_registrar_seguimiento_en_lote_ajeno(): void
    {
        $cantidadInicial = DB::table('seguimientos_lote')
            ->where('lote_id', $this->loteB)
            ->count();

        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.seguimientos-lote.store'),
                [
                    'lote_id' => $this->loteB,
                    'fecha_revision' => now()->toDateString(),
                    'frascos_activos' => 1,
                    'semillas_germinadas' => 5,
                    'altura_promedio_cm' => 2.5,
                    'etapa_desarrollo_id' => $this->etapaId,
                    'observaciones' => 'Intento no autorizado.',
                ]
            )
            ->assertNotFound();

        $this->assertSame(
            $cantidadInicial,
            DB::table('seguimientos_lote')
                ->where('lote_id', $this->loteB)
                ->count()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Seguimientos de frasco
    |--------------------------------------------------------------------------
    */

    public function test_encargado_puede_registrar_seguimiento_en_su_frasco(): void
    {
        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.seguimientos-frasco.store'),
                [
                    'frasco_id' => $this->frascoA,
                    'fecha_revision' => now()->toDateString(),
                    'semillas_germinadas' => 4,
                    'altura_promedio_cm' => 2,
                    'estado_frasco_id' => $this->estadoFrascoId,
                    'observaciones' => 'Seguimiento autorizado.',
                ]
            )
            ->assertRedirect(
                route('encargado.seguimientos-frasco.index')
            );

        $this->assertDatabaseHas('seguimientos_frasco', [
            'frasco_id' => $this->frascoA,
            'user_id' => $this->encargadoA->id,
            'semillas_germinadas' => 4,
        ]);
    }

    public function test_encargado_no_puede_registrar_seguimiento_en_frasco_ajeno(): void
    {
        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.seguimientos-frasco.store'),
                [
                    'frasco_id' => $this->frascoB,
                    'fecha_revision' => now()->toDateString(),
                    'semillas_germinadas' => 4,
                    'altura_promedio_cm' => 2,
                    'estado_frasco_id' => $this->estadoFrascoId,
                    'observaciones' => 'Intento no autorizado.',
                ]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('seguimientos_frasco', [
            'frasco_id' => $this->frascoB,
            'user_id' => $this->encargadoA->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Evidencias
    |--------------------------------------------------------------------------
    */

    public function test_encargado_puede_subir_evidencia_a_su_seguimiento(): void
    {
        Storage::fake('public');

        $archivo = $this->imagenPrueba(
            'evidencia-propia.png'
        );

        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.evidencias-lote.store'),
                [
                    'seguimiento_lote_id' => $this->seguimientoA,
                    'archivo' => $archivo,
                    'descripcion' => 'Evidencia autorizada.',
                ]
            )
            ->assertRedirect(
                route('encargado.evidencias-lote.index')
            );

        $evidencia = DB::table('evidencias_lote')
            ->where(
                'seguimiento_lote_id',
                $this->seguimientoA
            )
            ->first();

        $this->assertNotNull($evidencia);

        $this->assertSame(
            'Evidencia autorizada.',
            $evidencia->descripcion
        );

        Storage::disk('public')
            ->assertExists($evidencia->archivo);
    }

    public function test_encargado_no_puede_subir_evidencia_a_seguimiento_ajeno(): void
    {
        Storage::fake('public');

        $archivo = $this->imagenPrueba(
            'evidencia-ajena.png'
        );

        $this->actingAs($this->encargadoA)
            ->post(
                route('encargado.evidencias-lote.store'),
                [
                    'seguimiento_lote_id' => $this->seguimientoB,
                    'archivo' => $archivo,
                    'descripcion' => 'Intento no autorizado.',
                ]
            )
            ->assertNotFound();

        $this->assertDatabaseMissing('evidencias_lote', [
            'seguimiento_lote_id' => $this->seguimientoB,
            'descripcion' => 'Intento no autorizado.',
        ]);
    }
}
