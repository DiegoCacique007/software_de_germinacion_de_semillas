<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

class ConceptKnowledgeService
{
    private const DEFINICIONES=[
        'alerta'=>[
            'titulo'=>'Alerta',
            'palabras'=>['alerta','alertas'],
            'respuesta'=>'Una alerta en MicroSeed Control indica una condición que requiere atención, por ejemplo temperatura o humedad fuera del rango configurado. Consultar su significado no implica que existan alertas activas.'
        ],
        'incubadora'=>[
            'titulo'=>'Incubadora',
            'palabras'=>['incubadora','incubadoras'],
            'respuesta'=>'Una incubadora es el equipo donde se alojan los cultivos y se monitorean las condiciones de microclima. Sus posiciones contienen lotes y puede tener encargados asignados.'
        ],
        'lectura'=>[
            'titulo'=>'Lectura de microclima',
            'palabras'=>['lectura','lecturas','lectura de microclima'],
            'respuesta'=>'Una lectura de microclima registra temperatura, humedad, incubadora y fecha/hora. La última lectura registrada puede no reflejar el estado actual si el sensor dejó de enviar datos.'
        ],
        'temperatura'=>[
            'titulo'=>'Temperatura',
            'palabras'=>['temperatura'],
            'respuesta'=>'La temperatura se registra en grados Celsius. Alta o baja significa por encima o por debajo del rango configurado para la especie; no existe un único rango válido para todas las semillas.'
        ],
        'humedad'=>[
            'titulo'=>'Humedad relativa',
            'palabras'=>['humedad','humedad relativa'],
            'respuesta'=>'La humedad relativa se registra como porcentaje. Alta o baja significa por encima o por debajo del rango configurado para la especie.'
        ],
        'lote'=>[
            'titulo'=>'Lote',
            'palabras'=>['lote','lotes'],
            'respuesta'=>'Un lote agrupa un cultivo de una especie, ubicado en una posición de incubadora, con fechas, estado, frascos y seguimientos asociados.'
        ],
        'frasco'=>[
            'titulo'=>'Frasco',
            'palabras'=>['frasco','frascos'],
            'respuesta'=>'Un frasco pertenece a un lote y registra su número dentro de ese lote, cantidad de semillas, estado y seguimientos. Su número no es necesariamente su ID.'
        ],
        'especie'=>[
            'titulo'=>'Especie',
            'palabras'=>['especie','especies'],
            'respuesta'=>'Una especie identifica la planta por su nombre común o científico. Puede tener condiciones óptimas configuradas y lotes asociados.'
        ],
        'usuario'=>[
            'titulo'=>'Usuario',
            'palabras'=>['usuario','usuarios'],
            'respuesta'=>'Un usuario es una cuenta de acceso con un rol. El Super Admin puede consultar globalmente y el encargado solo los recursos de sus asignaciones vigentes.'
        ],
        'seguimiento'=>[
            'titulo'=>'Seguimiento',
            'palabras'=>['seguimiento','seguimientos'],
            'respuesta'=>'Un seguimiento documenta una revisión biológica de un lote o frasco: fecha, germinación, altura y observaciones. Los seguimientos de lote también registran una etapa de desarrollo.'
        ],
        'control'=>[
            'titulo'=>'Control de incubadora',
            'palabras'=>['control','controles','control de incubadora'],
            'respuesta'=>'Un control de incubadora registra tipo, modo, valor aplicado y fecha/hora. Un registro histórico no confirma el estado físico actual del actuador. El chatbot solo consulta: no activa dispositivos.'
        ],
        'asignacion'=>[
            'titulo'=>'Asignación',
            'palabras'=>['asignacion','asignaciones'],
            'respuesta'=>'Una asignación vincula un encargado con una incubadora durante un periodo. Es vigente cuando ya comenzó y no ha terminado.'
        ],
        'condicion'=>[
            'titulo'=>'Condiciones óptimas',
            'palabras'=>['condicion optima','condiciones optimas','rango optimo','rangos optimos'],
            'respuesta'=>'Las condiciones óptimas son los límites de temperatura y humedad configurados para una especie. El chatbot solo informa los valores registrados, sin inventar rangos.'
        ],
        'microclima'=>[
            'titulo'=>'Microclima',
            'palabras'=>['microclima'],
            'respuesta'=>'El microclima es el conjunto de condiciones ambientales de la incubadora; este sistema registra principalmente temperatura y humedad relativa.'
        ]
    ];

    public function ejecutar(array $plan): array
    {
        if(($plan['recurso']??null)!=='conocimiento'){
            return $this->error(
                'recurso_no_soportado',
                'ConceptKnowledgeService solamente procesa conocimiento conceptual.'
            );
        }

        if(($plan['operacion']??null)!=='explicar'){
            return $this->error(
                'operacion_no_soportada',
                'La operación solicitada no corresponde a una explicación conceptual.'
            );
        }

        $texto=$this->normalizar(
            $plan['mensaje_normalizado']
            ??$plan['mensaje_original']
            ??''
        );

        if($texto===''){
            return $this->error(
                'requiere_aclaracion',
                'Indica qué concepto de MicroSeed Control deseas conocer.'
            );
        }

        $resultado=$this->buscarConcepto($texto);

        if(!$resultado){
            return [
                'ok'=>false,
                'tipo'=>'concepto_no_encontrado',
                'recurso'=>'conocimiento',
                'operacion'=>'explicar',
                'mensaje'=>'No encontré una explicación precisa para ese concepto. Puedes preguntar por incubadora, lectura, alerta, lote, frasco, microclima, sensores, ESP32, nebulización, calefacción, ventilación o control automático.'
            ];
        }

        return [
            'ok'=>true,
            'tipo'=>'conocimiento_conceptual',
            'recurso'=>'conocimiento',
            'operacion'=>'explicar',
            'concepto'=>$resultado['id'],
            'titulo'=>$resultado['titulo'],
            'respuesta'=>$resultado['respuesta'],
            'fuente'=>$resultado['fuente']
        ];
    }

    private function buscarConcepto(string $texto): ?array
    {
        $mejor=null;
        $longitud=0;

        foreach(self::DEFINICIONES as $id=>$concepto){
            foreach($concepto['palabras'] as $palabra){
                $palabra=$this->normalizar($palabra);

                if(
                    $this->contiene($texto,$palabra)
                    &&mb_strlen($palabra)>$longitud
                ){
                    $mejor=[
                        'id'=>$id,
                        'titulo'=>$concepto['titulo'],
                        'respuesta'=>$concepto['respuesta'],
                        'fuente'=>'definicion'
                    ];

                    $longitud=mb_strlen($palabra);
                }
            }
        }

        foreach(config('microseed_chatbot.conocimiento',[]) as $tema){
            if(
                empty($tema['respuesta'])
                ||empty($tema['palabras'])
            ){
                continue;
            }

            foreach($tema['palabras'] as $palabra){
                $palabra=$this->normalizar((string)$palabra);

                if(
                    $palabra!==''
                    &&$this->contiene($texto,$palabra)
                    &&mb_strlen($palabra)>$longitud
                ){
                    $mejor=[
                        'id'=>$tema['id']??'concepto',
                        'titulo'=>$tema['titulo']??'Concepto de MicroSeed Control',
                        'respuesta'=>(string)$tema['respuesta'],
                        'fuente'=>'configuracion'
                    ];

                    $longitud=mb_strlen($palabra);
                }
            }
        }

        return $mejor;
    }

    private function contiene(string $texto,string $frase): bool
    {
        return preg_match(
                '~(?:^|\b)'
                .preg_quote($frase,'~')
                .'(?:\b|$)~u',
                $texto
            )===1;
    }

    private function normalizar(string $texto): string
    {
        $texto=mb_strtolower(Str::ascii(trim($texto)));
        $texto=preg_replace('/[^a-z0-9\s_\-]/u',' ',$texto);

        return trim(
            preg_replace('/\s+/u',' ',$texto)
        );
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
