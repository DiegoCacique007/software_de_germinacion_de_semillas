@extends('layouts.guest')

@section('content')
    <style>
        :root {
            --brand-dark-blue: #1f6f86;
            --brand-teal: #2f9aa0;
            --brand-green: #39b39f;
            --brand-soft: #eef8f7;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(57, 179, 159, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(31, 111, 134, 0.20), transparent 30%),
                linear-gradient(135deg, #eef8f7 0%, #f7fbfb 100%);
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 1120px;
            border: none;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(20, 60, 70, 0.18);
            animation: fadeUp 0.9s ease;
            background: #fff;
        }

        .register-card .row {
            min-height: 700px;
            align-items: stretch;
        }

        .register-card .col-lg-6 {
            display: flex;
        }

        .register-left,
        .register-right {
            width: 100%;
            height: 100%;
        }

        .register-left {
            position: relative;
            background: linear-gradient(135deg, #236f87 0%, #2f8f97 50%, #39b39f 100%);
            color: white;
            /* Padding ajustado para mover el contenido hacia arriba */
            padding: 20px 45px 140px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .register-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.16) 1.1px, transparent 0);
            background-size: 30px 30px;
            opacity: 0.35;
            pointer-events: none;
        }

        .register-left::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -90px;
            left: -70px;
            filter: blur(10px);
            animation: floatOrb 8s ease-in-out infinite;
        }

        .brand-badge {
            position: relative;
            z-index: 2;
            display: inline-block;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
            animation: fadeLeft 1s ease;
        }

        /* Contenedor del logo ajustado: sin fondo, sin sombra, más grande */
        .logo-box {
            position: relative;
            z-index: 2;
            width: 480px;
            height: 380px;
            background: transparent;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
            box-shadow: none;
            animation: floatLogo 4.5s ease-in-out infinite;
        }

        /* Imagen del logo ajustada: más grande */
        .logo-box img {
            width: 360px;
            height: 360px;
            object-fit: contain;
            display: block;
            background: transparent;
        }

        .left-title {
            position: relative;
            z-index: 2;
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 18px;
            animation: fadeLeft 1.05s ease;
            max-width: 470px;
        }

        .left-text {
            position: relative;
            z-index: 2;
            font-size: 1.02rem;
            line-height: 1.8;
            color: rgba(255,255,255,0.95);
            max-width: 470px;
            margin-bottom: 30px;
            animation: fadeLeft 1.15s ease;
        }

        .info-card {
            position: relative;
            z-index: 2;
            background: rgba(34, 122, 139, 0.35);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 22px;
            padding: 22px 24px;
            backdrop-filter: blur(10px);
            max-width: 480px;
            animation: fadeLeft 1.25s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .info-card h6 {
            font-weight: 800;
            margin-bottom: 10px;
            font-size: 1.05rem;
        }

        .info-card p {
            margin: 0;
            color: rgba(255,255,255,0.92);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .register-right {
            background: #ffffff;
            padding: 55px 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeRight 1s ease;
        }

        .form-area {
            width: 100%;
            max-width: 450px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 28px;
            animation: fadeUp 1s ease;
        }

        .form-title {
            color: var(--brand-dark-blue);
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.7;
        }

        .form-label {
            color: var(--brand-dark-blue);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 14px;
            min-height: 52px;
            border: 1px solid #d9e4e8;
            padding-left: 15px;
            transition: all 0.25s ease;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 0.22rem rgba(57, 179, 159, 0.18);
            transform: translateY(-1px);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-green) 100%);
            border: none;
            color: white;
            font-weight: 700;
            min-height: 52px;
            border-radius: 14px;
            width: 100%;
            box-shadow: 0 12px 24px rgba(28, 96, 122, 0.18);
            transition: all 0.28s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(28, 96, 122, 0.22);
            color: white;
        }

        .btn-register::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
            transform: skewX(-20deg);
        }

        .btn-register:hover::before {
            animation: shine 0.9s ease;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.88rem;
            margin-top: 7px;
        }

        .bottom-login-wrap {
            margin-top: 22px;
            text-align: center;
            animation: fadeUp 1.15s ease;
        }

        .bottom-login-text {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .login-link-centered {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 240px;
            padding: 12px 22px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--brand-dark-blue);
            font-weight: 700;
            border: 2px solid rgba(31, 111, 134, 0.16);
            background: linear-gradient(180deg, #ffffff 0%, #f7fbfb 100%);
            box-shadow: 0 10px 22px rgba(28, 96, 122, 0.08);
            transition: all 0.28s ease;
            position: relative;
            overflow: hidden;
        }

        .login-link-centered:hover {
            color: white;
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-green) 100%);
            border-color: transparent;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 28px rgba(28, 96, 122, 0.18);
        }

        .login-link-centered::before {
            content: "";
            position: absolute;
            top: 0;
            left: -140%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.26), transparent);
            transform: skewX(-20deg);
        }

        .login-link-centered:hover::before {
            animation: shine 0.8s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(35px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeLeft {
            from {
                opacity: 0;
                transform: translateX(-35px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeRight {
            from {
                opacity: 0;
                transform: translateX(35px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-12px) translateX(8px); }
        }

        @keyframes shine {
            from {
                transform: translateX(-140%) skewX(-20deg);
            }
            to {
                transform: translateX(260%) skewX(-20deg);
            }
        }

        @media (max-width: 991.98px) {
            .register-card .row {
                min-height: unset;
            }

            .register-left {
                min-height: auto;
                /* Padding ajustado para móvil */
                padding: 30px 28px 70px 28px;
            }

            .register-right {
                padding: 38px 24px;
            }

            .left-title {
                font-size: 2rem;
            }

            /* Contenedor logo móvil */
            .logo-box {
                width: 380px;
                height: 280px;
                background: transparent;
                box-shadow: none;
                margin-bottom: 10px;
            }

            /* Logo móvil */
            .logo-box img {
                width: 250px;
                height: 250px;
            }

            .login-link-centered {
                width: 100%;
                min-width: unset;
            }
        }
    </style>

    <div class="register-wrapper">
        <div class="card register-card">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="register-left">

                        <div class="logo-box">
                            <img src="{{ asset('img/logo.png') }}" alt="Microseed Control Logo">
                        </div>

                        <h1 class="left-title">Crea tu cuenta en Microseed Control</h1>

                        <p class="left-text">
                            Registra tus credenciales para acceder al sistema de monitoreo, control y gestión de incubadoras de semillas en un entorno moderno y seguro.
                        </p>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="register-right">
                        <div class="form-area">
                            <div class="form-header">
                                <h2 class="form-title">Registrarse</h2>
                                <p class="form-subtitle">
                                    Completa los siguientes datos para crear tu cuenta de acceso.
                                </p>
                            </div>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre completo</label>
                                    <input
                                        id="name"
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        autocomplete="name"
                                        autofocus
                                        placeholder="Ingresa tu nombre"
                                    >
                                    @error('name')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input
                                        id="email"
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="username"
                                        placeholder="ejemplo@correo.com"
                                    >
                                    @error('email')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                    >
                                    @error('password')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                    >
                                    @error('password_confirmation')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-register">
                                    Crear cuenta
                                </button>
                            </form>

                            <div class="bottom-login-wrap">
                                <div class="bottom-login-text">¿Ya tienes cuenta?</div>
                                <a href="{{ route('login') }}" class="login-link-centered">
                                    Iniciar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
