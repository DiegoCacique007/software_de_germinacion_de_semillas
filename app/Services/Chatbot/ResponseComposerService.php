<?php

namespace App\Services\Chatbot;

use Carbon\Carbon;

class ResponseComposerService
{
    private string $timezone='America/Mexico_City';

    public function componer(array $plan,array $resolucion,array $resultado): array
    {
        if(($resolucion['estado']??null)==='ambiguo'){
            return $this->ambigua($resolucion);
        }

        if(($resolucion['estado']??null)==='no_encontrado'){
            return $this->noEncontrada($resolucion);
        }

        if(!($resultado['ok']??false)){
            return $this->error($resultado);
        }

        if(!empty($resultado['respuesta_directa'])){
            return $this->respuesta(
                $resultado['respuesta_directa'],
                $resultado['tipo']??'conversacion',
                $resultado['sugerencias']??[]
            );
        }

        return match($resultado['tipo']??null){
            'ultima_lectura'=>$this->lectura($resultado,'última'),
            'primera_lectura'=>$this->lectura($resultado,'primera'),
            'lista_lecturas'=>$this->listaLecturas($resultado),
            'estadistica_microclima'=>$this->estadistica($resultado),
            'conteo_lecturas'=>$this->conteoLecturas($resultado),

            'conteo_alertas'=>$this->conteoAlertas($resultado),
            'ultima_alerta'=>$this->ultimaAlerta($resultado),
            'lista_alertas'=>$this->listaAlertas($resultado),
            'ranking_alertas'=>$this->rankingAlertas($resultado),

            'recurso_conteo'=>$this->conteoRecurso($resultado),
            'recurso_lista'=>$this->listaRecurso($resultado),
            'recurso_detalle'=>$this->detalleRecurso($resultado),

            'condiciones_optimas'=>$this->condicionesOptimas($resultado),

            'usuario_conteo'=>$this->conteoUsuarios($resultado),
            'usuario_lista'=>$this->listaUsuarios($resultado),
            'usuario_detalle'=>$this->detalleUsuario($resultado),

            'asignacion_conteo'=>$this->conteoAsignaciones($resultado),
            'asignacion_lista'=>$this->listaAsignaciones($resultado),
            'asignacion_detalle'=>$this->detalleAsignacion($resultado),

            'seguimiento_conteo'=>$this->conteoSeguimientos($resultado),
            'seguimiento_lista'=>$this->listaSeguimientos($resultado),
            'seguimiento_detalle'=>$this->detalleSeguimiento($resultado),

            'evidencia_conteo'=>$this->conteoEvidencias($resultado),
            'evidencia_lista'=>$this->listaEvidencias($resultado),
            'evidencia_detalle'=>$this->detalleEvidencia($resultado),


            'control_conteo'=>$this->conteoControles($resultado),
            'control_lista'=>$this->listaControles($resultado),
            'control_detalle'=>$this->detalleControl($resultado),

            'posicion_conteo'=>$this->conteoPosiciones($resultado),
            'posicion_lista'=>$this->listaPosiciones($resultado),
            'posicion_detalle'=>$this->detallePosicion($resultado),

            'catalogo_conteo'=>$this->conteoCatalogo($resultado),
            'catalogo_lista'=>$this->listaCatalogo($resultado),

            'resumen_sistema'=>$this->resumenSistema($resultado),

            'conocimiento_conceptual'=>$this->conocimientoConceptual($resultado),

            default=>$this->respuesta(
                'La consulta fue procesada, pero todavía no tengo un formato específico para este resultado.',
                'resultado_generico'
            )
        };
    }

    private function lectura(array $r,string $tipo): array
    {
        $d=$r['datos'];
        $m=[];

        $m[]=$d['temperatura']!==null
            ?$this->numero($d['temperatura'],1).' °C de temperatura'
            :'temperatura sin dato';

        $m[]=$d['humedad']!==null
            ?$this->numero($d['humedad'],1).' % de humedad relativa'
            :'humedad relativa sin dato';

        $texto=
            "La {$tipo} lectura registrada de "
            .$this->incubadora($d['incubadora'])
            .' indica '
            .implode(' y ',$m)
            .'. Fecha: '
            .($d['fecha_formateada']??$this->fecha($d['fecha_hora']??null))
            .'.';

        if($tipo==='última'){
            $f=$this->frescura(
                $d['frescura']??null,
                (int)($d['minutos_desde_lectura']??0)
            );

            if($f!=='') $texto.=' '.$f;
        }

        foreach($r['comparaciones']??[] as $comparacion){
            $texto.="\n".$comparacion['especie'].': ';
            if(isset($comparacion['advertencia'])){
                $texto.=$comparacion['advertencia'];
                continue;
            }
            $partes=[];
            foreach($comparacion['variables'] as $variable=>$dato){
                $partes[]=$variable.' '.$dato['estado'].' ('.$this->numero($dato['min'],1).'–'.$this->numero($dato['max'],1).($variable==='temperatura'?' °C':' %').')';
            }
            $texto.=empty($partes)?'No hay datos suficientes para comparar.':implode('; ',$partes).'.';
        }

        if(!empty($d['observaciones'])){
            $texto.=' Observaciones: '.$this->conPunto((string)$d['observaciones']);
        }

        return $this->respuesta(
            $texto,
            $r['tipo'],
            [
                '¿Cuál fue la temperatura máxima de hoy?',
                '¿Cuál fue la humedad mínima de hoy?',
                'Dame las últimas 10 lecturas'
            ]
        );
    }

    private function listaLecturas(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$d){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->incubadora($d['incubadora'])
                .' — '
                .$this->valorUnidad($d['temperatura']??null,'°C',1)
                .' — '
                .$this->valorUnidad($d['humedad']??null,'%',1)
                .' — '
                .($d['fecha_formateada']??$this->fecha($d['fecha_hora']??null));
        }

        $texto=
            'Encontré '
            .count($items)
            .' '
            .(count($items)===1?'lectura':'lecturas')
            .' para '
            .$this->alcanceMicroclima($r['alcance']??[])
            .$this->sufijoPeriodo($r['periodo']??[])
            .":\n\n"
            .implode("\n",$lineas);

        return $this->respuesta(
            $texto,
            'lista_lecturas',
            [
                '¿Cuál fue la temperatura promedio?',
                '¿Cuál fue la humedad máxima?',
                '¿Cuántas lecturas hay registradas?'
            ]
        );
    }

    private function estadistica(array $r): array
    {
        $op=match($r['operacion']){
            'maximo'=>'máximo',
            'minimo'=>'mínimo',
            default=>$r['operacion']
        };

        $variable=($r['variable']??null)==='humedad'
            ?'humedad relativa'
            :($r['variable']??'variable');

        $texto=
            "El {$op} de {$variable} para "
            .$this->alcanceMicroclima($r['alcance']??[])
            .' durante '
            .($r['periodo']['descripcion']??'todo el historial disponible')
            .' fue de '
            .$this->valorUnidad(
                $r['valor']??null,
                $r['unidad']??'',
                2
            )
            .'.';

        $total=(int)($r['total_registros']??0);

        if($total>0){
            $texto.=
                " El cálculo utilizó {$total} "
                .($total===1?'lectura.':'lecturas.');
        }

        return $this->respuesta(
            $texto,
            'estadistica_microclima',
            [
                '¿Cuál fue la temperatura máxima?',
                '¿Cuál fue la humedad mínima?',
                'Dame las últimas 10 lecturas'
            ]
        );
    }

    private function conteoLecturas(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'lectura registrada':'lecturas registradas')
            .' para '
            .$this->alcanceMicroclima($r['alcance']??[])
            .' durante '
            .($r['periodo']['descripcion']??'todo el historial disponible')
            .'.',
            'conteo_lecturas',
            [
                'Dame las últimas 10 lecturas',
                '¿Cuál fue la temperatura promedio?',
                '¿Cuál fue la humedad promedio?'
            ]
        );
    }

    private function conteoAlertas(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'alerta':'alertas')
            .$this->descripcionFiltrosAlertas($r['filtros']??[])
            .' para '
            .$this->alcance($r['alcance']??[])
            .' durante '
            .($r['periodo']['descripcion']??'todo el historial disponible')
            .'.',
            'conteo_alertas',
            [
                'Dame las últimas 10 alertas',
                '¿Cuántas alertas están pendientes?',
                '¿Qué incubadora tiene más alertas?'
            ]
        );
    }

    private function ultimaAlerta(array $r): array
    {
        $a=$r['datos'];

        $texto=
            'La última alerta registrada corresponde a '
            .$this->incubadora($a['incubadora'])
            .'.';

        if(!empty($a['lote']['codigo'])) $texto.=' Lote: '.$a['lote']['codigo'].'.';
        if(!empty($a['tipo']['nombre'])) $texto.=' Tipo: '.$a['tipo']['nombre'].'.';
        if(!empty($a['nivel']['nombre'])) $texto.=' Nivel: '.$a['nivel']['nombre'].'.';
        if(!empty($a['estado']['nombre'])) $texto.=' Estado: '.$a['estado']['nombre'].'.';
        if(!empty($a['mensaje'])) $texto.=' '.$this->conPunto((string)$a['mensaje']);
        if(!empty($a['fecha_hora'])) $texto.=' Fecha: '.$this->fecha($a['fecha_hora']).'.';

        return $this->respuesta(
            $texto,
            'ultima_alerta',
            [
                '¿Cuántas alertas están pendientes?',
                'Dame las últimas 10 alertas',
                '¿Qué incubadora tiene más alertas?'
            ]
        );
    }

    private function listaAlertas(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$a){
            $linea=
                ($i+1)
                .'. '
                .$this->incubadora($a['incubadora']);

            if(!empty($a['lote']['codigo'])) $linea.=' — lote '.$a['lote']['codigo'];
            if(!empty($a['tipo']['nombre'])) $linea.=' — '.$a['tipo']['nombre'];
            if(!empty($a['estado']['nombre'])) $linea.=' — '.$a['estado']['nombre'];
            if(!empty($a['nivel']['nombre'])) $linea.=' — nivel '.$a['nivel']['nombre'];
            if(!empty($a['fecha_hora'])) $linea.=' — '.$this->fecha($a['fecha_hora']);
            if(!empty($a['mensaje'])) $linea.="\n   ".rtrim((string)$a['mensaje']);

            $lineas[]=$linea;
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .($total===1?'alerta':'alertas')
            .$this->descripcionFiltrosAlertas($r['filtros']??[])
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'lista_alertas',
            [
                '¿Cuántas alertas están pendientes?',
                'Alertas resueltas de hoy',
                '¿Qué incubadora tiene más alertas?'
            ]
        );
    }

    private function rankingAlertas(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$item){
            $total=(int)$item['total_alertas'];

            $lineas[]=
                ($i+1)
                .'. '
                .$this->incubadora($item['incubadora'])
                .' — '
                .$total
                .' '
                .($total===1?'alerta':'alertas');
        }

        return $this->respuesta(
            'Conteo de alertas por incubadora durante '
            .($r['periodo']['descripcion']??'todo el historial disponible')
            .":\n\n"
            .implode("\n",$lineas),
            'ranking_alertas',
            [
                '¿Cuántas alertas están pendientes?',
                'Dame las últimas 10 alertas',
                'Dame la última lectura'
            ]
        );
    }

    private function conteoRecurso(array $r): array
    {
        $total=(int)($r['total']??0);
        $recurso=$r['recurso']??'registros';
        $alcance=$r['alcance']??[];

        if(
            $recurso==='frascos'
            &&($alcance['tipo']??null)==='lote'
        ){
            $lote=$this->codigoLote($alcance);

            $texto=$lote
                ?"El lote {$lote} tiene {$total} ".$this->nombreRecurso($recurso,$total).' '.($total===1?'registrado':'registrados')
                :'Hay '.$total.' '.$this->nombreRecurso($recurso,$total).' '.($total===1?'registrado':'registrados');
        }else{
            $texto=
                "Hay {$total} "
                .$this->nombreRecurso($recurso,$total)
                .' '
                .$this->contextoAlcance($alcance);
        }

        $texto.=$this->sufijoPeriodo($r['periodo']??[]);

        return $this->respuesta(
            $this->conPunto($texto),
            'recurso_conteo',
            $this->sugerenciasRecurso($recurso)
        );
    }

    private function listaRecurso(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->describirRecurso(
                    $r['recurso'],
                    $item
                );
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .$this->nombreRecurso($r['recurso'],$total)
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'recurso_lista',
            $this->sugerenciasRecurso($r['recurso'])
        );
    }

    private function detalleRecurso(array $r): array
    {
        return $this->respuesta(
            $this->describirRecurso(
                $r['recurso'],
                $r['datos']
            ),
            'recurso_detalle',
            $this->sugerenciasRecurso($r['recurso'])
        );
    }

    private function describirRecurso(string $recurso,array $item): string
    {
        return match($recurso){
            'lotes'=>$this->describirLote($item),
            'frascos'=>$this->describirFrasco($item),
            'especies'=>$this->describirEspecie($item),
            'incubadoras'=>$this->describirIncubadora($item),
            default=>'Registro de MicroSeed Control.'
        };
    }

    private function describirLote(array $i): string
    {
        $t='Lote '.($i['codigo']??'#'.($i['id']??''));

        if(!empty($i['especie']['nombre_comun'])){
            $t.=' — especie: '.$i['especie']['nombre_comun'];
        }

        if(!empty($i['incubadora']['codigo'])){
            $t.=' — incubadora: '.$i['incubadora']['codigo'];
        }

        if(isset($i['posicion']['numero'])&&$i['posicion']['numero']!==null){
            $t.=' — posición: '.$i['posicion']['numero'];
        }

        if(!empty($i['estado']['nombre'])){
            $t.=' — estado: '.$i['estado']['nombre'];
        }

        if(!empty($i['fecha_siembra'])){
            $t.=' — siembra: '.$this->fechaDia($i['fecha_siembra']);
        }

        return $this->conPunto($t);
    }

    private function describirFrasco(array $i): string
    {
        $t='Frasco '.($i['numero']??'#'.($i['id']??''));

        if(!empty($i['lote']['codigo'])){
            $t.=' — lote: '.$i['lote']['codigo'];
        }

        if(array_key_exists('cantidad_semillas',$i)&&$i['cantidad_semillas']!==null){
            $t.=' — semillas: '.$i['cantidad_semillas'];
        }

        if(!empty($i['especie']['nombre_comun'])){
            $t.=' — especie: '.$i['especie']['nombre_comun'];
        }

        if(!empty($i['incubadora']['codigo'])){
            $t.=' — incubadora: '.$i['incubadora']['codigo'];
        }

        if(!empty($i['estado']['nombre'])){
            $t.=' — estado: '.$i['estado']['nombre'];
        }

        return $this->conPunto($t);
    }

    private function describirEspecie(array $i): string
    {
        $t=$i['nombre_comun']??'Especie #'.($i['id']??'');

        if(!empty($i['nombre_cientifico'])){
            $t.=' ('.$i['nombre_cientifico'].')';
        }

        if(!empty($i['familia'])){
            $t.=' — familia: '.$i['familia'];
        }

        if(!empty($i['descripcion'])){
            $t.='. '.trim((string)$i['descripcion']);
        }

        return $this->conPunto($t);
    }

    private function describirIncubadora(array $i): string
    {
        $t='Incubadora '.($i['codigo']??'#'.($i['id']??''));

        if(!empty($i['nombre'])) $t.=' — '.$i['nombre'];
        if(!empty($i['estado']['nombre'])) $t.=' — estado: '.$i['estado']['nombre'];
        if(!empty($i['ubicacion'])) $t.=' — ubicación: '.$i['ubicacion'];

        $t.=' — alertas activas: '.(int)($i['alertas_activas']??0);

        if(!empty($i['ultima_lectura'])){
            $l=$i['ultima_lectura'];

            $t.=
                ' — última lectura registrada: '
                .$this->valorUnidad($l['temperatura']??null,'°C',1)
                .' / '
                .$this->valorUnidad($l['humedad']??null,'%',1);

            if(!empty($l['fecha_hora'])){
                $t.=' — '.$this->fecha($l['fecha_hora']);
            }
        }else{
            $t.=' — sin lecturas registradas';
        }

        return $this->conPunto($t);
    }

    private function condicionesOptimas(array $r): array
    {
        $items=$r['datos']??[];

        $especie=
            $r['especie']['nombre_comun']
            ??$r['especie']['nombre_cientifico']
            ??'la especie consultada';

        $variable=$r['variable']??null;
        $lineas=[];

        foreach($items as $c){
            $p=[];

            if($variable!=='humedad'){
                $p[]=
                    'temperatura '
                    .$this->numero($c['temperatura_min']??null,2)
                    .'–'
                    .$this->numero($c['temperatura_max']??null,2)
                    .' °C';
            }

            if($variable!=='temperatura'){
                $p[]=
                    'humedad '
                    .$this->numero($c['humedad_min']??null,2)
                    .'–'
                    .$this->numero($c['humedad_max']??null,2)
                    .' %';
            }

            $l='Configuración #'.$c['id'].': '.implode('; ',$p);

            if(!empty($c['observaciones'])){
                $l.='. '.trim((string)$c['observaciones']);
            }

            $lineas[]=$this->conPunto($l);
        }

        $texto=
            'Rangos registrados para '
            .$especie
            .":\n\n"
            .implode("\n",$lineas);

        $total=(int)($r['total']??count($items));

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} configuraciones.";
        }

        if($total>1){
            $texto.="\n\nHay varias configuraciones registradas; el chatbot no asume automáticamente cuál está vigente.";
        }

        return $this->respuesta(
            $texto,
            'condiciones_optimas',
            [
                'Última lectura',
                'Lotes de '.$especie,
                '¿Cuántas especies hay?'
            ]
        );
    }

    private function conteoUsuarios(array $r): array
    {
        $total=(int)($r['total']??0);
        $f=$r['filtros']??[];

        $nombre=
            ($f['rol_usuario']??null)==='encargado'
                ?($total===1?'encargado':'encargados')
                :($total===1?'usuario':'usuarios');

        $estado=
            array_key_exists('usuario_activo',$f)
                ?($f['usuario_activo']?' activos':' inactivos')
                :'';

        return $this->respuesta(
            "Hay {$total} {$nombre}{$estado} dentro de "
            .$this->alcance($r['alcance']??[])
            .'.',
            'usuario_conteo',
            [
                'Muéstrame los usuarios activos',
                '¿Cuántos encargados hay?',
                'Muéstrame las asignaciones vigentes'
            ]
        );
    }

    private function listaUsuarios(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=($i+1).'. '.$this->describirUsuario($item);
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .($total===1?'usuario':'usuarios')
            .$this->sufijoUsuarioFiltros($r['filtros']??[])
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'usuario_lista',
            [
                '¿Cuántos encargados hay?',
                'Muéstrame los usuarios activos',
                'Muéstrame las asignaciones vigentes'
            ]
        );
    }

    private function detalleUsuario(array $r): array
    {
        return $this->respuesta(
            $this->describirUsuario($r['datos']),
            'usuario_detalle',
            [
                '¿Qué incubadoras tiene asignadas este encargado?',
                'Muéstrame las asignaciones vigentes',
                'Resumen del sistema'
            ]
        );
    }

    private function describirUsuario(array $u): string
    {
        $t=
            'Usuario #'
            .($u['id']??'')
            .' — '
            .($u['nombre']??'sin nombre');

        if(!empty($u['rol']['nombre'])){
            $t.=' — rol: '.$u['rol']['nombre'];
        }

        $t.=' — '.(($u['activo']??false)?'activo':'inactivo');

        return $this->conPunto($t);
    }

    private function conteoAsignaciones(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'asignación vigente':'asignaciones vigentes')
            .$this->alcanceAsignacion($r['alcance']??[])
            .'.',
            'asignacion_conteo',
            [
                'Muéstrame las asignaciones vigentes',
                'Estado de incubadoras'
            ]
        );
    }

    private function listaAsignaciones(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $total=(int)($r['total']??count($items));

        if($total===1&&count($items)===1){
            $a=$items[0];
            $inc=$a['incubadora']??[];
            $u=$a['usuario']??[];

            $t=
                'La incubadora '
                .$this->incubadora($inc)
                .' está asignada actualmente a '
                .($u['nombre']??'un usuario sin nombre');

            if(!empty($u['rol']['nombre'])){
                $t.=' ('.$u['rol']['nombre'].')';
            }

            $t.=
                '. Asignación vigente desde '
                .$this->fechaDia($a['fecha_inicio']??null)
                .' hasta '
                .(!empty($a['fecha_fin'])
                    ?$this->fechaDia($a['fecha_fin'])
                    :'sin fecha de fin')
                .'.';

            if(!empty($a['observaciones'])){
                $t.=' Observaciones: '.$this->conPunto((string)$a['observaciones']);
            }

            return $this->respuesta(
                $t,
                'asignacion_lista',
                [
                    '¿Cuál es la última lectura de esa incubadora?',
                    'Muéstrame los lotes de esa incubadora',
                    '¿Cuántas alertas tiene esa incubadora?'
                ]
            );
        }

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=($i+1).'. '.$this->describirAsignacion($item);
        }

        $texto=
            "Encontré {$total} "
            .($total===1?'asignación vigente':'asignaciones vigentes')
            .$this->alcanceAsignacion($r['alcance']??[])
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'asignacion_lista',
            [
                '¿Cuántas asignaciones vigentes hay?',
                'Estado de incubadoras',
                'Lotes activos'
            ]
        );
    }

    private function detalleAsignacion(array $r): array
    {
        $a=$r['datos']??[];

        if(empty($a)) return $this->sinDatos($r);

        $inc=$a['incubadora']??[];
        $u=$a['usuario']??[];

        $t=
            'Asignación vigente: '
            .$this->incubadora($inc)
            .' — encargado: '
            .($u['nombre']??'usuario sin nombre');

        if(!empty($u['rol']['nombre'])){
            $t.=' ('.$u['rol']['nombre'].')';
        }

        $t.=
            ' — desde '
            .$this->fechaDia($a['fecha_inicio']??null)
            .' hasta '
            .(!empty($a['fecha_fin'])
                ?$this->fechaDia($a['fecha_fin'])
                :'sin fecha de fin')
            .'.';

        if(!empty($a['observaciones'])){
            $t.=' Observaciones: '.$this->conPunto((string)$a['observaciones']);
        }

        return $this->respuesta(
            $t,
            'asignacion_detalle',
            [
                '¿Cuál es la última lectura de esa incubadora?',
                'Muéstrame los lotes de esa incubadora',
                '¿Cuántas alertas tiene esa incubadora?'
            ]
        );
    }

    private function describirAsignacion(array $a): string
    {
        $inc=$a['incubadora']??[];
        $u=$a['usuario']??[];

        $t=
            $this->incubadora($inc)
            .' — usuario: '
            .($u['nombre']??'sin nombre');

        if(!empty($u['rol']['nombre'])){
            $t.=' — rol: '.$u['rol']['nombre'];
        }

        $t.=
            ' — desde '
            .$this->fechaDia($a['fecha_inicio']??null)
            .' hasta '
            .(!empty($a['fecha_fin'])
                ?$this->fechaDia($a['fecha_fin'])
                :'sin fecha de fin');

        if(!empty($a['observaciones'])){
            $t.=' — '.$this->conPunto((string)$a['observaciones']);
        }

        return $this->conPunto($t);
    }

    private function conteoSeguimientos(array $r): array
    {
        $total=(int)($r['total']??0);
        $s=$r['subtipo']??'lote';

        $nombre=
            $s==='frasco'
                ?($total===1?'seguimiento de frasco':'seguimientos de frasco')
                :($total===1?'seguimiento de lote':'seguimientos de lote');

        return $this->respuesta(
            "Hay {$total} {$nombre}"
            .$this->sufijoAlcancePeriodo($r)
            .'.',
            'seguimiento_conteo',
            $this->sugerenciasSeguimiento($s)
        );
    }

    private function listaSeguimientos(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $s=$r['subtipo']??'lote';
        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->describirSeguimiento(
                    $s,
                    $item,
                    false
                );
        }

        $total=(int)($r['total']??count($items));

        $nombre=
            $s==='frasco'
                ?($total===1?'seguimiento de frasco':'seguimientos de frasco')
                :($total===1?'seguimiento de lote':'seguimientos de lote');

        $texto=
            "Encontré {$total} {$nombre}"
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'seguimiento_lista',
            $this->sugerenciasSeguimiento($s)
        );
    }

    private function detalleSeguimiento(array $r): array
    {
        $d=$r['datos']??[];

        if(empty($d)) return $this->sinDatos($r);

        $s=$r['subtipo']??'lote';
        $v=$r['variable']??null;

        if($v==='germinacion'&&$s==='lote'){
            return $this->respuesta(
                'El seguimiento más reciente del lote '
                .($d['lote']['codigo']??'lote consultado')
                .' registra '
                .$this->valorUnidad($d['porcentaje_germinacion']??null,'%',2)
                .' de germinación. Revisión: '
                .$this->fechaDia($d['fecha_revision']??null)
                .'.',
                'seguimiento_detalle',
                $this->sugerenciasSeguimiento($s)
            );
        }

        if($v==='semillas_germinadas'){
            $obj=
                $s==='frasco'
                    ?'el frasco '.($d['frasco']['numero']??'#'.($d['frasco']['id']??'')).' del lote '.($d['lote']['codigo']??'consultado')
                    :'el lote '.($d['lote']['codigo']??'consultado');

            $t=
                'El seguimiento más reciente de '
                .$obj
                .' registra '
                .(int)($d['semillas_germinadas']??0)
                .' semillas germinadas';

            if($s==='frasco'&&isset($d['frasco']['cantidad_semillas'])){
                $t.=
                    ' de '
                    .(int)$d['frasco']['cantidad_semillas']
                    .' semillas registradas';
            }

            $t.=
                '. Revisión: '
                .$this->fechaDia($d['fecha_revision']??null)
                .'.';

            return $this->respuesta(
                $t,
                'seguimiento_detalle',
                $this->sugerenciasSeguimiento($s)
            );
        }

        if($v==='altura'){
            $obj=
                $s==='frasco'
                    ?'frasco '.($d['frasco']['numero']??'#'.($d['frasco']['id']??'')).' del lote '.($d['lote']['codigo']??'consultado')
                    :'lote '.($d['lote']['codigo']??'consultado');

            return $this->respuesta(
                'La altura promedio registrada en el seguimiento más reciente del '
                .$obj
                .' es '
                .$this->valorUnidad($d['altura_promedio_cm']??null,'cm',2)
                .'. Revisión: '
                .$this->fechaDia($d['fecha_revision']??null)
                .'.',
                'seguimiento_detalle',
                $this->sugerenciasSeguimiento($s)
            );
        }

        if($v==='etapa'){
            if($s!=='lote'){
                return $this->respuesta(
                    'Los seguimientos de frasco no registran una etapa de desarrollo propia; registran el estado del frasco.',
                    'seguimiento_detalle',
                    $this->sugerenciasSeguimiento($s)
                );
            }

            return $this->respuesta(
                'La etapa registrada en el seguimiento más reciente del lote '
                .($d['lote']['codigo']??'lote consultado')
                .' es '
                .($d['etapa']['nombre']??'sin etapa registrada')
                .'. Revisión: '
                .$this->fechaDia($d['fecha_revision']??null)
                .'.',
                'seguimiento_detalle',
                $this->sugerenciasSeguimiento($s)
            );
        }

        return $this->respuesta(
            $this->describirSeguimiento(
                $s,
                $d,
                true
            ),
            'seguimiento_detalle',
            $this->sugerenciasSeguimiento($s)
        );
    }

    private function describirSeguimiento(string $s,array $d,bool $detalle=true): string
    {
        if($s==='frasco'){
            $f=$d['frasco']??[];
            $l=$d['lote']??[];

            $t=
                'Frasco '
                .($f['numero']??'#'.($f['id']??''))
                .' del lote '
                .($l['codigo']??'sin código')
                .' — revisión: '
                .$this->fechaDia($d['fecha_revision']??null)
                .' — semillas germinadas: '
                .(int)($d['semillas_germinadas']??0);

            if(isset($f['cantidad_semillas'])){
                $t.='/'.(int)$f['cantidad_semillas'];
            }

            if(($d['altura_promedio_cm']??null)!==null){
                $t.=
                    ' — altura promedio: '
                    .$this->numero($d['altura_promedio_cm'],2)
                    .' cm';
            }

            if(!empty($d['estado']['nombre'])){
                $t.=' — estado: '.$d['estado']['nombre'];
            }

            if(!empty($d['incubadora']['codigo'])){
                $t.=' — incubadora: '.$d['incubadora']['codigo'];
            }

            if($detalle&&!empty($d['usuario']['nombre'])){
                $t.=' — registrado por: '.$d['usuario']['nombre'];
            }

            if($detalle&&!empty($d['observaciones'])){
                $t.='. Observaciones: '.trim((string)$d['observaciones']);
            }

            return $this->conPunto($t);
        }

        $l=$d['lote']??[];

        $t=
            'Lote '
            .($l['codigo']??'sin código')
            .' — revisión: '
            .$this->fechaDia($d['fecha_revision']??null)
            .' — semillas germinadas: '
            .(int)($d['semillas_germinadas']??0)
            .' — germinación: '
            .$this->valorUnidad(
                $d['porcentaje_germinacion']??null,
                '%',
                2
            );

        if(isset($d['frascos_activos'])){
            $t.=' — frascos activos: '.(int)$d['frascos_activos'];
        }

        if(($d['altura_promedio_cm']??null)!==null){
            $t.=
                ' — altura promedio: '
                .$this->numero($d['altura_promedio_cm'],2)
                .' cm';
        }

        if(!empty($d['etapa']['nombre'])){
            $t.=' — etapa: '.$d['etapa']['nombre'];
        }

        if(!empty($d['incubadora']['codigo'])){
            $t.=' — incubadora: '.$d['incubadora']['codigo'];
        }

        if($detalle&&!empty($d['usuario']['nombre'])){
            $t.=' — registrado por: '.$d['usuario']['nombre'];
        }

        if($detalle&&!empty($d['observaciones'])){
            $t.='. Observaciones: '.trim((string)$d['observaciones']);
        }

        return $this->conPunto($t);
    }

    private function sugerenciasSeguimiento(string $s): array
    {
        return $s==='frasco'
            ?[
                '¿Cuántas semillas germinaron en ese frasco?',
                '¿Cuál es la altura promedio de ese frasco?',
                'Muéstrame los seguimientos de ese frasco'
            ]
            :[
                '¿Cuál es el porcentaje de germinación de ese lote?',
                '¿Cuál es la etapa actual de ese lote?',
                'Muéstrame los últimos seguimientos de ese lote'
            ];
    }

    private function conteoEvidencias(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'evidencia de lote':'evidencias de lote')
            .$this->sufijoAlcancePeriodo($r)
            .'.',
            'evidencia_conteo',
            $this->sugerenciasEvidencia()
        );
    }

    private function listaEvidencias(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->describirEvidencia(
                    $item,
                    false
                );
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .($total===1?'evidencia de lote':'evidencias de lote')
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'evidencia_lista',
            $this->sugerenciasEvidencia()
        );
    }

    private function detalleEvidencia(array $r): array
    {
        $d=$r['datos']??[];

        if(empty($d)) return $this->sinDatos($r);

        return $this->respuesta(
            $this->describirEvidencia(
                $d,
                true
            ),
            'evidencia_detalle',
            $this->sugerenciasEvidencia()
        );
    }

    private function describirEvidencia(array $d,bool $detalle=true): string
    {
        $t=
            'Evidencia #'
            .($d['id']??'')
            .' del lote '
            .($d['lote']['codigo']??'sin código');

        if(!empty($d['seguimiento']['fecha_revision'])){
            $t.=
                ' — seguimiento del '
                .$this->fechaDia($d['seguimiento']['fecha_revision']);
        }

        if(array_key_exists('porcentaje_germinacion',$d['seguimiento']??[])){
            $t.=
                ' — germinación registrada: '
                .$this->valorUnidad(
                    $d['seguimiento']['porcentaje_germinacion']??null,
                    '%',
                    2
                );
        }

        if(!empty($d['fecha_registro'])){
            $t.=' — evidencia registrada: '.$this->fecha($d['fecha_registro']);
        }

        if(!empty($d['incubadora']['codigo'])){
            $t.=' — incubadora: '.$d['incubadora']['codigo'];
        }

        if(!empty($d['especie']['nombre_comun'])){
            $t.=' — especie: '.$d['especie']['nombre_comun'];
        }

        if($detalle&&!empty($d['usuario']['nombre'])){
            $t.=' — seguimiento registrado por: '.$d['usuario']['nombre'];
        }

        if(!empty($d['descripcion'])){
            $t.='. Descripción: '.trim((string)$d['descripcion']);
        }

        if($d['archivo_disponible']??false){
            $t.='. Tiene un archivo de evidencia asociado; consúltalo desde el módulo de evidencias';
        }

        return $this->conPunto($t);
    }

    private function sugerenciasEvidencia(): array
    {
        return [
            '¿Cuál es la última evidencia de ese lote?',
            '¿Cuántas evidencias tiene ese lote?',
            'Muéstrame las evidencias de ese lote'
        ];
    }

    private function conteoControles(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'registro de control':'registros de control')
            .$this->sufijoAlcancePeriodo($r)
            .'. Estos registros representan historial y no confirman por sí solos el estado físico actual de los actuadores.',
            'control_conteo',
            $this->sugerenciasControl()
        );
    }

    private function listaControles(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->describirControl(
                    $item,
                    false
                );
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .($total===1?'registro de control':'registros de control')
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        $texto.="\n\nEstos son registros históricos; no confirman el estado físico actual de los actuadores.";

        return $this->respuesta(
            $texto,
            'control_lista',
            $this->sugerenciasControl()
        );
    }

    private function detalleControl(array $r): array
    {
        $d=$r['datos']??[];

        if(empty($d)) return $this->sinDatos($r);

        $v=$r['variable']??null;
        $inc=$this->incubadora($d['incubadora']??[]);
        $fecha=$this->fecha($d['fecha_hora']??null);

        if($v==='modo_control'){
            return $this->respuesta(
                'El último control registrado de '
                .$inc
                .' utilizó el modo '
                .($d['modo']['nombre']??$d['modo']['clave']??'sin dato')
                .'. Fecha: '
                .$fecha
                .'. Es un registro histórico y no confirma el modo operativo actual del equipo.',
                'control_detalle',
                $this->sugerenciasControl()
            );
        }

        if($v==='tipo_control'){
            return $this->respuesta(
                'El último control registrado de '
                .$inc
                .' fue de tipo '
                .($d['tipo']['nombre']??$d['tipo']['clave']??'sin dato')
                .'. Fecha: '
                .$fecha
                .'.',
                'control_detalle',
                $this->sugerenciasControl()
            );
        }

        if($v==='valor_aplicado'){
            return $this->respuesta(
                'El último control registrado de '
                .$inc
                .' tiene un valor aplicado de '
                .$this->numero($d['valor_aplicado']??null,2)
                .'. Fecha: '
                .$fecha
                .'.',
                'control_detalle',
                $this->sugerenciasControl()
            );
        }

        return $this->respuesta(
            $this->describirControl(
                $d,
                true
            )
            .' Este registro es histórico y no confirma el estado físico actual del actuador.',
            'control_detalle',
            $this->sugerenciasControl()
        );
    }

    private function describirControl(array $d,bool $detalle=true): string
    {
        $t='Control #'.($d['id']??'');

        if(!empty($d['incubadora'])){
            $t.=' — incubadora: '.$this->incubadora($d['incubadora']);
        }

        if(!empty($d['tipo']['nombre'])){
            $t.=' — tipo: '.$d['tipo']['nombre'];
        }elseif(!empty($d['tipo']['clave'])){
            $t.=' — tipo: '.$d['tipo']['clave'];
        }

        if(!empty($d['modo']['nombre'])){
            $t.=' — modo: '.$d['modo']['nombre'];
        }elseif(!empty($d['modo']['clave'])){
            $t.=' — modo: '.$d['modo']['clave'];
        }

        if(array_key_exists('valor_aplicado',$d)&&$d['valor_aplicado']!==null){
            $t.=' — valor aplicado: '.$this->numero($d['valor_aplicado'],2);
        }

        if(!empty($d['fecha_hora'])){
            $t.=' — fecha: '.$this->fecha($d['fecha_hora']);
        }

        if($detalle&&!empty($d['usuario']['nombre'])){
            $t.=' — registrado por: '.$d['usuario']['nombre'];
        }

        if($detalle&&!empty($d['observaciones'])){
            $t.='. Observaciones: '.trim((string)$d['observaciones']);
        }

        return $this->conPunto($t);
    }

    private function sugerenciasControl(): array
    {
        return [
            '¿Cuál fue el último control de esa incubadora?',
            'Muéstrame los controles manuales de esa incubadora',
            '¿Cuál fue el valor aplicado del último control de esa incubadora?'
        ];
    }

    private function conteoPosiciones(array $r): array
    {
        $total=(int)($r['total']??0);

        return $this->respuesta(
            "Hay {$total} "
            .($total===1?'posición registrada':'posiciones registradas')
            .$this->sufijoAlcancePeriodo($r)
            .'.',
            'posicion_conteo',
            $this->sugerenciasPosicion()
        );
    }

    private function listaPosiciones(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        if(isset($items['id'])){
            $items=[$items];
        }

        $lineas=[];

        foreach($items as $i=>$item){
            $lineas[]=
                ($i+1)
                .'. '
                .$this->describirPosicion($item);
        }

        $total=(int)($r['total']??count($items));

        $texto=
            "Encontré {$total} "
            .($total===1?'posición':'posiciones')
            .$this->sufijoAlcancePeriodo($r)
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'posicion_lista',
            $this->sugerenciasPosicion()
        );
    }

    private function detallePosicion(array $r): array
    {
        $d=$r['datos']??[];

        if(empty($d)) return $this->sinDatos($r);

        return $this->respuesta(
            $this->describirPosicion(
                $d,
                true
            ),
            'posicion_detalle',
            $this->sugerenciasPosicion()
        );
    }

    private function describirPosicion(array $d,bool $detalle=true): string
    {
        $t=
            'Posición '
            .($d['numero']??'#'.($d['id']??''));

        if(!empty($d['incubadora'])){
            $t.=
                ' — incubadora: '
                .$this->incubadora($d['incubadora']);
        }

        $total=(int)($d['total_lotes']??count($d['lotes']??[]));

        $t.=
            ' — '
            .$total
            .' '
            .($total===1?'lote asociado':'lotes asociados');

        $lotes=$d['lotes']??[];

        if(!empty($lotes)){
            $nombres=[];

            foreach($lotes as $lote){
                $nombre=$lote['codigo']??'#'.($lote['id']??'');

                if(!empty($lote['estado']['nombre'])){
                    $nombre.=' ('.$lote['estado']['nombre'].')';
                }

                $nombres[]=$nombre;
            }

            $t.=': '.implode(', ',$nombres);
        }

        if($detalle&&!empty($d['descripcion'])){
            $t.='. Descripción: '.trim((string)$d['descripcion']);
        }

        return $this->conPunto($t);
    }

    private function sugerenciasPosicion(): array
    {
        return [
            '¿Cuántas posiciones tiene esa incubadora?',
            'Muéstrame la posición 2 de esa incubadora',
            'Muéstrame los lotes de esa incubadora'
        ];
    }

    private function conteoCatalogo(array $r): array
    {
        $total=(int)($r['total']??0);
        $nombre=$r['nombre_catalogo']??'elementos del catálogo';

        return $this->respuesta(
            "Hay {$total} "
            .$nombre
            .' registrados.',
            'catalogo_conteo',
            $this->sugerenciasCatalogo()
        );
    }

    private function listaCatalogo(array $r): array
    {
        $items=$r['datos']??[];

        if(empty($items)) return $this->sinDatos($r);

        $nombre=$r['nombre_catalogo']??'elementos del catálogo';
        $lineas=[];

        foreach($items as $i=>$item){
            $linea=
                ($i+1)
                .'. '
                .($item['nombre']??'Sin nombre');

            if(!empty($item['clave'])){
                $linea.=' ('.$item['clave'].')';
            }

            if(array_key_exists('activo',$item)){
                $linea.=$item['activo']
                    ?' — activo'
                    :' — inactivo';
            }

            if(!empty($item['descripcion'])){
                $linea.=' — '.trim((string)$item['descripcion']);
            }

            $lineas[]=$this->conPunto($linea);
        }

        $total=(int)($r['total']??count($items));

        $texto=
            'Catálogo de '
            .$nombre
            .":\n\n"
            .implode("\n",$lineas);

        if($total>count($items)){
            $texto.="\n\nSe muestran ".count($items)." de {$total} resultados.";
        }

        return $this->respuesta(
            $texto,
            'catalogo_lista',
            $this->sugerenciasCatalogo()
        );
    }

    private function sugerenciasCatalogo(): array
    {
        return [
            'Muéstrame los estados de incubadora',
            '¿Qué estados de lote existen?',
            'Muéstrame las etapas de desarrollo',
            '¿Qué tipos de alerta existen?'
        ];
    }

    private function resumenSistema(array $r): array
    {
        $t=$r['totales']??[];

        $incubadoras=(int)($t['incubadoras']??0);
        $lotes=(int)($t['lotes_activos']??0);
        $frascos=(int)($t['frascos']??0);
        $alertas=(int)($t['alertas_activas']??0);

        $alcance=($r['alcance']??null)==='global'
            ?' en MicroSeed Control'
            :' dentro de tus asignaciones vigentes';

        $texto=
            'Resumen actual'
            .$alcance
            .":\n\n"
            .'• '.$incubadoras.' '.($incubadoras===1?'incubadora':'incubadoras').".\n"
            .'• '.$lotes.' '.($lotes===1?'lote activo o en seguimiento':'lotes activos o en seguimiento').".\n"
            .'• '.$frascos.' '.($frascos===1?'frasco':'frascos').".\n"
            .'• '.$alertas.' '.($alertas===1?'alerta activa':'alertas activas').' (pendientes o atendidas).';

        $lectura=$r['ultima_lectura']??null;

        if($lectura){
            $texto.=
                "\n• Última lectura "
                .$this->incubadora($lectura['incubadora']??[])
                .': '
                .$this->valorUnidad($lectura['temperatura']??null,'°C',1)
                .' de temperatura y '
                .$this->valorUnidad($lectura['humedad']??null,'%',1)
                .' de humedad relativa'
                .' — '
                .$this->fecha($lectura['fecha_hora']??null)
                .'.';
        }else{
            $periodo=$r['periodo']['descripcion']??null;

            $texto.="\n• No hay lecturas";

            if(
                !empty($periodo)
                &&($r['periodo']['clave']??null)!==null
            ){
                $texto.=' durante '.$periodo;
            }

            $texto.='.';
        }

        if($alertas>0){
            $texto.=
                "\n\nHay alertas abiertas registradas que requieren revisión. "
                .'Su existencia no confirma que la condición física que las originó continúe presente actualmente.';
        }

        if(($r['periodo']['clave']??null)!==null){
            $nuevas=(int)($r['alertas_periodo']??0);
            $descripcion=$r['periodo']['descripcion']??'el periodo consultado';

            $texto.=
                "\n\nDurante "
                .$descripcion
                .' se registraron '
                .$nuevas
                .' '
                .($nuevas===1?'alerta':'alertas')
                .'. Los conteos de incubadoras, lotes, frascos y alertas abiertas representan el estado visible actual y no únicamente los registros creados durante ese periodo.';
        }

        return $this->respuesta(
            $texto,
            'resumen_sistema',
            [
                'Alertas activas',
                'Estado de incubadoras',
                'Dame la última lectura',
                'Lotes activos'
            ]
        );
    }

    private function conocimientoConceptual(array $r): array
    {
        $titulo=trim((string)($r['titulo']??''));
        $contenido=trim((string)($r['respuesta']??''));

        if($contenido===''){
            return $this->respuesta(
                'No encontré una explicación disponible para ese concepto.',
                'conocimiento_conceptual',
                $this->sugerenciasConocimiento()
            );
        }

        $texto=$titulo!==''
            ?$titulo.":\n\n".$contenido
            :$contenido;

        return $this->respuesta(
            $texto,
            'conocimiento_conceptual',
            $this->sugerenciasConocimiento()
        );
    }

    private function sugerenciasConocimiento(): array
    {
        return [
            '¿Qué es el microclima?',
            '¿Para qué sirve el nebulizador?',
            '¿Cómo funciona el modo automático?',
            '¿Para qué sirve la ventilación?'
        ];
    }

    private function ambigua(array $r): array
    {
        $a=$r['ambiguas']??[];
        $tipo=array_key_first($a);

        if(!$tipo){
            return $this->respuesta(
                'Encontré varias coincidencias. Indícame cuál deseas consultar.',
                'ambiguo'
            );
        }

        $lineas=[];
        $sugerencias=[];

        foreach($a[$tipo] as $i=>$item){
            $nombre=$this->nombreEntidad($tipo,$item);
            $lineas[]=($i+1).'. '.$nombre;
            $sugerencias[]=$nombre;
        }

        return $this->respuesta(
            "Encontré varias coincidencias:\n\n"
            .implode("\n",$lineas)
            ."\n\nIndícame cuál deseas consultar.",
            'ambiguo',
            $sugerencias
        );
    }

    private function noEncontrada(array $r): array
    {
        $faltantes=$r['no_encontradas']??[];
        $tipo=array_key_first($faltantes);
        $ref=$tipo?$faltantes[$tipo]??[]:[];
        $valor=$ref['codigo']??$ref['id']??$ref['numero_frasco']??null;

        $t='No encontré '.$this->tipoEntidad($tipo);

        if($valor!==null){
            $t.=' que coincida con "'.$valor.'"';
        }

        $t.=' dentro de la información que tienes permitido consultar.';

        return $this->respuesta(
            $t,
            'no_encontrado'
        );
    }

    private function error(array $r): array
    {
        return match($r['tipo']??null){
            'variable_requerida',
            'requiere_aclaracion'=>$this->respuesta(
                $r['mensaje']??'Necesito más información para completar la consulta.',
                'requiere_aclaracion',
                $r['sugerencias']??[
                'Promedio de temperatura',
                'Promedio de humedad',
                'Temperatura máxima',
                'Humedad máxima'
            ]
            ),

            'sin_datos'=>!empty($r['mensaje'])
                ?$this->respuesta(
                    $r['mensaje'],
                    'sin_datos'
                )
                :$this->sinDatos($r),

            'acceso_denegado'=>$this->respuesta(
                $r['mensaje']??'No tienes permiso para realizar esta consulta.',
                'acceso_denegado'
            ),

            'concepto_no_encontrado'=>$this->respuesta(
                $r['mensaje']??'No encontré una explicación precisa para ese concepto.',
                'concepto_no_encontrado',
                $this->sugerenciasConocimiento()
            ),

            default=>$this->respuesta(
                $r['mensaje']??'No pude completar esa consulta.',
                $r['tipo']??'error'
            )
        };
    }

    private function sinDatos(array $r=[]): array
    {
        $recurso=$r['recurso']??null;

        $nombre=
            $recurso
                ?$this->nombreRecurso($recurso,2)
                :'datos';

        $t='No encontré '.$nombre;

        if(!empty($r['alcance'])){
            $t.=' para '.$this->alcance($r['alcance']);
        }

        if(
            !empty($r['periodo']['descripcion'])
            &&($r['periodo']['clave']??null)!==null
        ){
            $t.=' durante '.$r['periodo']['descripcion'];
        }

        return $this->respuesta(
            $this->conPunto($t),
            'sin_datos'
        );
    }

    private function respuesta(string $texto,string $tipo,array $sugerencias=[]): array
    {
        return [
            'respuesta'=>$texto,
            'tipo'=>$tipo,
            'sugerencias'=>$sugerencias?:$this->sugerenciasGenerales()
        ];
    }

    private function frescura(?string $estado,int $minutos): string
    {
        return match($estado){
            'actual'=>'La lectura se considera actual.',
            'reciente'=>"La lectura tiene aproximadamente {$minutos} minutos de antigüedad y se considera reciente.",
            'desactualizada'=>"La lectura tiene aproximadamente {$minutos} minutos de antigüedad, por lo que no debe considerarse una medición actual.",
            'inconsistente'=>'La fecha de la lectura es posterior a la hora actual del sistema y debe revisarse antes de interpretarla.',
            default=>''
        };
    }

    private function alcanceMicroclima(array $a): string
    {
        if(($a['tipo']??null)==='lote'){
            $l=$this->codigoLote($a);
            $i=$a['incubadora']??null;

            if(
                is_array($i)
                &&(
                    !empty($i['codigo'])
                    ||!empty($i['nombre'])
                )
            ){
                $t=
                    'la incubadora '
                    .$this->incubadora($i)
                    .' asociada';

                if($l) $t.=' al lote '.$l;

                return $t;
            }
        }

        return $this->alcance($a);
    }

    private function alcance(array $a): string
    {
        $tipo=$a['tipo']??null;

        $t=match($tipo){
            'incubadora'=>$this->incubadora($a),

            'lote'=>$this->codigoLote($a)
                ?'el lote '.$this->codigoLote($a)
                :'el lote consultado',

            'especie'=>!empty($a['nombre_comun'])
                ?'la especie '.$a['nombre_comun']
                :'la especie consultada',

            'frasco'=>isset($a['numero'])
                ?'el frasco '.$a['numero']
                :'el frasco consultado',

            'usuario'=>!empty($a['nombre'])
                ?'el usuario '.$a['nombre']
                :'el usuario consultado',

            'usuario_propio'=>'tu propia cuenta',

            default=>'la información que tienes permitido consultar'
        };

        if(
            $tipo!=='lote'
            &&!empty($a['lote']['codigo'])
        ){
            $t.=' del lote '.$a['lote']['codigo'];
        }

        if(
            $tipo!=='especie'
            &&!empty($a['especie']['nombre_comun'])
        ){
            $t.=' de la especie '.$a['especie']['nombre_comun'];
        }

        if(
            $tipo!=='incubadora'
            &&!empty($a['incubadora'])
            &&is_array($a['incubadora'])
        ){
            $t.=' en '.$this->incubadora($a['incubadora']);
        }

        if(
            $tipo!=='usuario'
            &&!empty($a['usuario'])
            &&is_array($a['usuario'])
        ){
            $t.=
                ' del usuario '
                .($a['usuario']['nombre']??'#'.$a['usuario']['id']);
        }

        return $t;
    }

    private function alcanceAsignacion(array $a): string
    {
        $tipo=$a['tipo']??null;

        if($tipo==='global_autorizado'){
            return ' en MicroSeed Control';
        }

        if($tipo==='usuario_propio'){
            return ' de tu cuenta';
        }

        if($tipo==='usuario'){
            return ' del usuario '.($a['nombre']??'#'.$a['id']);
        }

        if($tipo==='incubadora'){
            return ' de '.$this->incubadora($a);
        }

        $p=[];

        if(!empty($a['usuario'])){
            $p[]=
                'del usuario '
                .($a['usuario']['nombre']??'#'.$a['usuario']['id']);
        }

        if(!empty($a['incubadora'])){
            $p[]='de '.$this->incubadora($a['incubadora']);
        }

        return $p
            ?' '.implode(' ',$p)
            :'';
    }

    private function contextoAlcance(array $a): string
    {
        return ($a['tipo']??null)==='global_autorizado'
            ?'registrados dentro de '.$this->alcance($a)
            :'para '.$this->alcance($a);
    }

    private function sufijoAlcancePeriodo(array $r): string
    {
        $t='';

        if(!empty($r['alcance'])){
            $a=$r['alcance'];

            $t.=
                ($a['tipo']??null)==='global_autorizado'
                    ?' dentro de '.$this->alcance($a)
                    :' para '.$this->alcance($a);
        }

        return $t.$this->sufijoPeriodo($r['periodo']??[]);
    }

    private function sufijoPeriodo(array $p): string
    {
        return empty($p['descripcion'])
        ||($p['clave']??null)===null
            ?''
            :' durante '.$p['descripcion'];
    }

    private function descripcionFiltrosAlertas(array $f): string
    {
        $t='';
        $estado=$f['estado_alerta']??null;

        if($estado){
            $t.=match($estado){
                'pendiente'=>' pendientes',
                'atendida'=>' atendidas',
                'resuelta'=>' resueltas',
                default=>''
            };
        }elseif(($f['estado_general']??null)==='activo'){
            $t.=' activas';
        }

        $nivel=$f['nivel_alerta']??null;

        if($nivel){
            $t.=
                ' de nivel '
                .match($nivel){
                    'critica'=>'crítico',
                    'alta'=>'alto',
                    'media'=>'medio',
                    'baja'=>'bajo',
                    default=>$nivel
                };
        }

        return $t;
    }

    private function sufijoUsuarioFiltros(array $f): string
    {
        $p=[];

        if(($f['rol_usuario']??null)==='encargado'){
            $p[]='con rol encargado';
        }

        if(array_key_exists('usuario_activo',$f)){
            $p[]=$f['usuario_activo']
                ?'activos'
                :'inactivos';
        }

        return $p
            ?' '.implode(' y ',$p)
            :'';
    }

    private function incubadora(array $i): string
    {
        $codigo=$i['codigo']??null;
        $nombre=$i['nombre']??null;

        return $codigo&&$nombre
            ?"{$codigo} — {$nombre}"
            :($codigo??$nombre??'la incubadora');
    }

    private function codigoLote(array $a): ?string
    {
        return $a['codigo']
            ??$a['lote']['codigo']
            ??null;
    }

    private function nombreEntidad(string $tipo,array $i): string
    {
        return match($tipo){
            'incubadora'=>$this->incubadora($i),
            'lote'=>$i['codigo']??'Lote',
            'especie'=>$i['nombre_comun']??$i['nombre_cientifico']??'Especie',
            'usuario'=>$i['nombre']??'Usuario',

            'frasco'=>'Frasco '
                .($i['numero']??'')
                .(!empty($i['lote']['codigo'])
                    ?' del lote '.$i['lote']['codigo']
                    :''),

            default=>'Registro'
        };
    }

    private function tipoEntidad(?string $tipo): string
    {
        return match($tipo){
            'incubadora'=>'una incubadora',
            'lote'=>'un lote',
            'frasco'=>'un frasco',
            'especie'=>'una especie',
            'usuario'=>'un usuario',
            default=>'un registro'
        };
    }

    private function nombreRecurso(string $r,int $total): string
    {
        return match($r){
            'lotes'=>$total===1?'lote':'lotes',
            'frascos'=>$total===1?'frasco':'frascos',
            'especies'=>$total===1?'especie':'especies',
            'incubadoras'=>$total===1?'incubadora':'incubadoras',
            'usuarios'=>$total===1?'usuario':'usuarios',
            'asignaciones'=>$total===1?'asignación':'asignaciones',
            'condiciones_optimas'=>$total===1?'condición óptima':'condiciones óptimas',
            'seguimientos'=>$total===1?'seguimiento':'seguimientos',
            'evidencias'=>$total===1?'evidencia de lote':'evidencias de lote',
            'controles'=>$total===1?'registro de control':'registros de control',
            'posiciones'=>$total===1?'posición':'posiciones',
            'catalogos'=>$total===1?'elemento de catálogo':'elementos de catálogo',
            'conocimiento'=>$total===1?'concepto':'conceptos',
            default=>$total===1?'registro':'registros'
        };
    }

    private function numero(mixed $v,int $d=1): string
    {
        return $v===null||$v===''
            ?'sin dato'
            :number_format((float)$v,$d,'.','');
    }

    private function valorUnidad(mixed $v,string $u,int $d=1): string
    {
        return $v===null||$v===''
            ?'sin dato'
            :$this->numero($v,$d).($u!==''?' '.$u:'');
    }

    private function fecha(mixed $f): string
    {
        return !$f
            ?'sin fecha'
            :Carbon::parse(
                $f,
                $this->timezone
            )
                ->timezone($this->timezone)
                ->format('d/m/Y H:i');
    }

    private function fechaDia(mixed $f): string
    {
        return !$f
            ?'sin fecha'
            :Carbon::parse(
                $f,
                $this->timezone
            )
                ->timezone($this->timezone)
                ->format('d/m/Y');
    }

    private function conPunto(string $t): string
    {
        $t=trim($t);

        if($t==='') return '';

        if(preg_match('/[.!?]$/u',$t)){
            return $t;
        }

        return rtrim(
                $t,
                " \t\n\r\0\x0B."
            ).'.';
    }

    private function sugerenciasRecurso(string $r): array
    {
        return match($r){
            'lotes'=>[
                '¿Cuántos lotes hay?',
                'Dame los últimos 5 lotes',
                '¿Cuántos frascos hay?'
            ],

            'frascos'=>[
                '¿Cuántos frascos hay?',
                'Dame los últimos 10 frascos',
                '¿Cuántos lotes hay?'
            ],

            'especies'=>[
                '¿Cuántas especies hay?',
                'Muéstrame las especies',
                '¿Cuántos lotes hay?'
            ],

            'incubadoras'=>[
                '¿Cuántas incubadoras hay?',
                'Muéstrame las incubadoras activas',
                'Dame la última lectura'
            ],

            default=>$this->sugerenciasGenerales()
        };
    }

    private function sugerenciasGenerales(): array
    {
        return [
            'Dame la última lectura',
            'Dame las últimas 10 lecturas',
            '¿Cuántas alertas están pendientes?',
            '¿Cuántos lotes hay?'
        ];
    }
}
