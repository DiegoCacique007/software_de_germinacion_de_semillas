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
        foreach ([
                     'pendiente' => ['Pendiente', 'La alerta fue detectada y aún requiere atención.'],
                     'atendida' => ['Atendida', 'La alerta fue revisada o atendida por un usuario.'],
                     'resuelta' => ['Resuelta', 'La condición que originó la alerta regresó a un rango normal.'],
                 ] as $clave => [$nombre, $descripcion]) {
            EstadoAlerta::firstOrCreate(
                ['clave' => $clave],
                ['nombre' => $nombre, 'descripcion' => $descripcion]
            );
        }

        foreach (['temperatura' => 'Temperatura', 'humedad' => 'Humedad'] as $clave => $nombre) {
            TipoAlerta::firstOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }

        foreach (['bajo' => 'Bajo', 'medio' => 'Medio', 'alto' => 'Alto'] as $clave => $nombre) {
            NivelAlerta::firstOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }
    }
}
