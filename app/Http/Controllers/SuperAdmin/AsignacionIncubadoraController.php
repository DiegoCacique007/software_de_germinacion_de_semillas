<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionIncubadora;
use App\Models\Incubadora;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsignacionIncubadoraController extends Controller
{
    public function index(): View
    {
        $asignaciones = AsignacionIncubadora::with(['incubadora.estado', 'user.rol'])
            ->latest()
            ->get();

        $incubadoras = Incubadora::with('estado')
            ->orderBy('nombre')
            ->get();

        $usuarios = User::with('rol')
            ->where('activo', true)
            ->whereHas('rol', fn($query) => $query->where('clave', 'encargado'))
            ->orderBy('name')
            ->get();

        return view(
            'vistas_principales.super_admin.asignaciones_incubadora.index',
            compact('asignaciones', 'incubadoras', 'usuarios')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validarDatos($request);

        $usuario = User::with('rol')->findOrFail($data['user_id']);

        if (!$usuario->activo || !$usuario->isEncargado()) {
            return back()
                ->withInput()
                ->with('error', 'La incubadora solamente puede asignarse a un encargado activo.');
        }

        if ($this->existeTraslape($data)) {
            return back()
                ->withInput()
                ->with('error', 'Este encargado ya tiene una asignación para la incubadora dentro del periodo seleccionado.');
        }

        AsignacionIncubadora::create($data);

        return redirect()
            ->route('super_admin.asignaciones-incubadora.index')
            ->with('success', 'Asignación registrada correctamente.');
    }

    public function update(Request $request, AsignacionIncubadora $asignacionIncubadora): RedirectResponse
    {
        $data = $this->validarDatos($request);

        $usuario = User::with('rol')->findOrFail($data['user_id']);

        if (!$usuario->activo || !$usuario->isEncargado()) {
            return back()
                ->withInput()
                ->with('error', 'La incubadora solamente puede asignarse a un encargado activo.');
        }

        if ($this->existeTraslape($data, $asignacionIncubadora->id)) {
            return back()
                ->withInput()
                ->with('error', 'Este encargado ya tiene otra asignación para la incubadora dentro del periodo seleccionado.');
        }

        $asignacionIncubadora->update($data);

        return redirect()
            ->route('super_admin.asignaciones-incubadora.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(AsignacionIncubadora $asignacionIncubadora): RedirectResponse
    {
        $asignacionIncubadora->delete();

        return redirect()
            ->route('super_admin.asignaciones-incubadora.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }

    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'incubadora_id' => ['required', 'integer', 'exists:incubadoras,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function existeTraslape(array $data, ?int $ignorarId = null): bool
    {
        $query = AsignacionIncubadora::where('incubadora_id', $data['incubadora_id'])
            ->where('user_id', $data['user_id'])
            ->where(function ($query) use ($data) {
                $inicio = $data['fecha_inicio'];
                $fin = $data['fecha_fin'] ?? null;

                $query->where(function ($query) use ($inicio, $fin) {
                    $query->whereNull('fecha_fin')
                        ->whereDate('fecha_inicio', '<=', $fin ?? '9999-12-31');
                })->orWhere(function ($query) use ($inicio, $fin) {
                    $query->whereNotNull('fecha_fin')
                        ->whereDate('fecha_fin', '>=', $inicio)
                        ->whereDate('fecha_inicio', '<=', $fin ?? '9999-12-31');
                });
            });

        if ($ignorarId) {
            $query->where('id', '!=', $ignorarId);
        }

        return $query->exists();
    }
}
