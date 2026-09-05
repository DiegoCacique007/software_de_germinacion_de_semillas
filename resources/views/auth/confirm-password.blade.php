@extends('layouts.guest')

@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 480px; width: 100%;">
            <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #1c607a 0%, #2fa58e 100%);">
                <div class="mb-3 d-inline-flex p-3 rounded-circle" style="background: rgba(255, 255, 255, 0.15);">
                    <i class="bi bi-shield-lock-fill fs-1 text-white"></i>
                </div>
                <h4 class="fw-bold mb-1">Confirmar contraseña</h4>
                <p class="mb-0 text-white-50 small">Área segura de Microseed Control</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <p class="text-secondary small mb-4">
                    {{ __('Esta es un área protegida del sistema. Por favor confirma tu contraseña antes de continuar.') }}
                </p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold small text-secondary">
                            {{ __('Contraseña') }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control rounded-3 py-2 px-3 @error('password') is-invalid @enderror"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-brand px-4 py-2 fw-bold text-white rounded-3 shadow-sm w-100" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                            <i class="bi bi-check-circle me-1"></i> {{ __('Confirmar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
