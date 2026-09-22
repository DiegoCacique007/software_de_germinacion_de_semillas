@php
    $usuario=auth()->user();

    $mostrarChatbot=$usuario
        &&(
            $usuario->isSuperAdmin()
            ||$usuario->isEncargado()
        );
@endphp

@if($mostrarChatbot)

    <style>
        [x-cloak]{display:none!important}

        .microseed-chat-wrapper{
            position:fixed;
            right:24px;
            bottom:24px;
            z-index:1090;
            font-family:inherit;
        }

        .microseed-chat-button{
            width:58px;
            height:58px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:0;
            border-radius:50%;
            color:#fff;
            background:linear-gradient(135deg,#1c607a,#3bb49c);
            box-shadow:0 12px 30px rgba(15,23,42,.28);
            transition:.2s ease;
            cursor:pointer;
        }

        .microseed-chat-button:hover{
            transform:translateY(-2px);
            box-shadow:0 15px 35px rgba(15,23,42,.35);
        }

        .microseed-chat-button i{
            font-size:25px;
        }

        .microseed-chat-panel{
            position:absolute;
            right:0;
            bottom:72px;
            width:390px;
            height:570px;
            max-height:calc(100vh - 120px);
            display:flex;
            flex-direction:column;
            overflow:hidden;
            background:#fff;
            border:1px solid rgba(20,66,85,.12);
            border-radius:22px;
            box-shadow:0 25px 70px rgba(15,23,42,.28);
        }

        .microseed-chat-header{
            padding:18px 19px;
            color:#fff;
            background:linear-gradient(135deg,#144255 0%,#216a73 52%,#3bb49c 100%);
        }

        .microseed-chat-header-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
        }

        .microseed-chat-identity{
            display:flex;
            align-items:center;
            gap:11px;
            min-width:0;
        }

        .microseed-chat-avatar{
            width:42px;
            height:42px;
            flex:0 0 42px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            background:rgba(255,255,255,.16);
            border:1px solid rgba(255,255,255,.22);
            font-size:20px;
        }

        .microseed-chat-title{
            margin:0;
            font-size:15px;
            font-weight:800;
        }

        .microseed-chat-status{
            display:flex;
            align-items:center;
            gap:5px;
            margin-top:2px;
            color:rgba(255,255,255,.8);
            font-size:11px;
        }

        .microseed-chat-status-dot{
            width:7px;
            height:7px;
            border-radius:50%;
            background:#86efac;
        }

        .microseed-chat-close{
            width:34px;
            height:34px;
            flex:0 0 34px;
            border:0;
            border-radius:10px;
            color:#fff;
            background:rgba(255,255,255,.11);
            cursor:pointer;
        }

        .microseed-chat-close:hover{
            background:rgba(255,255,255,.2);
        }

        .microseed-chat-context{
            padding:9px 18px;
            color:#64748b;
            background:#f8fafc;
            border-bottom:1px solid #e8eef1;
            font-size:10px;
        }

        .microseed-chat-messages{
            flex:1;
            overflow-y:auto;
            padding:17px;
            background:#f8fafc;
            scroll-behavior:smooth;
        }

        .microseed-chat-row{
            display:flex;
            margin-bottom:12px;
        }

        .microseed-chat-row.bot{
            justify-content:flex-start;
        }

        .microseed-chat-row.user{
            justify-content:flex-end;
        }

        .microseed-chat-message{
            max-width:85%;
            padding:10px 12px;
            border-radius:15px;
            font-size:12px;
            line-height:1.55;
            word-break:break-word;
        }

        .microseed-chat-row.bot .microseed-chat-message{
            color:#334155;
            background:#fff;
            border:1px solid #e2e8f0;
            border-bottom-left-radius:5px;
            box-shadow:0 3px 10px rgba(15,23,42,.04);
        }

        .microseed-chat-row.user .microseed-chat-message{
            color:#fff;
            background:linear-gradient(135deg,#1c607a,#2f8f83);
            border-bottom-right-radius:5px;
        }

        .microseed-chat-time{
            margin-top:4px;
            font-size:8px;
            opacity:.6;
        }

        .microseed-chat-suggestions{
            display:flex;
            flex-wrap:wrap;
            gap:6px;
            margin-top:8px;
        }

        .microseed-chat-suggestion{
            padding:6px 9px;
            border:1px solid #b8deda;
            border-radius:999px;
            color:#216a73;
            background:#f0fdfa;
            font-size:9px;
            font-weight:700;
            cursor:pointer;
        }

        .microseed-chat-suggestion:hover{
            color:#fff;
            background:#2f8f83;
            border-color:#2f8f83;
        }

        .microseed-chat-typing{
            display:flex;
            align-items:center;
            gap:4px;
            width:max-content;
            padding:11px 13px;
            background:#fff;
            border:1px solid #e2e8f0;
            border-radius:15px;
            border-bottom-left-radius:5px;
        }

        .microseed-chat-typing span{
            width:6px;
            height:6px;
            border-radius:50%;
            background:#94a3b8;
            animation:microseedTyping 1.1s infinite ease-in-out;
        }

        .microseed-chat-typing span:nth-child(2){
            animation-delay:.15s;
        }

        .microseed-chat-typing span:nth-child(3){
            animation-delay:.3s;
        }

        @keyframes microseedTyping{
            0%,60%,100%{transform:translateY(0);opacity:.45}
            30%{transform:translateY(-4px);opacity:1}
        }

        .microseed-chat-footer{
            padding:12px;
            background:#fff;
            border-top:1px solid #e8eef1;
        }

        .microseed-chat-input-group{
            display:flex;
            align-items:flex-end;
            gap:8px;
        }

        .microseed-chat-input{
            flex:1;
            min-height:42px;
            max-height:90px;
            resize:none;
            padding:11px 12px;
            border:1px solid #cbd5e1;
            border-radius:13px;
            color:#334155;
            background:#fff;
            font-size:12px;
            line-height:1.4;
            outline:none;
        }

        .microseed-chat-input:focus{
            border-color:#3bb49c;
            box-shadow:0 0 0 3px rgba(59,180,156,.12);
        }

        .microseed-chat-send{
            width:42px;
            height:42px;
            flex:0 0 42px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:0;
            border-radius:12px;
            color:#fff;
            background:linear-gradient(135deg,#1c607a,#3bb49c);
            cursor:pointer;
        }

        .microseed-chat-send:disabled{
            cursor:not-allowed;
            opacity:.5;
        }

        .microseed-chat-footer-text{
            margin-top:7px;
            color:#94a3b8;
            font-size:8px;
            text-align:center;
        }

        @media(max-width:576px){
            .microseed-chat-wrapper{
                right:14px;
                bottom:14px;
            }

            .microseed-chat-panel{
                position:fixed;
                top:12px;
                right:12px;
                bottom:12px;
                left:12px;
                width:auto;
                height:auto;
                max-height:none;
                border-radius:18px;
            }

            .microseed-chat-button{
                width:54px;
                height:54px;
            }
        }
    </style>

    <div
        class="microseed-chat-wrapper"
        x-data="microseedChatbot()"
        x-cloak
        @keydown.escape.window="abierto=false">

        <div
            x-show="abierto"
            x-transition.opacity.duration.180ms
            class="microseed-chat-panel">

            <div class="microseed-chat-header">
                <div class="microseed-chat-header-row">

                    <div class="microseed-chat-identity">
                        <div class="microseed-chat-avatar">
                            <i class="bi bi-flower1"></i>
                        </div>

                        <div>
                            <h3 class="microseed-chat-title">
                                Asistente MicroSeed
                            </h3>

                            <div class="microseed-chat-status">
                                <span class="microseed-chat-status-dot"></span>
                                Asistente del sistema
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="microseed-chat-close"
                        @click="abierto=false"
                        aria-label="Cerrar chatbot">
                        <i class="bi bi-x-lg"></i>
                    </button>

                </div>
            </div>

            <div class="microseed-chat-context">
                Sesión:
                <strong>{{ $usuario->name }}</strong>
                ·
                {{ $usuario->isSuperAdmin() ? 'Super Admin' : 'Encargado' }}
            </div>

            <div
                x-ref="mensajes"
                class="microseed-chat-messages">

                <template
                    x-for="(mensaje,index) in mensajes"
                    :key="index">

                    <div>
                        <div
                            class="microseed-chat-row"
                            :class="mensaje.tipo">

                            <div class="microseed-chat-message">

                                <div x-text="mensaje.texto"></div>

                                <div
                                    class="microseed-chat-time"
                                    x-text="mensaje.hora">
                                </div>

                                <template
                                    x-if="
                                    mensaje.tipo==='bot'
                                    && mensaje.sugerencias
                                    && mensaje.sugerencias.length
                                ">

                                    <div class="microseed-chat-suggestions">

                                        <template
                                            x-for="sugerencia in mensaje.sugerencias"
                                            :key="sugerencia">

                                            <button
                                                type="button"
                                                class="microseed-chat-suggestion"
                                                @click="usarSugerencia(sugerencia)"
                                                x-text="sugerencia">
                                            </button>

                                        </template>

                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>

                </template>

                <div
                    x-show="cargando"
                    class="microseed-chat-row bot">

                    <div class="microseed-chat-typing">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                </div>

            </div>

            <div class="microseed-chat-footer">

                <form
                    @submit.prevent="enviar()">

                    <div class="microseed-chat-input-group">

                    <textarea
                        x-model="mensaje"
                        x-ref="input"
                        class="microseed-chat-input"
                        rows="1"
                        maxlength="500"
                        placeholder="Pregunta sobre MicroSeed Control..."
                        :disabled="cargando"
                        @keydown.enter="
                            if(!$event.shiftKey){
                                $event.preventDefault();
                                enviar();
                            }
                        ">
                    </textarea>

                        <button
                            type="submit"
                            class="microseed-chat-send"
                            :disabled="cargando || !mensaje.trim()"
                            aria-label="Enviar mensaje">

                            <i class="bi bi-send-fill"></i>

                        </button>

                    </div>

                </form>

                <div class="microseed-chat-footer-text">
                    Consulta información de MicroSeed Control
                </div>

            </div>

        </div>

        <button
            type="button"
            class="microseed-chat-button"
            @click="toggle()"
            :aria-label="abierto ? 'Cerrar asistente' : 'Abrir asistente'">

            <i
                class="bi"
                :class="abierto ? 'bi-x-lg' : 'bi-chat-dots-fill'">
            </i>

        </button>

    </div>

    <script>
        function microseedChatbot(){
            return {
                abierto:false,
                mensaje:'',
                cargando:false,

                mensajes:[
                    {
                        tipo:'bot',
                        texto:'Hola {{ addslashes($usuario->name) }}. Soy el asistente de MicroSeed Control. Puedo consultar información real del sistema. ¿En qué puedo ayudarte?',
                        hora:thisHora(),
                        sugerencias:[
                            'Estado de mi incubadora',
                            'Última lectura',
                            'Alertas activas',
                            'Lotes activos',
                            '¿Qué puedes hacer?'
                        ]
                    }
                ],

                toggle(){
                    this.abierto=!this.abierto;

                    if(this.abierto){
                        this.$nextTick(()=>{
                            this.scrollAbajo();
                            this.$refs.input?.focus();
                        });
                    }
                },

                usarSugerencia(texto){
                    if(this.cargando){
                        return;
                    }

                    this.mensaje=texto;
                    this.enviar();
                },

                async enviar(){
                    const texto=this.mensaje.trim();

                    if(!texto || this.cargando){
                        return;
                    }

                    this.mensajes.push({
                        tipo:'user',
                        texto:texto,
                        hora:this.horaActual(),
                        sugerencias:[]
                    });

                    this.mensaje='';
                    this.cargando=true;

                    this.$nextTick(()=>{
                        this.scrollAbajo();
                    });

                    try{
                        const response=await fetch(
                            @json(route('chatbot.mensaje')),
                            {
                                method:'POST',
                                headers:{
                                    'Content-Type':'application/json',
                                    'Accept':'application/json',
                                    'X-CSRF-TOKEN':@json(csrf_token()),
                                    'X-Requested-With':'XMLHttpRequest'
                                },
                                body:JSON.stringify({
                                    mensaje:texto
                                })
                            }
                        );

                        const data=await response.json();

                        if(!response.ok){
                            throw new Error(
                                data.respuesta
                                ||'No fue posible procesar la consulta.'
                            );
                        }

                        this.mensajes.push({
                            tipo:'bot',
                            texto:data.respuesta,
                            hora:this.horaActual(),
                            sugerencias:data.sugerencias||[]
                        });

                    }catch(error){

                        this.mensajes.push({
                            tipo:'bot',
                            texto:error.message
                                ||'Ocurrió un problema al consultar el asistente. Inténtalo nuevamente.',
                            hora:this.horaActual(),
                            sugerencias:[
                                'Estado de mi incubadora',
                                'Alertas activas',
                                '¿Qué puedes hacer?'
                            ]
                        });

                    }finally{
                        this.cargando=false;

                        this.$nextTick(()=>{
                            this.scrollAbajo();
                            this.$refs.input?.focus();
                        });
                    }
                },

                scrollAbajo(){
                    const contenedor=this.$refs.mensajes;

                    if(contenedor){
                        contenedor.scrollTop=contenedor.scrollHeight;
                    }
                },

                horaActual(){
                    return new Date().toLocaleTimeString(
                        'es-MX',
                        {
                            hour:'2-digit',
                            minute:'2-digit'
                        }
                    );
                }
            }
        }

        function thisHora(){
            return new Date().toLocaleTimeString(
                'es-MX',
                {
                    hour:'2-digit',
                    minute:'2-digit'
                }
            );
        }
    </script>

@endif
