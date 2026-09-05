@extends('layouts.guest')

@section('content')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:500,700|playfair-display:400,400i,700,800" rel="stylesheet" />

    <style>
        :root {
            --brand-dark-blue: #1f6f86;
            --brand-teal: #2f9aa0;
            --brand-green: #39b39f;
            --brand-soft: #eef8f7;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Instrument Sans', sans-serif;
            position: relative;
            background:
                radial-gradient(circle at top left, rgba(59, 180, 156, 0.35), transparent 35%),
                radial-gradient(circle at bottom right, rgba(28, 96, 122, 0.45), transparent 40%),
                linear-gradient(135deg, #f0f6f6 0%, #d8f3ee 45%, #9fd8cf 100%);
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            position: relative;
            z-index: 10;
        }

        .auth-card {
            width: 100%;
            max-width: 1000px;
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(20, 60, 70, 0.25);
            animation: fadeUp 0.9s ease;
            background: #fff;
        }

        .auth-card .row {
            min-height: 600px;
            margin: 0;
        }

        .auth-card .col-lg-6 {
            display: flex;
            padding: 0;
        }

        .auth-left,
        .auth-right {
            width: 100%;
            height: 100%;
        }

        .auth-left {
            position: relative;
            background: linear-gradient(135deg, #236f87 0%, #2f8f97 50%, #39b39f 100%);
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }

        .auth-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.16) 1.1px, transparent 0);
            background-size: 30px 30px;
            opacity: 0.35;
            pointer-events: none;
        }

        .logo-box {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-box img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.35));
        }

        .left-title {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .left-text {
            position: relative;
            z-index: 2;
            font-size: 0.95rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            max-width: 360px;
        }

        .auth-right {
            background: #ffffff;
            padding: 40px 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-area {
            width: 100%;
            max-width: 380px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .form-title {
            color: var(--brand-dark-blue);
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
        }

        .form-subtitle {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 0.92rem;
        }

        .form-label {
            color: var(--brand-dark-blue);
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 0.92rem;
        }

        .form-control {
            border-radius: 12px;
            min-height: 48px;
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
            width: 50px;
            min-height: 48px;
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

        .btn-submit {
            background: linear-gradient(135deg, var(--brand-dark-blue) 0%, var(--brand-green) 100%);
            border: none;
            color: white;
            font-weight: 700;
            font-family: 'Instrument Sans', sans-serif;
            letter-spacing: 0.5px;
            font-size: 1rem;
            min-height: 50px;
            border-radius: 12px;
            width: 100%;
            box-shadow: 0 10px 20px rgba(28, 96, 122, 0.18);
            transition: all 0.28s ease;
            margin-top: 15px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(28, 96, 122, 0.25);
            color: white;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991.98px) {
            .auth-card .row { min-height: unset; }
            .auth-left { padding: 30px 20px; }
            .auth-right { padding: 35px 20px; }
            .logo-box img { width: 150px; height: 150px; }
        }
    </style>

    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="row g-0">

                <div class="col-lg-6">
                    <div class="auth-left">
                        <div class="logo-box">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo Microseed Control">
                        </div>
                        <h2 class="left-title">Restablecer contraseña</h2>
                        <p class="left-text">
                            Ingresa tu nueva contraseña para recuperar el acceso a Microseed Control.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="auth-right">
                        <div class="form-area">

                            <div class="form-header">
                                <h2 class="form-title">Nueva contraseña</h2>
                                <p class="form-subtitle">
                                    Define tus nuevas credenciales de acceso seguro.
                                </p>
                            </div>

                            <form method="POST" action="{{ route('password.store') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email', $request->email) }}"
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
                                    <label for="password" class="form-label">Nueva contraseña</label>
                                    <div class="input-group password-group">
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Mínimo 8 caracteres"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-toggle-password"
                                            onclick="togglePassword('password', this)"
                                            aria-label="Mostrar contraseña"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                    <div class="input-group password-group">
                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Repite tu contraseña"
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-toggle-password"
                                            onclick="togglePassword('password_confirmation', this)"
                                            aria-label="Mostrar contraseña"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                    <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-submit">
                                    Restablecer contraseña
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: var(--brand-dark-blue); font-size: 0.92rem;">
                                    ← Volver al inicio de sesión
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        }
    </script>
@endsection
