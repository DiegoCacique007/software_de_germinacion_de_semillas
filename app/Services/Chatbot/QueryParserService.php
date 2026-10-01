<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

class QueryParserService
{
    private const LIMITE_DEFECTO=5;
    private const LIMITE_MAXIMO=50;

    public function parse(string $mensaje): array
    {
        $original=trim($mensaje);
        $normalizado=$this->normalizar($original);
        $limite=$this->detectarLimite($normalizado);
        $recurso=$this->detectarRecurso($normalizado);
        $operacion=$this->detectarOperacion($normalizado,$recurso,$limite);
        $variable=$this->detectarVariable($normalizado);
        $periodo=$this->detectarPeriodo($normalizado);
        $orden=$this->detectarOrden($normalizado);
        $agrupacion=$this->detectarAgrupacion($normalizado,$operacion);
        $filtros=$this->detectarFiltros($normalizado,$recurso);
        $referenciaContextual=$this->detectarReferenciaContextual($normalizado);
        $requiereContexto=$referenciaContextual!==null;
        $entidades=$this->detectarReferencias($normalizado,$recurso);
        $requiereAclaracion=$this->requiereAclaracion($recurso,$operacion,$variable);
        $confianza=$this->calcularConfianza($recurso,$operacion,$variable,$entidades,$requiereContexto);

        return [
            'mensaje_original'=>$original,
            'mensaje_normalizado'=>$normalizado,
            'recurso'=>$recurso,
            'operacion'=>$operacion,
            'variable'=>$variable,
            'periodo'=>$periodo,
            'limite'=>$limite,
            'orden'=>$orden,
            'agrupacion'=>$agrupacion,
            'filtros'=>$filtros,
            'entidades'=>$entidades,
            'referencia_contextual'=>$referenciaContextual,
            'requiere_contexto'=>$requiereContexto,
            'requiere_aclaracion'=>$requiereAclaracion,
            'confianza'=>$confianza
        ];
    }

    private function normalizar(string $mensaje): string
    {
        $mensaje=trim(mb_strtolower($mensaje));
        $mensaje=preg_replace('/[^\p{L}\p{N}\s\-_\.#%]+/u',' ',$mensaje);

        return trim(preg_replace('/\s+/u',' ',$mensaje));
    }

    private function ascii(string $texto): string
    {
        return mb_strtolower(Str::ascii($texto));
    }

    private function detectarRecurso(string $mensaje): ?string
    {
        $texto=$this->ascii($mensaje);

        // Datos retirados: no reinterpretarlos como métricas de seguimiento.
        if(preg_match('/\b(?:carbono|nitrogeno|fosforo|estratificacion)\b|\bregistros?\s+biologicos?\b/u',$texto)) return null;

        if($this->esConversacionGeneral($texto)) return 'conversacion';

        if(
            preg_match('/\b(?:resumen|resumen\s+del\s+sistema|resumen\s+de\s+microseed)\b/u',$texto)
            ||preg_match('/\bcomo\s+esta\s+(?:el\s+)?sistema\b/u',$texto)
            ||preg_match('/\bque\s+(?:esta\s+pasando|paso|requiere\s+atencion)\b/u',$texto)
            ||preg_match('/\bhay\s+algo(?:\s+importante)?\b/u',$texto)
            ||preg_match('/\balgo\s+importante\b/u',$texto)
        ) return 'resumen';

        if($this->detectarCatalogo($texto)!==null) return 'catalogos';

        if($texto==='alerta'||$this->esPreguntaConceptual($texto)) return 'conocimiento';

        if(preg_match('/\bcomo\s+esta\s+(?:(?:la\s+)?incubadora\s+)?inc-[a-z0-9-]+\b/u',$texto)) return 'lecturas';
        if(preg_match('/\bdetalle\s+(?:de\s+)?(?:(?:la\s+)?incubadora\s+)?inc-[a-z0-9-]+\b/u',$texto)) return 'incubadoras';
        if(preg_match('/\bincubadoras\s+con\s+alertas\b/u',$texto)) return 'incubadoras';
        if(preg_match('/\bsin\s+seguimiento\s+reciente\b/u',$texto)){
            if(preg_match('/\bfrascos\b/u',$texto)) return 'frascos';
            if(preg_match('/\blotes\b/u',$texto)) return 'lotes';
        }

        if(
            preg_match('/\b(?:quien|quienes)\s+(?:esta|estan|es|son)\s+(?:el\s+|la\s+|los\s+|las\s+)?encargad(?:o|a|os|as)\s+de\s+(?:la\s+|el\s+)?incubadora\b/u',$texto)
            ||preg_match('/\b(?:quien|quienes)\s+(?:tiene|tienen)\s+asignad(?:a|as|o|os)\s+(?:la\s+|el\s+)?incubadora\b/u',$texto)
            ||preg_match('/\bencargad(?:o|a|os|as)\s+de\s+(?:la\s+|el\s+)?incubadora\b/u',$texto)
            ||preg_match('/\b(?:que|cual|cuales)\s+incubadoras?\s+(?:tiene|tienen)\s+asignad(?:a|as|o|os)\b/u',$texto)
            ||preg_match('/\bincubadoras?\s+(?:que\s+)?(?:tiene|tienen)\s+asignad(?:a|as|o|os)\b/u',$texto)
            ||preg_match('/\bincubadoras?\s+asignad(?:a|as|o|os)\b/u',$texto)
            ||preg_match('/\b(?:asignacion|asignaciones)\b/u',$texto)
            ||preg_match('/\bmis\s+asignaciones\b/u',$texto)
            ||preg_match('/\bincubadoras?\s+(?:tiene|tienen)\s+\p{L}/u',$texto)
            ||preg_match('/\bquien\s+tiene\s+asignada\s+inc-[a-z0-9-]+\b/u',$texto)
        ) return 'asignaciones';

        if(preg_match('/\balerta(?:s)?\b/u',$texto)) return 'alertas';

        if(
            str_contains($texto,'condiciones optimas')
            ||str_contains($texto,'condicion optima')
            ||preg_match('/\btemperatura\s+(?:optima|ideal|recomendada)\b/u',$texto)
            ||preg_match('/\bhumedad(?:\s+relativa)?\s+(?:optima|ideal|recomendada)\b/u',$texto)
        ) return 'condiciones_optimas';

        if(
            preg_match('/\b(?:control|controles|actuador|actuadores|valor\s+aplicado)\b/u',$texto)
            ||preg_match('/\bmodo\s+(?:de\s+control|manual|automatico)\b/u',$texto)
        ) return 'controles';

        if(preg_match('/\b(?:lectura|lecturas|temperatura|humedad|microclima)\b/u',$texto)) return 'lecturas';

        if(
            preg_match('/\bevidencia(?:s)?\b/u',$texto)
            ||preg_match('/\b(?:foto|fotos|fotografia|fotografias|imagen|imagenes)\b.*\blote\b/u',$texto)
        ) return 'evidencias';

        if(
            preg_match('/\b(?:seguimiento|seguimientos)\b/u',$texto)
            ||preg_match('/\b(?:porcentaje|tasa)\s+de\s+germinacion\b/u',$texto)
            ||preg_match('/\bsemillas?\s+germinad(?:a|as|o|os)\b/u',$texto)
            ||preg_match('/\bsemillas?\s+germinaron\b/u',$texto)
            ||preg_match('/\b(?:altura\s+promedio|etapa\s+actual|etapa\s+de\s+desarrollo)\b/u',$texto)
            ||(preg_match('/\bgerminacion\b/u',$texto)&&preg_match('/\b(?:lote|frasco)\b/u',$texto))
        ) return 'seguimientos';

        if(preg_match('/\bposicion(?:es)?\b/u',$texto)) return 'posiciones';

        if(
            preg_match('/\bfrascos\b/u',$texto)
            ||preg_match('/\b(?:cuantos|cuantas|muestra|muestrame|lista|listar|dame)\b.*\bfrasco\b/u',$texto)
        ) return 'frascos';

        if(preg_match('/\blotes\b/u',$texto)) return 'lotes';

        if(
            preg_match('/\bespecies\b/u',$texto)
            ||preg_match('/\b(?:que|cual|cuales)\s+especie\b/u',$texto)
        ) return 'especies';

        if(preg_match('/\b(?:usuarios|encargados)\b/u',$texto)) return 'usuarios';

        if(
            preg_match('/\bincubadoras\b/u',$texto)
            ||preg_match('/\b(?:que|cual|cuales)\s+incubadora\b/u',$texto)
        ) return 'incubadoras';

        if(preg_match('/\b(?:reporte|reportes|informe|informes)\b/u',$texto)) return 'reportes';
        if(preg_match('/\bfrasco\b/u',$texto)) return 'frascos';
        if(preg_match('/\blote\b/u',$texto)) return 'lotes';
        if(preg_match('/\bespecie\b/u',$texto)) return 'especies';
        if(preg_match('/\b(?:usuario|encargado)\b/u',$texto)) return 'usuarios';
        if(preg_match('/\bincubadora\b/u',$texto)) return 'incubadoras';

        return 'conversacion';
    }

    private function esConversacionGeneral(string $texto): bool
    {
        if(preg_match('/^(?:hola|saludo|buenos dias|buenas tardes|buenas noches|hey)(?: microseed)?$/u',$texto)) return true;

        if(preg_match('/^(?:adios|despedida|hasta luego|nos vemos|hasta pronto)$/u',$texto)) return true;

        if(preg_match('/^(?:muchas )?gracias(?: por .*)?$/u',$texto)) return true;

        if(preg_match('/^(?:ayuda|opciones)$/u',$texto)) return true;

        if(
            preg_match('/\bque (?:puedes hacer|sabes hacer|puedo preguntar)\b/u',$texto)
            ||preg_match('/\bcomo me puedes ayudar\b/u',$texto)
        ) return true;

        if(
            preg_match('/\breportes?\b/u',$texto)
            &&preg_match('/\b(?:que|cual|cuales|existen|hay|disponibles|puedo ver|puedo consultar)\b/u',$texto)
        ) return true;

        return false;
    }

    private function esPreguntaConceptual(string $texto): bool
    {
        return preg_match(
                '/\b(?:que\s+(?:es|son|hace|hacen|significa|significan)|para\s+que\s+(?:sirve|sirven|se\s+usa|se\s+usan)|como\s+(?:funciona|funcionan)|explica|explicame|explicacion|define|definicion|funcion\s+(?:de|del|de\s+la)|que\s+pasa\s+si)\b/u',
                $texto
            )===1;
    }

    private function detectarCatalogo(string $mensaje): ?string
    {
        $texto=$this->ascii($mensaje);

        if(preg_match('/\bestados\s+(?:de\s+|del\s+)?incubadoras?\b/u',$texto)) return 'estados_incubadora';
        if(preg_match('/\bestados\s+(?:de\s+|del\s+)?lotes?\b/u',$texto)) return 'estados_lote';
        if(preg_match('/\bestados\s+(?:de\s+|del\s+)?frascos?\b/u',$texto)) return 'estados_frasco';
        if(preg_match('/\betapas\s+de\s+desarrollo\b/u',$texto)) return 'etapas_desarrollo';
        if(preg_match('/\btipos\s+de\s+alerta\b/u',$texto)) return 'tipos_alerta';
        if(preg_match('/\bniveles\s+de\s+alerta\b/u',$texto)) return 'niveles_alerta';
        if(preg_match('/\bestados\s+de\s+alerta\b/u',$texto)) return 'estados_alerta';
        if(preg_match('/\btipos\s+de\s+control\b/u',$texto)) return 'tipos_control';
        if(preg_match('/\bmodos\s+de\s+control\b/u',$texto)) return 'modos_control';
        if(preg_match('/\broles\b/u',$texto)) return 'roles';

        return null;
    }

    private function detectarOperacion(string $mensaje,?string $recurso,int $limite): ?string
    {
        $texto=$this->ascii($mensaje);

        if(
            preg_match('/\b(?:elimina|eliminar|borra|borrar|crea|crear|agrega|agregar|registra|registrar|modifica|modificar|edita|editar|actualiza|actualizar|guarda|guardar|genera|generar|activar|desactivar|enciende|encender|apaga|apagar|abre|abrir|cierra|cerrar|asigna|asignar|desasigna|desasignar|configura|configurar)\b/u',$texto)
            ||preg_match('/^(?:por favor\s+)?(?:activa|desactiva)\b/u',$texto)
            ||preg_match('/\b(?:cambia|cambiar)\b/u',$texto)
            ||preg_match('/\b(?:drop\s+(?:table|database)|truncate\s+(?:table\s+)?\w+|delete\s+from)\b/u',$texto)
        ) return 'accion_no_permitida';

        if($recurso==='lecturas'&&preg_match('/\bcomo\s+esta\b/u',$texto)) return 'estado';
        if($recurso==='incubadoras'&&preg_match('/\bdetalle\b/u',$texto)) return 'estado';
        if(in_array($recurso,['lotes','frascos'],true)&&preg_match('/\bsin\s+seguimiento\s+reciente\b/u',$texto)) return 'listar';
        if($recurso==='lecturas'&&preg_match('/^(?:que\s+)?(?:temperatura|humedad)\s+(?:hay|actual)\b/u',$texto)) return 'ultimo';

        if($recurso==='conversacion'){
            return $this->detectarOperacionConversacional($texto);
        }

        if($recurso==='resumen') return 'resumen';
        if($recurso==='conocimiento') return 'explicar';

        if(preg_match('/\b(?:que es|que significa|para que sirve|como funciona|explica|explicame|define|definicion)\b/u',$texto)) return 'explicar';

        if(preg_match('/\b(?:promedio|media|media aritmetica)\b/u',$texto)&&$recurso==='lecturas') return 'promedio';
        if(preg_match('/\b(?:maximo|maxima|mayor valor|mas alta|mas alto)\b/u',$texto)&&$recurso==='lecturas') return 'maximo';
        if(preg_match('/\b(?:minimo|minima|menor valor|mas baja|mas bajo)\b/u',$texto)&&$recurso==='lecturas') return 'minimo';

        if(
            preg_match('/\b(?:ranking|top)\b/u',$texto)
            ||preg_match('/\bincubadora\b.*\bmas alertas\b/u',$texto)
            ||preg_match('/\b(?:mayor cantidad|mas cantidad)\b.*\balertas\b/u',$texto)
            ||preg_match('/\b(?:cual|que)\s+incubadora\s+tiene\s+mas\s+alertas\b/u',$texto)
        ) return 'ranking';

        if(
            $recurso==='seguimientos'
            &&preg_match('/\b(?:(?:porcentaje|tasa)\s+de\s+germinacion|semillas?\s+germinad(?:a|as|o|os)|semillas?\s+germinaron|altura(?:\s+promedio)?|etapa\s+actual|etapa\s+de\s+desarrollo)\b/u',$texto)
        ) return 'ultimo';

        if(
            $recurso==='controles'
            &&preg_match('/\b(?:modo(?:\s+de\s+control)?|tipo\s+de\s+control|valor\s+aplicado)\b/u',$texto)
        ) return 'ultimo';

        if(preg_match('/\b(?:cuantos|cuantas|cantidad|total)\b/u',$texto)||str_contains($texto,'numero de')) return 'contar';

        if(preg_match('/\b(?:primera|primer)\s+(?:lectura|registro)\b/u',$texto)) return 'primero';

        if(
            preg_match('/\b(?:ultimas|ultimos)\b/u',$texto)
            &&(
                $limite>1
                ||preg_match('/\b(?:lecturas|alertas|lotes|frascos|especies|incubadoras|usuarios|encargados|asignaciones|seguimientos|evidencias|registros|controles|posiciones)\b/u',$texto)
            )
        ) return 'listar';

        if(preg_match('/\b(?:ultima|ultimo|mas reciente|reciente)\b/u',$texto)) return 'ultimo';
        if(preg_match('/\b(?:estado|situacion)\b/u',$texto)) return 'estado';
        if(preg_match('/\b(?:muestra|muestrame|mostrar|lista|listar|dame|ver|consulta|consultar)\b/u',$texto)) return 'listar';
        if(preg_match('/\b(?:quien|quienes|que|cual|cuales)\b/u',$texto)&&$recurso!==null) return 'listar';
        if($recurso!==null) return 'listar';

        return null;
    }

    private function detectarOperacionConversacional(string $texto): string
    {
        if(preg_match('/^(?:hola|saludo|buenos dias|buenas tardes|buenas noches|hey)(?: microseed)?$/u',$texto)) return 'saludo';

        if(preg_match('/^(?:adios|despedida|hasta luego|nos vemos|hasta pronto)$/u',$texto)) return 'despedida';

        if(preg_match('/^(?:muchas )?gracias(?: por .*)?$/u',$texto)) return 'agradecimiento';

        if(preg_match('/^(?:ayuda|opciones)$/u',$texto)) return 'ayuda';

        if(
            preg_match('/\bque (?:puedes hacer|sabes hacer|puedo preguntar)\b/u',$texto)
            ||preg_match('/\bcomo me puedes ayudar\b/u',$texto)
        ) return 'capacidades';

        if(preg_match('/\breportes?\b/u',$texto)) return 'reportes';

        return 'desconocida';
    }

    private function detectarVariable(string $mensaje): ?string
    {
        $texto=$this->ascii($mensaje);

        if(preg_match('/\bmodo(?:\s+de\s+control)?\b/u',$texto)) return 'modo_control';
        if(preg_match('/\btipo\s+de\s+control\b/u',$texto)) return 'tipo_control';
        if(preg_match('/\bvalor\s+aplicado\b/u',$texto)) return 'valor_aplicado';


        if(preg_match('/\b(?:temperatura|temperaturas|grados|termica|termico)\b/u',$texto)) return 'temperatura';
        if(preg_match('/\b(?:humedad|humedad relativa)\b/u',$texto)) return 'humedad';
        if(preg_match('/\b(?:germinacion|porcentaje de germinacion)\b/u',$texto)) return 'germinacion';
        if(preg_match('/\b(?:semillas germinadas|semilla germinada|germinadas|germinaron)\b/u',$texto)) return 'semillas_germinadas';
        if(preg_match('/\b(?:altura|crecimiento)\b/u',$texto)) return 'altura';
        if(preg_match('/\b(?:etapa actual|etapa de desarrollo|etapa)\b/u',$texto)) return 'etapa';

        return null;
    }

    private function detectarPeriodo(string $mensaje): ?string
    {
        $texto=$this->ascii($mensaje);

        if(preg_match('/\b(?:hoy|del dia|este dia)\b/u',$texto)) return 'hoy';
        if(preg_match('/\bayer\b/u',$texto)) return 'ayer';
        if(preg_match('/\b(?:esta semana|semana actual)\b/u',$texto)) return 'esta_semana';
        if(preg_match('/\b(?:semana pasada|ultima semana)\b/u',$texto)) return 'semana_pasada';
        if(preg_match('/\b(?:este mes|mes actual)\b/u',$texto)) return 'este_mes';
        if(preg_match('/\b(?:mes pasado|ultimo mes)\b/u',$texto)) return 'mes_pasado';
        if(preg_match('/\b(?:ultimas|ultimos)\s+24\s+horas\b/u',$texto)) return 'ultimas_24_horas';
        if(preg_match('/\b(?:ultimos|ultimas)\s+7\s+dias\b/u',$texto)) return 'ultimos_7_dias';
        if(preg_match('/\b(?:ultimos|ultimas)\s+30\s+dias\b/u',$texto)) return 'ultimos_30_dias';

        return null;
    }

    private function detectarLimite(string $mensaje): int
    {
        $texto=$this->ascii($mensaje);

        foreach([
                    '/\b(?:ultimos|ultimas|primeros|primeras)\s+(\d{1,3})\b/u',
                    '/\b(?:dame|muestra|muestrame|lista|listar)\s+(?:los\s+|las\s+)?(\d{1,3})\b/u',
                    '/\btop\s+(\d{1,3})\b/u'
                ] as $patron){
            if(preg_match($patron,$texto,$match)){
                return max(1,min((int)$match[1],self::LIMITE_MAXIMO));
            }
        }

        return self::LIMITE_DEFECTO;
    }

    private function detectarOrden(string $mensaje): string
    {
        return preg_match(
            '/\b(?:primeros|primeras|mas antiguos|mas antiguas|ascendente)\b/u',
            $this->ascii($mensaje)
        )?'asc':'desc';
    }

    private function detectarAgrupacion(string $mensaje,?string $operacion): ?string
    {
        if($operacion!=='ranking') return null;

        $texto=$this->ascii($mensaje);

        if(preg_match('/\bincubadora(?:s)?\b/u',$texto)) return 'incubadora';
        if(preg_match('/\blote(?:s)?\b/u',$texto)) return 'lote';
        if(preg_match('/\bespecie(?:s)?\b/u',$texto)) return 'especie';

        return null;
    }

    private function detectarFiltros(string $mensaje,?string $recurso): array
    {
        $texto=$this->ascii($mensaje);
        $filtros=[];

        if(in_array($recurso,['lotes','frascos'],true)&&preg_match('/\bsin\s+seguimiento\s+reciente\b/u',$texto)){
            $filtros['sin_seguimiento_dias']=7;
        }

        if($recurso==='catalogos'){
            $catalogo=$this->detectarCatalogo($texto);

            if($catalogo!==null){
                $filtros['catalogo']=$catalogo;
            }

            if($catalogo==='roles'){
                if(preg_match('/\b(?:inactivo|inactiva|inactivos|inactivas)\b/u',$texto)){
                    $filtros['activo']=false;
                }elseif(preg_match('/\b(?:activo|activa|activos|activas)\b/u',$texto)){
                    $filtros['activo']=true;
                }
            }
        }

        if($recurso==='alertas'){
            if($texto==='me preocupa una alerta') $filtros['estado_general']='activo';
            if(preg_match('/\bpendiente(?:s)?\b/u',$texto)) $filtros['estado_alerta']='pendiente';
            if(preg_match('/\batendida(?:s)?\b/u',$texto)) $filtros['estado_alerta']='atendida';
            if(preg_match('/\bresuelta(?:s)?\b/u',$texto)) $filtros['estado_alerta']='resuelta';

            if(
                preg_match('/\b(?:activa|activas|abierta|abiertas)\b/u',$texto)
                &&!isset($filtros['estado_alerta'])
            ){
                $filtros['estado_general']='activo';
            }

            if(preg_match('/\b(?:critica|criticas|critico|criticos)\b/u',$texto)) $filtros['nivel_alerta']='critica';
            if(preg_match('/\b(?:alta|altas)\b/u',$texto)) $filtros['nivel_alerta']='alta';
            if(preg_match('/\b(?:media|medias)\b/u',$texto)) $filtros['nivel_alerta']='media';
            if(preg_match('/\b(?:baja|bajas)\b/u',$texto)) $filtros['nivel_alerta']='baja';
        }

        if(
            in_array($recurso,['lotes','frascos'],true)
            &&preg_match('/\b(?:activo|activa|activos|activas)\b/u',$texto)
        ){
            $filtros['estado_general']='activo';
        }

        if(
            $recurso==='incubadoras'
            &&preg_match('/\b(?:activa|activas)\b/u',$texto)
        ){
            $filtros['estado_incubadora']='activa';
        }

        if(
            $recurso==='incubadoras'
            &&preg_match('/\bcon\s+alertas\b/u',$texto)
        ){
            $filtros['con_alertas']=true;
        }

        if($recurso==='usuarios'){
            if(preg_match('/\b(?:inactivo|inactiva|inactivos|inactivas)\b/u',$texto)){
                $filtros['usuario_activo']=false;
            }elseif(preg_match('/\b(?:activo|activa|activos|activas)\b/u',$texto)){
                $filtros['usuario_activo']=true;
            }

            if(preg_match('/\bencargad(?:o|a|os|as)\b/u',$texto)){
                $filtros['rol_usuario']='encargado';
            }elseif(preg_match('/\b(?:super[_\s-]?admin|superadministrador|superadministradores)\b/u',$texto)){
                $filtros['rol_usuario']='super_admin';
            }
        }

        if($recurso==='controles'){
            if(preg_match('/\bmanual(?:es)?\b/u',$texto)){
                $filtros['modo_control']='manual';
            }elseif(preg_match('/\bautomatic(?:o|a|os|as)\b/u',$texto)){
                $filtros['modo_control']='automatico';
            }

            if(preg_match('/\b(?:nebulizacion|nebulizador|nebulizadores)\b/u',$texto)){
                $filtros['tipo_control']='nebulizacion';
            }elseif(preg_match('/\bniebla\b/u',$texto)){
                $filtros['tipo_control']='niebla';
            }elseif(preg_match('/\b(?:iluminacion|luz|led)\b/u',$texto)){
                $filtros['tipo_control']='iluminacion';
            }elseif(preg_match('/\btemperatura\b/u',$texto)){
                $filtros['tipo_control']='temperatura';
            }elseif(preg_match('/\bhumedad\b/u',$texto)){
                $filtros['tipo_control']='humedad';
            }
        }

        if(
            $recurso==='posiciones'
            &&preg_match('/\bposicion\s+(?:numero\s+|n[uú]mero\s+|no\.?\s*)?(\d+)\b/u',$texto,$m)
        ){
            $filtros['numero_posicion']=(int)$m[1];
        }

        return $filtros;
    }

    private function detectarReferenciaContextual(string $mensaje): ?string
    {
        $texto=$this->ascii($mensaje);

        foreach([
                    'ese lote','este lote','el lote anterior',
                    'esa incubadora','esta incubadora','la incubadora anterior',
                    'ese frasco','este frasco','el frasco anterior',
                    'esa especie','esta especie','la especie anterior',
                    'ese usuario','este usuario','el usuario anterior',
                    'ese encargado','este encargado','el encargado anterior'
                ] as $referencia){
            if(str_contains($texto,$referencia)) return $referencia;
        }

        if(
            preg_match('/\b(?:su|sus)\s+(?:etapa(?:\s+actual)?|porcentaje(?:\s+de\s+germinacion)?|tasa\s+de\s+germinacion|altura(?:\s+promedio)?|seguimiento(?:s)?|evidencia(?:s)?|lectura(?:s)?|alerta(?:s)?|control(?:es)?|posicion(?:es)?|modo(?:\s+de\s+control)?|tipo\s+de\s+control|valor\s+aplicado|estado|condicion(?:es)?\s+optima(?:s)?)\b/u',$texto)
        ){
            return 'referencia_generica';
        }

        return null;
    }

    private function detectarReferencias(string $mensaje,?string $recurso): array
    {
        $entidades=[];

        // Los códigos explícitos no deben depender de coincidencias aproximadas.
        if(preg_match('/\bINC-[a-z0-9]+(?:-[a-z0-9]+)*\b/ui',$mensaje,$m)){
            $entidades['incubadora']=['codigo'=>mb_strtoupper($m[0])];
        }
        if(preg_match('/\bLOT-[a-z0-9]+(?:-[a-z0-9]+)*\b/ui',$mensaje,$m)){
            $entidades['lote']=['codigo'=>mb_strtoupper($m[0])];
        }
        // Sin la palabra "código", lote N es el ID; "lote código N" conserva el código.
        if(preg_match('/\blote\s+#?(\d+)(?![a-z0-9._\-])\b/ui',$mensaje,$m)){
            $entidades['lote']=['id'=>(int)$m[1]];
        }

        if(preg_match('/\blote\s+(?:con\s+)?id\s*[:#\-]?\s*(\d+)\b/ui',$mensaje,$m)){
            $entidades['lote']=['id'=>(int)$m[1]];
        }

        if(preg_match('/\bincubadora\s+(?:con\s+)?id\s*[:#\-]?\s*(\d+)\b/ui',$mensaje,$m)){
            $entidades['incubadora']=['id'=>(int)$m[1]];
        }

        if(preg_match('/\bfrasco\s+(?:con\s+)?id\s*[:#\-]?\s*(\d+)\b/ui',$mensaje,$m)){
            $entidades['frasco']=['id'=>(int)$m[1]];
        }

        if(preg_match('/\bespecie\s+(?:con\s+)?id\s*[:#\-]?\s*(\d+)\b/ui',$mensaje,$m)){
            $entidades['especie']=['id'=>(int)$m[1]];
        }

        if(preg_match('/\b(?:usuario|encargado)\s+(?:con\s+)?id\s*[:#\-]?\s*(\d+)\b/ui',$mensaje,$m)){
            $entidades['usuario']=['id'=>(int)$m[1]];
        }

        if(
            !isset($entidades['lote'])
            &&preg_match('/\blote\s+(?:(?:con\s+)?c[oó]digo\s+)?([a-z0-9][a-z0-9._\-]*)\b/ui',$mensaje,$m)
        ){
            $valor=trim($m[1]);

            if(!$this->esReferenciaGenerica($valor)){
                $entidades['lote']=['codigo'=>mb_strtoupper($valor)];
            }
        }

        if(
            !isset($entidades['incubadora'])
            &&preg_match('/\bincubadora\s+(?:(?:con\s+)?c[oó]digo\s+)?([a-z0-9][a-z0-9._\-]*)\b/ui',$mensaje,$m)
        ){
            $valor=trim($m[1]);

            if(!$this->esReferenciaGenerica($valor)){
                $entidades['incubadora']=['codigo'=>mb_strtoupper($valor)];
            }
        }

        if(
            $recurso==='posiciones'
            &&!isset($entidades['incubadora'])
            &&preg_match('/\bposicion(?:es)?(?:\s+\d+)?\s+de\s+(?:la\s+)?(?:incubadora\s+)?([a-z0-9][a-z0-9._\-]*)\b/ui',$mensaje,$m)
        ){
            $valor=trim($m[1]);

            if(!$this->esReferenciaGenerica($valor)){
                $entidades['incubadora']=['codigo'=>mb_strtoupper($valor)];
            }
        }

        if(
            !isset($entidades['frasco'])
            &&preg_match('/\bfrasco\s+(?:n[uú]mero\s+|numero\s+|no\.?\s*)?(\d+)\b/ui',$mensaje,$m)
        ){
            $entidades['frasco']=['numero_frasco'=>(int)$m[1]];
        }

        if(
            !isset($entidades['especie'])
            &&preg_match('/\bespecie\s+(?:llamada\s+|denominada\s+)?(.+)$/ui',$mensaje,$m)
        ){
            $valor=$this->limpiarTextoEntidad($m[1]);

            if($valor!==''&&!$this->esReferenciaGenerica($valor)){
                $entidades['especie']=['texto'=>$valor];
            }
        }

        if(
            !isset($entidades['especie'])
            &&$recurso==='lotes'
            &&preg_match('/\blotes?\s+de\s+(.+)$/ui',$mensaje,$m)
        ){
            $valor=$this->limpiarTextoEntidad($m[1]);
            $ascii=$this->ascii($valor);

            $otra=
                str_starts_with($ascii,'la incubadora')
                ||str_starts_with($ascii,'incubadora')
                ||str_starts_with($ascii,'el lote')
                ||str_starts_with($ascii,'lote');

            if($valor!==''&&!$otra&&!$this->esReferenciaGenerica($valor)){
                $entidades['especie']=['texto'=>$valor];
            }
        }

        if(
            !isset($entidades['usuario'])
            &&preg_match('/\b(?:usuario|encargado)\s+(?:llamado|llamada|nombre)?\s*([a-záéíóúñ][a-záéíóúñ\s._\-]*)$/ui',$mensaje,$m)
        ){
            $valor=$this->limpiarTextoEntidad($m[1]);

            if($valor!==''&&!$this->esReferenciaGenerica($valor)){
                $entidades['usuario']=['texto'=>$valor];
            }
        }

        if($recurso==='condiciones_optimas'&&!isset($entidades['especie'])
            &&preg_match('/\b(?:de|del|para)\s+(.+)$/ui',$mensaje,$m)){
            $entidades['especie']=['texto'=>$this->limpiarTextoEntidad($m[1])];
        }

        if($recurso==='asignaciones'&&!isset($entidades['usuario'])
            &&preg_match('/\bincubadoras?\s+(?:tiene|tienen)\s+(.+)$/ui',$mensaje,$m)){
            $entidades['usuario']=['texto'=>$this->limpiarTextoEntidad($m[1])];
        }

        return $entidades;
    }

    private function limpiarTextoEntidad(string $texto): string
    {
        $texto=trim($texto);

        $texto=preg_replace(
            '/\s+(?:de\s+)?(?:hoy|ayer|esta semana|semana pasada|este mes|mes pasado)$/ui',
            '',
            $texto
        );

        $texto=preg_replace(
            '/\s+(?:registrado|registrada|registrados|registradas)$/ui',
            '',
            $texto
        );

        return trim($texto);
    }

    private function esReferenciaGenerica(string $valor): bool
    {
        return in_array($this->ascii(trim($valor)),[
            'el','la','los','las','un','una','unos','unas','mi','mis','de','del','con','en',
            'que','cual','cuales','quien','quienes',
            'lote','lotes','frasco','frascos','incubadora','incubadoras',
            'especie','especies','posicion','posiciones',
            'alerta','alertas','lectura','lecturas','usuario','usuarios',
            'encargado','encargados','evidencia','evidencias',
            'registro','registros','biologico','biologicos',
            'ese','esa','esos','esas','este','esta','estos','estas',
            'anterior','anteriores','hoy','ayer',
            'activo','activa','activos','activas',
            'inactivo','inactiva','inactivos','inactivas',
            'pendiente','pendientes','atendida','atendidas',
            'resuelta','resueltas','actual','actuales',
            'vigente','vigentes','asignada','asignadas',
            'asignado','asignados','llamada','llamado','nombre'
        ],true);
    }

    private function requiereAclaracion(?string $recurso,?string $operacion,?string $variable): bool
    {
        return $recurso==='lecturas'
            &&in_array($operacion,['promedio','maximo','minimo'],true)
            &&$variable===null;
    }

    private function calcularConfianza(
        ?string $recurso,
        ?string $operacion,
        ?string $variable,
        array $entidades,
        bool $requiereContexto
    ): float {
        $p=0;

        if($recurso!==null) $p+=3;
        if($operacion!==null) $p+=3;
        if($variable!==null) $p++;
        if(!empty($entidades)) $p++;
        if($requiereContexto) $p++;

        return match(true){
            $p>=7=>0.95,
            $p>=6=>0.90,
            $p>=5=>0.85,
            $p>=4=>0.75,
            $p>=3=>0.65,
            default=>0.40
        };
    }
}
