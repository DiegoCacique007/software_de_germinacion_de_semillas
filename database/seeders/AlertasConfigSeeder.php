<?php

namespace Database\Seeders;

use App\Models\EstadoAlerta;
use App\Models\NivelAlerta;
use App\Models\TipoAlerta;
use Illuminate\Database\Seeder;

class AlertasConfigSeeder extends Seeder
{
    public function run(): void
    {
        EstadoAlerta::firstOrCreate(['clave' => 'pendiente'], ['nombre' => 'Pendiente']);

        foreach (['temperatura' => 'Temperatura', 'humedad' => 'Humedad'] as $clave => $nombre) {
            TipoAlerta::firstOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }

        foreach (['bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto'] as $clave => $nombre) {
            NivelAlerta::firstOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }
    }
}
