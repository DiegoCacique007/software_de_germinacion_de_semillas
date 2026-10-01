@php
    $usuario = auth()->user();

    $mostrarChatbot = $usuario
        && ($usuario->isSuperAdmin() || $usuario->isEncargado());
@endphp

@if($mostrarChatbot)

    <style>
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

        .microseed-chat-panel[hidden]{
            display:none!important;
        }

        .microseed-chat-header{
            flex:0 0 auto;
            padding:18px 19px;
            color:#fff;
            background:linear-gradient(
                135deg,
                #144255 0%,
                #216a73 52%,
                #3bb49c 100%
            );
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
            display:flex;
            align-items:center;
            justify-content:center;
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
            flex:0 0 auto;
            padding:9px 18px;
            color:#64748b;
            background:#f8fafc;
            border-bottom:1px solid #e8eef1;
            font-size:10px;
        }

        .microseed-chat-messages{
            flex:1 1 auto;
            min-height:0;
            overflow-y:auto;
            padding:17px;
            background:#f8fafc;
            scroll-behavior:smooth;
            overscroll-behavior:contain;
            scrollbar-width:thin;
            scrollbar-color:#9bc8c4 #edf3f3;
        }

        .microseed-chat-messages::-webkit-scrollbar{
            width:7px;
        }

        .microseed-chat-messages::-webkit-scrollbar-track{
            background:#edf3f3;
        }

        .microseed-chat-messages::-webkit-scrollbar-thumb{
            background:#9bc8c4;
            border-radius:999px;
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

        .microseed-chat-message-text{
            white-space:pre-wrap;
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
            transition:.18s ease;
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
            0%,60%,100%{
                transform:translateY(0);
                opacity:.45;
            }

            30%{
                transform:translateY(-4px);
                opacity:1;
            }
        }

        .microseed-chat-footer{
            flex:0 0 auto;
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

        .microseed-chat-input:disabled{
            background:#f8fafc;
            cursor:not-allowed;
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
        id="microseed-chatbot"
        class="microseed-chat-wrapper"
    >

        <div
            id="microseed-chat-panel"
            class="microseed-chat-panel"
            hidden
        >

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
                        id="microseed-chat-close"
                        type="button"
                        class="microseed-chat-close"
                        aria-label="Cerrar chatbot"
                    >
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
                id="microseed-chat-messages"
                class="microseed-chat-messages"
            ></div>

            <div class="microseed-chat-footer">

                <form id="microseed-chat-form">

                    <div class="microseed-chat-input-group">

                    <textarea
                        id="microseed-chat-input"
                        class="microseed-chat-input"
                        rows="1"
                        maxlength="500"
                        placeholder="Pregunta sobre MicroSeed Control..."
                    ></textarea>

                        <button
                            id="microseed-chat-send"
                            type="submit"
                            class="microseed-chat-send"
                            aria-label="Enviar mensaje"
                        >
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
            id="microseed-chat-toggle"
            type="button"
            class="microseed-chat-button"
            aria-label="Abrir asistente"
        >
            <i
                id="microseed-chat-toggle-icon"
                class="bi bi-chat-dots-fill"
            ></i>
        </button>

    </div>

    <script>
        (function() {
            const wrapper =
                document.getElementById('microseed-chatbot');

            if (!wrapper) {
                return;
            }

            if (wrapper.dataset.initialized === '1') {
                return;
            }

            wrapper.dataset.initialized = '1';

            const panel =
                document.getElementById('microseed-chat-panel');

            const toggle =
                document.getElementById('microseed-chat-toggle');

            const toggleIcon =
                document.getElementById('microseed-chat-toggle-icon');

            const closeButton =
                document.getElementById('microseed-chat-close');

            const messages =
                document.getElementById('microseed-chat-messages');

            const form =
                document.getElementById('microseed-chat-form');

            const input =
                document.getElementById('microseed-chat-input');

            const sendButton =
                document.getElementById('microseed-chat-send');

            const endpoint =
                @json(route('chatbot.mensaje'));

            const csrf =
                @json(csrf_token());

            const chatId=crypto.randomUUID();

            let abierto = false;
            let enviando = false;
            let typingElement = null;

            const horaActual = () => {
                return new Date().toLocaleTimeString(
                    'es-MX',
                    {
                        hour:'2-digit',
                        minute:'2-digit'
                    }
                );
            };

            const scrollAbajo = (suave = true) => {
                requestAnimationFrame(() => {
                    messages.scrollTo({
                        top:messages.scrollHeight,
                        behavior:suave ? 'smooth' : 'auto'
                    });
                });
            };

            const actualizarEstadoBoton = () => {
                const sinTexto =
                    input.value.trim().length === 0;

                sendButton.disabled =
                    enviando || sinTexto;

                input.disabled =
                    enviando;
            };

            const abrirChat = () => {
                abierto = true;

                panel.hidden = false;

                toggle.setAttribute(
                    'aria-label',
                    'Cerrar asistente'
                );

                toggleIcon.className =
                    'bi bi-x-lg';

                scrollAbajo(false);

                setTimeout(() => {
                    input.focus();
                },50);
            };

            const cerrarChat = () => {
                abierto = false;

                panel.hidden = true;

                toggle.setAttribute(
                    'aria-label',
                    'Abrir asistente'
                );

                toggleIcon.className =
                    'bi bi-chat-dots-fill';
            };

            const alternarChat = () => {
                if(abierto){
                    cerrarChat();
                }else{
                    abrirChat();
                }
            };

            const crearSugerencias = (
                contenedor,
                sugerencias
            ) => {
                if(
                    !Array.isArray(sugerencias)
                    || sugerencias.length === 0
                ){
                    return;
                }

                const suggestions =
                    document.createElement('div');

                suggestions.className =
                    'microseed-chat-suggestions';

                sugerencias.forEach((texto) => {
                    const button =
                        document.createElement('button');

                    button.type =
                        'button';

                    button.className =
                        'microseed-chat-suggestion';

                    button.textContent =
                        texto;

                    button.addEventListener(
                        'click',
                        () => {
                            if(enviando){
                                return;
                            }

                            input.value =
                                texto;

                            actualizarEstadoBoton();

                            enviarMensaje();
                        }
                    );

                    suggestions.appendChild(
                        button
                    );
                });

                contenedor.appendChild(
                    suggestions
                );
            };

            const agregarMensaje = (
                tipo,
                texto,
                sugerencias = []
            ) => {
                const row =
                    document.createElement('div');

                row.className =
                    `microseed-chat-row ${tipo}`;

                const bubble =
                    document.createElement('div');

                bubble.className =
                    'microseed-chat-message';

                const textoElement =
                    document.createElement('div');

                textoElement.className =
                    'microseed-chat-message-text';

                textoElement.textContent =
                    String(texto ?? '');

                const time =
                    document.createElement('div');

                time.className =
                    'microseed-chat-time';

                time.textContent =
                    horaActual();

                bubble.appendChild(
                    textoElement
                );

                bubble.appendChild(
                    time
                );

                if(tipo === 'bot'){
                    crearSugerencias(
                        bubble,
                        sugerencias
                    );
                }

                row.appendChild(
                    bubble
                );

                messages.appendChild(
                    row
                );

                scrollAbajo();

                return row;
            };

            const mostrarEscribiendo = () => {
                quitarEscribiendo();

                const row =
                    document.createElement('div');

                row.className =
                    'microseed-chat-row bot';

                const typing =
                    document.createElement('div');

                typing.className =
                    'microseed-chat-typing';

                typing.innerHTML =
                    '<span></span><span></span><span></span>';

                row.appendChild(
                    typing
                );

                messages.appendChild(
                    row
                );

                typingElement =
                    row;

                scrollAbajo();
            };

            const quitarEscribiendo = () => {
                if(
                    typingElement
                    && typingElement.parentNode
                ){
                    typingElement.parentNode.removeChild(
                        typingElement
                    );
                }

                typingElement =
                    null;
            };

            const obtenerRespuestaJson =
                async (response) => {

                    const contentType =
                        response.headers.get(
                            'content-type'
                        ) || '';

                    if(
                        contentType.includes(
                            'application/json'
                        )
                    ){
                        return await response.json();
                    }

                    const text =
                        await response.text();

                    return {
                        respuesta:
                            text || null
                    };
                };

            const enviarMensaje = async () => {
                const texto =
                    input.value.trim();

                if(
                    !texto
                    || enviando
                ){
                    return;
                }

                agregarMensaje(
                    'user',
                    texto
                );

                input.value = '';

                enviando = true;

                actualizarEstadoBoton();

                mostrarEscribiendo();

                try{
                    const response =
                        await fetch(
                            endpoint,
                            {
                                method:'POST',

                                headers:{
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                    csrf,

                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },

                                body:JSON.stringify({
                                    mensaje:texto,
                                    chat_id:chatId
                                })
                            }
                        );

                    const data =
                        await obtenerRespuestaJson(
                            response
                        );

                    quitarEscribiendo();

                    if(!response.ok){
                        throw new Error(
                            data.respuesta
                            || data.message
                            || `Error ${response.status}`
                        );
                    }

                    const respuesta =
                        data.respuesta
                        || 'No recibí una respuesta válida del asistente.';

                    const sugerencias =
                        Array.isArray(data.sugerencias)
                            ? data.sugerencias
                            : [];

                    agregarMensaje(
                        'bot',
                        respuesta,
                        sugerencias
                    );

                }catch(error){
                    quitarEscribiendo();

                    agregarMensaje(
                        'bot',
                        error?.message
                        || 'Ocurrió un problema al consultar el asistente. Inténtalo nuevamente.',
                        [
                            'Estado de mi incubadora',
                            'Alertas activas',
                            '¿Qué puedes hacer?'
                        ]
                    );

                }finally{
                    enviando = false;

                    actualizarEstadoBoton();

                    input.focus();

                    scrollAbajo();
                }
            };

            toggle.addEventListener(
                'click',
                alternarChat
            );

            closeButton.addEventListener(
                'click',
                cerrarChat
            );

            form.addEventListener(
                'submit',
                (event) => {
                    event.preventDefault();
                    enviarMensaje();
                }
            );

            input.addEventListener(
                'input',
                actualizarEstadoBoton
            );

            input.addEventListener(
                'keydown',
                (event) => {
                    if(
                        event.key === 'Enter'
                        && !event.shiftKey
                    ){
                        event.preventDefault();
                        enviarMensaje();
                    }
                }
            );

            document.addEventListener(
                'keydown',
                (event) => {
                    if(
                        event.key === 'Escape'
                        && abierto
                    ){
                        cerrarChat();
                    }
                }
            );

            agregarMensaje(
                'bot',
                @json(
                    'Hola '.$usuario->name.'. Soy el asistente de MicroSeed Control. Puedo consultar información real del sistema. ¿En qué puedo ayudarte?'
                ),
                [
                    'Estado de mi incubadora',
                    'Última lectura',
                    'Alertas activas',
                    'Lotes activos',
                    '¿Qué puedes hacer?'
                ]
            );

            actualizarEstadoBoton();
        })();
    </script>

@endif
