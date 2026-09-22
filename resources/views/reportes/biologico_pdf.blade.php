<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Seguimiento Biológico - MicroSeed Control</title>

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

        .scientific{
            margin-top:2px;
            color:#64748b;
            font-size:8px;
            font-style:italic;
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

    $codigoLote=$loteSeleccionado?->codigo_lote??'Todos los lotes';
    $especie=$loteSeleccionado?->especie;

    $primerRegistro=!empty($resumen['primer_registro'])
        ?\Carbon\Carbon::parse($resumen['primer_registro'])->format('d/m/Y')
        :'Sin datos';

    $ultimoRegistro=!empty($resumen['ultimo_registro'])
        ?\Carbon\Carbon::parse($resumen['ultimo_registro'])->format('d/m/Y')
        :'Sin datos';
@endphp

<div class="header">
    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="brand">
                    MicroSeed Control
                </div>

                <div class="subtitle">
                    Sistema de monitoreo y control para germinación de semillas
                </div>

                <div class="title">
                    Reporte de Seguimiento Biológico
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
        <td width="25%">
            <div class="label">Lote</div>
            <div class="value">{{ $codigoLote }}</div>
        </td>

        <td width="25%">
            <div class="label">Especie</div>

            <div class="value">
                {{ $especie?->nombre_comun??($loteSeleccionado?'Sin especie registrada':'Todas las especies') }}
            </div>

            @if($especie?->nombre_cientifico)
                <div class="scientific">
                    {{ $especie->nombre_cientifico }}
                </div>
            @endif
        </td>

        <td width="20%">
            <div class="label">Fecha inicial</div>
            <div class="value">{{ $fechaInicio }}</div>
        </td>

        <td width="20%">
            <div class="label">Fecha final</div>
            <div class="value">{{ $fechaFin }}</div>
        </td>

        <td width="10%">
            <div class="label">Lotes incluidos</div>
            <div class="value">{{ $resumen['lotes'] }}</div>
        </td>
    </tr>
</table>

<div class="section">
    Resumen biológico
</div>

<table class="summary">
    <tr>
        <td width="20%">
            <div class="label">Total de registros</div>
            <div class="value">{{ number_format($resumen['total']) }}</div>
        </td>

        <td width="20%">
            <div class="label">Germinación promedio</div>
            <div class="value">
                {{ $resumen['germinacion_promedio']!==null
                    ?number_format($resumen['germinacion_promedio'],1).' %'
                    :'—' }}
            </div>
        </td>

        <td width="20%">
            <div class="label">Germinación mínima</div>
            <div class="value">
                {{ $resumen['germinacion_min']!==null
                    ?number_format($resumen['germinacion_min'],1).' %'
                    :'—' }}
            </div>
        </td>

        <td width="20%">
            <div class="label">Germinación máxima</div>
            <div class="value">
                {{ $resumen['germinacion_max']!==null
                    ?number_format($resumen['germinacion_max'],1).' %'
                    :'—' }}
            </div>
        </td>

        <td width="20%">
            <div class="label">Estratificación promedio</div>
            <div class="value">
                {{ $resumen['estratificacion_promedio']!==null
                    ?number_format($resumen['estratificacion_promedio'],1).' días'
                    :'—' }}
            </div>
        </td>
    </tr>
</table>

<table class="info" style="margin-top:7px">
    <tr>
        <td width="50%">
            <div class="label">Primer registro del periodo</div>
            <div class="value">{{ $primerRegistro }}</div>
        </td>

        <td width="50%">
            <div class="label">Último registro del periodo</div>
            <div class="value">{{ $ultimoRegistro }}</div>
        </td>
    </tr>
</table>

<div class="section">
    Detalle de seguimiento
</div>

@if($detalleLimitado)
    <div class="note">
        El resumen considera los
        <strong>{{ number_format($resumen['total']) }}</strong>
        registros encontrados.

        Para mantener el documento ligero, se muestran los
        <strong>{{ $limiteDetalle }}</strong>
        registros más recientes.

        El historial completo está disponible mediante
        <strong>Exportar datos completos</strong>.
    </div>
@endif

<table class="detail">
    <thead>
    <tr>
        <th width="17%">Lote</th>
        <th width="18%">Especie</th>
        <th width="11%">Fecha</th>
        <th width="13%" class="center">Estratificación</th>
        <th width="13%" class="center">Germinación</th>
        <th width="14%">Responsable</th>
        <th width="14%">Observaciones</th>
    </tr>
    </thead>

    <tbody>
    @forelse($registros as $registro)
        <tr>
            <td>
                {{ $registro->lote->codigo_lote??('Lote #'.$registro->lote_id) }}
            </td>

            <td>
                @if($registro->lote?->especie)
                    {{ $registro->lote->especie->nombre_comun }}

                    @if($registro->lote->especie->nombre_cientifico)
                        <br>

                        <span class="scientific">
                                {{ $registro->lote->especie->nombre_cientifico }}
                            </span>
                    @endif
                @else
                    —
                @endif
            </td>

            <td>
                {{ $registro->fecha_registro
                    ?\Carbon\Carbon::parse($registro->fecha_registro)->format('d/m/Y')
                    :'—' }}
            </td>

            <td class="center">
                {{ $registro->dias_estratificacion!==null
                    ?$registro->dias_estratificacion.' días'
                    :'—' }}
            </td>

            <td class="center">
                {{ $registro->tasa_germinacion!==null
                    ?number_format((float)$registro->tasa_germinacion,1).' %'
                    :'—' }}
            </td>

            <td>
                {{ $registro->usuario->name??'Sin usuario' }}
            </td>

            <td>
                {{ $registro->observaciones?:'Sin observaciones' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="center">
                No se encontraron registros biológicos para los filtros seleccionados.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    MicroSeed Control · Reporte de Seguimiento Biológico

    <span class="right">
        Página <span class="page"></span>
    </span>
</div>

</body>
</html>
