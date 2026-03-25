<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Germinación de Semillas</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Personalización de colores temáticos */
            .bg-seed-green { background-color: #f0fdf4; } /* Verde muy claro */
            .text-seed-dark { color: #166534; } /* Verde bosque para textos */
            .border-seed-light { border-color: #dcfce7; }
            .accent-seed { color: #22c55e; } /* Verde vibrante para links */
        </style>
    </head>
    <body class="bg-seed-green text-seed-dark flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 border-seed-dark border text-seed-dark rounded-sm text-sm font-medium hover:bg-green-100 transition-colors">
                            Ir al Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 text-seed-dark hover:underline font-medium">
                            Ingresar
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 bg-green-600 text-white rounded-sm text-sm font-medium hover:bg-green-700 shadow-sm transition-all">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row shadow-2xl rounded-lg overflow-hidden border border-green-200">
                
                <div class="flex-1 p-6 pb-12 lg:p-20 bg-white">
                    <div class="mb-6">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Proyecto Botánico</span>
                    </div>
                    
                    <h1 class="mb-4 text-3xl font-bold text-gray-900 lg:text-4xl">Sistema de Control de Germinación</h1>
                    <p class="mb-6 text-gray-600 leading-relaxed">
                        Gestiona el ciclo de vida de tus semillas, desde la siembra hasta el primer brote. Monitorea tiempos, variedades y optimiza tu producción de forma digital.
                    </p>
                    
                    <ul class="flex flex-col mb-8 space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="mt-1 bg-green-500 rounded-full p-1">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>Registro detallado de variedades de semillas.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-1 bg-green-500 rounded-full p-1">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>Seguimiento de fechas de brote y éxito.</span>
                        </li>
                    </ul>

                    <a href="" class="inline-block px-8 py-3 bg-green-600 text-white font-bold rounded-md hover:bg-green-700 transition-all shadow-md">
                        Comenzar Cultivo
                    </a>
                </div>

                <div class="bg-green-600 relative lg:w-[400px] shrink-0 flex items-center justify-center p-12 text-white">
                    <div class="text-center">
                        <svg class="w-32 h-32 mx-auto mb-4 opacity-80" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,3.05 16.21,4.57 14,8.31C13,8 12,8 11,8C11,10.29 11.41,12.26 12.09,13.91C10.85,14.08 9.53,14.24 8,14.24C8,14.24 8,14.24 8,14.24C12,14.24 11,12 11,12C11,12 11,12 11,12C11,12 11,12 11,12C11,12 12,14.24 8,14.24C8,14.24 8,14.24 8,14.24C4,14.24 3,11 3,11C3,11 3,11 3,11C6,11 6,8 11,8C11,8 12,8 13,8.31C14,8.31 15,8 17,8Z" />
                        </svg>
                        <h2 class="text-xl font-semibold italic">"Cada semilla es una promesa de vida."</h2>
                    </div>
                    
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-green-500 rounded-tl-full opacity-50"></div>
                </div>
            </main>
        </div>

        <footer class="mt-8 text-gray-400 text-xs">
            &copy; {{ date('Y') }} - Software de Germinación de Semillas
        </footer>

    </body>
</html>