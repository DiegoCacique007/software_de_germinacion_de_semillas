<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CatalogQueryService
{
    private const CATALOGOS=[
        'estados_incubadora'=>[
            'tabla'=>'estados_incubadora',
            'nombre'=>'estados de incubadora',
            'privado'=>false
        ],
        'estados_lote'=>[
            'tabla'=>'estados_lote',
            'nombre'=>'estados de lote',
            'privado'=>false
        ],
        'estados_frasco'=>[
            'tabla'=>'estados_frasco',
            'nombre'=>'estados de frasco',
            'privado'=>false
        ],
        'etapas_desarrollo'=>[
            'tabla'=>'etapas_desarrollo',
            'nombre'=>'etapas de desarrollo',
            'privado'=>false
        ],
        'tipos_alerta'=>[
            'tabla'=>'tipos_alerta',
            'nombre'=>'tipos de alerta',
            'privado'=>false
        ],
        'niveles_alerta'=>[
            'tabla'=>'niveles_alerta',
            'nombre'=>'niveles de alerta',
            'privado'=>false
        ],
        'estados_alerta'=>[
            'tabla'=>'estados_alerta',
            'nombre'=>'estados de alerta',
            'privado'=>false
        ],
        'tipos_control'=>[
            'tabla'=>'tipos_control_incubadora',
            'nombre'=>'tipos de control',
            'privado'=>false
        ],
        'modos_control'=>[
            'tabla'=>'modos_control_incubadora',
            'nombre'=>'modos de control',
            'privado'=>false
        ],
        'roles'=>[
            'tabla'=>'roles',
            'nombre'=>'roles',
            'privado'=>true
        ]
    ];

    public function ejecutar(array $plan,User $user): array
    {
        if(($plan['recurso']??null)!=='catalogos'){
            return $this->error(
                'recurso_no_soportado',
                'CatalogQueryService solamente procesa catálogos.'
            );
        }

        $catalogo=
            $plan['catalogo']
            ??$plan['filtros']['catalogo']
            ??null;

        if(!$catalogo||!isset(self::CATALOGOS[$catalogo])){
            return [
                'ok'=>false,
                'tipo'=>'requiere_aclaracion',
                'recurso'=>'catalogos',
                'mensaje'=>'Indica qué catálogo deseas consultar.',
                'catalogos_disponibles'=>$this->catalogosPermitidos($user)
            ];
        }

        $config=self::CATALOGOS[$catalogo];

        if($config['privado']&&!$user->isSuperAdmin()){
            return [
                'ok'=>false,
                'tipo'=>'acceso_denegado',
                'recurso'=>'catalogos',
                'catalogo'=>$catalogo,
                'mensaje'=>'El catálogo de roles está disponible únicamente para el Super Admin.'
            ];
        }

        $query=$this->query($catalogo,$config);
        $filtros=$this->aplicarFiltros($query,$plan['filtros']??[]);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$catalogo,$config,$filtros),
            'listar','ultimo','estado'=>$this->listar($query,$catalogo,$config,$filtros,$plan),
            default=>$this->error(
                'operacion_no_soportada',
                'La operación solicitada todavía no está disponible para catálogos.'
            )
        };
    }

    private function query(string $catalogo,array $config): Builder
    {
        $campos=[
            'id',
            'clave',
            'nombre',
            'descripcion'
        ];

        if($catalogo==='roles'){
            $campos[]='activo';
        }

        return DB::table($config['tabla'])->select($campos);
    }

    private function aplicarFiltros(Builder $query,array $filtros): array
    {
        $aplicados=[];

        if(!empty($filtros['clave_catalogo'])){
            $clave=mb_strtolower(trim((string)$filtros['clave_catalogo']));

            $query->whereRaw(
                'LOWER(clave)=?',
                [$clave]
            );

            $aplicados['clave_catalogo']=$clave;
        }

        if(array_key_exists('activo',$filtros)){
            $query->where('activo',(bool)$filtros['activo']);
            $aplicados['activo']=(bool)$filtros['activo'];
        }

        return $aplicados;
    }

    private function contar(
        Builder $query,
        string $catalogo,
        array $config,
        array $filtros
    ): array {
        return [
            'ok'=>true,
            'tipo'=>'catalogo_conteo',
            'recurso'=>'catalogos',
            'operacion'=>'contar',
            'catalogo'=>$catalogo,
            'nombre_catalogo'=>$config['nombre'],
            'total'=>(clone $query)->count(),
            'filtros'=>$filtros
        ];
    }

    private function listar(
        Builder $query,
        string $catalogo,
        array $config,
        array $filtros,
        array $plan
    ): array {
        $limite=max(
            1,
            min(
                (int)($plan['limite']??20),
                50
            )
        );

        $orden=
            ($plan['orden']??'asc')==='desc'
                ?'desc'
                :'asc';

        $total=(clone $query)->count();

        $items=(clone $query)
            ->orderBy('id',$orden)
            ->limit($limite)
            ->get();

        if($items->isEmpty()){
            return [
                'ok'=>false,
                'tipo'=>'sin_datos',
                'recurso'=>'catalogos',
                'catalogo'=>$catalogo,
                'mensaje'=>'No hay '.$config['nombre'].' que cumplan con los criterios de la consulta.',
                'filtros'=>$filtros
            ];
        }

        return [
            'ok'=>true,
            'tipo'=>'catalogo_lista',
            'recurso'=>'catalogos',
            'operacion'=>'listar',
            'catalogo'=>$catalogo,
            'nombre_catalogo'=>$config['nombre'],
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items
                ->map(
                    fn($item)=>
                    $this->mapear(
                        $item,
                        $catalogo
                    )
                )
                ->values()
                ->all(),
            'filtros'=>$filtros
        ];
    }

    private function mapear(object $item,string $catalogo): array
    {
        $resultado=[
            'id'=>(int)$item->id,
            'clave'=>$item->clave,
            'nombre'=>$item->nombre,
            'descripcion'=>$item->descripcion
        ];

        if($catalogo==='roles'){
            $resultado['activo']=(bool)$item->activo;
        }

        return $resultado;
    }

    private function catalogosPermitidos(User $user): array
    {
        return collect(self::CATALOGOS)
            ->filter(
                fn($config)=>
                    !$config['privado']
                    ||$user->isSuperAdmin()
            )
            ->map(
                fn($config,$clave)=>[
                    'clave'=>$clave,
                    'nombre'=>$config['nombre']
                ]
            )
            ->values()
            ->all();
    }

    private function error(string $tipo,string $mensaje): array
    {
        return [
            'ok'=>false,
            'tipo'=>$tipo,
            'mensaje'=>$mensaje
        ];
    }
}
