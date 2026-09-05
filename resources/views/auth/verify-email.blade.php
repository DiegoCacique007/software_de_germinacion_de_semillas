@extends('layouts.guest')

@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 540px; width: 100%;">
            <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #1c607a 0%, #2fa58e 100%);">
                <div class="mb-3 d-inline-flex p-3 rounded-circle" style="background: rgba(255, 255, 255, 0.15);">
                    <i class="bi bi-envelope-check fs-1 text-white"></i>
                </div>
                <h4 class="fw-bold mb-1">Verificación de correo</h4>
                <p class="mb-0 text-white-50 small">Microseed Control</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <p class="text-secondary small mb-4">
                    {{ __('¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.') }}
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 small" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>
                            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada.') }}
                        </div>
                    </div>
                @endif

                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-2">
                    <form method="POST" action="{{ route('verification.send') }}" class="w-100 w-sm-auto">
                        @csrf
                        <button type="submit" class="btn btn-brand w-100 px-4 py-2 fw-bold text-white rounded-3 shadow-sm" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                            <i class="bi bi-send me-1"></i> {{ __('Reenviar correo') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="w-100 w-sm-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 px-3 py-2 fw-semibold rounded-3">
                            <i class="bi bi-box-arrow-right me-1"></i> {{ __('Cerrar sesión') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
