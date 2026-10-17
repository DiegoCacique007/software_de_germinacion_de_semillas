@extends('layouts.guest')

@section('content')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:500,700|playfair-display:400,400i,700,800" rel="stylesheet">

    <style>
        :root {
            --brand-dark-blue: #1f6f86;
            --brand-teal: #2f9aa0;
            --brand-green: #39b39f;
            --brand-soft: #eef8f7;
            --brand-wave-1: #9fd8cf;
            --brand-wave-2: #c9ece6;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            font-family: 'Instrument Sans', sans-serif;
            overflow: hidden;
            background: #ffffff;
        }

        /* =========================================================
           ESTRUCTURA A PANTALLA COMPLETA
        ========================================================= */

        .login-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            display: flex;
            position: relative;
            z-index: 10;
        }

        .login-card {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            border: none;
            border-radius: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .login-card .row {
            flex: 1;
            margin: 0;
        }

        .login-card .row > [class*="col-"] {
            display: flex;
            padding: 0;
        }

        .login-left,
        .login-right {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* División entre paneles: mitad y mitad (50% / 50%) en escritorio */
        @media (min-width: 992px) {
            .login-card .row > .login-col-left,
            .login-card .row > .login-col-right {
                width: 50%;
            }
        }

        /* =========================================================
           PANEL IZQUIERDO
        ========================================================= */

        .login-left {
            position: relative;
            background: linear-gradient(160deg, #236f87 0%, #2f8f97 55%, #39b39f 100%);
            color: white;
            /* El padding derecho deja espacio para el borde ondulado */
            padding: 30px 125px 60px 30px;
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
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -100px;
            left: -80px;
            filter: blur(12px);
            animation: floatOrb 8s ease-in-out infinite;
        }

        /* ---------- LOGO GRANDE ----------
           El tamaño del logo y su separación del texto curvo se calculan
           en % del ancho del bloque, así se mantienen alineados en
           cualquier pantalla. Para agrandar o achicar todo, cambia
           solo el "width" de .logo-wrapper. */

        .logo-wrapper {
            position: relative;
            width: min(100%, 600px, 66vh);
            aspect-ratio: 100 / 94;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            margin: 0 auto;
            z-index: 2;
            animation: fadeLeft 1.05s ease;
        }

        .curved-text-svg {
            position: absolute;
            top: 0;
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
            font-size: 52px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .logo-main {
            width: 72%;
            height: auto;
            aspect-ratio: 1 / 1;
            object-fit: contain;
            display: block;
            margin-top: 22%;
            /* Halo blanco suave + sombra para que el logo resalte */
            filter:
                drop-shadow(0 0 28px rgba(255,255,255,0.35))
                drop-shadow(0 18px 30px rgba(0,0,0,0.35));
            z-index: 10;
            animation: floatLogo 4.5s ease-in-out infinite;
        }

        .left-text {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.2rem;
            line-height: 1.5;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.95);
            max-width: 420px;
            margin-top: 4px;
            margin-bottom: 0;
            text-shadow: 0 3px 6px rgba(0,0,0,0.2);
            animation: fadeLeft 1.15s ease;
        }

        .left-footer {
            position: absolute;
            left: 0;
            right: 90px;
            bottom: 26px;
            margin: 0;
            z-index: 2;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.75);
        }

        /* ---------- Borde ondulado (como en la imagen) ---------- */

        .login-wave {
            position: absolute;
            pointer-events: none;
            z-index: 3;
        }

        .login-wave--vertical {
            top: 0;
            right: -1px;
            width: 210px;
            height: 100%;
        }

        .login-wave--horizontal {
            display: none;
        }

        /* =========================================================
           PANEL DERECHO
        ========================================================= */

        .login-right {
            background: #ffffff;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeRight 1s ease;
        }

        /* Cuadro de credenciales con contorno y fondo en degradado */
        .form-area {
            width: 100%;
            max-width: 450px;
            padding: 44px 40px 34px;
            border-radius: 24px;
            border: 3px solid transparent;
            background:
                /* Interior: blanco que se tiñe suavemente de verde agua hacia abajo */
                linear-gradient(165deg, #ffffff 0%, #ffffff 45%, #eef8f7 80%, #e1f4f0 100%) padding-box,
                    /* Contorno: azul → turquesa → verde → turquesa claro */
                linear-gradient(140deg, #1f6f86 0%, #2f9aa0 35%, #39b39f 65%, #7fd3c4 100%) border-box;
            /* Resplandor en degradado: azul a la izquierda, verde a la derecha */
            box-shadow:
                -18px 22px 48px -18px rgba(31, 111, 134, 0.45),
                18px 26px 52px -18px rgba(57, 179, 159, 0.45);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeUp 1s ease;
        }

        .form-title {
            color: var(--brand-dark-blue);
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 8px;
            font-family: 'Playfair Display', serif;
        }

        .form-subtitle {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .form-label {
            color: var(--brand-dark-blue);
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        /* Campos rellenos, sin borde visible, como en la imagen */
        .form-control {
            border-radius: 12px;
            min-height: 50px;
            border: 1.5px solid transparent;
            background-color: var(--brand-soft);
            padding-left: 16px;
            transition: all 0.25s ease;
            box-shadow: none;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 1rem;
            color: #1d3b44;
        }

        .form-control::placeholder {
            color: #8fa9ad;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--brand-green);
            box-shadow: 0 0 0 0.22rem rgba(57, 179, 159, 0.18);
            transform: translateY(-1px);
        }

        .form-control.is-invalid {
            border-color: #dc6b6b;
            background-color: #fff6f6;
            box-shadow: none;
        }

        .form-control.is-invalid:focus {
            border-color: #dc6b6b;
            box-shadow: 0 0 0 0.22rem rgba(220, 107, 107, 0.12);
        }

        /* ---------- Contraseña + botón del ojo como un solo campo ---------- */

        .password-group {
            border-radius: 12px;
            transition: box-shadow 0.25s ease;
        }

        .password-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
        }

        .password-group .form-control:focus {
            box-shadow: none;
            transform: none;
        }

        .btn-toggle-password {
            width: 55px;
            min-height: 50px;
            border-radius: 0 12px 12px 0;
            border: 1.5px solid transparent;
            border-left: none;
            color: var(--brand-dark-blue);
            background: var(--brand-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .btn-toggle-password:hover {
            color: var(--brand-green);
        }

        .password-group:focus-within {
            box-shadow: 0 0 0 0.22rem rgba(57, 179, 159, 0.18);
        }

        .password-group:focus-within .btn-toggle-password {
            background: #ffffff;
            border-color: var(--brand-green);
        }

        .password-group:has(.is-invalid) .btn-toggle-password {
            background: #fff6f6;
            border-color: #dc6b6b;
        }

        .btn-toggle-password svg {
            width: 20px;
            height: 20px;
            stroke-width: 2.2;
        }

        .remember-row {
            margin-top: 15px;
            margin-bottom: 26px;
            font-size: 0.9rem;
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
            font-size: 1.05rem;
            min-height: 52px;
            border-radius: 12px;
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

        /* Separador y ayuda debajo del botón (como en la imagen) */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 26px 0 10px;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .form-divider::before,
        .form-divider::after {
            content: "";
            flex: 1;
            height: 1px;
        }

        .form-divider::before {
            background: linear-gradient(90deg, transparent, var(--brand-teal));
        }

        .form-divider::after {
            background: linear-gradient(90deg, var(--brand-teal), transparent);
        }

        .form-help {
            margin: 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        /* =========================================================
           PARTÍCULAS (ahora suben dentro del panel izquierdo)
        ========================================================= */

        .seed-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            z-index: 1;
            pointer-events: none;
        }

        /* =========================================================
           ANIMACIONES
        ========================================================= */

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

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        /* Tableta y móvil: el panel de marca pasa arriba */
        @media (max-width: 991.98px) {
            body {
                overflow-y: auto;
            }

            .login-left,
            .login-right {
                min-height: auto;
            }

            .login-left {
                padding: 30px 20px 80px;
            }

            .login-right {
                padding: 20px 20px 50px;
                align-items: flex-start;
            }

            .left-footer {
                display: none;
            }

            .login-wave--vertical {
                display: none;
            }

            .login-wave--horizontal {
                display: block;
                left: 0;
                right: 0;
                bottom: -1px;
                width: 100%;
                height: 80px;
            }

            .logo-wrapper {
                width: min(100%, 400px);
            }
        }

        @media (max-width: 767.98px) {
            .logo-wrapper {
                width: min(100%, 330px);
            }

            .left-text {
                font-size: 1rem;
            }

            .form-title {
                font-size: 1.7rem;
            }

            .form-area {
                padding: 32px 22px 26px;
                border-radius: 20px;
            }
        }

        @media (max-width: 480px) {
            .logo-wrapper {
                width: min(100%, 270px);
            }
        }
    </style>


    <div class="login-wrapper">

        <div class="login-card">

            <div class="row g-0">

                {{-- =====================================================
                    PANEL IZQUIERDO
                ====================================================== --}}

                <div class="col-lg-6 login-col-left">

                    <div class="login-left" id="loginLeft">

                        <div class="logo-wrapper" id="logoWrapper">

                            <svg class="curved-text-svg" viewBox="0 0 1000 350" id="curvedText">

                                <defs>
                                    <path id="curve" d="M100,300 Q500,10 900,300"/>
                                </defs>

                                <text>
                                    <textPath href="#curve" startOffset="50%" text-anchor="middle">
                                        Microseed Control
                                    </textPath>
                                </text>

                            </svg>

                            <img src="{{ asset('img/logo.png') }}" alt="Logo Microseed Control" class="logo-main">

                        </div>


                        <p class="left-text">
                            Sistema web de control de microclima en prototipo de incubadora de semillas
                        </p>


                        <p class="left-footer">
                            &copy; {{ date('Y') }} Microseed Control
                        </p>


                        {{-- Borde ondulado en escritorio --}}
                        <svg class="login-wave login-wave--vertical" viewBox="0 0 200 1000" preserveAspectRatio="none" aria-hidden="true">
                            <path fill="#9fd8cf" fill-opacity=".45" d="M200 -10H30.7A122.0 122.0 0 0 0 38.6 105.4A140.1 140.1 0 0 0 89.7 259.0A118.4 118.4 0 0 0 116.8 391.1A45.1 45.1 0 0 0 115.6 440.0A81.0 81.0 0 0 0 115.6 560.0A45.1 45.1 0 0 0 116.8 608.9A118.4 118.4 0 0 0 89.7 741.0A140.1 140.1 0 0 0 38.6 894.6A122.0 122.0 0 0 0 30.7 1010.0H200Z"/>
                            <path fill="#c9ece6" fill-opacity=".9" d="M200 -10H58.5A116.3 116.3 0 0 0 89.7 140.7A147.9 147.9 0 0 0 119.5 287.3A66.0 66.0 0 0 0 120.5 368.3A54.4 54.4 0 0 0 139.6 440.0A81.0 81.0 0 0 0 139.6 560.0A54.4 54.4 0 0 0 120.5 631.7A66.0 66.0 0 0 0 119.5 712.7A147.9 147.9 0 0 0 89.7 859.3A116.3 116.3 0 0 0 58.5 1010.0H200Z"/>
                            <path fill="#ffffff" d="M200 -10H77.1A149.4 149.4 0 0 0 93.7 126.6A77.7 77.7 0 0 0 109.5 184.3A66.2 66.2 0 0 0 133.6 253.1A66.0 66.0 0 0 0 150.6 343.9A62.1 62.1 0 0 0 163.6 440.0A81.0 81.0 0 0 0 163.6 560.0A62.1 62.1 0 0 0 150.6 656.1A66.0 66.0 0 0 0 133.6 746.9A66.2 66.2 0 0 0 109.5 815.7A77.7 77.7 0 0 0 93.7 873.4A149.4 149.4 0 0 0 77.1 1010.0H200Z"/>
                        </svg>

                        {{-- Borde ondulado en tableta y móvil --}}
                        <svg class="login-wave login-wave--horizontal" viewBox="0 0 1000 80" preserveAspectRatio="none" aria-hidden="true">
                            <path fill="#9fd8cf" fill-opacity=".45" d="M-10 80V23.0A265.0 265.0 0 0 1 142.3 26.6A299.6 299.6 0 0 1 343.2 41.9A139.9 139.9 0 0 1 430.0 43.2A126.0 126.0 0 0 1 570.0 43.2A139.9 139.9 0 0 1 656.8 41.9A299.6 299.6 0 0 1 857.7 26.6A265.0 265.0 0 0 1 1010.0 23.0V80Z"/>
                            <path fill="#c9ece6" fill-opacity=".9" d="M-10 80V36.1A273.2 273.2 0 0 1 194.9 47.0A391.3 391.3 0 0 1 430.0 55.2A126.0 126.0 0 0 1 570.0 55.2A391.3 391.3 0 0 1 805.1 47.0A273.2 273.2 0 0 1 1010.0 36.1V80Z"/>
                            <path fill="#ffffff" d="M-10 80V46.4A339.2 339.2 0 0 1 185.5 53.4A180.8 180.8 0 0 1 275.7 59.4A159.5 159.5 0 0 1 380.8 66.7A76.0 76.0 0 0 1 430.0 67.2A126.0 126.0 0 0 1 570.0 67.2A76.0 76.0 0 0 1 619.2 66.7A159.5 159.5 0 0 1 724.3 59.4A180.8 180.8 0 0 1 814.5 53.4A339.2 339.2 0 0 1 1010.0 46.4V80Z"/>
                        </svg>

                    </div>

                </div>


                {{-- =====================================================
                    PANEL DERECHO
                ====================================================== --}}

                <div class="col-lg-6 login-col-right">

                    <div class="login-right">

                        <div class="form-area">

                            <div class="form-header">

                                <h2 class="form-title">
                                    Iniciar sesión
                                </h2>

                                <p class="form-subtitle">
                                    Ingresa tus credenciales para acceder al panel del sistema.
                                </p>

                            </div>


                            {{-- =================================================
                                IMPORTANTE:
                                YA NO MOSTRAMOS session('status') AQUÍ.

                                El sistema global de alertas se encarga.
                            ================================================== --}}


                            <form method="POST" action="{{ route('login') }}">

                                @csrf


                                {{-- CORREO --}}
                                <div class="mb-3">

                                    <label for="email" class="form-label">
                                        Correo electrónico
                                    </label>

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

                                    {{--
                                        El campo se pone rojo cuando hay un error;
                                        la explicación aparece en la alerta global.
                                    --}}

                                </div>


                                {{-- CONTRASEÑA --}}
                                <div class="mb-3">

                                    <label for="password" class="form-label">
                                        Contraseña
                                    </label>


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
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.25A3.25 3.25 0 1 0 12 8.75a3.25 3.25 0 0 0 0 6.5z"/>
                                            </svg>

                                        </button>

                                    </div>

                                </div>


                                {{-- RECORDAR / RECUPERAR --}}
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between remember-row gap-2">

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="remember"
                                            id="remember_me"
                                        >

                                        <label class="form-check-label text-secondary" for="remember_me">
                                            Recordarme
                                        </label>

                                    </div>


                                    @if(Route::has('password.request'))

                                        <a class="forgot-link" href="{{ route('password.request') }}">
                                            ¿Olvidaste tu contraseña?
                                        </a>

                                    @endif

                                </div>


                                {{-- ENTRAR --}}
                                <button type="submit" class="btn btn-login">
                                    Entrar al sistema
                                </button>

                            </form>


                            {{-- AYUDA --}}
                            <div class="form-divider">
                                <span>¿Necesitas ayuda?</span>
                            </div>

                            <p class="form-help">
                                Si no puedes entrar, pide al administrador del sistema que revise tu cuenta.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>
        /* =============================================================
           ICONOS MOSTRAR / OCULTAR CONTRASEÑA
        ============================================================== */

        const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.25A3.25 3.25 0 1 0 12 8.75a3.25 3.25 0 0 0 0 6.5z"/>
        </svg>
    `;

        const eyeOffIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.5A9.7 9.7 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.9 17.9 0 0 1-3.1 3.85"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 6.9C3.85 8.55 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.25 0 2.42-.3 3.47-.78"/>
        </svg>
    `;

        window.togglePassword = function(inputId, button) {
            const input = document.getElementById(inputId);
            if (!input) return;

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


        /* =============================================================
           EFECTOS DEL LOGIN
        ============================================================== */

        document.addEventListener('DOMContentLoaded', function() {

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const loginLeft = document.getElementById('loginLeft');
            const logoWrapper = document.getElementById('logoWrapper');
            const curvedText = document.getElementById('curvedText');

            if (reduceMotion || !loginLeft) return;


            /* Movimiento ligero del logo con el mouse */
            document.addEventListener('mousemove', function(event) {
                if (!logoWrapper || !curvedText || window.innerWidth <= 991) return;

                const x = (window.innerWidth / 2 - event.clientX) / 45;
                const y = (window.innerHeight / 2 - event.clientY) / 45;

                logoWrapper.style.transform = `translate(${x * -0.2}px, ${y * -0.2}px)`;
                curvedText.style.transform = `translateX(-50%) translate(${x * 0.15}px, ${y * 0.15}px)`;
            });


            /* Partículas: suben dentro del panel izquierdo */
            function createSeedParticle() {
                const particle = document.createElement('span');
                particle.classList.add('seed-particle');

                const size = Math.random() * 6 + 4;
                const startX = Math.random() * loginLeft.offsetWidth;
                const duration = Math.random() * 5000 + 6000;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${startX}px`;
                particle.style.bottom = '-20px';

                loginLeft.appendChild(particle);

                particle.animate(
                    [
                        { transform: 'translateY(0) translateX(0) scale(0.8)', opacity: 0 },
                        { opacity: 0.65 },
                        {
                            transform: `translateY(-${loginLeft.offsetHeight + 80}px) translateX(${Math.random() * 120 - 60}px) scale(1.15)`,
                            opacity: 0
                        }
                    ],
                    { duration: duration, easing: 'linear', fill: 'forwards' }
                );

                setTimeout(function() { particle.remove(); }, duration);
            }

            setInterval(createSeedParticle, 650);
        });
    </script>

@endsection
