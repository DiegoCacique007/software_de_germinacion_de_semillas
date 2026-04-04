<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión | {{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-dark-blue: #1f6f86;
            --brand-teal: #2f9aa0;
            --brand-green: #39b39f;
            --brand-soft: #eef8f7;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(57, 179, 159, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(31, 111, 134, 0.20), transparent 30%),
                linear-gradient(135deg, #eef8f7 0%, #f7fbfb 100%);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 1120px;
            border: none;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(20, 60, 70, 0.18);
            animation: fadeUp 0.9s ease;
            background: #fff;
        }

        .login-card .row {
            min-height: 700px;
            align-items: stretch;
        }

        .login-card .col-lg-6 {
            display: flex;
        }

        .login-left,
        .login-right {
            width: 100%;
            height: 100%;
        }

        .login-left {
            position: relative;
            background: linear-gradient(135deg, #236f87 0%, #2f8f97 50%, #39b39f 100%);
            color: white;
            /* Se redujo el padding superior y se aumentó mucho el inferior para empujar todo hacia arriba */
            padding: 20px 45px 140px 45px;
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
            width: 260px;
            height: 260px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -90px;
            left: -70px;
            filter: blur(10px);
            animation: floatOrb 8s ease-in-out infinite;
        }

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

        .login-right {
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

        .status-box {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: 0.95rem;
            animation: fadeUp 0.7s ease;
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

        .remember-row {
            margin-top: 18px;
            margin-bottom: 22px;
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
        }

        .forgot-link:hover {
            color: var(--brand-green);
        }

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(28, 96, 122, 0.22);
            color: white;
        }

        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
            transform: skewX(-20deg);
        }

        .btn-login:hover::before {
            animation: shine 0.9s ease;
        }

        .bottom-register-wrap {
            margin-top: 22px;
            text-align: center;
            animation: fadeUp 1.15s ease;
        }

        .bottom-register-text {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .register-link-centered {
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

        .register-link-centered:hover {
            color: white;
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-green) 100%);
            border-color: transparent;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 28px rgba(28, 96, 122, 0.18);
        }

        .register-link-centered::before {
            content: "";
            position: absolute;
            top: 0;
            left: -140%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.26), transparent);
            transform: skewX(-20deg);
        }

        .register-link-centered:hover::before {
            animation: shine 0.8s ease;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.88rem;
            margin-top: 7px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            color: var(--brand-dark-blue);
            font-weight: 700;
            margin-top: 18px;
            width: 100%;
            text-align: center;
            transition: all 0.25s ease;
        }

        .back-home:hover {
            color: var(--brand-green);
            transform: translateY(-1px);
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
            .login-card .row {
                min-height: unset;
            }

            .login-left {
                min-height: auto;
                /* También ajusté el padding en móvil para subir el contenido */
                padding: 30px 28px 70px 28px;
            }

            .login-right {
                padding: 38px 24px;
            }

            .left-title {
                font-size: 2rem;
            }

            .logo-box {
                width: 380px;
                height: 280px;
                background: transparent;
                box-shadow: none;
                margin-bottom: 10px;
            }

            .logo-box img {
                width: 250px;
                height: 250px;
            }

            .register-link-centered {
                width: 100%;
                min-width: unset;
            }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="card login-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="login-left">


                    <div class="logo-box">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo Microseed Control">
                    </div>

                    <h1 class="left-title">Bienvenido a Microseed Control</h1>

                    <p class="left-text">
                        Gestiona el monitoreo microclimático de tu incubadora de semillas desde una interfaz moderna, segura y profesional.
                    </p>


                </div>
            </div>

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
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                >
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

                        @if (Route::has('register'))
                            <div class="bottom-register-wrap">
                                <div class="bottom-register-text">¿No tienes cuenta todavía?</div>
                                <a href="{{ route('register') }}" class="register-link-centered">
                                    Registrarse
                                </a>
                            </div>
                        @endif

                        <a href="{{ url('/') }}" class="back-home">
                            ← Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
