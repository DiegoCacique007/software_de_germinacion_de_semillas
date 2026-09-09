<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Registrar último acceso
        |--------------------------------------------------------------------------
        */
        $user->forceFill([
            'ultimo_acceso_at' => now(),
        ])->saveQuietly();

        /*
        |--------------------------------------------------------------------------
        | Redirección según rol
        |--------------------------------------------------------------------------
        */
        return match ($user->rol_clave) {
            'super_admin' => redirect()
                ->route('super_admin.dashboard')
                ->with('success', 'Bienvenido de nuevo al sistema.'),

            'administrador' => redirect()
                ->route('administrador.dashboard')
                ->with('success', 'Bienvenido de nuevo al sistema.'),

            'encargado' => redirect()
                ->route('dashboard')
                ->with('success', 'Bienvenido de nuevo al sistema.'),

            default => $this->rolNoValido($request),
        };
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Cierra la sesión si el usuario no tiene un rol válido.
     */
    private function rolNoValido(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('error', 'Tu cuenta no tiene un rol válido asignado.');
    }
}
