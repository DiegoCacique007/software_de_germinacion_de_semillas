<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MicroclimaActuatorService
{
    private string $archivo='microseed_actuadores.json';

    public function obtenerActuador(string $actuador): array
    {
        $estados=$this->leerEstados();
        return $estados[$actuador];
    }

    public function actualizarActuador(string $actuador,string $accion,?int $userId=null): array
    {
        $estados=$this->leerEstados();

        $estados[$actuador]=[
            'comando'=>$accion,
            'actualizado_en'=>now('America/Mexico_City')->format('Y-m-d H:i:s'),
            'actualizado_por'=>$userId,
        ];

        $this->guardarEstados($estados);

        return $estados[$actuador];
    }

    public function obtenerModo(): array
    {
        $estados=$this->leerEstados();
        return $estados['modo'];
    }

    public function actualizarModo(string $modo,?int $userId=null): array
    {
        $estados=$this->leerEstados();

        $estados['modo']=[
            'valor'=>$modo,
            'actualizado_en'=>now('America/Mexico_City')->format('Y-m-d H:i:s'),
            'actualizado_por'=>$userId,
        ];

        $this->guardarEstados($estados);

        return $estados['modo'];
    }

    public function actuadorValido(string $actuador): bool
    {
        return in_array($actuador,['niebla','luz'],true);
    }

    public function modoValido(string $modo): bool
    {
        return in_array($modo,['automatico','manual'],true);
    }

    private function leerEstados(): array
    {
        $default=[
            'modo'=>[
                'valor'=>'automatico',
                'actualizado_en'=>null,
                'actualizado_por'=>null,
            ],
            'niebla'=>[
                'comando'=>'apagar',
                'actualizado_en'=>null,
                'actualizado_por'=>null,
            ],
            'luz'=>[
                'comando'=>'apagar',
                'actualizado_en'=>null,
                'actualizado_por'=>null,
            ],
        ];

        if(!Storage::disk('local')->exists($this->archivo)){
            $this->guardarEstados($default);
            return $default;
        }

        $contenido=Storage::disk('local')->get($this->archivo);
        $estados=json_decode($contenido,true);

        if(!is_array($estados)) return $default;

        return array_replace_recursive($default,$estados);
    }

    private function guardarEstados(array $estados): void
    {
        Storage::disk('local')->put(
            $this->archivo,
            json_encode($estados,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)
        );
    }
}
