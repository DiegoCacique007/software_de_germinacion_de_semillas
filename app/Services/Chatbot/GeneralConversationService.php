<?php

namespace App\Services\Chatbot;

use App\Models\User;

class GeneralConversationService
{
    public function ejecutar(array $plan,User $user): array
    {
        $operacion=$plan['operacion']??null;

        return match($operacion){
            'saludo'=>$this->respuesta(
                'Hola, '.$user->name.'. Soy el asistente de consulta de MicroSeed Control. ¿Qué deseas revisar?',
                'conversacion_saludo',
                ['Resumen del sistema','Estado de incubadoras','Última lectura','Alertas activas']
            ),

            'despedida'=>$this->respuesta(
                'Hasta luego. Cuando lo necesites, puedo ayudarte a consultar MicroSeed Control.',
                'conversacion_despedida'
            ),

            'agradecimiento'=>$this->respuesta(
                'De nada. Puedes continuar consultando información de MicroSeed Control.',
                'conversacion_agradecimiento'
            ),

            'ayuda'=>$this->respuesta(
                'Puedo ayudarte a consultar incubadoras, lecturas de temperatura y humedad, alertas, lotes, frascos, especies, condiciones óptimas, usuarios autorizados, asignaciones, seguimientos, evidencias, controles, posiciones, catálogos y el resumen del sistema. También puedo explicar conceptos relacionados con MicroSeed Control.',
                'conversacion_ayuda',
                [
                    'Resumen del sistema',
                    'Dame la última lectura',
                    'Alertas activas',
                    '¿Qué es el microclima?'
                ]
            ),

            'capacidades'=>$this->respuesta(
                'Puedo consultar datos reales de MicroSeed Control según tu rol y permisos, mantener contexto dentro de la conversación y explicar conceptos del sistema. No modifico registros ni ejecuto acciones físicas sobre los actuadores.',
                'conversacion_capacidades',
                [
                    'Muéstrame las incubadoras',
                    'Dame la última lectura',
                    'Muéstrame las alertas pendientes',
                    '¿Qué hace el nebulizador?'
                ]
            ),

            'reportes'=>$this->respuesta(
                $user->isSuperAdmin()
                    ?'El sistema dispone de módulos de reportes para consultar información registrada y generar los formatos disponibles desde la interfaz. El chatbot puede consultar los datos, pero no genera ni modifica reportes directamente.'
                    :'Puedes consultar desde el chatbot la información correspondiente a tus incubadoras asignadas. Los reportes globales corresponden al Super Admin.',
                'conversacion_reportes',
                [
                    'Resumen del sistema',
                    'Dame las últimas 10 lecturas',
                    'Muéstrame las alertas pendientes'
                ]
            ),

            'desconocida'=>$this->respuesta(
                'No pude identificar esa consulta como una pregunta relacionada con MicroSeed Control. Puedes preguntarme sobre incubadoras, lecturas, alertas, lotes, frascos, especies, seguimientos, controles o conceptos del sistema.',
                'pregunta_desconocida',
                [
                    'Resumen del sistema',
                    'Estado de incubadoras',
                    'Última lectura',
                    '¿Qué puedes hacer?'
                ]
            ),

            default=>[
                'ok'=>false,
                'tipo'=>'operacion_no_soportada',
                'mensaje'=>'No pude identificar la operación conversacional solicitada.'
            ]
        };
    }

    private function respuesta(string $texto,string $tipo,array $sugerencias=[]): array
    {
        return [
            'ok'=>true,
            'tipo'=>$tipo,
            'recurso'=>'conversacion',
            'operacion'=>str_replace('conversacion_','',$tipo),
            'respuesta_directa'=>$texto,
            'sugerencias'=>$sugerencias
        ];
    }
}
