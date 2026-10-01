<?php

namespace App\Services;

use App\Models\User;
use App\Services\Chatbot\AlertQueryService;
use App\Services\Chatbot\AssignmentQueryService;
use App\Services\Chatbot\CatalogQueryService;
use App\Services\Chatbot\ConceptKnowledgeService;
use App\Services\Chatbot\ControlQueryService;
use App\Services\Chatbot\ConversationContextService;
use App\Services\Chatbot\EntityResolverService;
use App\Services\Chatbot\EvidenceQueryService;
use App\Services\Chatbot\FollowUpQueryService;
use App\Services\Chatbot\GeneralConversationService;
use App\Services\Chatbot\IncubatorQueryService;
use App\Services\Chatbot\OptimalConditionQueryService;
use App\Services\Chatbot\PositionQueryService;
use App\Services\Chatbot\QueryParserService;
use App\Services\Chatbot\ResourceQueryService;
use App\Services\Chatbot\ResponseComposerService;
use App\Services\Chatbot\StatisticsService;
use App\Services\Chatbot\SystemSummaryService;
use App\Services\Chatbot\UserQueryService;
use Throwable;

class ChatbotService
{
    private const OPERACIONES_LECTURAS=['ultimo','primero','listar','promedio','maximo','minimo','contar','estado'];
    private const OPERACIONES_ALERTAS=['contar','ultimo','listar','ranking'];
    private const OPERACIONES_RECURSOS=['contar','listar','estado','ultimo'];
    private const OPERACIONES_INCUBADORAS=['contar','listar','estado','ultimo'];
    private const OPERACIONES_CONDICIONES=['listar','ultimo'];
    private const OPERACIONES_USUARIOS=['contar','listar','estado','ultimo'];
    private const OPERACIONES_ASIGNACIONES=['contar','listar','estado','ultimo'];
    private const OPERACIONES_SEGUIMIENTOS=['contar','listar','estado','ultimo'];
    private const OPERACIONES_EVIDENCIAS=['contar','listar','ultimo'];
    private const OPERACIONES_CONTROLES=['contar','listar','estado','ultimo'];
    private const OPERACIONES_POSICIONES=['contar','listar','estado','ultimo'];
    private const OPERACIONES_CATALOGOS=['contar','listar','estado','ultimo'];
    private const OPERACIONES_RESUMEN=['resumen'];
    private const OPERACIONES_CONOCIMIENTO=['explicar'];

    private const OPERACIONES_CONVERSACION=[
        'saludo',
        'despedida',
        'agradecimiento',
        'ayuda',
        'capacidades',
        'reportes',
        'desconocida'
    ];

    private const RECURSOS_ESTRUCTURADOS=[
        'lotes',
        'frascos',
        'especies'
    ];

    public function __construct(
        private readonly QueryParserService $parser,
        private readonly EntityResolverService $resolver,
        private readonly ConversationContextService $context,
        private readonly StatisticsService $statistics,
        private readonly AlertQueryService $alerts,
        private readonly ResourceQueryService $resources,
        private readonly IncubatorQueryService $incubators,
        private readonly OptimalConditionQueryService $conditions,
        private readonly UserQueryService $users,
        private readonly AssignmentQueryService $assignments,
        private readonly FollowUpQueryService $followUps,
        private readonly EvidenceQueryService $evidences,
        private readonly ControlQueryService $controls,
        private readonly PositionQueryService $positions,
        private readonly CatalogQueryService $catalogs,
        private readonly SystemSummaryService $summary,
        private readonly ConceptKnowledgeService $knowledge,
        private readonly GeneralConversationService $conversation,
        private readonly ResponseComposerService $composer
    ){}

    public function responder(string $mensaje,User $user,string $chatId): array
    {
        if(!$this->usuarioPermitido($user)){
            return $this->respuesta(
                'acceso_denegado',
                'Tu cuenta no tiene acceso a estas consultas.',
                false
            );
        }

        $mensaje=trim($mensaje);

        if($mensaje===''){
            return $this->respuesta(
                'mensaje_vacio',
                'Escribe una consulta sobre MicroSeed Control.',
                false
            );
        }

        try{
            $plan=$this->parser->parse($mensaje);
        }catch(Throwable $e){
            report($e);

            return $this->respuesta(
                'error_interpretacion',
                'No pude interpretar la consulta en este momento. Intenta formularla nuevamente.',
                false
            );
        }

        if(($plan['operacion']??null)==='accion_no_permitida'){
            return $this->respuesta(
                'accion_no_permitida',
                'El chatbot es de consulta y no ejecuta cambios, eliminaciones, asignaciones ni acciones físicas sobre actuadores.',
                true,
                [
                    'Estado de incubadoras',
                    'Última lectura',
                    'Alertas activas',
                    'Lotes activos'
                ]
            );
        }

        if(!$this->usarMotorEstructurado($plan)){
            return $this->respuesta(
                'consulta_no_soportada',
                'No pude identificar una consulta compatible con las funciones disponibles de MicroSeed Control.',
                false,
                [
                    'Resumen del sistema',
                    'Estado de incubadoras',
                    'Última lectura',
                    '¿Qué puedes hacer?'
                ]
            );
        }

        try{
            $resolucion=$this->resolver->resolver(
                $plan,
                $user
            );
        }catch(Throwable $e){
            report($e);

            return $this->respuesta(
                'error_resolucion',
                'No pude validar los registros mencionados en la consulta. Intenta nuevamente indicando un ID, código o nombre.',
                false
            );
        }

        try{
            $resolucion=$this->context->aplicar(
                $plan,
                $resolucion,
                $user,
                $chatId
            );
        }catch(Throwable $e){
            report($e);
        }

        if(!empty($resolucion['contexto_aplicado'])){
            try{
                $resolucion=$this->resolver->revalidarContexto(
                    $resolucion,
                    $user
                );
            }catch(Throwable $e){
                report($e);

                return $this->respuesta(
                    'error_contexto',
                    'No pude validar la referencia anterior. Indica nuevamente el registro que deseas consultar.',
                    false
                );
            }
        }

        if(
            in_array(
                $resolucion['estado']??null,
                ['ambiguo','no_encontrado'],
                true
            )
        ){
            return $this->respuestaResolucion(
                $plan,
                $resolucion
            );
        }

        if(($resolucion['estado']??null)==='contexto_faltante'){
            return $this->adaptarRespuesta(
                $plan,
                $this->composer->componer(
                    $plan,
                    $resolucion,
                    [
                        'ok'=>false,
                        'tipo'=>'requiere_aclaracion',
                        'mensaje'=>$resolucion['mensaje']
                            ??'Necesito que indiques qué registro deseas consultar.'
                    ]
                )
            );
        }

        try{
            $resultado=$this->ejecutarConsulta(
                $plan,
                $resolucion,
                $user
            );
        }catch(Throwable $e){
            report($e);

            return $this->respuesta(
                'error_consulta',
                'No pude completar la consulta en este momento. Intenta nuevamente con una referencia más específica.',
                false
            );
        }

        try{
            $this->context->actualizar(
                $plan,
                $resolucion,
                $resultado,
                $user,
                $chatId
            );
        }catch(Throwable $e){
            report($e);
        }

        try{
            $compuesta=$this->composer->componer(
                $plan,
                $resolucion,
                $resultado
            );
        }catch(Throwable $e){
            report($e);

            return $this->respuesta(
                'error_respuesta',
                'La consulta fue procesada, pero ocurrió un problema al preparar la respuesta.',
                false
            );
        }

        return $this->adaptarRespuesta(
            $plan,
            $compuesta
        );
    }

    private function ejecutarConsulta(
        array $plan,
        array $resolucion,
        User $user
    ): array {
        return match($plan['recurso']??null){
            'lecturas'=>$this->statistics->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'alertas'=>$this->alerts->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'lotes','frascos','especies'=>$this->resources->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'incubadoras'=>$this->incubators->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'condiciones_optimas'=>$this->conditions->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'usuarios'=>$this->users->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'asignaciones'=>$this->assignments->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'seguimientos'=>$this->followUps->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'evidencias'=>$this->evidences->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'controles'=>$this->controls->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'posiciones'=>$this->positions->ejecutar(
                $plan,
                $resolucion,
                $user
            ),

            'catalogos'=>$this->catalogs->ejecutar(
                $plan,
                $user
            ),

            'resumen'=>$this->summary->ejecutar(
                $plan,
                $user
            ),

            'conocimiento'=>$this->knowledge->ejecutar(
                $plan
            ),

            'conversacion'=>$this->conversation->ejecutar(
                $plan,
                $user
            ),

            default=>[
                'ok'=>false,
                'tipo'=>'recurso_no_soportado',
                'mensaje'=>'No existe un servicio disponible para esta consulta.'
            ]
        };
    }

    private function usarMotorEstructurado(array $plan): bool
    {
        $recurso=$plan['recurso']??null;
        $operacion=$plan['operacion']??null;

        if(
            $operacion==='explicar'
            &&$recurso!=='conocimiento'
        ){
            return false;
        }

        if($recurso==='lecturas'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_LECTURAS,true);
        }

        if($recurso==='alertas'){
            if(!empty($plan['filtros'])) return true;

            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_ALERTAS,true);
        }

        if(in_array($recurso,self::RECURSOS_ESTRUCTURADOS,true)){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_RECURSOS,true);
        }

        if($recurso==='incubadoras'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_INCUBADORAS,true);
        }

        if($recurso==='condiciones_optimas'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_CONDICIONES,true);
        }

        if($recurso==='usuarios'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_USUARIOS,true);
        }

        if($recurso==='asignaciones'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_ASIGNACIONES,true);
        }

        if($recurso==='seguimientos'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_SEGUIMIENTOS,true);
        }

        if($recurso==='evidencias'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_EVIDENCIAS,true);
        }

        if($recurso==='controles'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_CONTROLES,true);
        }

        if($recurso==='posiciones'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_POSICIONES,true);
        }

        if($recurso==='catalogos'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_CATALOGOS,true);
        }

        if($recurso==='resumen'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_RESUMEN,true);
        }

        if($recurso==='conocimiento'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_CONOCIMIENTO,true);
        }

        if($recurso==='conversacion'){
            return $operacion!==null
                &&in_array($operacion,self::OPERACIONES_CONVERSACION,true);
        }

        return false;
    }

    private function respuestaResolucion(
        array $plan,
        array $resolucion
    ): array {
        return $this->adaptarRespuesta(
            $plan,
            $this->composer->componer(
                $plan,
                $resolucion,
                [
                    'ok'=>false,
                    'tipo'=>'resolucion_entidad'
                ]
            )
        );
    }

    private function adaptarRespuesta(
        array $plan,
        array $compuesta
    ): array {
        $recurso=$plan['recurso']??'consulta';
        $operacion=$plan['operacion']??'listar';

        $intencion=
            'consulta_estructurada_'
            .$recurso
            .'_'
            .$operacion;

        if(!empty($plan['variable'])){
            $intencion.='_'.$plan['variable'];
        }

        return [
            'respuesta'=>$compuesta['respuesta']
                ??'No pude preparar una respuesta para esta consulta.',
            'intencion'=>$intencion,
            'reconocida'=>true,
            'sugerencias'=>$compuesta['sugerencias']??[]
        ];
    }

    private function usuarioPermitido(User $user): bool
    {
        return (bool)$user->activo
            &&(
                $user->isSuperAdmin()
                ||$user->isEncargado()
            );
    }

    private function respuesta(
        string $intencion,
        string $texto,
        bool $reconocida,
        array $sugerencias=[]
    ): array {
        return [
            'respuesta'=>$texto,
            'intencion'=>$intencion,
            'reconocida'=>$reconocida,
            'sugerencias'=>$sugerencias?:[
                'Estado de incubadoras',
                'Última lectura',
                'Alertas activas',
                'Lotes activos',
                'Resumen del sistema'
            ]
        ];
    }
}
