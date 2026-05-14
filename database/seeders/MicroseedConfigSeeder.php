<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoControlIncubadora;
use App\Models\ModoControlIncubadora;
use App\Models\Incubadora;
use App\Models\EstadoIncubadora;

class MicroseedConfigSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Estados de Incubadora
        $estadoActiva = EstadoIncubadora::updateOrCreate(
            ['clave' => 'activa'],
            ['nombre' => 'Activa', 'descripcion' => 'La incubadora está operando normalmente.']
        );

        // 2. Modos de Control
        $modoAuto = ModoControlIncubadora::updateOrCreate(
            ['clave' => 'automatico'],
            ['nombre' => 'Automático', 'descripcion' => 'El sistema controla los actuadores según los setpoints.']
        );
        $modoManual = ModoControlIncubadora::updateOrCreate(
            ['clave' => 'manual'],
            ['nombre' => 'Manual', 'descripcion' => 'El usuario controla los actuadores manualmente.']
        );

        // 3. Tipos de Control
        // Claves: niebla, iluminacion, temperatura, humedad
        TipoControlIncubadora::updateOrCreate(
            ['clave' => 'niebla'],
            ['nombre' => 'Generador de Niebla', 'descripcion' => 'Control del relé del nebulizador.']
        );
        TipoControlIncubadora::updateOrCreate(
            ['clave' => 'iluminacion'],
            ['nombre' => 'Tira LED Blanca', 'descripcion' => 'Control de la iluminación de fotoperiodo.']
        );
        TipoControlIncubadora::updateOrCreate(
            ['clave' => 'temperatura'],
            ['nombre' => 'Temperatura', 'descripcion' => 'Ajuste de setpoint de temperatura.']
        );
        TipoControlIncubadora::updateOrCreate(
            ['clave' => 'humedad'],
            ['nombre' => 'Humedad', 'descripcion' => 'Ajuste de setpoint de humedad.']
        );

        // 4. Incubadora de prueba
        Incubadora::updateOrCreate(
            ['codigo' => 'INC-001'],
            [
                'nombre' => 'Incubadora Microseed v1',
                'ubicacion' => 'Laboratorio Principal',
                'descripcion' => 'Prototipo de germinación automatizada.',
                'estado_incubadora_id' => $estadoActiva->id
            ]
        );
    }
}
