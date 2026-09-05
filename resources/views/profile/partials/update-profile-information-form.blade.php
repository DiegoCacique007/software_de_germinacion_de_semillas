<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="d-flex flex-column gap-3">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="form-label fw-bold small text-secondary">
                {{ __('Nombre completo') }}
            </label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-control rounded-3 py-2 px-3 @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="form-label fw-bold small text-secondary">
                {{ __('Correo electrónico') }}
            </label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control rounded-3 py-2 px-3 @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-3 py-2 px-3 rounded-3 small">
                    <p class="mb-1 text-dark">
                        {{ __('Tu dirección de correo no está verificada.') }}
                    </p>
                    <button form="send-verification" class="btn btn-link btn-sm p-0 text-decoration-underline text-dark fw-semibold">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 mb-0 text-success fw-bold small">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn rounded-3 px-4 py-2 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                <i class="bi bi-check-lg me-1"></i> {{ __('Guardar cambios') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold"
                >
                    <i class="bi bi-check-circle me-1"></i> {{ __('Guardado exitosamente') }}
                </span>
            @endif
        </div>
    </form>
</section>
