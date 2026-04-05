<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();

        return view('vistas_principales.super_admin.usuarios.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
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

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'role'     => 'required|in:super_admin,administrador,encargado',
            'password' => 'nullable|string|min:8',
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->role = $request->role;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
