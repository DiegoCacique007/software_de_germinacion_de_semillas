<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Germinación Control System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    {{-- Assets compilados mediante Vite (Bootstrap 5, Bootstrap Icons, Alpine.js, SweetAlert2) --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Instrument Sans', sans-serif;
            overflow-x: hidden;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(57, 179, 159, 0.18), transparent 32%),
                radial-gradient(circle at bottom right, rgba(31, 111, 134, 0.20), transparent 32%),
                linear-gradient(135deg, #eef8f7 0%, #f8fbfb 45%, #ecfeff 100%);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>

@yield('content')
{{ $slot ?? '' }}

</body>

</html>
