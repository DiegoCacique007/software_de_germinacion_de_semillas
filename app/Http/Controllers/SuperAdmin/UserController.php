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

        $usuariosQuery = User::with('rol');

        if ($authUser->isAdministrador()) {
            $usuariosQuery->whereHas('rol', fn($query) => $query->where('clave', 'encargado'));
        }

        $usuarios = $usuariosQuery->orderBy('name')->get();

        $roles = $authUser->isSuperAdmin()
            ? Role::where('activo', true)->orderBy('nombre')->get()
            : Role::where('clave', 'encargado')->where('activo', true)->get();

        $estadosUsuario = collect([
            (object) ['id' => 1, 'nombre' => 'Activo'],
            (object) ['id' => 0, 'nombre' => 'Inactivo'],
        ]);

        $routeBase = $authUser->isAdministrador()
            ? 'administrador.usuarios'
            : 'super_admin.usuarios';

        return view('vistas_principales.super_admin.usuarios.index', compact(
            'usuarios',
            'roles',
            'estadosUsuario',
            'routeBase'
        ));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $authUser = auth()->user();
        $this->verificarAcceso($authUser);

        $data = $request->validated();
        $role = Role::findOrFail($data['role_id']);

        if ($authUser->isAdministrador() && $role->clave !== 'encargado') {
            return back()->with('error', 'Un administrador solo puede registrar usuarios con rol de encargado.');
        }

        User::create($data);

        return redirect()
            ->route($this->rutaUsuarios($authUser))
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $authUser = auth()->user();
        $this->verificarAcceso($authUser);

        $data = $request->validated();
        $nuevoRol = Role::findOrFail($data['role_id']);

        if ($authUser->isAdministrador()) {
            if (!$usuario->isEncargado()) abort(403, 'No tienes permisos para modificar este usuario.');

            if ($nuevoRol->clave !== 'encargado') {
                return back()->with('error', 'Un administrador solamente puede asignar el rol de encargado.');
            }
        }

        if ($usuario->id === $authUser->id && !(bool) $data['activo']) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if ($usuario->id === $authUser->id && $authUser->isSuperAdmin() && $nuevoRol->clave !== 'super_admin') {
            return back()->with('error', 'No puedes retirar tu propio rol de superadministrador.');
        }

        if ($usuario->isSuperAdmin() && $nuevoRol->clave !== 'super_admin' && $this->esUltimoSuperAdminActivo($usuario)) {
            return back()->with('error', 'No puedes cambiar el rol del último superadministrador activo.');
        }

        if ($usuario->isSuperAdmin() && !(bool) $data['activo'] && $this->esUltimoSuperAdminActivo($usuario)) {
            return back()->with('error', 'No puedes desactivar al último superadministrador activo.');
        }

        if (empty($data['password'])) unset($data['password']);

        $usuario->update($data);

        return redirect()
            ->route($this->rutaUsuarios($authUser))
            ->with('success', 'Usuario actualizado correctamente.');
    }

    private function verificarAcceso(?User $user): void
    {
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdministrador())) {
            abort(403, 'No tienes permisos para administrar usuarios.');
        }
    }

    private function rutaUsuarios(User $user): string
    {
        return $user->isAdministrador()
            ? 'administrador.usuarios.index'
            : 'super_admin.usuarios.index';
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
