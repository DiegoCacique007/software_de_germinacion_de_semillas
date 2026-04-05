<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Biológico</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin-bottom: 5px; }
        p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
<h1>Reporte biológico</h1>
<p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
<p>
    Filtros:
    Inicio: {{ $filtros['fecha_inicio'] ?? 'Todos' }} |
    Fin: {{ $filtros['fecha_fin'] ?? 'Todos' }} |
    Lote: {{ $filtros['lote_id'] ?? 'Todos' }}
</p>

<table>
    <thead>
    <tr>
        <th>Lote</th>
        <th>Fecha</th>
        <th>Estratificación</th>
        <th>% C</th>
        <th>% N</th>
        <th>% P</th>
        <th>Germinación</th>
        <th>Usuario</th>
        <th>Observaciones</th>
    </tr>
    </thead>
    <tbody>
    @forelse($registros as $registro)
        <tr>
            <td>{{ $registro->lote->codigo_lote ?? ('Lote #' . $registro->lote_id) }}</td>
            <td>{{ optional($registro->fecha_registro)->format('d/m/Y') }}</td>
            <td>{{ $registro->dias_estratificacion }} días</td>
            <td>{{ $registro->porcentaje_carbono ?? '—' }}</td>
            <td>{{ $registro->porcentaje_nitrogeno ?? '—' }}</td>
            <td>{{ $registro->porcentaje_fosforo ?? '—' }}</td>
            <td>{{ $registro->tasa_germinacion }} %</td>
            <td>{{ $registro->usuario->name ?? 'Sin usuario' }}</td>
            <td>{{ $registro->observaciones ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9">No hay registros biológicos para este filtro.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
