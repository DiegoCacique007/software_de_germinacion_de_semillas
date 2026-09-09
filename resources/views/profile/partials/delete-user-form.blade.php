<section x-data="{ openDeactivateModal: @js($errors->userDeletion->isNotEmpty()) }">
    <p class="text-secondary small mb-3">
        Al desactivar tu cuenta conservarás tu información e historial dentro de MicroSeed Control, pero ya no podrás iniciar sesión hasta que un administrador reactive tu acceso.
    </p>

    <button type="button" class="btn btn-danger px-4 py-2 fw-bold rounded-3 shadow-sm d-inline-flex align-items-center gap-2" @click="openDeactivateModal = true">
        <i class="bi bi-person-x"></i>
        Desactivar cuenta
    </button>

    <template x-teleport="body">
        <div
            x-show.important="openDeactivateModal"
            x-cloak
            x-transition.opacity
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="position:fixed;inset:0;z-index:999999;width:100vw;height:100vh;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);"
            @click.self="openDeactivateModal = false"
            @keydown.escape.window="openDeactivateModal = false"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                    <div class="modal-header border-0 p-4" style="background:linear-gradient(135deg,#dc3545 0%,#e35d6a 100%);">
                        <h5 class="modal-title d-flex align-items-center gap-2 mb-0 text-white fw-semibold fs-5">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-white"></i>
                            ¿Deseas desactivar tu cuenta?
                        </h5>

                        <button type="button" class="btn-close btn-close-white" aria-label="Cerrar" @click="openDeactivateModal = false"></button>
                    </div>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body p-4">
                            <p class="text-secondary small mb-3">
                                Al confirmar esta acción tu cuenta cambiará a estado <strong>inactivo</strong> y se cerrará tu sesión actual.
                            </p>

                            <div class="alert alert-warning border-0 rounded-3 small mb-4">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill mt-1"></i>
                                    <div>
                                        <strong>Tu información no será eliminada.</strong><br>
                                        Los registros, seguimientos, asignaciones y demás información relacionada con tu usuario permanecerán almacenados en el sistema.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="deactivate_password" class="form-label fw-semibold small text-secondary">Contraseña</label>

                                <input
                                    id="deactivate_password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    class="form-control rounded-3 py-2 px-3 @if($errors->userDeletion->has('password')) is-invalid @endif"
                                    placeholder="Ingresa tu contraseña para confirmar"
                                    required
                                >

                                @if($errors->userDeletion->has('password'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->userDeletion->first('password') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center gap-2 flex-nowrap">
                            <button type="button" class="btn btn-light rounded-3 px-4 py-2 fw-semibold text-secondary" style="min-width:170px;" @click="openDeactivateModal = false">
                                Cancelar
                            </button>

                            <button type="submit" class="btn btn-danger rounded-3 px-4 py-2 fw-semibold text-white shadow-sm d-inline-flex align-items-center justify-content-center gap-2" style="min-width:170px;">
                                <i class="bi bi-person-x text-white"></i>
                                Desactivar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </template>
</section>
