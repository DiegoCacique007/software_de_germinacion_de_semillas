<section x-data="{ openDeleteModal: @js($errors->userDeletion->isNotEmpty()) }">
    <p class="text-secondary small mb-3">
        {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos serán borrados permanentemente.') }}
    </p>

    <button
        type="button"
        class="btn btn-danger px-4 py-2 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2"
        @click="openDeleteModal = true"
    >
        <i class="bi bi-trash3"></i>
        {{ __('Eliminar cuenta') }}
    </button>

    {{-- MODAL ELIMINAR USUARIO --}}
    <div
        x-show.important="openDeleteModal"
        x-cloak
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);"
        @click.self="openDeleteModal = false"
        @keydown.escape.window="openDeleteModal = false"
    >
        <div class="modal-dialog modal-dialog-centered" @click.outside="openDeleteModal = false">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 bg-danger text-white p-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        {{ __('¿Estás seguro de que deseas eliminar tu cuenta?') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="openDeleteModal = false" aria-label="Cerrar"></button>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-4">
                            {{ __('Una vez eliminada la cuenta, todos sus recursos y datos serán eliminados permanentemente. Por favor ingresa tu contraseña para confirmar.') }}
                        </p>

                        <div class="mb-3">
                            <label for=\"delete_password\" class="form-label fw-bold small text-secondary">
                                {{ __('Contraseña') }}
                            </label>
                            <input
                                id="delete_password"
                                name="password"
                                type="password"
                                class="form-control rounded-3 py-2 px-3 @if($errors->userDeletion->has('password')) is-invalid @endif"
                                placeholder="{{ __('Ingresa tu contraseña') }}"
                            >
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback d-block">
                                    {{ $errors->userDeletion->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-4 py-2 fw-semibold text-secondary" @click="openDeleteModal = false">
                            {{ __('Cancelar') }}
                        </button>

                        <button type="submit" class="btn btn-danger rounded-3 px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2">
                            <i class="bi bi-trash3"></i>
                            {{ __('Eliminar definitivamente') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
