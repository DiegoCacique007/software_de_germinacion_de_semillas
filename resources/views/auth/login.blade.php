@extends('layouts.guest')

@section('content')
    <!-- Fuentes para el diseño integrado -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:500,700|playfair-display:400,400i,700,800" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --brand-dark-blue: #1f6f86;
            --brand-teal: #2f9aa0;
            --brand-green: #39b39f;
            --brand-soft: #eef8f7;
        }

        /* Fondo integrado y bloqueo de scroll en escritorio */
        body {
            margin: 0;
            height: 100vh;
            font-family: 'Instrument Sans', sans-serif;
            overflow: hidden;
            position: relative;
            background:
                radial-gradient(circle at top left, rgba(59, 180, 156, 0.35), transparent 35%),
                radial-gradient(circle at bottom right, rgba(28, 96, 122, 0.45), transparent 40%),
                linear-gradient(135deg, #f0f6f6 0%, #d8f3ee 45%, #9fd8cf 100%);
        }

        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2vh 2vw;
            position: relative;
            z-index: 10;
            box-sizing: border-box;
        }

        /* AJUSTE DE CONTENEDOR PRINCIPAL: Más angosto y un poco menos alto */
        .login-card {
            width: 100%;
            max-width: 1000px; /* Antes 1300px */
            height: 85vh;      /* Antes 92vh */
            max-height: 700px; /* Antes 850px */
            border: none;
            border-radius: 24px; /* Ligeramente menor para mantener proporción */
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(20, 60, 70, 0.25);
            animation: fadeUp 0.9s ease;
            background: #fff;
            position: relative;
            z-index: 15;
            display: flex;
            flex-direction: column;
        }

        .login-card .row {
            flex: 1;
            height: 100%;
            margin: 0;
        }

        .login-card .col-lg-6 {
            display: flex;
            padding: 0;
        }

        .login-left,
        .login-right {
            width: 100%;
            height: 100%;
        }

        /* Panel Izquierdo */
        .login-left {
            position: relative;
            background: linear-gradient(135deg, #236f87 0%, #2f8f97 50%, #39b39f 100%);
            color: white;
            padding: 20px 30px 30px 30px; /* Espaciado ajustado */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .login-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.16) 1.1px, transparent 0);
            background-size: 30px 30px;
            opacity: 0.35;
            pointer-events: none;
        }

        .login-left::after {
            content: "";
            position: absolute;
            width: 300px; /* Reducido */
            height: 300px; /* Reducido */
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -100px;
            left: -80px;
            filter: blur(12px);
            animation: floatOrb 8s ease-in-out infinite;
        }

        /* Integración del Logo y Texto Curvo Ajustados */
        .logo-wrapper {
            position: relative;
            width: 100%;
            max-width: 500px; /* Reducido */
            height: 320px; /* Reducido */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            z-index: 2;
            animation: fadeLeft 1.05s ease;
        }

        .curved-text-svg {
            position: absolute;
            top: 0px;
            left: 50%;
            transform: translateX(-50%);
            width: 110%;
            height: auto;
            overflow: visible;
            pointer-events: none;
            z-index: 20;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.25));
        }

        .curved-text-svg text {
            fill: #ffffff;
            font-family: 'Playfair Display', serif;
            font-size: 52px; /* Reducido de 68px */
            font-weight: 800;
            letter-spacing: 4px; /* Reducido */
            text-transform: uppercase;
        }

        .logo-main {
            width: 260px; /* Reducido de 430px */
            height: 260px; /* Reducido de 430px */
            object-fit: contain;
            display: block;
            margin-top: 30px; /* Ajustado */
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.35));
            z-index: 10;
            animation: floatLogo 4.5s ease-in-out infinite;
        }

        /* TEXTO DESCRIPTIVO (Ajustado) */
        .left-text {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.15rem; /* Reducido de 1.4rem */
            line-height: 1.5;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.95);
            max-width: 400px; /* Reducido */
            margin-top: -15px; /* Ajustado */
            margin-bottom: 10px;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            animation: fadeLeft 1.15s ease;
        }

        /* Panel Derecho (Formulario) */
        .login-right {
            background: #ffffff;
            padding: 30px 40px; /* Reducido de 40px 50px */
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeRight 1s ease;
        }

        .form-area {
            width: 100%;
            max-width: 380px; /* Reducido de 450px */
        }

        .form-header {
            text-align: center;
            margin-bottom: 24px; /* Reducido */
            animation: fadeUp 1s ease;
        }

        .form-title {
            color: var(--brand-dark-blue);
            font-size: 1.8rem; /* Reducido de 2.2rem */
            font-weight: 800;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
        }

        .form-subtitle {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 0.95rem; /* Reducido */
        }

        .status-box {
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            animation: fadeUp 0.7s ease;
        }

        .form-label {
            color: var(--brand-dark-blue);
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .form-control {
            border-radius: 12px;
            min-height: 48px; /* Reducido de 55px */
            border: 1px solid #d9e4e8;
            padding-left: 15px;
            transition: all 0.25s ease;
            box-shadow: none;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 0.22rem rgba(57, 179, 159, 0.18);
            transform: translateY(-1px);
        }

        .password-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-toggle-password {
            width: 55px; /* Reducido */
            min-height: 48px; /* Reducido */
            border-radius: 0 12px 12px 0;
            border: 1px solid #d9e4e8;
            border-left: none;
            color: var(--brand-dark-blue);
            background: #f7fbfb;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .btn-toggle-password:hover {
            background: var(--brand-soft);
            color: var(--brand-green);
        }

        .btn-toggle-password svg {
            width: 20px; /* Reducido */
            height: 20px; /* Reducido */
            stroke-width: 2.2;
        }

        .remember-row {
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem; /* Ajustado */
        }

        .form-check-input:checked {
            background-color: var(--brand-dark-blue);
            border-color: var(--brand-dark-blue);
        }

        .forgot-link {
            color: var(--brand-dark-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.25s ease;
            font-size: 0.9rem;
        }

        .forgot-link:hover {
            color: var(--brand-green);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-green) 100%);
            border: none;
            color: white;
            font-weight: 700;
            font-family: 'Instrument Sans', sans-serif;
            letter-spacing: 0.5px;
            font-size: 1.05rem; /* Reducido */
            min-height: 50px; /* Reducido de 55px */
            border-radius: 12px; /* Ajustado */
            width: 100%;
            box-shadow: 0 10px 20px rgba(28, 96, 122, 0.18);
            transition: all 0.28s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(28, 96, 122, 0.25);
            color: white;
        }

        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
            transform: skewX(-20deg);
        }

        .btn-login:hover::before {
            animation: shine 0.9s ease;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.85rem; /* Ajustado */
            margin-top: 5px;
        }

        /* Semillas Animadas */
        .seed-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(28, 96, 122, 0.25);
            border-radius: 50%;
            z-index: 1;
            pointer-events: none;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeLeft {
            from { opacity: 0; transform: translateX(-25px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeRight {
            from { opacity: 0; transform: translateX(25px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-10px) translateX(8px); }
        }

        @keyframes shine {
            from { transform: translateX(-140%) skewX(-20deg); }
            to { transform: translateX(260%) skewX(-20deg); }
        }

        /* Responsive - Pantallas tipo Tablet */
        @media (max-width: 991.98px) {
            body {
                overflow-y: auto;
                height: auto;
                min-height: 100vh;
            }
            .login-wrapper {
                height: auto;
                padding: 20px 15px;
            }
            .login-card {
                height: auto;
                max-height: none;
                max-width: 600px; /* Ajuste para que no quede estirado en vertical */
            }
            .login-card .col-lg-6 {
                flex-direction: column;
            }
            .logo-wrapper {
                height: 280px;
            }
            .curved-text-svg text {
                font-size: 46px;
            }
            .logo-main {
                width: 220px;
                height: 220px;
            }
        }

        /* Responsive - Pantallas de Celular (Smartphones) */
        @media (max-width: 767.98px) {
            .logo-wrapper {
                height: 220px;
            }
            .curved-text-svg text {
                font-size: 38px;
                letter-spacing: 2px;
            }
            .logo-main {
                width: 160px;
                height: 160px;
                margin-top: 20px;
            }
            .left-text {
                font-size: 1rem;
                margin-top: -10px;
            }
            .login-right {
                padding: 30px 20px;
            }
            .form-title {
                font-size: 1.6rem;
            }
        }

        /* Responsive - Celulares muy pequeños */
        @media (max-width: 480px) {
            .logo-wrapper {
                height: 180px;
            }
            .curved-text-svg text {
                font-size: 32px;
            }
            .logo-main {
                width: 130px;
                height: 130px;
                margin-top: 15px;
            }
        }
    </style>

    <div class="login-wrapper">
        <div class="card login-card">
            <div class="row g-0">

                <!-- Panel Izquierdo Integrado -->
                <div class="col-lg-6">
                    <div class="login-left">

                        <div class="logo-wrapper" id="logoWrapper">
                            <svg class="curved-text-svg" viewBox="0 0 1000 350" id="curvedText">
                                <defs>
                                    <path id="curve" d="M100,300 Q500,10 900,300" />
                                </defs>
                                <text>
                                    <textPath href="#curve" startOffset="50%" text-anchor="middle">
                                        Microseed Control
                                    </textPath>
                                </text>
                            </svg>
                            <img src="{{ asset('img/logo.png') }}" alt="Logo Microseed Control" class="logo-main" id="logoMain">
                        </div>

                        <p class="left-text">
                            Sistema web de control de microclima en prototipo de incubadora de semillas
                        </p>

                    </div>
                </div>

                <!-- Panel Derecho (Formulario) -->
                <div class="col-lg-6">
                    <div class="login-right">
                        <div class="form-area">

                            <div class="form-header">
                                <h2 class="form-title">Iniciar sesión</h2>
                                <p class="form-subtitle">
                                    Ingresa tus credenciales para acceder al panel del sistema.
                                </p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success status-box">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        placeholder="ejemplo@correo.com"
                                    >
                                    @error('email')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>

                                    <div class="input-group password-group">
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            required
                                            autocomplete="current-password"
                                            placeholder="••••••••"
                                        >

                                        <button
                                            type="button"
                                            class="btn btn-toggle-password"
                                            onclick="togglePassword('password', this)"
                                            aria-label="Mostrar contraseña"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.25A3.25 3.25 0 1 0 12 8.75a3.25 3.25 0 0 0 0 6.5z" />
                                            </svg>
                                        </button>
                                    </div>

                                    @error('password')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between remember-row gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                        <label class="form-check-label text-secondary" for="remember_me">
                                            Recordarme
                                        </label>
                                    </div>

                                    @if (Route::has('password.request'))
                                        <a class="forgot-link" href="{{ route('password.request') }}">
                                            ¿Olvidaste tu contraseña?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-login">
                                    Entrar al sistema
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.25A3.25 3.25 0 1 0 12 8.75a3.25 3.25 0 0 0 0 6.5z" />
        </svg>`;

        const eyeOffIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.5A9.7 9.7 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.9 17.9 0 0 1-3.1 3.85" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 6.9C3.85 8.55 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.25 0 2.42-.3 3.47-.78" />
        </svg>`;

        window.togglePassword = function(inputId, button) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                button.innerHTML = eyeOffIcon;
                button.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                input.type = 'password';
                button.innerHTML = eyeIcon;
                button.setAttribute('aria-label', 'Mostrar contraseña');
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const logoWrapper = document.getElementById('logoWrapper');
            const curvedText = document.getElementById('curvedText');

            document.addEventListener('mousemove', (event) => {
                if (!logoWrapper || !curvedText || window.innerWidth <= 991) return;

                const x = (window.innerWidth / 2 - event.clientX) / 45;
                const y = (window.innerHeight / 2 - event.clientY) / 45;

                logoWrapper.style.transform = `translate(${x * -0.2}px, ${y * -0.2}px)`;
                curvedText.style.transform = `translateX(-50%) translate(${x * 0.15}px, ${y * 0.15}px)`;
            });

            function createSeedParticle() {
                const particle = document.createElement('span');
                particle.classList.add('seed-particle');

                const size = Math.random() * 6 + 4; /* Semillas ligeramente más pequeñas */
                const startX = Math.random() * window.innerWidth;
                const duration = Math.random() * 5000 + 6000;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${startX}px`;
                particle.style.bottom = '-20px';

                document.body.appendChild(particle);

                particle.animate([
                    {
                        transform: 'translateY(0) translateX(0) scale(0.8)',
                        opacity: 0
                    },
                    {
                        opacity: 0.65
                    },
                    {
                        transform: `translateY(-${window.innerHeight + 80}px) translateX(${Math.random() * 120 - 60}px) scale(1.15)`,
                        opacity: 0
                    }
                ], {
                    duration: duration,
                    easing: 'linear',
                    fill: 'forwards'
                });

                setTimeout(() => {
                    particle.remove();
                }, duration);
            }

            setInterval(createSeedParticle, 650);
        });

        // Alertas con SweetAlert2
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#39b39f',
            timer: 3000,
            timerProgressBar: true,
            width: '18em',
            customClass: { popup: 'text-sm' },
            confirmButtonText: 'Aceptar'
        });
        @endif

        @if (session('status'))
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: '{{ session('status') }}',
            confirmButtonColor: '#39b39f',
            width: '18em',
            customClass: { popup: 'text-sm' },
            confirmButtonText: 'Aceptar'
        });
        @endif

        @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error de acceso',
            text: 'Las credenciales proporcionadas no son correctas.',
            confirmButtonColor: '#1f6f86',
            width: '18em',
            customClass: { popup: 'text-sm' },
            confirmButtonText: 'Aceptar'
        });
        @endif
    </script>
@endsection
