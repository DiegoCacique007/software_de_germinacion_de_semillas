<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UserQueryService
{
    private const LIMITE_DEFECTO=5;
    private const LIMITE_MAXIMO=50;

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='usuarios') return ['ok'=>false,'tipo'=>'recurso_no_soportado','mensaje'=>'Este servicio solamente consulta usuarios.'];

        $query=$this->queryPermitidos($user);
        $alcance=$this->aplicarResolucion($query,$resolucion,$user);
        $this->aplicarFiltros($query,$plan['filtros']??[]);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$plan,$alcance),
            'estado'=>$this->detalle($query,$alcance),
            'ultimo'=>$this->ultimo($query,$alcance),
            'listar'=>$this->listar($query,$plan,$alcance),
            default=>['ok'=>false,'tipo'=>'operacion_no_soportada','mensaje'=>'La operación solicitada no está disponible para usuarios.']
        };
    }

    private function contar(Builder $query,array $plan,array $alcance): array
    {
        return [
            'ok'=>true,
            'tipo'=>'usuario_conteo',
            'recurso'=>'usuarios',
            'operacion'=>'contar',
            'total'=>(clone $query)->count('u.id'),
            'filtros'=>$plan['filtros']??[],
            'alcance'=>$alcance
        ];
    }

    private function listar(Builder $query,array $plan,array $alcance): array
    {
        $limite=max(
            1,
            min(
                (int)($plan['limite']??self::LIMITE_DEFECTO),
                self::LIMITE_MAXIMO
            )
        );

        $total=(clone $query)->count('u.id');
        $direccion=($plan['orden']??'asc')==='desc'?'desc':'asc';

        $items=(clone $query)
            ->orderBy('u.id',$direccion)
            ->limit($limite)
            ->get();

        if($items->isEmpty()) return $this->sinDatos($alcance);

        return [
            'ok'=>true,
            'tipo'=>'usuario_lista',
            'recurso'=>'usuarios',
            'operacion'=>'listar',
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items->map(
                fn($item)=>$this->mapear($item)
            )->values()->all(),
            'filtros'=>$plan['filtros']??[],
            'alcance'=>$alcance
        ];
    }

    private function detalle(Builder $query,array $alcance): array
    {
        $total=(clone $query)->count('u.id');

        if($total===0) return $this->sinDatos($alcance);

        if($total>1){
            return [
                'ok'=>false,
                'tipo'=>'requiere_aclaracion',
                'mensaje'=>'La consulta coincide con varios usuarios. Indica el ID o nombre del usuario que deseas revisar.',
                'recurso'=>'usuarios',
                'total'=>$total,
                'alcance'=>$alcance
            ];
        }

        return [
            'ok'=>true,
            'tipo'=>'usuario_detalle',
            'recurso'=>'usuarios',
            'operacion'=>'estado',
            'datos'=>$this->mapear(
                (clone $query)->first()
            ),
            'alcance'=>$alcance
        ];
    }

    private function ultimo(Builder $query,array $alcance): array
    {
        $item=(clone $query)
            ->orderByDesc('u.created_at')
            ->orderByDesc('u.id')
            ->first();

        if(!$item) return $this->sinDatos($alcance);

        return [
            'ok'=>true,
            'tipo'=>'usuario_detalle',
            'recurso'=>'usuarios',
            'operacion'=>'ultimo',
            'datos'=>$this->mapear($item),
            'alcance'=>$alcance
        ];
    }

    private function queryPermitidos(User $user): Builder
    {
        $query=DB::table('users as u')
            ->leftJoin('roles as r','r.id','=','u.role_id')
            ->select([
                'u.id',
                'u.name',
                'u.activo',
                'u.created_at',
                'r.clave as rol_clave',
                'r.nombre as rol_nombre'
            ]);

        if($user->isSuperAdmin()) return $query;

        if($user->isEncargado()){
            return $query->where(
                'u.id',
                $user->id
            );
        }

        return $query->whereRaw('1 = 0');
    }

    private function aplicarResolucion(Builder $query,array $resolucion,User $user): array
    {
        $entidad=$resolucion['entidades']['usuario']??null;

        if(
            !$entidad
            ||empty($entidad['id'])
        ){
            return $user->isSuperAdmin()
                ?[
                    'tipo'=>'global_autorizado'
                ]
                :[
                    'tipo'=>'usuario_propio',
                    'id'=>$user->id,
                    'nombre'=>$user->name
                ];
        }

        $query->where(
            'u.id',
            (int)$entidad['id']
        );

        return [
            'tipo'=>'usuario',
            'id'=>(int)$entidad['id'],
            'nombre'=>$entidad['nombre']??null
        ];
    }

    private function aplicarFiltros(Builder $query,array $filtros): void
    {
        if(isset($filtros['usuario_activo'])){
            $query->where(
                'u.activo',
                (bool)$filtros['usuario_activo']
            );
        }

        if(!empty($filtros['rol_usuario'])){
            $query->where(
                'r.clave',
                $filtros['rol_usuario']
            );
        }
    }

    private function mapear(object $item): array
    {
        return [
            'id'=>(int)$item->id,
            'nombre'=>$item->name,
            'activo'=>(bool)$item->activo,
            'rol'=>[
                'clave'=>$item->rol_clave,
                'nombre'=>$item->rol_nombre
            ],
            'created_at'=>$item->created_at
        ];
    }

    private function sinDatos(array $alcance): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'usuarios',
            'mensaje'=>'No se encontraron usuarios que coincidan con la consulta dentro de la información permitida.',
            'alcance'=>$alcance
        ];
    }
}
