<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerfilFotoController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'foto_perfil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        $user->foto_perfil = $request->file('foto_perfil')->store('usuarios/perfiles', 'public');
        $user->save();

        return back()->with('success', 'Foto de perfil actualizada correctamente.');
    }
}
