<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Microclima - MicroSeed Control</title>

    <style>
        @page{margin:24px 28px 35px}
        *{box-sizing:border-box}

        body{
            margin:0;
            color:#334155;
            font-family:DejaVu Sans,sans-serif;
            font-size:9px;
        }

        .header{
            width:100%;
            margin-bottom:14px;
            padding-bottom:10px;
            border-bottom:3px solid #216a73;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .header-table td{
            padding:0;
            border:0;
            vertical-align:middle;
        }

        .header-left{width:78%}
        .header-right{width:22%;text-align:right}

        .logo{
            width:120px;
            height:auto;
        }

        .brand{
            color:#144255;
            font-size:21px;
            font-weight:bold;
        }

        .subtitle{
            margin-top:4px;
            color:#64748b;
            font-size:9px;
        }

        .title{
            margin-top:10px;
            color:#216a73;
            font-size:17px;
            font-weight:bold;
        }

        .generated{
            margin-top:4px;
            color:#64748b;
            font-size:8px;
        }

        .section{
            margin:14px 0 7px;
            color:#144255;
            font-size:10px;
            font-weight:bold;
            letter-spacing:.3px;
            text-transform:uppercase;
        }

        .info,
        .summary,
        .detail{
            width:100%;
            border-collapse:collapse;
        }

        .info td,
        .summary td{
            padding:8px;
            border:1px solid #dbe3e8;
            background:#f8fafc;
            vertical-align:top;
        }

        .label{
            color:#64748b;
            font-size:7px;
            text-transform:uppercase;
        }

        .value{
            margin-top:3px;
            color:#144255;
            font-size:11px;
            font-weight:bold;
        }

        .detail th{
            padding:6px;
            color:#fff;
            background:#144255;
            border:1px solid #144255;
            font-size:8px;
            text-align:left;
        }

        .detail td{
            padding:5px;
            border:1px solid #dbe3e8;
            font-size:8px;
            vertical-align:top;
        }

        .detail tr:nth-child(even) td{
            background:#f8fafc;
        }

        .center{text-align:center}

        .note{
            margin:8px 0;
            padding:8px 10px;
            color:#475569;
            background:#f0f9ff;
            border:1px solid #bae6fd;
        }

        .footer{
            position:fixed;
            right:0;
            bottom:-23px;
            left:0;
            padding-top:6px;
            color:#94a3b8;
            border-top:1px solid #dbe3e8;
            font-size:7px;
        }

        .right{float:right}
        .page:after{content:counter(page)}
    </style>
</head>

<body>

@php
    $logoPath=public_path('img/logo.png');

    $logoSrc=file_exists($logoPath)
        ?'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
        :null;

    $fechaInicio=!empty($filtros['fecha_inicio'])
        ?\Carbon\Carbon::parse($filtros['fecha_inicio'])->format('d/m/Y')
        :'Sin límite';

    $fechaFin=!empty($filtros['fecha_fin'])
        ?\Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y')
        :'Sin límite';

    $incubadora=$incubadoraSeleccionada
        ?(($incubadoraSeleccionada->codigo??'').' — '.$incubadoraSeleccionada->nombre)
        :'Todas las incubadoras';

    $primeraLectura=!empty($resumen['primera_lectura'])
        ?\Carbon\Carbon::parse($resumen['primera_lectura'])->format('d/m/Y H:i')
        :'Sin datos';

    $ultimaLectura=!empty($resumen['ultima_lectura'])
        ?\Carbon\Carbon::parse($resumen['ultima_lectura'])->format('d/m/Y H:i')
        :'Sin datos';
@endphp

<div class="header">
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="brand">MicroSeed Control</div>

                <div class="subtitle">
                    Sistema de monitoreo y control para germinación de semillas
                </div>

                <div class="title">
                    Reporte de Microclima
                </div>

                <div class="generated">
                    Generado el {{ now('America/Mexico_City')->format('d/m/Y H:i') }}
                </div>
            </td>

            <td class="header-right">
                @if($logoSrc)
                    <img
                        src="{{ $logoSrc }}"
                        class="logo"
                        alt="MicroSeed Control">
                @endif
            </td>
        </tr>
    </table>
</div>

<div class="section">
    Información del reporte
</div>

<table class="info">
    <tr>
        <td width="40%">
            <div class="label">Incubadora</div>
            <div class="value">{{ $incubadora }}</div>
        </td>

        <td width="20%">
            <div class="label">Fecha inicial</div>
            <div class="value">{{ $fechaInicio }}</div>
        </td>

        <td width="20%">
            <div class="label">Fecha final</div>
            <div class="value">{{ $fechaFin }}</div>
        </td>

        <td width="20%">
            <div class="label">Incubadoras incluidas</div>
            <div class="value">{{ $resumen['incubadoras'] }}</div>
        </td>
    </tr>
</table>

<div class="section">
    Resumen ambiental
</div>

<table class="summary">
    <tr>
        <td width="25%">
            <div class="label">Total de lecturas</div>
            <div class="value">{{ number_format($resumen['total']) }}</div>
        </td>

        <td width="25%">
            <div class="label">Temperatura promedio</div>
            <div class="value">
                {{ $resumen['temperatura_promedio']!==null
                    ?number_format($resumen['temperatura_promedio'],1).' °C'
                    :'—' }}
            </div>
        </td>

        <td width="25%">
            <div class="label">Temperatura mínima</div>
            <div class="value">
                {{ $resumen['temperatura_min']!==null
                    ?number_format($resumen['temperatura_min'],1).' °C'
                    :'—' }}
            </div>
        </td>

        <td width="25%">
            <div class="label">Temperatura máxima</div>
            <div class="value">
                {{ $resumen['temperatura_max']!==null
                    ?number_format($resumen['temperatura_max'],1).' °C'
                    :'—' }}
            </div>
        </td>
    </tr>
</table>

<table class="summary" style="margin-top:7px">
    <tr>
        <td width="33.33%">
            <div class="label">Humedad promedio</div>
            <div class="value">
                {{ $resumen['humedad_promedio']!==null
                    ?number_format($resumen['humedad_promedio'],1).' %'
                    :'—' }}
            </div>
        </td>

        <td width="33.33%">
            <div class="label">Humedad mínima</div>
            <div class="value">
                {{ $resumen['humedad_min']!==null
                    ?number_format($resumen['humedad_min'],1).' %'
                    :'—' }}
            </div>
        </td>

        <td width="33.33%">
            <div class="label">Humedad máxima</div>
            <div class="value">
                {{ $resumen['humedad_max']!==null
                    ?number_format($resumen['humedad_max'],1).' %'
                    :'—' }}
            </div>
        </td>
    </tr>
</table>

<table class="info" style="margin-top:7px">
    <tr>
        <td width="50%">
            <div class="label">Primera lectura registrada</div>
            <div class="value">{{ $primeraLectura }}</div>
        </td>

        <td width="50%">
            <div class="label">Última lectura registrada</div>
            <div class="value">{{ $ultimaLectura }}</div>
        </td>
    </tr>
</table>

<div class="section">
    Detalle de lecturas
</div>

@if($detalleLimitado)
    <div class="note">
        El resumen estadístico considera los
        <strong>{{ number_format($resumen['total']) }}</strong>
        registros que cumplen los filtros.

        Para mantener el PDF ligero y rápido, el detalle muestra las
        <strong>{{ $limiteDetalle }}</strong>
        lecturas más recientes.

        El historial completo puede descargarse mediante
        <strong>Exportar datos completos</strong>.
    </div>
@endif

<table class="detail">
    <thead>
    <tr>
        <th width="20%">Incubadora</th>
        <th width="12%" class="center">Temperatura</th>
        <th width="12%" class="center">Humedad</th>
        <th width="18%">Fecha y hora</th>
        <th width="38%">Observaciones</th>
    </tr>
    </thead>

    <tbody>
    @forelse($lecturas as $lectura)
        <tr>
            <td>
                <strong>{{ $lectura->incubadora->codigo??'—' }}</strong>
                <br>
                {{ $lectura->incubadora->nombre??('Incubadora #'.$lectura->incubadora_id) }}
            </td>

            <td class="center">
                {{ number_format((float)$lectura->temperatura,1) }} °C
            </td>

            <td class="center">
                {{ number_format((float)$lectura->humedad,1) }} %
            </td>

            <td>
                {{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i:s') }}
            </td>

            <td>
                {{ $lectura->observaciones?:'Sin observaciones' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="center">
                No se encontraron lecturas para los filtros seleccionados.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    MicroSeed Control · Reporte de Microclima

    <span class="right">
        Página <span class="page"></span>
    </span>
</div>

</body>
</html>
