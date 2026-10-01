<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntityResolverService
{
    public function resolver(array $plan, User $user): array
    {
        $mensajeOriginal=trim($plan['mensaje_original']??'');
        $mensaje=$this->normalizar($mensajeOriginal);
        $referencias=$plan['entidades']??[];
        $resultado=['estado'=>'sin_referencia','entidades'=>[],'ambiguas'=>[],'no_encontradas'=>[],'mensaje'=>null];

        $this->resolverIncubadoraExplicita($referencias,$user,$resultado);
        $this->resolverLoteExplicito($referencias,$user,$resultado);
        $this->resolverEspecieExplicita($referencias,$user,$resultado);
        $this->resolverUsuarioExplicito($referencias,$user,$resultado);

        if(!empty($resultado['no_encontradas'])||!empty($resultado['ambiguas'])) return $this->finalizar($resultado);
        if(($plan['operacion']??null)==='explicar'&&empty($resultado['entidades'])&&empty($resultado['no_encontradas'])) return $this->finalizar($resultado);

        foreach($this->tiposQueConvieneResolver($plan,$mensaje) as $tipo){
            if(isset($resultado['entidades'][$tipo])||isset($resultado['no_encontradas'][$tipo])) continue;
            $coincidencias=$this->buscarCoincidencias($tipo,$mensaje,$user);
            if($coincidencias->isEmpty()) continue;

            $mejorPuntaje=(int)$coincidencias->max('_puntaje');
            $mejores=$tipo==='usuario'?$coincidencias:$coincidencias->filter(fn($item)=>(int)$item['_puntaje']===$mejorPuntaje)->values();

            if($mejores->count()===1){
                $entidad=$mejores->first();
                unset($entidad['_puntaje']);
                $resultado['entidades'][$tipo]=$entidad;
                continue;
            }

            $resultado['ambiguas'][$tipo]=$mejores->map(function($item){unset($item['_puntaje']);return $item;})->values()->all();
        }

        $this->resolverFrasco($referencias,$user,$resultado);

        return $this->finalizar($resultado);
    }

    public function revalidarContexto(array $resolucion, User $user): array
    {
        if(empty($resolucion['contexto_aplicado'])) return $resolucion;

        $tipo=$resolucion['tipo_contexto']??null;
        if(!in_array($tipo,['incubadora','lote','frasco','especie','usuario'],true)) return $resolucion;

        $id=$resolucion['entidades'][$tipo]['id']??null;
        if(!$id) return [...$resolucion,'estado'=>'contexto_faltante','mensaje'=>'La referencia anterior no contiene un identificador válido.'];

        $registro=match($tipo){
            'incubadora'=>$this->queryIncubadorasPermitidas($user)->where('i.id',$id)->first(),
            'lote'=>$this->queryLotesPermitidos($user)->where('l.id',$id)->first(),
            'frasco'=>$this->queryFrascosPermitidos($user)->where('f.id',$id)->first(),
            'especie'=>$this->queryEspeciesPermitidas($user)->where('e.id',$id)->first(),
            'usuario'=>$this->queryUsuariosPermitidos($user)->where('u.id',$id)->first()
        };

        if(!$registro){
            $entidades=$resolucion['entidades']??[];
            unset($entidades[$tipo]);
            $noEncontradas=$resolucion['no_encontradas']??[];
            $noEncontradas[$tipo]=['id'=>(int)$id];

            return [...$resolucion,'estado'=>'no_encontrado','entidades'=>$entidades,'no_encontradas'=>$noEncontradas,'mensaje'=>'La referencia anterior ya no existe o ya no está dentro de los datos permitidos.'];
        }

        $entidad=match($tipo){
            'incubadora'=>$this->mapIncubadora($registro),
            'lote'=>$this->mapLote($registro),
            'frasco'=>$this->mapFrasco($registro),
            'especie'=>$this->mapEspecie($registro),
            'usuario'=>$this->mapUsuario($registro)
        };

        $entidades=$resolucion['entidades']??[];
        $entidades[$tipo]=$entidad;

        return [...$resolucion,'estado'=>'resuelto','entidades'=>$entidades,'mensaje'=>'Referencia contextual revalidada correctamente.'];
    }

    private function resolverIncubadoraExplicita(array $referencias, User $user, array &$resultado): void
    {
        $referencia=$referencias['incubadora']??[];
        $id=$referencia['id']??$referencias['incubadora_id']??null;
        $codigo=$referencia['codigo']??$referencias['incubadora_codigo']??null;

        if(!$id&&!$codigo) return;

        $query=$this->queryIncubadorasPermitidas($user);

        if($id) $query->where('i.id',(int)$id);
        if($codigo) $query->whereRaw('UPPER(i.codigo) = ?',[Str::upper(trim((string)$codigo))]);

        $incubadora=$query->first();

        if(!$incubadora){
            $resultado['no_encontradas']['incubadora']=['id'=>$id?(int)$id:null,'codigo'=>$codigo];
            return;
        }

        $resultado['entidades']['incubadora']=$this->mapIncubadora($incubadora);
    }

    private function resolverLoteExplicito(array $referencias, User $user, array &$resultado): void
    {
        $referencia=$referencias['lote']??[];
        $id=$referencia['id']??$referencias['lote_id']??null;
        $codigo=$referencia['codigo']??$referencias['lote_codigo']??null;

        if(!$id&&!$codigo) return;

        $query=$this->queryLotesPermitidos($user);

        if($id) $query->where('l.id',(int)$id);
        if($codigo) $query->whereRaw('UPPER(l.codigo_lote) = ?',[Str::upper(trim((string)$codigo))]);

        $lote=$query->first();

        if(!$lote){
            $resultado['no_encontradas']['lote']=['id'=>$id?(int)$id:null,'codigo'=>$codigo];
            return;
        }

        $resultado['entidades']['lote']=$this->mapLote($lote);
    }

    private function resolverEspecieExplicita(array $referencias, User $user, array &$resultado): void
    {
        $referencia=$referencias['especie']??[];
        $id=$referencia['id']??$referencias['especie_id']??null;

        if(!$id&&!empty($referencia['texto'])){
            $texto=$this->normalizar($referencia['texto']);
            $especies=$this->queryEspeciesPermitidas($user)->get()->filter(fn($item)=>
                $this->normalizar($item->nombre_comun)===$texto
                ||$this->normalizar($item->nombre_cientifico??'')===$texto
            )->values();
            if($especies->isEmpty()) $resultado['no_encontradas']['especie']=['codigo'=>$referencia['texto']];
            elseif($especies->count()>1) $resultado['ambiguas']['especie']=$especies->map(fn($item)=>$this->mapEspecie($item))->all();
            else $resultado['entidades']['especie']=$this->mapEspecie($especies->first());
            return;
        }

        if(!$id) return;

        $especie=$this->queryEspeciesPermitidas($user)->where('e.id',(int)$id)->first();

        if(!$especie){
            $resultado['no_encontradas']['especie']=['id'=>(int)$id];
            return;
        }

        $resultado['entidades']['especie']=$this->mapEspecie($especie);
    }

    private function resolverUsuarioExplicito(array $referencias, User $user, array &$resultado): void
    {
        $referencia=$referencias['usuario']??[];
        $id=$referencia['id']??$referencias['usuario_id']??null;

        if(!$id&&!empty($referencia['texto'])){
            $texto=$this->normalizar($referencia['texto']);
            $usuarios=$this->queryUsuariosPermitidos($user)->get()->filter(function($item) use($texto){
                return preg_match('/(?<![a-z0-9])'.preg_quote($texto,'/').'(?![a-z0-9])/u',$this->normalizar($item->name))===1;
            })->values();
            if($usuarios->isEmpty()) $resultado['no_encontradas']['usuario']=['codigo'=>$referencia['texto']];
            elseif($usuarios->count()>1) $resultado['ambiguas']['usuario']=$usuarios->map(fn($item)=>$this->mapUsuario($item))->all();
            else $resultado['entidades']['usuario']=$this->mapUsuario($usuarios->first());
            return;
        }

        if(!$id) return;

        $usuario=$this->queryUsuariosPermitidos($user)->where('u.id',(int)$id)->first();

        if(!$usuario){
            $resultado['no_encontradas']['usuario']=['id'=>(int)$id];
            return;
        }

        $resultado['entidades']['usuario']=$this->mapUsuario($usuario);
    }

    private function resolverFrasco(array $referencias, User $user, array &$resultado): void
    {
        $referencia=$referencias['frasco']??[];
        $frascoId=$referencia['id']??$referencias['frasco_id']??null;
        $numero=$referencia['numero_frasco']??$referencia['numero']??$referencias['frasco_numero']??null;

        if(!$frascoId&&!$numero) return;

        $query=$this->queryFrascosPermitidos($user);
        $loteId=$resultado['entidades']['lote']['id']??null;
        if($loteId) $query->where('f.lote_id',$loteId);
        if(isset($resultado['entidades']['incubadora']['id'])){
            $query->where('i.id',$resultado['entidades']['incubadora']['id']);
        }

        if($frascoId){
            $frasco=$query->where('f.id',(int)$frascoId)->first();

            if(!$frasco){
                $resultado['no_encontradas']['frasco']=['id'=>(int)$frascoId];
                return;
            }

            $resultado['entidades']['frasco']=$this->mapFrasco($frasco);
            return;
        }

        $frascos=$query->where('f.numero_frasco',(int)$numero)->get();

        if($frascos->isEmpty()){
            $resultado['no_encontradas']['frasco']=['numero_frasco'=>$numero,'lote_id'=>$loteId];
            return;
        }

        if($frascos->count()===1){
            $resultado['entidades']['frasco']=$this->mapFrasco($frascos->first());
            return;
        }

        $resultado['ambiguas']['frasco']=$frascos->map(fn($frasco)=>$this->mapFrasco($frasco))->values()->all();
    }

    private function tiposQueConvieneResolver(array $plan, string $mensaje): array
    {
        $tipos=[];
        $recurso=$plan['recurso']??null;

        if(in_array($recurso,['lecturas','alertas','controles','incubadoras'],true)){$tipos[]='incubadora';$tipos[]='lote';$tipos[]='especie';}
        if(in_array($recurso,['lotes','frascos','seguimientos','condiciones_optimas'],true)){$tipos[]='lote';$tipos[]='especie';$tipos[]='incubadora';}
        if($recurso==='especies') $tipos[]='especie';
        if(in_array($recurso,['usuarios','asignaciones'],true)){$tipos[]='usuario';$tipos[]='incubadora';}

        if(str_contains($mensaje,'incubadora')||str_contains($mensaje,'camara')) $tipos[]='incubadora';
        if(str_contains($mensaje,'lote')){$tipos[]='lote';$tipos[]='especie';}
        if(str_contains($mensaje,'especie')||str_contains($mensaje,'semilla')) $tipos[]='especie';
        if(str_contains($mensaje,'usuario')||str_contains($mensaje,'encargado')||str_contains($mensaje,'responsable')) $tipos[]='usuario';

        return array_values(array_unique($tipos));
    }

    private function buscarCoincidencias(string $tipo, string $mensaje, User $user): Collection
    {
        $registros=match($tipo){
            'incubadora'=>$this->queryIncubadorasPermitidas($user)->get(),
            'lote'=>$this->queryLotesPermitidos($user)->get(),
            'especie'=>$this->queryEspeciesPermitidas($user)->get(),
            'usuario'=>$this->queryUsuariosPermitidos($user)->get(),
            default=>collect()
        };

        if($registros->isEmpty()) return collect();

        return $registros->map(function($registro) use($tipo,$mensaje){
            $puntaje=$this->puntuar($mensaje,$this->aliasesRegistro($tipo,$registro));

            if($puntaje<=0) return null;

            $datos=match($tipo){
                'incubadora'=>$this->mapIncubadora($registro),
                'lote'=>$this->mapLote($registro),
                'especie'=>$this->mapEspecie($registro),
                'usuario'=>$this->mapUsuario($registro),
                default=>[]
            };

            $datos['_puntaje']=$puntaje;

            return $datos;
        })->filter()->sortByDesc('_puntaje')->values();
    }

    private function aliasesRegistro(string $tipo, object $registro): array
    {
        return match($tipo){
            'incubadora'=>array_filter([$registro->codigo??null,$registro->nombre??null]),
            'lote'=>array_filter([$registro->codigo_lote??null]),
            'especie'=>array_filter([$registro->nombre_comun??null,$registro->nombre_cientifico??null]),
            'usuario'=>array_filter([$registro->name??null]),
            default=>[]
        };
    }

    private function puntuar(string $mensaje, array $aliases): int
    {
        $mejor=0;

        foreach($aliases as $alias){
            $alias=$this->normalizar((string)$alias);

            if($alias==='') continue;
            if($mensaje===$alias){$mejor=max($mejor,200);continue;}
            if(str_contains($mensaje,$alias)){$mejor=max($mejor,150+min(strlen($alias),40));continue;}

            $tokensAlias=$this->tokensSignificativos($alias);

            if(empty($tokensAlias)) continue;

            $tokensMensaje=$this->tokensSignificativos($mensaje);
            $coincidentes=array_values(array_intersect($tokensAlias,$tokensMensaje));

            if(empty($coincidentes)) continue;

            $proporcion=count($coincidentes)/count($tokensAlias);

            if($proporcion<0.5&&count($coincidentes)<2) continue;

            $mejor=max($mejor,(int)round(($proporcion*80)+(count($coincidentes)*10)));
        }

        return $mejor;
    }

    private function tokensSignificativos(string $texto): array
    {
        $stopwords=['incubadora','incubadoras','camara','camaras','lote','lotes','especie','especies','frasco','frascos','usuario','usuarios','encargado','encargados','microseed','control','del','de','la','las','el','los','un','una','unos','unas','para','por','con','sin','que','cual','cuales','como','esta','este','ese','esa','actual','actualmente'];

        return collect(preg_split('/\s+/',$this->normalizar($texto)))->filter(fn($token)=>strlen($token)>=3&&!in_array($token,$stopwords,true))->unique()->values()->all();
    }

    private function queryIncubadorasPermitidas(User $user): Builder
    {
        $query=DB::table('incubadoras as i')->leftJoin('estados_incubadora as ei','ei.id','=','i.estado_incubadora_id')->select(['i.id','i.codigo','i.nombre','i.ubicacion','i.descripcion','ei.clave as estado_clave','ei.nombre as estado_nombre']);

        $this->restringirPorIncubadora($query,$user,'i.id');

        return $query;
    }

    private function queryLotesPermitidos(User $user): Builder
    {
        $query=DB::table('lotes as l')->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')->join('incubadoras as i','i.id','=','p.incubadora_id')->join('especies as e','e.id','=','l.especie_id')->leftJoin('estados_lote as el','el.id','=','l.estado_lote_id')->select(['l.id','l.codigo_lote','l.especie_id','l.fecha_siembra','l.fecha_inicio','l.fecha_fin','e.nombre_comun','e.nombre_cientifico','i.id as incubadora_id','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre','p.id as posicion_id','p.numero_posicion','el.clave as estado_clave','el.nombre as estado_nombre']);

        $this->restringirPorIncubadora($query,$user,'i.id');

        return $query;
    }

    private function queryFrascosPermitidos(User $user): Builder
    {
        $query=DB::table('frascos as f')->join('lotes as l','l.id','=','f.lote_id')->join('posiciones_incubadora as p','p.id','=','l.posicion_incubadora_id')->join('incubadoras as i','i.id','=','p.incubadora_id')->join('especies as e','e.id','=','l.especie_id')->leftJoin('estados_frasco as ef','ef.id','=','f.estado_frasco_id')->select(['f.id','f.lote_id','f.numero_frasco','f.cantidad_semillas','l.codigo_lote','e.id as especie_id','e.nombre_comun','e.nombre_cientifico','i.id as incubadora_id','i.codigo as incubadora_codigo','i.nombre as incubadora_nombre','ef.clave as estado_clave','ef.nombre as estado_nombre']);

        $this->restringirPorIncubadora($query,$user,'i.id');

        return $query;
    }

    private function queryEspeciesPermitidas(User $user): Builder
    {
        $query=DB::table('especies as e')->select(['e.id','e.nombre_comun','e.nombre_cientifico','e.familia','e.descripcion']);

        if($user->isSuperAdmin()) return $query;
        if(!$user->isEncargado()) return $query->whereRaw('1 = 0');

        $hoy=now('America/Mexico_City')->toDateString();

        $query->whereExists(function($subquery) use($user,$hoy){
            $subquery->selectRaw('1')->from('lotes as l2')->join('posiciones_incubadora as p2','p2.id','=','l2.posicion_incubadora_id')->join('asignaciones_incubadora as ai2','ai2.incubadora_id','=','p2.incubadora_id')->whereColumn('l2.especie_id','e.id')->where('ai2.user_id',$user->id)->whereDate('ai2.fecha_inicio','<=',$hoy)->where(function($query) use($hoy){$query->whereNull('ai2.fecha_fin')->orWhereDate('ai2.fecha_fin','>=',$hoy);});
        });

        return $query;
    }

    private function queryUsuariosPermitidos(User $user): Builder
    {
        $query=DB::table('users as u')->leftJoin('roles as r','r.id','=','u.role_id')->select(['u.id','u.name','u.email','u.activo','r.clave as rol_clave','r.nombre as rol_nombre']);

        return $user->isSuperAdmin()?$query:$query->where('u.id',$user->id);
    }

    private function restringirPorIncubadora(Builder $query, User $user, string $columnaIncubadora): void
    {
        if($user->isSuperAdmin()) return;

        if(!$user->isEncargado()){
            $query->whereRaw('1 = 0');
            return;
        }

        $hoy=now('America/Mexico_City')->toDateString();

        $query->whereExists(function($subquery) use($user,$columnaIncubadora,$hoy){
            $subquery->selectRaw('1')->from('asignaciones_incubadora as ai')->whereColumn('ai.incubadora_id',$columnaIncubadora)->where('ai.user_id',$user->id)->whereDate('ai.fecha_inicio','<=',$hoy)->where(function($query) use($hoy){$query->whereNull('ai.fecha_fin')->orWhereDate('ai.fecha_fin','>=',$hoy);});
        });
    }

    private function mapIncubadora(object $item): array
    {
        return ['tipo'=>'incubadora','id'=>$item->id,'codigo'=>$item->codigo,'nombre'=>$item->nombre,'ubicacion'=>$item->ubicacion,'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre]];
    }

    private function mapLote(object $item): array
    {
        return ['tipo'=>'lote','id'=>$item->id,'codigo'=>$item->codigo_lote,'fecha_siembra'=>$item->fecha_siembra,'fecha_inicio'=>$item->fecha_inicio,'fecha_fin'=>$item->fecha_fin,'especie'=>['id'=>$item->especie_id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico],'incubadora'=>['id'=>$item->incubadora_id,'codigo'=>$item->incubadora_codigo,'nombre'=>$item->incubadora_nombre],'posicion'=>['id'=>$item->posicion_id,'numero'=>$item->numero_posicion],'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre]];
    }

    private function mapFrasco(object $item): array
    {
        return ['tipo'=>'frasco','id'=>$item->id,'numero'=>$item->numero_frasco,'cantidad_semillas'=>$item->cantidad_semillas,'lote'=>['id'=>$item->lote_id,'codigo'=>$item->codigo_lote],'especie'=>['id'=>$item->especie_id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico],'incubadora'=>['id'=>$item->incubadora_id,'codigo'=>$item->incubadora_codigo,'nombre'=>$item->incubadora_nombre],'estado'=>['clave'=>$item->estado_clave,'nombre'=>$item->estado_nombre]];
    }

    private function mapEspecie(object $item): array
    {
        return ['tipo'=>'especie','id'=>$item->id,'nombre_comun'=>$item->nombre_comun,'nombre_cientifico'=>$item->nombre_cientifico,'familia'=>$item->familia];
    }

    private function mapUsuario(object $item): array
    {
        return ['tipo'=>'usuario','id'=>$item->id,'nombre'=>$item->name,'email'=>$item->email,'activo'=>(bool)$item->activo,'rol'=>['clave'=>$item->rol_clave,'nombre'=>$item->rol_nombre]];
    }

    private function finalizar(array $resultado): array
    {
        if(!empty($resultado['no_encontradas'])) return [...$resultado,'estado'=>'no_encontrado','mensaje'=>'La referencia indicada no existe o no está dentro de los datos permitidos para este usuario.'];
        if(!empty($resultado['ambiguas'])) return [...$resultado,'estado'=>'ambiguo','mensaje'=>'Se encontraron varias coincidencias. Es necesario que el usuario indique cuál desea consultar.'];
        if(!empty($resultado['entidades'])) return [...$resultado,'estado'=>'resuelto','mensaje'=>'Las entidades mencionadas fueron resueltas contra los registros actuales de MicroSeed Control.'];

        return [...$resultado,'estado'=>'sin_referencia','mensaje'=>'La pregunta no contiene una entidad concreta que sea necesario resolver.'];
    }

    private function normalizar(string $texto): string
    {
        $texto=Str::ascii(Str::lower($texto));
        $texto=preg_replace('/[^a-z0-9\s\-_]/',' ',$texto);

        return trim(preg_replace('/\s+/',' ',$texto));
    }
}
