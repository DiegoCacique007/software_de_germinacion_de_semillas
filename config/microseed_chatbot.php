<?php

return [

    'nombre'=>'Asistente MicroSeed',

    'descripcion'=>'Asistente virtual especializado en el funcionamiento de MicroSeed Control, control de microclima y germinación de semillas.',

    'sugerencias'=>[
        '¿Qué es MicroSeed Control?',
        '¿Cómo funciona el control de temperatura?',
        '¿Cómo se controla la humedad?',
        '¿Qué hace el modo automático?',
        '¿Para qué sirve el nebulizador?',
        '¿Para qué sirve la ventilación?',
        '¿Qué pasa si la temperatura es muy alta?',
        '¿Qué pasa si la humedad es muy baja?',
    ],

    'conocimiento'=>[

        /*
        |--------------------------------------------------------------------------
        | MICROSEED CONTROL
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'microseed',
            'titulo'=>'MicroSeed Control',
            'palabras'=>[
                'microseed',
                'microseed control',
                'que es microseed',
                'para que sirve microseed',
                'que hace el sistema',
                'funcion del sistema',
                'incubadora'
            ],
            'respuesta'=>'MicroSeed Control es un sistema diseñado para supervisar y regular las condiciones ambientales dentro de una incubadora destinada a la germinación de semillas. Su función principal es mantener un microclima adecuado mediante el monitoreo de variables como temperatura y humedad relativa, además del control de actuadores como nebulización, calefacción y ventilación.'
        ],

        /*
        |--------------------------------------------------------------------------
        | MICROCLIMA
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'microclima',
            'titulo'=>'Microclima',
            'palabras'=>[
                'microclima',
                'que es microclima',
                'ambiente interno',
                'ambiente incubadora',
                'clima incubadora'
            ],
            'respuesta'=>'El microclima es el conjunto de condiciones ambientales que existen dentro de la incubadora. En MicroSeed Control se busca mantener principalmente la temperatura y la humedad relativa dentro de niveles adecuados para favorecer la germinación. Estas condiciones deben mantenerse lo más uniformes posible en toda la cámara.'
        ],

        /*
        |--------------------------------------------------------------------------
        | TEMPERATURA
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'temperatura',
            'titulo'=>'Temperatura',
            'palabras'=>[
                'temperatura',
                'grados',
                'calor',
                'frio',
                'temperatura ideal',
                'control temperatura'
            ],
            'respuesta'=>'La temperatura influye directamente en la velocidad de las reacciones metabólicas de la semilla. MicroSeed Control utiliza sensores para conocer la temperatura interna de la incubadora y puede actuar mediante calefacción o ventilación. El rango correcto no es igual para todas las semillas, por lo que debe definirse según la especie que se esté germinando.'
        ],

        [
            'id'=>'temperatura_alta',
            'titulo'=>'Temperatura alta',
            'palabras'=>[
                'temperatura alta',
                'mucho calor',
                'demasiado calor',
                'se calienta',
                'sobrecalentamiento',
                'temperatura por encima',
                'temperatura elevada'
            ],
            'respuesta'=>'Cuando la temperatura supera el rango establecido para una especie, MicroSeed Control puede activar la ventilación para favorecer la circulación del aire y reducir la acumulación de calor. Mantener temperaturas excesivamente altas durante demasiado tiempo puede afectar la germinación y aumentar la pérdida de humedad.'
        ],

        [
            'id'=>'temperatura_baja',
            'titulo'=>'Temperatura baja',
            'palabras'=>[
                'temperatura baja',
                'mucho frio',
                'demasiado frio',
                'temperatura por debajo',
                'incubadora fria',
                'hace frio'
            ],
            'respuesta'=>'Cuando la temperatura disminuye por debajo del rango configurado, el sistema puede activar el mecanismo de calefacción. La finalidad es recuperar gradualmente la temperatura adecuada sin producir cambios bruscos dentro de la incubadora.'
        ],

        /*
        |--------------------------------------------------------------------------
        | HUMEDAD
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'humedad',
            'titulo'=>'Humedad relativa',
            'palabras'=>[
                'humedad',
                'humedad relativa',
                'control humedad',
                'humedad ideal',
                'agua ambiente'
            ],
            'respuesta'=>'La humedad relativa indica la cantidad de vapor de agua presente en el aire. Durante la germinación es importante evitar ambientes excesivamente secos o saturados. En MicroSeed Control la humedad puede incrementarse mediante nebulización y regularse mediante circulación de aire.'
        ],

        [
            'id'=>'humedad_baja',
            'titulo'=>'Humedad baja',
            'palabras'=>[
                'humedad baja',
                'ambiente seco',
                'muy seco',
                'poca humedad',
                'falta humedad'
            ],
            'respuesta'=>'Cuando la humedad está por debajo del rango configurado, MicroSeed Control puede activar el nebulizador. Este dispositivo produce pequeñas partículas de agua que aumentan la humedad del aire dentro de la incubadora sin necesidad de mojar directamente las semillas.'
        ],

        [
            'id'=>'humedad_alta',
            'titulo'=>'Humedad alta',
            'palabras'=>[
                'humedad alta',
                'mucha humedad',
                'exceso humedad',
                'muy humedo',
                'saturado'
            ],
            'respuesta'=>'Una humedad excesivamente alta puede producir condensación y favorecer condiciones no deseadas dentro de la incubadora. El sistema puede detener la nebulización y utilizar ventilación para ayudar a renovar y distribuir el aire hasta regresar al rango establecido.'
        ],

        /*
        |--------------------------------------------------------------------------
        | NEBULIZADOR
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'nebulizador',
            'titulo'=>'Nebulizador',
            'palabras'=>[
                'nebulizador',
                'niebla',
                'nebulizacion',
                'humidificador',
                'vapor de agua'
            ],
            'respuesta'=>'El nebulizador es el actuador encargado de incrementar la humedad dentro de la incubadora. Utiliza agua almacenada en un depósito para generar una fina niebla que se distribuye en el ambiente. MicroSeed Control puede encenderlo automáticamente cuando la humedad disminuye demasiado.'
        ],

        /*
        |--------------------------------------------------------------------------
        | DEPÓSITO
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'deposito',
            'titulo'=>'Depósito de agua',
            'palabras'=>[
                'deposito',
                'deposito de agua',
                'agua nebulizador',
                'tanque agua'
            ],
            'respuesta'=>'El depósito almacena el agua utilizada por el sistema de humidificación. Su función es suministrar agua al mecanismo de nebulización. Debe mantenerse con suficiente nivel de agua y en condiciones adecuadas para evitar que el sistema de humidificación funcione sin suministro.'
        ],

        /*
        |--------------------------------------------------------------------------
        | ELECTROVÁLVULA
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'electrovalvula',
            'titulo'=>'Electroválvula',
            'palabras'=>[
                'electrovalvula',
                'valvula',
                'valvula agua'
            ],
            'respuesta'=>'La electroválvula permite controlar automáticamente el paso de agua dentro del sistema. Puede abrirse o cerrarse según la lógica definida por el controlador, evitando que el suministro de agua dependa de una operación manual.'
        ],

        /*
        |--------------------------------------------------------------------------
        | CALEFACCIÓN
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'calefaccion',
            'titulo'=>'Calefacción',
            'palabras'=>[
                'calefaccion',
                'calefactor',
                'generar calor',
                'aumentar temperatura',
                'subir temperatura'
            ],
            'respuesta'=>'La calefacción se utiliza cuando la temperatura interna está por debajo del rango requerido. El sistema debe aportar calor de forma controlada y distribuirlo con ayuda de la circulación de aire para evitar zonas demasiado calientes dentro de la incubadora.'
        ],

        /*
        |--------------------------------------------------------------------------
        | VENTILACIÓN
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'ventilacion',
            'titulo'=>'Ventilación',
            'palabras'=>[
                'ventilacion',
                'ventilador',
                'aire',
                'circulacion aire',
                'distribuir calor',
                'distribuir humedad'
            ],
            'respuesta'=>'La ventilación ayuda a mover el aire dentro de la incubadora. Su función no es solamente enfriar: también ayuda a distribuir de manera uniforme la temperatura y la humedad, reduciendo diferencias entre distintas zonas de la cámara.'
        ],

        /*
        |--------------------------------------------------------------------------
        | HOMOGENEIDAD
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'homogeneidad',
            'titulo'=>'Distribución uniforme del ambiente',
            'palabras'=>[
                'mismo ambiente',
                'misma temperatura',
                'uniforme',
                'homogeneo',
                'todo igual',
                'distribucion temperatura',
                'distribucion humedad'
            ],
            'respuesta'=>'Para que toda la incubadora mantenga condiciones semejantes es necesario combinar medición ambiental con circulación de aire. La ventilación ayuda a distribuir el calor y la humedad generados por los actuadores, reduciendo zonas calientes, frías, secas o excesivamente húmedas.'
        ],

        /*
        |--------------------------------------------------------------------------
        | MODO AUTOMÁTICO
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'automatico',
            'titulo'=>'Modo automático',
            'palabras'=>[
                'modo automatico',
                'automatico',
                'automatizacion',
                'control automatico'
            ],
            'respuesta'=>'En modo automático, MicroSeed Control compara las mediciones ambientales con los rangos establecidos. Si detecta una condición fuera de rango puede activar o desactivar los actuadores necesarios. Por ejemplo, puede encender la nebulización ante humedad baja, calefacción ante temperatura baja o ventilación ante temperatura elevada.'
        ],

        /*
        |--------------------------------------------------------------------------
        | MODO MANUAL
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'manual',
            'titulo'=>'Modo manual',
            'palabras'=>[
                'modo manual',
                'manual',
                'control manual'
            ],
            'respuesta'=>'El modo manual permite que un usuario autorizado controle directamente los actuadores. Es útil durante pruebas, mantenimiento o situaciones donde se requiere intervenir independientemente de la lógica automática.'
        ],

        /*
        |--------------------------------------------------------------------------
        | HISTÉRESIS
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'histeresis',
            'titulo'=>'Histéresis',
            'palabras'=>[
                'histeresis',
                'prender apagar seguido',
                'enciende y apaga',
                'cambio constante',
                'oscilacion'
            ],
            'respuesta'=>'La histéresis evita que un actuador se encienda y apague continuamente cuando una medición se encuentra exactamente cerca del límite configurado. Se utiliza un pequeño margen antes de cambiar nuevamente el estado del actuador, lo que proporciona un control más estable y reduce el desgaste de los componentes.'
        ],

        /*
        |--------------------------------------------------------------------------
        | WATCHDOG
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'watchdog',
            'titulo'=>'Watchdog de seguridad',
            'palabras'=>[
                'watchdog',
                'sensor deja de enviar',
                'sin lectura',
                'sensor desconectado',
                'no llegan datos',
                'seguridad actuadores'
            ],
            'respuesta'=>'MicroSeed Control contempla un mecanismo de seguridad para detectar cuando dejan de recibirse lecturas ambientales. Si los datos dejan de actualizarse durante un periodo determinado, los actuadores ambientales pueden apagarse para evitar que permanezcan activos sin información confiable del sensor.'
        ],

        /*
        |--------------------------------------------------------------------------
        | SENSORES
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'sensores',
            'titulo'=>'Sensores',
            'palabras'=>[
                'sensor',
                'sensores',
                'medir temperatura',
                'medir humedad',
                'datos ambientales'
            ],
            'respuesta'=>'Los sensores son los encargados de medir las condiciones ambientales dentro de la incubadora. Las mediciones de temperatura y humedad se envían al controlador y posteriormente pueden visualizarse dentro de MicroSeed Control para monitoreo, generación de alertas y toma de decisiones automáticas.'
        ],

        /*
        |--------------------------------------------------------------------------
        | ESP32
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'esp32',
            'titulo'=>'ESP32',
            'palabras'=>[
                'esp32',
                'microcontrolador',
                'controlador'
            ],
            'respuesta'=>'El ESP32 funciona como controlador electrónico del sistema. Puede recibir las mediciones de los sensores, comunicarse mediante Wi-Fi con la plataforma MicroSeed Control y ejecutar órdenes sobre los actuadores relacionados con el control ambiental.'
        ],

        /*
        |--------------------------------------------------------------------------
        | GERMINACIÓN
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'germinacion',
            'titulo'=>'Germinación',
            'palabras'=>[
                'germinacion',
                'germinar',
                'semilla germina',
                'semillas'
            ],
            'respuesta'=>'La germinación es el proceso mediante el cual una semilla inicia su desarrollo y comienza a formar una nueva planta. Las condiciones necesarias dependen de la especie, pero factores como temperatura, disponibilidad de humedad, oxígeno y tiempo influyen directamente en el proceso.'
        ],

        /*
        |--------------------------------------------------------------------------
        | RANGOS
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'rangos',
            'titulo'=>'Rangos ambientales',
            'palabras'=>[
                'rango',
                'rangos',
                'valor ideal',
                'valores ideales',
                'temperatura correcta',
                'humedad correcta'
            ],
            'respuesta'=>'No existe un único rango de temperatura y humedad válido para todas las semillas. MicroSeed Control debe permitir establecer condiciones óptimas según la especie germinada. La automatización utiliza esos límites para determinar cuándo debe intervenir sobre el ambiente.'
        ],

        /*
        |--------------------------------------------------------------------------
        | ALERTAS
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'alertas',
            'titulo'=>'Alertas',
            'palabras'=>[
                'alerta',
                'alertas',
                'fuera de rango',
                'advertencia'
            ],
            'respuesta'=>'Las alertas permiten informar que una variable ambiental se encuentra fuera de las condiciones esperadas. Una alerta puede indicar, por ejemplo, temperatura elevada, temperatura baja, humedad alta o humedad baja. Su finalidad es ayudar al usuario a detectar rápidamente una condición que requiere atención.'
        ],

        /*
        |--------------------------------------------------------------------------
        | REPORTES
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'reportes',
            'titulo'=>'Reportes',
            'palabras'=>[
                'reporte',
                'reportes',
                'pdf',
                'csv',
                'exportar',
                'datos completos'
            ],
            'respuesta'=>'MicroSeed Control permite generar reportes de microclima y seguimiento biológico. Los PDF están pensados para presentar información resumida y fácil de consultar, mientras que la exportación CSV permite conservar todos los registros para análisis o respaldo.'
        ],

        /*
        |--------------------------------------------------------------------------
        | ILUMINACIÓN
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'iluminacion',
            'titulo'=>'Iluminación',
            'palabras'=>[
                'luz',
                'iluminacion',
                'fotoperiodo'
            ],
            'respuesta'=>'La iluminación puede formar parte del ambiente controlado, pero su automatización debe definirse según las necesidades de la especie y el fotoperiodo requerido. En la versión actual de MicroSeed Control se considera independiente de la regulación automática de temperatura y humedad.'
        ],

        /*
        |--------------------------------------------------------------------------
        | FRASCOS
        |--------------------------------------------------------------------------
        */

        [
            'id'=>'frascos',
            'titulo'=>'Frascos de germinación',
            'palabras'=>[
                'frasco',
                'frascos',
                'semillas en frascos',
                'sin sustrato'
            ],
            'respuesta'=>'MicroSeed Control está pensado para trabajar con semillas colocadas en frascos dentro de la incubadora. Por ello, el sistema busca controlar principalmente el ambiente general de la cámara y no depende necesariamente de medir humedad directamente en un sustrato.'
        ],

    ],

];
