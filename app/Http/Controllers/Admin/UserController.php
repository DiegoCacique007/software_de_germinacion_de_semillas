<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios en la tabla.
     */
    public function index()
{
    $users = User::all();
    // Ajustamos la ruta al nuevo archivo
    return view('vistas_principales.super_admin.gestion_usuarios', compact('users'));
}

    /**
     * Registra un nuevo usuario desde el modal de "Nuevo Usuario".
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required|in:super_admin,administrador,encargado',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return back()->with('success', 'Usuario registrado con éxito.');
    }

    /**
     * Actualiza los datos de un usuario desde el modal de "Editar".
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'role'     => 'required|in:super_admin,administrador,encargado',
            'password' => 'nullable|min:8', // Opcional al editar
        ]);

        $usuario->name  = $request->name;
        $usuario->email = $request->email;
        $usuario->role  = $request->role;

        // Solo cambiamos la contraseña si el Super Admin escribió algo en ese campo
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario del sistema.
     */
    public function destroy(User $usuario)
    {
        // Seguridad: No dejar que el Super Admin se borre a sí mismo
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}