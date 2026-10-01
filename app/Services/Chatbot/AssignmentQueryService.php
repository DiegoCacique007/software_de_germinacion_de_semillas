<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AssignmentQueryService
{
    private const LIMITE_DEFECTO=5;
    private const LIMITE_MAXIMO=50;
    private string $timezone='America/Mexico_City';

    public function ejecutar(array $plan,array $resolucion,User $user): array
    {
        if(($plan['recurso']??null)!=='asignaciones'){
            return [
                'ok'=>false,
                'tipo'=>'recurso_no_soportado',
                'mensaje'=>'Este servicio solamente consulta asignaciones de incubadoras.'
            ];
        }

        $query=$this->queryPermitidas($user);
        $alcance=$this->aplicarResolucion($query,$resolucion,$user);
        $operacion=$plan['operacion']??'listar';

        return match($operacion){
            'contar'=>$this->contar($query,$alcance),
            'estado'=>$this->detalle($query,$alcance),
            'ultimo'=>$this->ultimo($query,$alcance),
            'listar'=>$this->listar($query,$plan,$alcance),
            default=>[
                'ok'=>false,
                'tipo'=>'operacion_no_soportada',
                'mensaje'=>'La operación solicitada no está disponible para asignaciones.'
            ]
        };
    }

    private function contar(Builder $query,array $alcance): array
    {
        return [
            'ok'=>true,
            'tipo'=>'asignacion_conteo',
            'recurso'=>'asignaciones',
            'operacion'=>'contar',
            'total'=>(clone $query)->count('ai.id'),
            'alcance'=>$alcance,
            'vigentes'=>true
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

        $total=(clone $query)->count('ai.id');

        $items=(clone $query)
            ->orderByDesc('ai.fecha_inicio')
            ->orderByDesc('ai.id')
            ->limit($limite)
            ->get();

        if($items->isEmpty()){
            return $this->sinDatos($alcance);
        }

        return [
            'ok'=>true,
            'tipo'=>'asignacion_lista',
            'recurso'=>'asignaciones',
            'operacion'=>'listar',
            'total'=>$total,
            'total_devuelto'=>$items->count(),
            'limite'=>$limite,
            'datos'=>$items
                ->map(fn($item)=>$this->mapear($item))
                ->values()
                ->all(),
            'alcance'=>$alcance,
            'vigentes'=>true
        ];
    }

    private function detalle(Builder $query,array $alcance): array
    {
        $total=(clone $query)->count('ai.id');

        if($total===0){
            return $this->sinDatos($alcance);
        }

        if($total>1){
            return [
                'ok'=>false,
                'tipo'=>'requiere_aclaracion',
                'mensaje'=>'La consulta coincide con varias asignaciones vigentes. Indica el usuario o la incubadora que deseas revisar.',
                'recurso'=>'asignaciones',
                'total'=>$total,
                'alcance'=>$alcance
            ];
        }

        return [
            'ok'=>true,
            'tipo'=>'asignacion_detalle',
            'recurso'=>'asignaciones',
            'operacion'=>'estado',
            'datos'=>$this->mapear(
                (clone $query)->first()
            ),
            'alcance'=>$alcance,
            'vigentes'=>true
        ];
    }

    private function ultimo(Builder $query,array $alcance): array
    {
        $item=(clone $query)
            ->orderByDesc('ai.fecha_inicio')
            ->orderByDesc('ai.id')
            ->first();

        if(!$item){
            return $this->sinDatos($alcance);
        }

        return [
            'ok'=>true,
            'tipo'=>'asignacion_detalle',
            'recurso'=>'asignaciones',
            'operacion'=>'ultimo',
            'datos'=>$this->mapear($item),
            'alcance'=>$alcance,
            'vigentes'=>true
        ];
    }

    private function queryPermitidas(User $user): Builder
    {
        $hoy=now($this->timezone)->toDateString();

        $query=DB::table('asignaciones_incubadora as ai')
            ->join('incubadoras as i','i.id','=','ai.incubadora_id')
            ->join('users as u','u.id','=','ai.user_id')
            ->leftJoin('roles as r','r.id','=','u.role_id')
            ->leftJoin(
                'estados_incubadora as ei',
                'ei.id',
                '=',
                'i.estado_incubadora_id'
            )
            ->select([
                'ai.id',
                'ai.incubadora_id',
                'ai.user_id',
                'ai.fecha_inicio',
                'ai.fecha_fin',
                'ai.observaciones',
                'ai.created_at',

                'i.codigo as incubadora_codigo',
                'i.nombre as incubadora_nombre',
                'i.ubicacion as incubadora_ubicacion',

                'ei.clave as incubadora_estado_clave',
                'ei.nombre as incubadora_estado_nombre',

                'u.name as usuario_nombre',
                'u.activo as usuario_activo',

                'r.clave as rol_clave',
                'r.nombre as rol_nombre'
            ])
            ->whereDate('ai.fecha_inicio','<=',$hoy)
            ->where(function($q) use($hoy){
                $q->whereNull('ai.fecha_fin')
                    ->orWhereDate('ai.fecha_fin','>=',$hoy);
            });

        if($user->isSuperAdmin()){
            return $query;
        }

        if($user->isEncargado()){
            return $query->where(
                'ai.user_id',
                $user->id
            );
        }

        return $query->whereRaw('1 = 0');
    }

    private function aplicarResolucion(Builder $query,array $resolucion,User $user): array
    {
        $entidades=$resolucion['entidades']??[];
        $alcance=$user->isSuperAdmin()
            ?['tipo'=>'global_autorizado']
            :[
                'tipo'=>'usuario_propio',
                'id'=>$user->id,
                'nombre'=>$user->name
            ];

        if(isset($entidades['usuario']['id'])){
            $usuario=$entidades['usuario'];

            $query->where(
                'ai.user_id',
                (int)$usuario['id']
            );

            $this->agregarAlcance(
                $alcance,
                'usuario',
                [
                    'id'=>(int)$usuario['id'],
                    'nombre'=>$usuario['nombre']??null
                ]
            );
        }

        if(isset($entidades['incubadora']['id'])){
            $incubadora=$entidades['incubadora'];

            $query->where(
                'ai.incubadora_id',
                (int)$incubadora['id']
            );

            $this->agregarAlcance(
                $alcance,
                'incubadora',
                [
                    'id'=>(int)$incubadora['id'],
                    'codigo'=>$incubadora['codigo']??null,
                    'nombre'=>$incubadora['nombre']??null
                ]
            );
        }

        return $alcance;
    }

    private function agregarAlcance(array &$alcance,string $tipo,array $dato): void
    {
        if(($alcance['tipo']??null)==='global_autorizado'){
            $alcance=[
                'tipo'=>$tipo,
                ...$dato
            ];

            return;
        }

        $alcance[$tipo]=$dato;
    }

    private function mapear(object $item): array
    {
        return [
            'id'=>(int)$item->id,

            'usuario'=>[
                'id'=>(int)$item->user_id,
                'nombre'=>$item->usuario_nombre,
                'activo'=>(bool)$item->usuario_activo,
                'rol'=>[
                    'clave'=>$item->rol_clave,
                    'nombre'=>$item->rol_nombre
                ]
            ],

            'incubadora'=>[
                'id'=>(int)$item->incubadora_id,
                'codigo'=>$item->incubadora_codigo,
                'nombre'=>$item->incubadora_nombre,
                'ubicacion'=>$item->incubadora_ubicacion,
                'estado'=>[
                    'clave'=>$item->incubadora_estado_clave,
                    'nombre'=>$item->incubadora_estado_nombre
                ]
            ],

            'fecha_inicio'=>$item->fecha_inicio,
            'fecha_fin'=>$item->fecha_fin,
            'observaciones'=>$item->observaciones,
            'vigente'=>true,
            'created_at'=>$item->created_at
        ];
    }

    private function sinDatos(array $alcance): array
    {
        return [
            'ok'=>false,
            'tipo'=>'sin_datos',
            'recurso'=>'asignaciones',
            'mensaje'=>'No se encontraron asignaciones vigentes que coincidan con la consulta dentro de la información permitida.',
            'alcance'=>$alcance
        ];
    }
}
