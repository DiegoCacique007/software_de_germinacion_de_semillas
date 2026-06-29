<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Microseed Control') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800"
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
            font-family: 'Instrument Sans', sans-serif;
        }

        * {
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

        .system-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(100, 116, 139, 0.34) transparent;
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
            background: rgba(100, 116, 139, 0.3);
        }

        .system-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.5);
        }

        .system-main-content {
            min-width: 0;
            min-height: 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: #f1f5f9;
        }
    </style>
</head>

<body class="bg-slate-100 antialiased">

<div
    class="h-screen w-full overflow-hidden bg-slate-100"
    x-data="{
            showMicroclimaModal: false,
            showBiologicoModal: false
        }"
    @open-microclima-modal.window="showMicroclimaModal = true"
    @open-biologico-modal.window="showBiologicoModal = true"
>
    @auth
        <div class="flex h-screen w-full overflow-hidden bg-slate-100">

            @include('layouts.sidebar')

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">

                @include('layouts.navigation')

                <div class="system-main-content system-scrollbar">

                    @isset($header)
                        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur-xl">
                            <div class="w-full px-6 py-5 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="m-0 min-h-full w-full p-0">
                        {{ $slot }}
                    </main>

                </div>
            </div>
        </div>
    @else
        <main class="system-scrollbar h-screen w-full overflow-y-auto overflow-x-hidden bg-slate-100">
            {{ $slot }}
        </main>
    @endauth

    @includeIf('layouts.reportes-modales')
</div>

<script>
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
    Swal.fire({
        icon: 'error',
        title: 'Revisa la información',
        html: `
                    <div style="text-align:left;">
                        <ul style="margin:0;padding-left:20px;">
                            @foreach($errors->all() as $error)
        <li>{{ addslashes($error) }}</li>
                            @endforeach
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
