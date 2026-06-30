<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Microseed Control') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <script
        defer
        src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"
    ></script>

    <script
        defer
        src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            font-family:
                'Instrument Sans',
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                'Segoe UI',
                sans-serif;
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
                radial-gradient(
                    circle at 90% 15%,
                    rgba(59, 180, 156, 0.16),
                    transparent 34%
                ),
                linear-gradient(
                    180deg,
                    #f8fafc 0%,
                    #eef7f5 55%,
                    #e4f4ef 100%
                );
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
    @else
        <main class="microseed-guest-shell system-scrollbar">
            {{ $slot }}
        </main>
    @endauth

    <div class="microseed-reportes-layer">
        @includeIf('layouts.reportes-modales')
    </div>
</div>

<script>
    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: @json(session('success')),
        confirmButtonColor: '#39b39f',
        timer: 3000,
        timerProgressBar: true,
        width: '20em',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'text-sm'
        }
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        confirmButtonColor: '#1f6f86',
        width: '20em',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'text-sm'
        }
    });
    @endif

    @if($errors->any())
    const validationErrors = @json($errors->all());

    Swal.fire({
        icon: 'error',
        title: 'Revisa la información',
        html: `
                    <div style="text-align:left;">
                        <ul style="margin:0;padding-left:20px;">
                            ${validationErrors
            .map(function(error) {
                return '<li>' + escapeHtml(error) + '</li>';
            })
            .join('')}
                        </ul>
                    </div>
                `,
        confirmButtonColor: '#1f6f86',
        width: '24em',
        confirmButtonText: 'Aceptar'
    });
    @endif
</script>
</body>
</html>
