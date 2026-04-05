<x-app-layout>
    <div class="flex min-h-screen bg-slate-100">
        <x-admin-sidebar />

        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                    <h1 class="text-3xl font-black text-slate-800 mb-2">Registros biológicos</h1>
                    <p class="text-slate-500">Captura manual de estratificación, nutrientes y germinación por lote.</p>
                </div>

                @if (session('success'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                        <p class="font-bold text-red-700 mb-2">Corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 text-red-600 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-black text-slate-800 mb-6">Nuevo registro</h2>

                    <form action="{{ route($routePrefix . '.registros-biologicos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Lote</label>
                            <select name="lote_id" class="w-full rounded-xl border-slate-300">
                                @foreach($lotes as $lote)
                                    <option value="{{ $lote->id }}">
                                        {{ $lote->codigo_lote ?? ('Lote #' . $lote->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Fecha</label>
                            <input type="date" name="fecha_registro" class="w-full rounded-xl border-slate-300" value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Días estratificación</label>
                            <input type="number" name="dias_estratificacion" class="w-full rounded-xl border-slate-300" min="0" value="0">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Tasa germinación (%)</label>
                            <input type="number" step="0.01" name="tasa_germinacion" class="w-full rounded-xl border-slate-300" min="0" max="100">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">% Carbono</label>
                            <input type="number" step="0.01" name="porcentaje_carbono" class="w-full rounded-xl border-slate-300" min="0" max="100">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">% Nitrógeno</label>
                            <input type="number" step="0.01" name="porcentaje_nitrogeno" class="w-full rounded-xl border-slate-300" min="0" max="100">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">% Fósforo</label>
                            <input type="number" step="0.01" name="porcentaje_fosforo" class="w-full rounded-xl border-slate-300" min="0" max="100">
                        </div>

                        <div class="md:col-span-2 xl:col-span-4">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Observaciones</label>
                            <textarea name="observaciones" rows="3" class="w-full rounded-xl border-slate-300"></textarea>
                        </div>

                        <div class="md:col-span-2 xl:col-span-4">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition">
                                Guardar registro biológico
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                    <h2 class="text-xl font-black text-slate-800 mb-6">Historial biológico</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-200 text-left text-slate-500 uppercase text-xs">
                                <th class="py-3 pr-4">Lote</th>
                                <th class="py-3 pr-4">Fecha</th>
                                <th class="py-3 pr-4">Estratificación</th>
                                <th class="py-3 pr-4">C</th>
                                <th class="py-3 pr-4">N</th>
                                <th class="py-3 pr-4">P</th>
                                <th class="py-3 pr-4">Germinación</th>
                                <th class="py-3 pr-4">Usuario</th>
                                <th class="py-3 pr-4">Observaciones</th>
                                <th class="py-3">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($registros as $registro)
                                <tr class="border-b border-slate-100 align-top">
                                    <td class="py-3 pr-4 font-semibold text-slate-800">{{ $registro->lote->codigo_lote ?? ('Lote #' . $registro->lote_id) }}</td>
                                    <td class="py-3 pr-4">{{ optional($registro->fecha_registro)->format('d/m/Y') }}</td>
                                    <td class="py-3 pr-4">{{ $registro->dias_estratificacion }} días</td>
                                    <td class="py-3 pr-4">{{ $registro->porcentaje_carbono ?? '—' }}</td>
                                    <td class="py-3 pr-4">{{ $registro->porcentaje_nitrogeno ?? '—' }}</td>
                                    <td class="py-3 pr-4">{{ $registro->porcentaje_fosforo ?? '—' }}</td>
                                    <td class="py-3 pr-4">{{ $registro->tasa_germinacion }} %</td>
                                    <td class="py-3 pr-4">{{ $registro->usuario->name ?? 'Sin usuario' }}</td>
                                    <td class="py-3 pr-4">{{ $registro->observaciones ?? '—' }}</td>
                                    <td class="py-3">
                                        <form action="{{ route($routePrefix . '.registros-biologicos.destroy', $registro) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 font-semibold">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-6 text-center text-slate-500">No hay registros biológicos todavía.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $registros->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
