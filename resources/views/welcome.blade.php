<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Microseed Control - Monitoreo de Microclima</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeLeft {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeRight {
            0% {
                opacity: 0;
                transform: translateX(35px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomSoft {
            0% {
                opacity: 0;
                transform: scale(0.92);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 rgba(59, 180, 156, 0);
            }
            50% {
                box-shadow: 0 0 30px rgba(59, 180, 156, 0.22);
            }
        }

        @keyframes shine {
            0% {
                transform: translateX(-140%) skewX(-20deg);
            }
            100% {
                transform: translateX(220%) skewX(-20deg);
            }
        }

        @keyframes drift {
            0% {
                transform: translateY(0px) translateX(0px);
            }
            50% {
                transform: translateY(-10px) translateX(8px);
            }
            100% {
                transform: translateY(0px) translateX(0px);
            }
        }

        @keyframes gridMove {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .bg-brand-gradient {
            background: linear-gradient(135deg, #1c607a 0%, #3bb49c 100%);
        }

        .main-card {
            animation: zoomSoft 0.9s ease-out;
        }

        .header-enter {
            opacity: 0;
            animation: fadeUp 0.8s ease-out forwards;
        }

        .hero-badge {
            opacity: 0;
            animation: zoomSoft 0.8s ease-out 0.2s forwards;
        }

        .hero-title-line-1 {
            opacity: 0;
            animation: fadeLeft 0.9s ease-out 0.35s forwards;
        }

        .hero-title-line-2 {
            opacity: 0;
            animation: fadeLeft 0.9s ease-out 0.55s forwards;
        }

        .hero-text {
            opacity: 0;
            animation: fadeUp 0.9s ease-out 0.75s forwards;
        }

        .feature-item {
            opacity: 0;
            animation: fadeLeft 0.8s ease-out forwards;
            transition: transform 0.25s ease, background-color 0.25s ease;
            border-radius: 14px;
            padding: 6px 8px;
        }

        .feature-item:nth-child(1) { animation-delay: 0.95s; }
        .feature-item:nth-child(2) { animation-delay: 1.10s; }
        .feature-item:nth-child(3) { animation-delay: 1.25s; }

        .feature-item:hover {
            transform: translateX(8px);
            background: rgba(28, 96, 122, 0.04);
        }

        .hero-actions {
            opacity: 0;
            animation: fadeUp 0.8s ease-out 1.35s forwards;
        }

        .btn-hero {
            position: relative;
            overflow: hidden;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        .btn-hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,0.35),
                transparent
            );
            transform: skewX(-20deg);
        }

        .btn-hero:hover::before {
            animation: shine 0.9s ease;
        }

        .side-panel {
            opacity: 0;
            animation: fadeRight 1s ease-out 0.35s forwards;
        }

        .logo-wrap {
            position: relative;
        }

        .logo-wrap::after {
            content: "";
            position: absolute;
            inset: -20px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
            filter: blur(25px);
            z-index: -1;
            animation: drift 5s ease-in-out infinite;
        }

        .project-card {
            opacity: 0;
            animation: fadeUp 0.9s ease-out 1.05s forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18);
            background: rgba(28, 96, 122, 0.38);
        }

        .soft-orb {
            animation: drift 6s ease-in-out infinite;
        }

        .grid-animate {
            animation: gridMove 7s ease-in-out infinite;
        }

        .brand-hover {
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .brand-hover:hover {
            transform: translateY(-2px) scale(1.04);
            filter: drop-shadow(0 8px 18px rgba(28, 96, 122, 0.2));
        }

        .nav-link-pro {
            transition: all 0.28s ease;
        }

        .nav-link-pro:hover {
            transform: translateY(-2px);
        }

        .footer-enter {
            opacity: 0;
            animation: fadeUp 0.8s ease-out 1.55s forwards;
        }
    </style>
</head>
<body class="bg-[#f0f6f6] text-gray-800 flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col font-sans">

<header class="header-enter w-full lg:max-w-6xl text-sm mb-6 flex justify-between items-center">
    <div class="brand-hover flex items-center gap-4 font-bold text-[#1c607a] text-2xl cursor-pointer">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-16 h-16 lg:w-20 lg:h-20 drop-shadow-md object-contain">
        Microseed Control
    </div>

    @if (Route::has('login'))
        <nav class="flex items-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="nav-link-pro inline-block px-5 py-2 border-2 border-[#3bb49c] text-[#1c607a] rounded-md text-sm font-bold hover:bg-[#3bb49c] hover:text-white transition-all duration-300 shadow-sm">
                    Ir a la Bitácora Electrónica
                </a>
            @else
                <a href="{{ route('login') }}" class="nav-link-pro inline-block px-4 py-2 text-[#1c607a] hover:text-[#3bb49c] font-bold transition-colors">
                    Login
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="nav-link-pro inline-block px-5 py-2 bg-[#1c607a] text-white rounded-md text-sm font-bold hover:bg-[#3bb49c] shadow-md transition-all duration-300 hover:-translate-y-0.5">
                        Registrarse
                    </a>
                @endif
            @endauth
        </nav>
    @endif
</header>

<div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-700 lg:grow">
    <main class="main-card flex w-full flex-col-reverse lg:max-w-6xl lg:flex-row shadow-2xl rounded-2xl overflow-hidden bg-white border border-gray-100">

        <div class="flex-1 p-8 pb-12 lg:p-16 flex flex-col justify-center relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-full opacity-[0.02] pointer-events-none" style="background-image: url('{{ asset('img/logo.png') }}'); background-repeat: no-repeat; background-position: center; background-size: 80%;"></div>

            <h1 class="mb-5 text-4xl font-extrabold text-[#1c607a] leading-tight lg:text-5xl relative z-10">
                <span class="hero-title-line-1 block">Control microclimático para la</span>
                <span class="hero-title-line-2 block text-[#3bb49c]">supervivencia forestal.</span>
            </h1>

            <p class="hero-text mb-8 text-gray-600 leading-relaxed text-lg relative z-10 font-medium">
                Centraliza la gestión telemétrica de tu incubadora de semillas. Mitiga la pérdida de especímenes mediante la automatización de variables ambientales y asegura la viabilidad germinativa aislando las especies de fluctuaciones externas.
            </p>

            <ul class="flex flex-col mb-10 space-y-4 relative z-10">
                <li class="feature-item flex items-start gap-3 text-gray-700 font-semibold group">
                    <div class="bg-[#3bb49c]/20 rounded-full p-1.5 text-[#3bb49c] mt-0.5 group-hover:bg-[#3bb49c] group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Registro continuo de temperatura y humedad en base de datos.</span>
                </li>
                <li class="feature-item flex items-start gap-3 text-gray-700 font-semibold group">
                    <div class="bg-[#3bb49c]/20 rounded-full p-1.5 text-[#3bb49c] mt-0.5 group-hover:bg-[#3bb49c] group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Reducción de dependencia manual para evitar estrés biológico.</span>
                </li>
                <li class="feature-item flex items-start gap-3 text-gray-700 font-semibold group">
                    <div class="bg-[#3bb49c]/20 rounded-full p-1.5 text-[#3bb49c] mt-0.5 group-hover:bg-[#3bb49c] group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span>Generación automática de reportes PDF para análisis metrológico.</span>
                </li>
            </ul>

            <div class="hero-actions relative z-10 flex justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero inline-block px-8 py-3.5 bg-[#3bb49c] text-white font-bold rounded-lg hover:bg-[#1c607a] transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                        Panel de Control
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero inline-block px-8 py-3.5 bg-[#3bb49c] text-white font-bold rounded-lg hover:bg-[#1c607a] transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                        Iniciar Monitoreo
                    </a>
                @endauth
            </div>
        </div>

        <div class="side-panel bg-brand-gradient relative lg:w-[480px] shrink-0 flex flex-col items-center justify-center p-12 text-white overflow-hidden shadow-inner">

            <div class="logo-wrap relative z-10 animate-float">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Microseed" class="w-[300px] lg:w-[420px] h-auto drop-shadow-[0_25px_35px_rgba(0,0,0,0.4)] object-contain">
            </div>

            <div class="soft-orb absolute top-0 right-0 w-72 h-72 bg-white opacity-5 rounded-full -mr-24 -mt-24 blur-3xl"></div>
            <div class="soft-orb absolute bottom-0 left-0 w-64 h-64 bg-[#3bb49c] opacity-50 rounded-full -ml-20 -mb-20 blur-3xl"></div>

            <div class="grid-animate absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>
    </main>
</div>

<footer class="footer-enter mt-8 flex flex-col items-center text-[#1c607a]/70 text-sm font-semibold">
    <p>&copy; {{ date('Y') }} - Sistema web para la optimización del microclima en incubadoras</p>

</footer>

</body>
</html>
