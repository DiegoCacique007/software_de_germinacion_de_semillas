<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Germinación Control System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html, body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Instrument Sans', sans-serif;
            overflow-x: hidden;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="antialiased bg-slate-100 w-full min-h-screen overflow-x-hidden">
<div class="w-full min-h-screen bg-slate-100">
    @isset($header)
        <header class="bg-white shadow-sm w-full">
            <div class="w-full px-6 py-6">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="w-full min-h-screen">
        {{ $slot }}
    </main>
</div>
</body>
</html>
