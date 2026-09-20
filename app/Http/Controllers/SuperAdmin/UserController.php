<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $authUser = auth()->user();
        $this->verificarAcceso($authUser);

        $usuarios = User::with('rol')->orderBy('name')->get();
        $roles = Role::whereIn('clave', ['super_admin', 'encargado'])->where('activo', true)->orderBy('nombre')->get();

        $estadosUsuario = collect([
            (object) ['id' => 1, 'nombre' => 'Activo'],
            (object) ['id' => 0, 'nombre' => 'Inactivo'],
        ]);

        $routeBase = 'super_admin.usuarios';

        return view('vistas_principales.super_admin.usuarios.index', compact('usuarios', 'roles', 'estadosUsuario', 'routeBase'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $authUser = auth()->user();
        $this->verificarAcceso($authUser);

        $data = $request->validated();
        $rol = Role::whereIn('clave', ['super_admin', 'encargado'])->findOrFail($data['role_id']);

        $data['role_id'] = $rol->id;

        User::create($data);

        return redirect()->route('super_admin.usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $authUser = auth()->user();
        $this->verificarAcceso($authUser);

        $data = $request->validated();
        $nuevoRol = Role::whereIn('clave', ['super_admin', 'encargado'])->findOrFail($data['role_id']);

        if ($usuario->id === $authUser->id && !(bool) $data['activo']) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if ($usuario->id === $authUser->id && $nuevoRol->clave !== 'super_admin') {
            return back()->with('error', 'No puedes retirar tu propio rol de superadministrador.');
        }

        if ($usuario->isSuperAdmin() && $nuevoRol->clave !== 'super_admin' && $this->esUltimoSuperAdminActivo($usuario)) {
            return back()->with('error', 'No puedes cambiar el rol del último superadministrador activo.');
        }

        if ($usuario->isSuperAdmin() && !(bool) $data['activo'] && $this->esUltimoSuperAdminActivo($usuario)) {
            return back()->with('error', 'No puedes desactivar al último superadministrador activo.');
        }

        if (empty($data['password'])) unset($data['password']);

        $data['role_id'] = $nuevoRol->id;

        $usuario->update($data);

        return redirect()->route('super_admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    private function verificarAcceso(?User $user): void
    {
        if (!$user || !$user->isSuperAdmin()) abort(403, 'No tienes permisos para administrar usuarios.');
    }

    private function esUltimoSuperAdminActivo(User $usuario): bool
    {
        if (!$usuario->isSuperAdmin() || !$usuario->activo) return false;

        return User::where('activo', true)
            ->where('id', '!=', $usuario->id)
            ->whereHas('rol', fn($query) => $query->where('clave', 'super_admin'))
            ->doesntExist();
    }
}
