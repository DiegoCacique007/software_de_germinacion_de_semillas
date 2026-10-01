<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class ConversationContextService
{
    private const TIPOS_ENTIDAD=['incubadora','lote','frasco','especie','usuario'];
    private const MAX_CANDIDATOS=10;

    public function obtener(User $user,string $chatId): array
    {
        return Session::get($this->clave($user,$chatId),$this->contextoVacio());
    }

    public function aplicar(array $plan,array $resolucion,User $user,string $chatId): array
    {
        // Una referencia explícita, incluso inválida o ambigua, prevalece sobre la sesión.
        if(!empty($plan['entidades'])||!empty($resolucion['entidades'])
            ||!empty($resolucion['no_encontradas'])||!empty($resolucion['ambiguas'])) return $resolucion;

        if(!$this->requiereContexto($plan)) return $resolucion;

        $contexto=$this->obtener($user,$chatId);
        $tipo=$this->detectarTipoContextual($plan);

        if($tipo) return $this->resolverTipo($tipo,$contexto,$resolucion);

        $ultimoTipo=$contexto['ultima_entidad_tipo']??null;

        if(
            $ultimoTipo
            &&isset($contexto['entidades'][$ultimoTipo])
        ){
            return $this->inyectarEntidad(
                $resolucion,
                $ultimoTipo,
                $contexto['entidades'][$ultimoTipo]
            );
        }

        return $this->contextoFaltante($resolucion,null);
    }

    public function actualizar(array $plan,array $resolucion,array $resultado,User $user,string $chatId): void
    {
        $contexto=$this->obtener($user,$chatId);

        $contexto['ultima_consulta']=[
            'recurso'=>$plan['recurso']??null,
            'operacion'=>$plan['operacion']??null,
            'variable'=>$plan['variable']??null,
            'periodo'=>$plan['periodo']??null
        ];

        $entidades=$resolucion['entidades']??[];

        foreach(self::TIPOS_ENTIDAD as $tipo){
            if(!isset($entidades[$tipo])||!is_array($entidades[$tipo])) continue;

            $entidad=$this->normalizarEntidad(
                $tipo,
                $entidades[$tipo]
            );

            if(!$entidad) continue;

            $contexto['entidades'][$tipo]=$entidad;
            $contexto['ultima_entidad_tipo']=$tipo;

            unset($contexto['candidatos'][$tipo]);
        }

        $this->actualizarDesdeResultado(
            $contexto,
            $resultado
        );

        $contexto['actualizado_en']=now()->toDateTimeString();

        Session::put(
            $this->clave($user,$chatId),
            $contexto
        );
    }

    public function limpiar(User $user,string $chatId): void
    {
        Session::forget(
            $this->clave($user,$chatId)
        );
    }

    private function resolverTipo(string $tipo,array $contexto,array $resolucion): array
    {
        if(isset($contexto['entidades'][$tipo])){
            return $this->inyectarEntidad(
                $resolucion,
                $tipo,
                $contexto['entidades'][$tipo]
            );
        }

        $candidatos=$contexto['candidatos'][$tipo]??[];

        if(count($candidatos)>1){
            return [
                'estado'=>'ambiguo',
                'entidades'=>$resolucion['entidades']??[],
                'ambiguas'=>[$tipo=>$candidatos],
                'no_encontradas'=>[],
                'mensaje'=>'La referencia corresponde a varios registros y necesito que indiques cuál deseas consultar.'
            ];
        }

        if(count($candidatos)===1){
            return $this->inyectarEntidad(
                $resolucion,
                $tipo,
                $candidatos[0]
            );
        }

        return $this->contextoFaltante(
            $resolucion,
            $tipo
        );
    }

    private function inyectarEntidad(array $resolucion,string $tipo,array $entidad): array
    {
        $entidades=$resolucion['entidades']??[];
        $entidades[$tipo]=$entidad;

        return [
            ...$resolucion,
            'estado'=>'resuelto',
            'entidades'=>$entidades,
            'contexto_aplicado'=>true,
            'tipo_contexto'=>$tipo,
            'mensaje'=>'Referencia contextual resuelta correctamente.'
        ];
    }

    private function contextoFaltante(array $resolucion,?string $tipo): array
    {
        return [
            ...$resolucion,
            'estado'=>'contexto_faltante',
            'tipo_contexto'=>$tipo,
            'mensaje'=>$tipo
                ?"No tengo un {$tipo} anterior claramente identificado en esta conversación."
                :'No tengo una entidad anterior claramente identificada en esta conversación.'
        ];
    }

    private function requiereContexto(array $plan): bool
    {
        if(
            !empty($plan['requiere_contexto'])
            ||!empty($plan['referencia_contextual'])
        ) return true;

        $mensaje=mb_strtolower(
            $plan['mensaje_normalizado']
            ??$plan['mensaje_original']
            ??''
        );

        $frases=[
            'ese lote','este lote','el lote anterior',
            'esa incubadora','esta incubadora','la incubadora anterior',
            'ese frasco','este frasco','el frasco anterior',
            'esa especie','esta especie','la especie anterior',
            'ese usuario','este usuario','el usuario anterior',
            'ese encargado','este encargado','el encargado anterior'
        ];

        foreach($frases as $frase){
            if(str_contains($mensaje,$frase)) return true;
        }

        return false;
    }

    private function detectarTipoContextual(array $plan): ?string
    {
        $referencia=mb_strtolower(
            (string)($plan['referencia_contextual']??'')
        );

        $mensaje=mb_strtolower(
            $plan['mensaje_normalizado']
            ??$plan['mensaje_original']
            ??''
        );

        $texto=$referencia.' '.$mensaje;

        if(str_contains($texto,'incubadora')) return 'incubadora';
        if(str_contains($texto,'lote')) return 'lote';
        if(str_contains($texto,'frasco')) return 'frasco';
        if(str_contains($texto,'especie')) return 'especie';

        if(
            str_contains($texto,'usuario')
            ||str_contains($texto,'encargado')
        ) return 'usuario';

        return null;
    }

    private function actualizarDesdeResultado(array &$contexto,array $resultado): void
    {
        if(!($resultado['ok']??false)) return;

        $tipoResultado=$resultado['tipo']??null;

        if($tipoResultado==='recurso_detalle'){
            $tipo=$this->tipoDesdeRecurso(
                $resultado['recurso']??null
            );

            if(!$tipo) return;

            $entidad=$this->normalizarEntidad(
                $tipo,
                $resultado['datos']??[]
            );

            if(!$entidad) return;

            $contexto['entidades'][$tipo]=$entidad;
            $contexto['ultima_entidad_tipo']=$tipo;

            unset($contexto['candidatos'][$tipo]);

            return;
        }

        if($tipoResultado==='recurso_lista'){
            $tipo=$this->tipoDesdeRecurso(
                $resultado['recurso']??null
            );

            if(!$tipo) return;

            $candidatos=[];

            foreach(
                array_slice(
                    $resultado['datos']??[],
                    0,
                    self::MAX_CANDIDATOS
                ) as $item
            ){
                $entidad=$this->normalizarEntidad(
                    $tipo,
                    $item
                );

                if($entidad) $candidatos[]=$entidad;
            }

            if(count($candidatos)===1){
                $contexto['entidades'][$tipo]=$candidatos[0];
                $contexto['ultima_entidad_tipo']=$tipo;

                unset($contexto['candidatos'][$tipo]);

                return;
            }

            if(count($candidatos)>1){
                $contexto['candidatos'][$tipo]=$candidatos;

                unset($contexto['entidades'][$tipo]);

                if(
                    ($contexto['ultima_entidad_tipo']??null)===$tipo
                ){
                    $contexto['ultima_entidad_tipo']=null;
                }
            }
        }
    }

    private function normalizarEntidad(string $tipo,array $entidad): ?array
    {
        if(!in_array($tipo,self::TIPOS_ENTIDAD,true)) return null;

        $id=$entidad['id']??null;

        if(!$id) return null;

        return match($tipo){
            'incubadora'=>[
                'id'=>(int)$id,
                'codigo'=>$entidad['codigo']??null,
                'nombre'=>$entidad['nombre']??null
            ],

            'lote'=>[
                'id'=>(int)$id,
                'codigo'=>$entidad['codigo']??$entidad['codigo_lote']??null
            ],

            'frasco'=>[
                'id'=>(int)$id,
                'numero'=>$entidad['numero']??$entidad['numero_frasco']??null
            ],

            'especie'=>[
                'id'=>(int)$id,
                'nombre_comun'=>$entidad['nombre_comun']??null,
                'nombre_cientifico'=>$entidad['nombre_cientifico']??null
            ],

            'usuario'=>[
                'id'=>(int)$id,
                'nombre'=>$entidad['nombre']??null,
                'email'=>$entidad['email']??null
            ]
        };
    }

    private function tipoDesdeRecurso(?string $recurso): ?string
    {
        return match($recurso){
            'incubadoras'=>'incubadora',
            'lotes'=>'lote',
            'frascos'=>'frasco',
            'especies'=>'especie',
            default=>null
        };
    }

    private function clave(User $user,string $chatId): string
    {
        return 'microseed.chatbot.contexto.'
            .$user->id
            .'.'
            .$chatId;
    }

    private function contextoVacio(): array
    {
        return [
            'ultima_entidad_tipo'=>null,
            'entidades'=>[],
            'candidatos'=>[],
            'ultima_consulta'=>null,
            'actualizado_en'=>null
        ];
    }
}
