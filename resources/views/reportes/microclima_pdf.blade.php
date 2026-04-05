<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de microclima</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        h1 {
            margin-bottom: 6px;
        }

        p {
            margin: 3px 0;
        }

        .resumen {
            margin-top: 14px;
            margin-bottom: 14px;
            padding: 10px;
            background: #f4f4f4;
            border: 1px solid #dcdcdc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Reporte de microclima</h1>
<p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
<p>
    Filtros:
    Inicio: {{ $filtros['fecha_inicio'] ?? 'Todos' }} |
    Fin: {{ $filtros['fecha_fin'] ?? 'Todos' }} |
    Incubadora: {{ $filtros['incubadora_id'] ?? 'Todas' }}
</p>

<div class="resumen">
    <p><strong>Total de lecturas:</strong> {{ $lecturas->count() }}</p>

    @php
        $promedioTemperatura = $lecturas->count() ? round($lecturas->avg('temperatura'), 2) : null;
        $promedioHumedad = $lecturas->count() ? round($lecturas->avg('humedad'), 2) : null;
        $tempMin = $lecturas->count() ? $lecturas->min('temperatura') : null;
        $tempMax = $lecturas->count() ? $lecturas->max('temperatura') : null;
        $humMin = $lecturas->count() ? $lecturas->min('humedad') : null;
        $humMax = $lecturas->count() ? $lecturas->max('humedad') : null;
    @endphp

    <p><strong>Promedio temperatura:</strong> {{ $promedioTemperatura !== null ? $promedioTemperatura . ' °C' : 'Sin datos' }}</p>
    <p><strong>Promedio humedad:</strong> {{ $promedioHumedad !== null ? $promedioHumedad . ' %' : 'Sin datos' }}</p>
    <p><strong>Temperatura mínima / máxima:</strong> {{ $tempMin !== null ? $tempMin . ' °C' : 'Sin datos' }} / {{ $tempMax !== null ? $tempMax . ' °C' : 'Sin datos' }}</p>
    <p><strong>Humedad mínima / máxima:</strong> {{ $humMin !== null ? $humMin . ' %' : 'Sin datos' }} / {{ $humMax !== null ? $humMax . ' %' : 'Sin datos' }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>Incubadora</th>
        <th>Temperatura</th>
        <th>Humedad</th>
        <th>Fecha y hora</th>
        <th>Observaciones</th>
    </tr>
    </thead>
    <tbody>
    @forelse($lecturas as $lectura)
        <tr>
            <td>{{ $lectura->incubadora->nombre ?? ('Incubadora #' . $lectura->incubadora_id) }}</td>
            <td>{{ $lectura->temperatura }} °C</td>
            <td>{{ $lectura->humedad }} %</td>
            <td>{{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i') }}</td>
            <td>{{ $lectura->observaciones ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5">No hay lecturas de microclima para este filtro.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
