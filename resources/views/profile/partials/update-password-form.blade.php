<section>
    <form method="post" action="{{ route('password.update') }}" class="d-flex flex-column gap-3">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label fw-bold small text-secondary">
                {{ __('Contraseña actual') }}
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="form-control rounded-3 py-2 px-3 @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                autocomplete="current-password"
                placeholder="••••••••"
            >
            @if($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="form-label fw-bold small text-secondary">
                {{ __('Nueva contraseña') }}
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                class="form-control rounded-3 py-2 px-3 @if($errors->updatePassword->has('password')) is-invalid @endif"
                autocomplete="new-password"
                placeholder="••••••••"
            >
            @if($errors->updatePassword->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label fw-bold small text-secondary">
                {{ __('Confirmar nueva contraseña') }}
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control rounded-3 py-2 px-3 @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                autocomplete="new-password"
                placeholder="••••••••"
            >
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn rounded-3 px-4 py-2 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #1c607a, #3bb49c); border: none;">
                <i class="bi bi-shield-lock me-1"></i> {{ __('Actualizar contraseña') }}
            </button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold"
                >
                    <i class="bi bi-check-circle me-1"></i> {{ __('Contraseña actualizada') }}
                </span>
            @endif
        </div>
    </form>
</section>
