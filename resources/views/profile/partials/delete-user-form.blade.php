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

    <template x-teleport="body">
        <div
            x-show.important="openDeleteModal"
            x-cloak
            x-transition.opacity
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="
                position: fixed;
                inset: 0;
                z-index: 999999;
                width: 100vw;
                height: 100vh;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            "
            @click.self="openDeleteModal = false"
            @keydown.escape.window="openDeleteModal = false"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                    <div
                        class="modal-header border-0 p-4"
                        style="background: linear-gradient(135deg, #dc3545 0%, #e35d6a 100%);"
                    >
                        <h5 class="modal-title d-flex align-items-center gap-2 mb-0 text-white fw-semibold fs-5">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-white"></i>
                            {{ __('¿Estás seguro de que deseas eliminar tu cuenta?') }}
                        </h5>

                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            aria-label="Cerrar"
                            @click="openDeleteModal = false"
                        ></button>
                    </div>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body p-4">
                            <p class="text-secondary small mb-4">
                                {{ __('Una vez eliminada la cuenta, todos sus recursos y datos serán eliminados permanentemente. Por favor ingresa tu contraseña para confirmar.') }}
                            </p>

                            <div class="mb-3">
                                <label for="delete_password" class="form-label fw-semibold small text-secondary">
                                    {{ __('Contraseña') }}
                                </label>

                                <input
                                    id="delete_password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
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

                        <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center gap-2 flex-nowrap">
                            <button
                                type="button"
                                class="btn btn-light rounded-3 px-4 py-2 fw-semibold text-secondary"
                                style="min-width: 170px;"
                                @click="openDeleteModal = false"
                            >
                                {{ __('Cancelar') }}
                            </button>

                            <button
                                type="submit"
                                class="btn btn-danger rounded-3 px-4 py-2 fw-semibold text-white shadow-sm d-inline-flex align-items-center justify-content-center gap-2"
                                style="min-width: 170px;"
                            >
                                <i class="bi bi-trash3 text-white"></i>
                                {{ __('Eliminar') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>
</section>
