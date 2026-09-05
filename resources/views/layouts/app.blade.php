<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Microseed Control') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN conservado TEMPORALMENTE para compatibilidad con vistas en proceso de migración --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Assets compilados mediante Vite (Bootstrap 5, Bootstrap Icons, Alpine.js, SweetAlert2, Chart.js) --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: #334155;
            background: #eef7f5;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        [x-cloak] {
            display: none !important;
        }

        .microseed-layout {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background: #eef7f5;
        }

        .microseed-layout::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 90% 15%, rgba(59, 180, 156, 0.16), transparent 34%),
                linear-gradient(180deg, #f8fafc 0%, #eef7f5 55%, #e4f4ef 100%);
        }

        .microseed-auth-shell {
            position: relative;
            z-index: 1;
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background: transparent;
        }

        .microseed-content-shell {
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: transparent;
        }

        .system-main-content {
            min-width: 0;
            min-height: 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: transparent;
            will-change: scroll-position;
        }

        .system-page-header {
            position: sticky;
            top: 0;
            z-index: 30;
            border-bottom: 1px solid rgba(203, 213, 225, 0.75);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .system-page-main {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            background: transparent;
        }

        .microseed-guest-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            background: transparent;
        }

        .microseed-reportes-layer {
            position: relative;
            z-index: 2;
        }

        .system-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(100, 116, 139, 0.28) transparent;
        }

        .system-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .system-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .system-scrollbar::-webkit-scrollbar-thumb {
            border-radius: 9999px;
            background: rgba(100, 116, 139, 0.26);
        }

        .system-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.42);
        }


        /* =========================================================
           ALERTAS GLOBALES MICROSEED CONTROL
        ========================================================== */

        .msc-alert-popup {
            width: 365px !important;
            max-width: calc(100% - 32px) !important;
            padding: 26px 28px 24px !important;
            border: 1px solid rgba(59, 180, 156, .20) !important;
            border-radius: 24px !important;
            background: #ffffff !important;
            box-shadow: 0 25px 65px rgba(20, 43, 57, .23) !important;
            font-family: inherit !important;
        }

        .msc-alert-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .msc-alert-custom-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
        }

        .msc-alert-custom-icon svg {
            width: 25px;
            height: 25px;
        }

        /* ÉXITO */
        .msc-alert-custom-icon.success {
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, .28);
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
        }

        /* ERROR */
        .msc-alert-custom-icon.error {
            color: #b45353;
            border: 1px solid rgba(185, 75, 75, .18);
            background: linear-gradient(145deg, #fff7f7, #fceeee);
        }

        /* ADVERTENCIA */
        .msc-alert-custom-icon.warning {
            color: #9a6700;
            border: 1px solid rgba(217, 164, 65, .25);
            background: linear-gradient(145deg, #fffdf5, #fbf3dd);
        }

        /* INFORMACIÓN */
        .msc-alert-custom-icon.info {
            color: #1c607a;
            border: 1px solid rgba(28, 96, 122, .18);
            background: linear-gradient(145deg, #f2f8fa, #e7f2f5);
        }

        /* PREGUNTA */
        .msc-alert-custom-icon.question {
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, .28);
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
        }

        /* ELIMINAR */
        .msc-alert-custom-icon.delete {
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, .28);
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
        }

        /* EDITAR */
        .msc-alert-custom-icon.edit {
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, .28);
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
        }

        /* CREAR */
        .msc-alert-custom-icon.create {
            color: #1c607a;
            border: 1px solid rgba(59, 180, 156, .28);
            background: linear-gradient(145deg, #f0fafa, #e5f5f2);
        }

        .msc-alert-custom-title {
            margin: 0;
            padding: 0;
            color: #475569;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.3;
            text-align: center;
        }

        .msc-alert-custom-text {
            max-width: 290px;
            margin: 8px auto 0;
            padding: 0;
            color: #64748b;
            font-size: 13.5px;
            font-weight: 500;
            line-height: 20px;
            text-align: center;
        }

        .msc-alert-actions {
            margin: 23px 0 0 !important;
            gap: 14px !important;
        }

        /* BOTÓN CONFIRMAR */
        .msc-alert-confirm {
            min-width: 135px !important;
            margin: 0 !important;
            padding: 10px 20px !important;
            border: 0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            color: #475569 !important;
            font-family: inherit !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            box-shadow: 0 9px 28px rgba(28, 96, 122, .14) !important;
            transition: transform .18s ease, box-shadow .18s ease, color .18s ease !important;
        }

        .msc-alert-confirm:hover {
            transform: translateY(-1px) !important;
            color: #1c607a !important;
            box-shadow: 0 12px 30px rgba(28, 96, 122, .19) !important;
        }

        .msc-alert-confirm:focus {
            outline: none !important;
            box-shadow:
                0 0 0 4px rgba(59, 180, 156, .12),
                0 9px 28px rgba(28, 96, 122, .14) !important;
        }

        /* BOTÓN CANCELAR */
        .msc-alert-cancel {
            min-width: 120px !important;
            margin: 0 !important;
            padding: 10px 18px !important;
            border: 0 !important;
            border-radius: 12px !important;
            background: transparent !important;
            color: #64748b !important;
            font-family: inherit !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: background .18s ease, color .18s ease !important;
        }

        .msc-alert-cancel:hover {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        /* ERRORES DE VALIDACIÓN */
        .msc-validation-list {
            max-width: 290px;
            margin: 12px auto 0;
            padding-left: 20px;
            color: #64748b;
            font-size: 13px;
            line-height: 20px;
            text-align: left;
        }

        .msc-validation-list li {
            margin-bottom: 5px;
        }

        .msc-validation-list li:last-child {
            margin-bottom: 0;
        }

        /* ANIMACIÓN PEQUEÑA COMO CERRAR SESIÓN */
        .msc-alert-show {
            animation: mscAlertShow .20s ease-out;
        }

        .msc-alert-hide {
            animation: mscAlertHide .15s ease-in forwards;
        }

        @keyframes mscAlertShow {
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes mscAlertHide {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(.95);
            }
        }

        @media (max-width: 480px) {
            .msc-alert-popup {
                width: calc(100% - 28px) !important;
                padding: 24px 20px 22px !important;
            }

            .msc-alert-actions {
                width: 100% !important;
                flex-direction: column-reverse !important;
                gap: 8px !important;
            }

            .msc-alert-confirm,
            .msc-alert-cancel {
                width: 100% !important;
            }
        }
    </style>
</head>


<body class="antialiased">

<div
    class="microseed-layout"
    x-data="{
        showMicroclimaModal: false,
        showBiologicoModal: false
    }"
    @open-microclima-modal.window="showMicroclimaModal = true"
    @open-biologico-modal.window="showBiologicoModal = true"
>

    {{-- =========================================================
        USUARIO AUTENTICADO
    ========================================================== --}}

    @auth

        <div class="microseed-auth-shell">

            @include('layouts.sidebar')

            <div class="microseed-content-shell">

                @include('layouts.navigation')

                <div class="system-main-content system-scrollbar">

                    @isset($header)
                        <header class="system-page-header">
                            <div class="w-full px-6 py-5 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="system-page-main">
                        {{ $slot }}
                    </main>

                </div>

            </div>

        </div>


        {{-- =========================================================
            INVITADO / LOGIN
        ========================================================== --}}

    @else

        <main class="microseed-guest-shell system-scrollbar">
            {{ $slot }}
        </main>

    @endauth


    {{-- =========================================================
        MODALES DE REPORTES
    ========================================================== --}}

    <div class="microseed-reportes-layer">
        @includeIf('layouts.reportes-modales')
    </div>

</div>


<script>
    /* =============================================================
       ESCAPAR HTML
    ============================================================== */

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /* =============================================================
       ICONOS GLOBALES
    ============================================================== */

    function microseedIcon(type) {
        const icons = {

            success: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M5 13l4 4L19 7"/>
                </svg>
            `,

            error: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            `,

            warning: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.3 3.6L2.6 17a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z"/>
                </svg>
            `,

            info: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0-8h.01"/>
                </svg>
            `,

            question: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.1 9a3 3 0 115.7 1.3c-.8 1.1-2.8 1.4-2.8 3.2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17h.01"/>
                </svg>
            `,

            delete: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16"/>
                </svg>
            `,

            edit: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            `,

            create: `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            `
        };

        return icons[type] ?? icons.info;
    }


    /* =============================================================
       ALERTA GLOBAL MICROSEED
    ============================================================== */

    window.microseedAlert = function(type, title, message) {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 aún no está disponible.');
            return;
        }

        return Swal.fire({

            html: `
                <div>
                    <div class="msc-alert-custom-icon ${type}">
                        ${microseedIcon(type)}
                    </div>

                    <h3 class="msc-alert-custom-title">
                        ${escapeHtml(title)}
                    </h3>

                    <p class="msc-alert-custom-text">
                        ${escapeHtml(message)}
                    </p>
                </div>
            `,

            showConfirmButton: true,
            showCancelButton: false,

            confirmButtonText: 'Aceptar',

            buttonsStyling: false,

            backdrop: 'rgba(39, 52, 65, .46)',

            allowOutsideClick: true,
            allowEscapeKey: true,

            customClass: {
                popup: 'msc-alert-popup',
                htmlContainer: 'msc-alert-content',
                actions: 'msc-alert-actions',
                confirmButton: 'msc-alert-confirm'
            },

            showClass: {
                popup: 'msc-alert-show'
            },

            hideClass: {
                popup: 'msc-alert-hide'
            }
        });
    };


    /* =============================================================
       CONFIRMACIÓN GLOBAL MICROSEED
    ============================================================== */

    window.microseedConfirm = function(options = {}) {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 aún no está disponible.');
            return;
        }

        const type = options.type ?? 'question';

        const title =
            options.title ??
            '¿Deseas continuar?';

        const message =
            options.message ??
            'Confirma que deseas realizar esta acción.';

        const confirmText =
            options.confirmText ??
            'Sí, continuar';

        const cancelText =
            options.cancelText ??
            'No, cancelar';


        return Swal.fire({

            html: `
                <div>
                    <div class="msc-alert-custom-icon ${type}">
                        ${microseedIcon(type)}
                    </div>

                    <h3 class="msc-alert-custom-title">
                        ${escapeHtml(title)}
                    </h3>

                    <p class="msc-alert-custom-text">
                        ${escapeHtml(message)}
                    </p>
                </div>
            `,

            showConfirmButton: true,
            showCancelButton: true,

            confirmButtonText: confirmText,
            cancelButtonText: cancelText,

            buttonsStyling: false,

            backdrop: 'rgba(39, 52, 65, .46)',

            allowOutsideClick: true,
            allowEscapeKey: true,

            reverseButtons: false,

            customClass: {
                popup: 'msc-alert-popup',
                htmlContainer: 'msc-alert-content',
                actions: 'msc-alert-actions',
                confirmButton: 'msc-alert-confirm',
                cancelButton: 'msc-alert-cancel'
            },

            showClass: {
                popup: 'msc-alert-show'
            },

            hideClass: {
                popup: 'msc-alert-hide'
            }
        });
    };


    /* =============================================================
       CONFIRMACIÓN CREAR
    ============================================================== */

    window.microseedConfirmCreate = function(message = 'Se guardará la información ingresada en el sistema.') {
        return microseedConfirm({
            type: 'create',
            title: '¿Deseas registrar este elemento?',
            message: message,
            confirmText: 'Sí, registrar',
            cancelText: 'No, cancelar'
        });
    };


    /* =============================================================
       CONFIRMACIÓN EDITAR
    ============================================================== */

    window.microseedConfirmEdit = function(message = 'Se actualizará la información de este registro.') {
        return microseedConfirm({
            type: 'edit',
            title: '¿Deseas guardar los cambios?',
            message: message,
            confirmText: 'Sí, actualizar',
            cancelText: 'No, cancelar'
        });
    };


    /* =============================================================
       CONFIRMACIÓN ELIMINAR
    ============================================================== */

    window.microseedConfirmDelete = function(message = 'Esta acción eliminará permanentemente la información y no podrá deshacerse.') {
        return microseedConfirm({
            type: 'delete',
            title: '¿Deseas eliminar este registro?',
            message: message,
            confirmText: 'Sí, eliminar',
            cancelText: 'No, cancelar'
        });
    };


    /* =============================================================
       EVENTOS DE SESIÓN Y VALIDACIONES DE LARAVEL
    ============================================================== */

    document.addEventListener('DOMContentLoaded', function() {

        @if(session('success'))
        microseedAlert(
            'success',
            '¡Operación completada!',
            @json(session('success'))
        );
        @endif

        @if(session('error'))
        microseedAlert(
            'error',
            'Ocurrió un problema',
            @json(session('error'))
        );
        @endif

        @if(session('warning'))
        microseedAlert(
            'warning',
            'Atención',
            @json(session('warning'))
        );
        @endif

        @if(session('info'))
        microseedAlert(
            'info',
            'Información',
            @json(session('info'))
        );
        @endif

        @if(session('status'))
        microseedAlert(
            'info',
            'Información',
            @json(session('status'))
        );
        @endif

        @if($errors->any())
        const validationErrors = @json($errors->all());

        const validationItems = validationErrors
            .map(function(error) {
                return `<li>${escapeHtml(error)}</li>`;
            })
            .join('');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                html: `
                    <div>
                        <div class="msc-alert-custom-icon warning">
                            ${microseedIcon('warning')}
                        </div>

                        <h3 class="msc-alert-custom-title">
                            Revisa la información
                        </h3>

                        <p class="msc-alert-custom-text">
                            Corrige los siguientes campos para continuar.
                        </p>

                        <ul class="msc-validation-list">
                            ${validationItems}
                        </ul>
                    </div>
                `,

                showConfirmButton: true,
                showCancelButton: false,

                confirmButtonText: 'Aceptar',

                buttonsStyling: false,

                backdrop: 'rgba(39, 52, 65, .46)',

                allowOutsideClick: true,
                allowEscapeKey: true,

                customClass: {
                    popup: 'msc-alert-popup',
                    htmlContainer: 'msc-alert-content',
                    actions: 'msc-alert-actions',
                    confirmButton: 'msc-alert-confirm'
                },

                showClass: {
                    popup: 'msc-alert-show'
                },

                hideClass: {
                    popup: 'msc-alert-hide'
                }
            });
        }
        @endif

    });
</script>

</body>
</html>
