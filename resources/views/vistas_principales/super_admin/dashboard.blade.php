<x-app-layout>
    @php
        $usuario=auth()->user();
        $nombreCorto=$usuario?explode(' ',trim($usuario->name))[0]:'Usuario';
        $rolNombre=$usuario?->rol_nombre??'Superadministrador';
        $incubadoraTiempoRealId=$incubadoraActualId??optional($incubadoras->first())->id;

        // Conteo inicial para la gráfica de condición de incubadoras
        $conteoEstable=0;$conteoAlerta=0;$conteoSinLectura=0;
        foreach($resumenIncubadoras as $item){
            if(!$item['ultima_lectura']){$conteoSinLectura++;}
            elseif($item['alertas_abiertas']>0){$conteoAlerta++;}
            else{$conteoEstable++;}
        }
        // Íconos de línea del sistema (mismo estilo que el menú lateral), sin paquetes externos
        $iconos = [
            'usuarios'    => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5"/><path d="M16 4.7a3.5 3.5 0 0 1 0 6.6"/><path d="M18 14.9c1.8.7 3 2.5 3 5.1"/>',
            'incubadora'  => '<rect x="7" y="7" width="10" height="10" rx="2"/><rect x="10" y="10" width="4" height="4" rx=".5"/><path d="M9 3v2M15 3v2M9 19v2M15 19v2M3 9h2M3 15h2M19 9h2M19 15h2"/>',
            'lecturas'    => '<path d="M4 20h16"/><path d="M7 16v-5M11 16V6M15 16v-7M19 16V4"/>',
            'alerta'      => '<path d="M10.3 4.3 2.9 17.2A2 2 0 0 0 4.6 20h14.8a2 2 0 0 0 1.7-2.8L13.7 4.3a2 2 0 0 0-3.4 0z"/><path d="M12 9.5v4"/><path d="M12 17h.01"/>',
            'monitoreo'   => '<path d="M3 12h4l2.5-6 5 12 2.5-6h4"/>',
            'temperatura' => '<path d="M10 13.5V5a2 2 0 1 1 4 0v8.5a4 4 0 1 1-4 0z"/><path d="M12 16v-5"/>',
            'humedad'     => '<path d="M12 3.5S6 9.9 6 14a6 6 0 0 0 12 0c0-4.1-6-10.5-6-10.5z"/><path d="M9.5 15a2.5 2.5 0 0 0 2.5 2.5"/>',
            'lote'        => '<path d="M12 20v-8"/><path d="M12 12C12 8.5 9.5 6 5 6c0 3.6 2.6 6 7 6z"/><path d="M12 14c0-3 2.3-5 6-5 0 3-2.3 5-6 5z"/>',
            'frasco'      => '<path d="M9 3h6"/><path d="M10 3v6l-5 9.2A1.5 1.5 0 0 0 6.3 20.5h11.4a1.5 1.5 0 0 0 1.3-2.3L14 9V3"/><path d="M7.4 15h9.2"/>',
            'niebla'      => '<path d="M7 14.5h10a3.5 3.5 0 0 0 .4-7A5 5 0 0 0 7.7 7.7 3.4 3.4 0 0 0 7 14.5z"/><path d="M5 18h9M9 21h10"/>',
            'calefaccion' => '<path d="M12 21a5.5 5.5 0 0 0 5.5-5.5c0-3.5-2.5-5.5-3.5-9-1.5 1.5-2 3-2 4.5-1-.8-1.5-2-1.5-3-2 2-4 4.5-4 7.5A5.5 5.5 0 0 0 12 21z"/>',
            'ventilacion' => '<circle cx="12" cy="12" r="1.5"/><path d="M12 10.5C11 7 12 4 15 4c2 0 2.5 2 1.5 3.5S13.5 10 12 10.5z"/><path d="M12 10.5C11 7 12 4 15 4c2 0 2.5 2 1.5 3.5S13.5 10 12 10.5z" transform="rotate(120 12 12)"/><path d="M12 10.5C11 7 12 4 15 4c2 0 2.5 2 1.5 3.5S13.5 10 12 10.5z" transform="rotate(240 12 12)"/>',
            'iluminacion' => '<path d="M9 18h6"/><path d="M10 21h4"/><path d="M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1v.5h5V16c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3z"/>',
            'control'     => '<path d="M4 6h9M17 6h3M4 12h3M11 12h9M4 18h11M19 18h1"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="17" cy="18" r="2"/>',
            'reloj'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        ];
        $icono=fn(string $nombre)=>'<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.($iconos[$nombre]??'').'</svg>';
    @endphp

    <style>
        /* =========================================================
           PANEL DE CONTROL
           Tarjetas blancas, bordes finos, gráficas protagonistas.
        ========================================================= */
        .pc {
            --pc-ink: #18262c;
            --pc-text: #3f5058;
            --pc-muted: #6b7a80;
            --pc-line: #e3e8ea;
            --pc-line-soft: #eef1f2;
            --pc-surface: #ffffff;
            --pc-head: #f7f9fa;
            --pc-brand: #1f6f86;
            --pc-icon: #5f7178;
            --pc-ok: #1e7a4f;
            --pc-ok-bg: #e7f4ec;
            --pc-danger: #b42318;
            --pc-danger-bg: #fbeceb;
            --pc-neutral-bg: #eef2f3;
            --pc-temp: #c2552d;
            --pc-hum: #1f6f86;
            color: var(--pc-text);
            font-size: .9rem;
        }
        .pc-num { font-variant-numeric: tabular-nums; }
        .pc .icono { width: 1.15em; height: 1.15em; flex-shrink: 0; color: var(--pc-icon); }

        /* Encabezado */
        .pc-page-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 24px; margin-bottom: 20px; }
        .pc-page-head h1 { margin: 0; font-size: 1.5rem; font-weight: 600; color: var(--pc-ink); letter-spacing: -.01em; }
        .pc-page-head p { margin: 2px 0 0; color: var(--pc-muted); }
        .pc-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .pc-meta-item {
            display: inline-flex; align-items: center; gap: 8px; height: 36px; padding: 0 14px;
            background: var(--pc-surface); border: 1px solid var(--pc-line); border-radius: 10px; font-size: .82rem; color: var(--pc-muted);
        }
        .pc-meta-item strong { color: var(--pc-ink); font-weight: 600; }

        /* Estados */
        .pc-state { display: inline-flex; align-items: center; gap: 7px; font-weight: 500; color: var(--pc-muted); white-space: nowrap; }
        .pc-state::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
        .pc-state--ok { color: var(--pc-ok); }
        .pc-state--danger { color: var(--pc-danger); }
        .pc-state--off { color: #8a979c; }

        .pc-badge {
            display: inline-flex; align-items: center; height: 22px; padding: 0 8px; border-radius: 6px;
            font-size: .74rem; font-weight: 600; white-space: nowrap; background: var(--pc-neutral-bg); color: var(--pc-text);
        }
        .pc-badge--ok { background: var(--pc-ok-bg); color: var(--pc-ok); }
        .pc-badge--danger { background: var(--pc-danger-bg); color: var(--pc-danger); }

        /* Tarjetas */
        .pc-card { background: var(--pc-surface); border: 1px solid var(--pc-line); border-radius: 12px; box-shadow: 0 1px 2px rgba(24, 38, 44, .04); }
        .pc-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; padding: 16px 20px 0; }
        .pc-card-title { margin: 0; display: flex; align-items: center; gap: 9px; font-size: .98rem; font-weight: 600; color: var(--pc-ink); }
        .pc-card-body { padding: 16px 20px 20px; }

        /* Indicadores con mini gráfica */
        .pc-kpi { padding: 16px 18px 14px; height: 100%; display: flex; flex-direction: column; }
        .pc-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .pc-kpi-label { display: inline-flex; align-items: center; gap: 8px; font-size: .84rem; font-weight: 500; color: var(--pc-text); }
        .pc-kpi-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-top: 12px; }
        .pc-kpi-value { font-size: 1.85rem; font-weight: 600; line-height: 1; color: var(--pc-ink); white-space: nowrap; }
        .pc-kpi-value small { font-size: .95rem; font-weight: 500; color: var(--pc-muted); margin-left: 3px; }
        .pc-kpi-spark { position: relative; width: 46%; max-width: 140px; height: 42px; }
        .pc-kpi-note { margin-top: 10px; font-size: .78rem; color: var(--pc-muted); }
        .pc-kpi.is-alert .pc-kpi-value { color: var(--pc-danger); }
        .pc-kpi.is-alert .pc-kpi-label .icono { color: var(--pc-danger); }

        /* Gráfica principal */
        .pc-legend { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 16px; font-size: .8rem; color: var(--pc-muted); }
        .pc-legend span { display: inline-flex; align-items: center; gap: 7px; }
        .pc-legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
        .pc-chart { position: relative; height: 300px; transition: opacity .2s ease; }
        .pc-card.is-loading .pc-chart { opacity: .4; }
        .pc-chart-empty {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            font-size: .86rem; color: var(--pc-muted); pointer-events: none;
        }
        .pc-chart-empty[hidden] { display: none; }
        .pc-card-sub { margin: 4px 0 0; font-size: .8rem; color: var(--pc-muted); }

        /* Selector de incubadora */
        .pc-select-wrap { display: inline-flex; align-items: center; gap: 8px; font-size: .8rem; color: var(--pc-muted); }
        .pc-select {
            height: 34px; min-width: 210px; max-width: 280px; padding: 0 32px 0 12px;
            border: 1px solid var(--pc-line); border-radius: 8px; background-color: var(--pc-surface);
            color: var(--pc-ink); font: inherit; font-size: .84rem; font-weight: 500; cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235f7178' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; background-size: 14px;
        }
        .pc-select:hover { border-color: #cfd8db; }
        .pc-select:focus { outline: none; border-color: var(--pc-brand); box-shadow: 0 0 0 .2rem rgba(31, 111, 134, .15); }
        .pc-select:disabled { cursor: not-allowed; color: var(--pc-muted); }
        .pc-chart-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 16px; }
        .pc-chart-foot { margin-top: 10px; font-size: .78rem; color: var(--pc-muted); }

        /* Actuadores */
        .pc-mode {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            margin: 14px 20px 6px; padding: 12px 14px; background: var(--pc-head); border: 1px solid var(--pc-line); border-radius: 10px;
        }
        .pc-act { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; align-items: center; gap: 14px; padding: 11px 20px; border-bottom: 1px solid var(--pc-line-soft); }
        .pc-act:last-of-type { border-bottom: 0; }
        .pc-act-name { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .pc-act-name .icono { font-size: 1.25rem; transition: color .2s ease; }
        .pc-act.is-on .pc-act-name .icono, .pc-mode .icono { color: var(--pc-brand); }
        .pc-act-state { min-width: 88px; font-size: .8rem; }
        .pc-cell-main { display: block; font-weight: 600; color: var(--pc-ink); }
        .pc-cell-sub { display: block; font-size: .78rem; color: var(--pc-muted); }
        .pc-note { margin: 0; padding: 12px 20px 16px; font-size: .78rem; color: var(--pc-muted); line-height: 1.5; border-top: 1px solid var(--pc-line-soft); }
        .pc .form-switch .form-check-input { width: 2.2em; height: 1.2em; cursor: pointer; }
        .pc .form-check-input:checked { background-color: var(--pc-brand); border-color: var(--pc-brand); }
        .pc .form-check-input:focus { box-shadow: 0 0 0 .2rem rgba(31, 111, 134, .2); border-color: var(--pc-brand); }
        .pc .form-check-input:disabled { cursor: not-allowed; }

        /* Tabla */
        .pc-table-wrap { overflow-x: auto; margin-top: 12px; }
        .pc-table { width: 100%; border-collapse: collapse; font-size: .86rem; }
        .pc-table th { text-align: left; font-weight: 600; font-size: .78rem; color: var(--pc-muted); padding: 9px 20px; border-bottom: 1px solid var(--pc-line); white-space: nowrap; }
        .pc-table td { padding: 11px 20px; border-bottom: 1px solid var(--pc-line-soft); vertical-align: middle; }
        .pc-table tbody tr:last-child td { border-bottom: 0; }
        .pc-table tbody tr:hover td { background: #fafbfc; }
        .pc-table td.pc-num, .pc-table td.nowrap, .pc-cell-main { white-space: nowrap; }
        .pc-meter { display: flex; align-items: center; gap: 10px; min-width: 150px; }
        .pc-meter-track { flex: 1; height: 6px; border-radius: 99px; background: var(--pc-line-soft); overflow: hidden; }
        .pc-meter-fill { display: block; height: 100%; border-radius: 99px; background: var(--pc-hum); }
        .pc-meter-value { min-width: 48px; text-align: right; font-weight: 600; color: var(--pc-ink); }
        .pc-empty { text-align: center; color: var(--pc-muted); padding: 28px 20px !important; }

        /* Distribución */
        .pc-donut { position: relative; width: 190px; height: 190px; margin: 6px auto 18px; }
        .pc-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none; }
        .pc-donut-center strong { font-size: 1.75rem; font-weight: 600; color: var(--pc-ink); line-height: 1; }
        .pc-donut-center span { font-size: .76rem; color: var(--pc-muted); margin-top: 4px; }
        .pc-dist { list-style: none; margin: 0; padding: 0; }
        .pc-dist li { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; font-size: .84rem; }
        .pc-dist li span { display: inline-flex; align-items: center; gap: 8px; }
        .pc-dist i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
        .pc-dist strong { color: var(--pc-ink); font-weight: 600; }
        .pc-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1px; background: var(--pc-line); border-top: 1px solid var(--pc-line); margin: 16px -20px -20px; border-radius: 0 0 12px 12px; overflow: hidden; }
        .pc-stat { background: var(--pc-surface); padding: 12px 20px; }
        .pc-stat span { display: flex; align-items: center; gap: 7px; font-size: .78rem; color: var(--pc-muted); }
        .pc-stat strong { display: block; margin-top: 4px; font-size: 1.15rem; font-weight: 600; color: var(--pc-ink); }

        @media (max-width: 575.98px) {
            .pc-card-head, .pc-card-body, .pc-act, .pc-note { padding-left: 16px; padding-right: 16px; }
            .pc-mode { margin-left: 16px; margin-right: 16px; }
            .pc-table th, .pc-table td { padding-left: 16px; padding-right: 16px; }
            .pc-chart { height: 240px; }
            .pc-select { min-width: 0; width: 100%; max-width: none; }
            .pc-select-wrap { width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) { .pc-act-name .icono { transition: none; } }
    </style>

    <div class="container-fluid py-4 px-3 px-lg-4 pc"
         x-data="{
            time:'',date:'',
            init(){this.updateClock();setInterval(()=>this.updateClock(),1000)},
            updateClock(){
                const now=new Date();
                this.time=new Intl.DateTimeFormat('es-MX',{timeZone:'America/Mexico_City',hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false}).format(now);
                const fecha=new Intl.DateTimeFormat('es-MX',{timeZone:'America/Mexico_City',weekday:'long',day:'numeric',month:'long'}).format(now);
                this.date=fecha.charAt(0).toUpperCase()+fecha.slice(1);
            }
         }">

        {{-- =====================================================
            ENCABEZADO
        ====================================================== --}}
        <header class="pc-page-head">
            <div>
                <h1>Panel de control</h1>
                <p>{{ $nombreCorto }}, {{ $rolNombre }}</p>
            </div>
            <div class="pc-meta">
                <span class="pc-meta-item">{!! $icono('reloj') !!}<span x-text="date"></span><strong class="pc-num" x-text="time">--:--:--</strong></span>
                <span class="pc-meta-item"><span id="connectionBadge" class="pc-state" role="status">Conectando</span></span>
            </div>
        </header>

        {{-- =====================================================
            INDICADORES
        ====================================================== --}}
        <section class="row g-3 mb-3" aria-label="Indicadores">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('temperatura') !!}Temperatura</span>
                        <span id="tempDelta" class="pc-badge pc-num">--</span>
                    </div>
                    <div class="pc-kpi-row">
                        <span class="pc-kpi-value pc-num"><span id="dht22Temp">--</span><small>°C</small></span>
                        <div class="pc-kpi-spark"><canvas id="sparkTemp" aria-hidden="true"></canvas></div>
                    </div>
                    <span class="pc-kpi-note">Incubadora seleccionada, variación del periodo</span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('humedad') !!}Humedad relativa</span>
                        <span id="humDelta" class="pc-badge pc-num">--</span>
                    </div>
                    <div class="pc-kpi-row">
                        <span class="pc-kpi-value pc-num"><span id="dht22Hum">--</span><small>%</small></span>
                        <div class="pc-kpi-spark"><canvas id="sparkHum" aria-hidden="true"></canvas></div>
                    </div>
                    <span class="pc-kpi-note">Incubadora seleccionada, variación del periodo</span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('lecturas') !!}Lecturas de hoy</span>
                        <span class="pc-badge">Hoy</span>
                    </div>
                    <div class="pc-kpi-row">
                        <span id="metricLecturasHoy" class="pc-kpi-value pc-num">{{ $lecturasHoy }}</span>
                    </div>
                    <span class="pc-kpi-note">Última lectura: <strong id="dht22Time" class="pc-num">--:--:--</strong></span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article id="kpiAlertas" class="pc-card pc-kpi {{ $alertasActivas>0?'is-alert':'' }}">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('alerta') !!}Alertas activas</span>
                        <span id="metricAlertasEstado">
                            @if($alertasActivas>0)
                                <span class="pc-badge pc-badge--danger">Requiere atención</span>
                            @else
                                <span class="pc-badge pc-badge--ok">Sin incidencias</span>
                            @endif
                        </span>
                    </div>
                    <div class="pc-kpi-row">
                        <span id="metricAlertasActivas" class="pc-kpi-value pc-num">{{ $alertasActivas }}</span>
                    </div>
                    <span class="pc-kpi-note">Pendientes de atender o resolver</span>
                </article>
            </div>
        </section>

        {{-- =====================================================
            GRÁFICA PRINCIPAL + ACTUADORES
        ====================================================== --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8">
                <section id="panelMonitoreo" class="pc-card h-100" aria-labelledby="tituloMonitoreo">
                    <div class="pc-card-head">
                        <div>
                            <h2 id="tituloMonitoreo" class="pc-card-title">{!! $icono('monitoreo') !!}Temperatura y humedad</h2>
                            <p class="pc-card-sub">Lecturas en tiempo real de la incubadora seleccionada</p>
                        </div>

                        <label class="pc-select-wrap" for="incubadoraSelect">
                            Incubadora
                            <select id="incubadoraSelect" class="pc-select" @disabled($incubadoras->isEmpty())>
                                @forelse($incubadoras as $opcion)
                                    <option value="{{ $opcion->id }}" @selected((string) $opcion->id === (string) $incubadoraTiempoRealId)>
                                        {{ $opcion->nombre }}{{ $opcion->codigo ? ' ('.$opcion->codigo.')' : '' }}
                                    </option>
                                @empty
                                    <option value="">Sin incubadoras registradas</option>
                                @endforelse
                            </select>
                        </label>
                    </div>
                    <div class="pc-card-body">
                        <div class="pc-chart">
                            <canvas id="ambienteChart" role="img" aria-label="Gráfica de temperatura y humedad de la incubadora seleccionada"></canvas>
                            <div id="graficaVacia" class="pc-chart-empty" hidden>Sin lecturas registradas para esta incubadora.</div>
                        </div>
                        <div class="pc-chart-foot">
                            <div class="pc-legend">
                                <span><i style="background:#c2552d"></i>Temperatura (°C), eje izquierdo</span>
                                <span><i style="background:#1f6f86"></i>Humedad relativa (%), eje derecho</span>
                            </div>
                            <span>Sensor DHT22</span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="pc-card h-100" aria-labelledby="tituloControl">
                    <div class="pc-card-head">
                        <h2 id="tituloControl" class="pc-card-title">{!! $icono('control') !!}Actuadores</h2>
                        <span id="modoBadge" class="pc-badge pc-badge--ok">Automático</span>
                    </div>

                    <div class="pc-mode">
                        <div class="pc-act-name">
                            {!! $icono('control') !!}
                            <div>
                                <span class="pc-cell-main">Control manual</span>
                                <span class="pc-cell-sub">Encender y apagar cada actuador</span>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input id="modoSwitch" class="form-check-input" type="checkbox" role="switch" aria-label="Activar control manual">
                        </div>
                    </div>

                    <div class="pc-act">
                        <div class="pc-act-name">
                            {!! $icono('niebla') !!}
                            <div><span class="pc-cell-main">Niebla</span><span class="pc-cell-sub">Eleva la humedad</span></div>
                        </div>
                        <span id="nieblaBadge" class="pc-act-state pc-state pc-state--off">Apagado</span>
                        <div class="form-check form-switch m-0"><input id="nieblaSwitch" class="form-check-input" type="checkbox" role="switch" disabled aria-label="Generador de niebla"></div>
                    </div>

                    <div class="pc-act">
                        <div class="pc-act-name">
                            {!! $icono('calefaccion') !!}
                            <div><span class="pc-cell-main">Calefacción</span><span class="pc-cell-sub">Eleva la temperatura</span></div>
                        </div>
                        <span id="calefaccionBadge" class="pc-act-state pc-state pc-state--off">Apagada</span>
                        <div class="form-check form-switch m-0"><input id="calefaccionSwitch" class="form-check-input" type="checkbox" role="switch" disabled aria-label="Calefacción"></div>
                    </div>

                    <div class="pc-act">
                        <div class="pc-act-name">
                            {!! $icono('ventilacion') !!}
                            <div><span class="pc-cell-main">Ventilación</span><span class="pc-cell-sub">Renueva el aire</span></div>
                        </div>
                        <span id="ventilacionBadge" class="pc-act-state pc-state pc-state--off">Apagada</span>
                        <div class="form-check form-switch m-0"><input id="ventilacionSwitch" class="form-check-input" type="checkbox" role="switch" disabled aria-label="Ventilación"></div>
                    </div>

                    <div class="pc-act">
                        <div class="pc-act-name">
                            {!! $icono('iluminacion') !!}
                            <div><span class="pc-cell-main">Iluminación</span><span class="pc-cell-sub">Luz de cultivo</span></div>
                        </div>
                        <span id="ledBadge" class="pc-act-state pc-state pc-state--off">Apagado</span>
                        <div class="form-check form-switch m-0"><input id="ledSwitch" class="form-check-input" type="checkbox" role="switch" disabled aria-label="Iluminación"></div>
                    </div>

                    <p id="modoNota" class="pc-note">En modo automático el sistema regula los actuadores según las condiciones óptimas de cada especie.</p>
                </section>
            </div>
        </div>

        {{-- =====================================================
            TABLA DE INCUBADORAS + DISTRIBUCIÓN
        ====================================================== --}}
        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <section class="pc-card h-100" aria-labelledby="tituloIncubadoras">
                    <div class="pc-card-head">
                        <h2 id="tituloIncubadoras" class="pc-card-title">{!! $icono('incubadora') !!}Incubadoras</h2>
                        <span class="pc-badge">Última lectura por equipo</span>
                    </div>

                    <div class="pc-table-wrap">
                        <table class="pc-table">
                            <thead>
                            <tr>
                                <th scope="col">Incubadora</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Condición</th>
                                <th scope="col" class="text-end">Temperatura</th>
                                <th scope="col">Humedad</th>
                                <th scope="col">Última lectura</th>
                            </tr>
                            </thead>
                            <tbody id="resumenIncubadorasLive">
                            @forelse($resumenIncubadoras as $item)
                                @php
                                    $inc=$item['incubadora'];
                                    $lectura=$item['ultima_lectura'];
                                    $alertas=$item['alertas_abiertas'];
                                    $hum=$lectura?max(0,min(100,(float)$lectura->humedad)):0;
                                @endphp
                                <tr>
                                    <td><span class="pc-cell-main">{{ $inc->nombre }}</span><span class="pc-cell-sub">{{ $inc->codigo }}</span></td>
                                    <td class="nowrap">{{ $inc->estado->nombre??'Sin estado' }}</td>
                                    <td>
                                        @if(!$lectura)
                                            <span class="pc-badge">Sin lecturas</span>
                                        @elseif($alertas>0)
                                            <span class="pc-badge pc-badge--danger">Alerta</span>
                                        @else
                                            <span class="pc-badge pc-badge--ok">Estable</span>
                                        @endif
                                    </td>
                                    <td class="text-end pc-num">{{ $lectura?$lectura->temperatura.' °C':'—' }}</td>
                                    <td>
                                        @if($lectura)
                                            <div class="pc-meter">
                                                <span class="pc-meter-track"><span class="pc-meter-fill" style="width: {{ $hum }}%"></span></span>
                                                <span class="pc-meter-value pc-num">{{ $lectura->humedad }} %</span>
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="pc-num">{{ $lectura?\Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i'):'—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="pc-empty">No hay incubadoras registradas.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="pc-card h-100" aria-labelledby="tituloDistribucion">
                    <div class="pc-card-head">
                        <h2 id="tituloDistribucion" class="pc-card-title">{!! $icono('incubadora') !!}Condición de incubadoras</h2>
                    </div>
                    <div class="pc-card-body">
                        <div class="pc-donut">
                            <canvas id="condicionChart" role="img" aria-label="Distribución de incubadoras por condición"></canvas>
                            <div class="pc-donut-center">
                                <strong id="donutTotal" class="pc-num">{{ $conteoEstable+$conteoAlerta+$conteoSinLectura }}</strong>
                                <span>incubadoras</span>
                            </div>
                        </div>

                        <ul class="pc-dist">
                            <li><span><i style="background:#1f6f86"></i>Estables</span><strong id="cuentaEstable" class="pc-num">{{ $conteoEstable }}</strong></li>
                            <li><span><i style="background:#c2552d"></i>Con alerta</span><strong id="cuentaAlerta" class="pc-num">{{ $conteoAlerta }}</strong></li>
                            <li><span><i style="background:#c9d3d7"></i>Sin lecturas</span><strong id="cuentaSinLectura" class="pc-num">{{ $conteoSinLectura }}</strong></li>
                        </ul>

                        <div class="pc-stats">
                            <div class="pc-stat"><span>{!! $icono('usuarios') !!}Usuarios</span><strong id="metricUsuariosTotal" class="pc-num">{{ $usuariosTotal }}</strong></div>
                            <div class="pc-stat"><span>{!! $icono('incubadora') !!}Incubadoras</span><strong id="metricIncubadorasTotal" class="pc-num">{{ $incubadorasTotal }}</strong></div>
                            <div class="pc-stat"><span>{!! $icono('lote') !!}Lotes</span><strong id="metricLotesTotal" class="pc-num">{{ $lotesTotal }}</strong></div>
                            <div class="pc-stat"><span>{!! $icono('frasco') !!}Frascos</span><strong id="metricFrascosTotal" class="pc-num">{{ $frascosTotal }}</strong></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded',()=>{
            const URL_TIEMPO_REAL_BASE=@json(route('super_admin.dashboard.tiempo-real'));

            /* ---------- Incubadora seleccionada ---------- */
            const selectorIncubadora=document.getElementById('incubadoraSelect');
            const panelMonitoreo=document.getElementById('panelMonitoreo');
            let incubadoraSeleccionada=String(@json($incubadoraTiempoRealId)??'');

            // Si la URL trae ?incubadora_id=, se respeta (así se conserva al recargar la página)
            const idDesdeUrl=new URLSearchParams(window.location.search).get('incubadora_id');
            if(idDesdeUrl&&selectorIncubadora&&[...selectorIncubadora.options].some(opcion=>opcion.value===idDesdeUrl)){
                incubadoraSeleccionada=idDesdeUrl;
                selectorIncubadora.value=idDesdeUrl;
            }

            function urlTiempoReal(){
                const parametros=new URLSearchParams();
                if(incubadoraSeleccionada)parametros.set('incubadora_id',incubadoraSeleccionada);
                parametros.set('t',Date.now());
                return `${URL_TIEMPO_REAL_BASE}?${parametros.toString()}`;
            }

            let numeroSolicitud=0;

            const ACTUADORES=['niebla','calefaccion','ventilacion','luz'];

            const URL_ACTUADORES={
                niebla:@json(route('super_admin.microclima.actuadores.update','niebla')),
                calefaccion:@json(route('super_admin.microclima.actuadores.update','calefaccion')),
                ventilacion:@json(route('super_admin.microclima.actuadores.update','ventilacion')),
                luz:@json(route('super_admin.microclima.actuadores.update','luz'))
            };

            const URL_ESTADOS_ACTUADORES={
                niebla:@json(route('super_admin.microclima.actuadores.estado','niebla')),
                calefaccion:@json(route('super_admin.microclima.actuadores.estado','calefaccion')),
                ventilacion:@json(route('super_admin.microclima.actuadores.estado','ventilacion')),
                luz:@json(route('super_admin.microclima.actuadores.estado','luz'))
            };

            const URL_MODO={
                show:@json(route('super_admin.microclima.modo')),
                update:@json(route('super_admin.microclima.modo.update'))
            };

            const COLORES={temp:'#c2552d',hum:'#1f6f86',sinLectura:'#c9d3d7',texto:'#6b7a80',linea:'#eef1f2',tinta:'#18262c'};

            const csrfToken=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const elementos={
                temperatura:document.getElementById('dht22Temp'),
                humedad:document.getElementById('dht22Hum'),
                hora:document.getElementById('dht22Time'),
                tempDelta:document.getElementById('tempDelta'),
                humDelta:document.getElementById('humDelta'),
                modoSwitch:document.getElementById('modoSwitch'),
                modoBadge:document.getElementById('modoBadge'),
                modoNota:document.getElementById('modoNota'),
                connectionBadge:document.getElementById('connectionBadge'),
                kpiAlertas:document.getElementById('kpiAlertas')
            };

            const controles={
                niebla:{switch:document.getElementById('nieblaSwitch'),badge:document.getElementById('nieblaBadge')},
                calefaccion:{switch:document.getElementById('calefaccionSwitch'),badge:document.getElementById('calefaccionBadge')},
                ventilacion:{switch:document.getElementById('ventilacionSwitch'),badge:document.getElementById('ventilacionBadge')},
                luz:{switch:document.getElementById('ledSwitch'),badge:document.getElementById('ledBadge')}
            };

            let ambienteChart=null;
            let sparkTemp=null;
            let sparkHum=null;
            let condicionChart=null;
            let peticionActiva=false;
            let modoManual=false;

            function normalizarArray(valor){
                if(!valor)return[];
                return Array.isArray(valor)?valor:Object.values(valor);
            }

            function escapeHtml(valor){
                const div=document.createElement('div');
                div.textContent=valor??'';
                return div.innerHTML;
            }

            function setText(id,valor){
                const elemento=document.getElementById(id);
                if(elemento)elemento.textContent=valor??'—';
            }

            function setConnectionState(online){
                if(!elementos.connectionBadge)return;
                elementos.connectionBadge.className=online?'pc-state pc-state--ok':'pc-state pc-state--danger';
                elementos.connectionBadge.textContent=online?'En línea':'Sin conexión';
            }

            function notify(type,title,message){
                if(typeof window.microseedAlert==='function'){window.microseedAlert(type,title,message);return;}
                if(window.Swal){window.Swal.fire({icon:type,title,text:message,confirmButtonText:'Aceptar'});return;}
                alert(message);
            }

            function actualizarEstadoAlertas(totalAlertas){
                const hayAlertas=Number(totalAlertas||0)>0;
                const contenedor=document.getElementById('metricAlertasEstado');
                if(contenedor){
                    contenedor.innerHTML=hayAlertas
                        ?'<span class="pc-badge pc-badge--danger">Requiere atención</span>'
                        :'<span class="pc-badge pc-badge--ok">Sin incidencias</span>';
                }
                elementos.kpiAlertas?.classList.toggle('is-alert',hayAlertas);
            }

            /* ---------- Etiqueta con el último valor de cada serie ---------- */
            const etiquetaUltimoValor={
                id:'etiquetaUltimoValor',
                afterDatasetsDraw(chart){
                    const {ctx,chartArea}=chart;
                    chart.data.datasets.forEach((ds,i)=>{
                        const meta=chart.getDatasetMeta(i);
                        if(meta.hidden||!meta.data.length)return;
                        const punto=meta.data[meta.data.length-1];
                        const valor=Number(ds.data[ds.data.length-1]);
                        if(!Number.isFinite(valor))return;

                        const texto=valor.toFixed(1)+(ds.yAxisID==='yHumedad'?' %':' °C');
                        ctx.save();
                        ctx.font='600 11px system-ui, -apple-system, "Segoe UI", sans-serif';
                        const ancho=ctx.measureText(texto).width+14;
                        const alto=22;
                        const x=Math.min(Math.max(punto.x-ancho/2,chartArea.left),chartArea.right-ancho);
                        const y=Math.max(punto.y-alto-10,chartArea.top);

                        ctx.fillStyle='#ffffff';
                        ctx.strokeStyle=ds.borderColor;
                        ctx.lineWidth=1;
                        ctx.beginPath();
                        if(ctx.roundRect){ctx.roundRect(x,y,ancho,alto,6);}else{ctx.rect(x,y,ancho,alto);}
                        ctx.fill();
                        ctx.stroke();

                        ctx.fillStyle=COLORES.tinta;
                        ctx.textBaseline='middle';
                        ctx.fillText(texto,x+7,y+alto/2+.5);
                        ctx.restore();
                    });
                }
            };

            function crearSpark(id,color){
                const canvas=document.getElementById(id);
                if(!canvas||!window.Chart)return null;
                const ctx=canvas.getContext('2d');
                const relleno=ctx.createLinearGradient(0,0,0,42);
                relleno.addColorStop(0,color==='#c2552d'?'rgba(194,85,45,.18)':'rgba(31,111,134,.18)');
                relleno.addColorStop(1,'rgba(255,255,255,0)');

                return new window.Chart(ctx,{
                    type:'line',
                    data:{labels:[],datasets:[{data:[],borderColor:color,backgroundColor:relleno,borderWidth:1.6,pointRadius:0,tension:.4,fill:true}]},
                    options:{
                        responsive:true,maintainAspectRatio:false,animation:false,
                        events:[],
                        plugins:{legend:{display:false},tooltip:{enabled:false}},
                        scales:{x:{display:false},y:{display:false,grace:'10%'}}
                    }
                });
            }

            function iniciarGraficas(){
                if(!window.Chart)return;

                /* Gráfica principal */
                const canvas=document.getElementById('ambienteChart');
                if(canvas){
                    const ctx=canvas.getContext('2d');
                    const rellenoHum=ctx.createLinearGradient(0,0,0,300);
                    rellenoHum.addColorStop(0,'rgba(31,111,134,.22)');
                    rellenoHum.addColorStop(1,'rgba(31,111,134,0)');
                    const rellenoTemp=ctx.createLinearGradient(0,0,0,300);
                    rellenoTemp.addColorStop(0,'rgba(194,85,45,.16)');
                    rellenoTemp.addColorStop(1,'rgba(194,85,45,0)');

                    const puntoFinal=contexto=>contexto.dataIndex===contexto.dataset.data.length-1?4:0;

                    ambienteChart=new window.Chart(ctx,{
                        type:'line',
                        data:{labels:[],datasets:[
                                {label:'Humedad relativa',data:[],yAxisID:'yHumedad',borderColor:COLORES.hum,backgroundColor:rellenoHum,borderWidth:2,pointRadius:puntoFinal,pointBackgroundColor:'#ffffff',pointBorderWidth:2,pointHoverRadius:5,tension:.4,fill:'origin'},
                                {label:'Temperatura',data:[],yAxisID:'yTemperatura',borderColor:COLORES.temp,backgroundColor:rellenoTemp,borderWidth:2,pointRadius:puntoFinal,pointBackgroundColor:'#ffffff',pointBorderWidth:2,pointHoverRadius:5,tension:.4,fill:'origin'}
                            ]},
                        options:{
                            responsive:true,maintainAspectRatio:false,animation:false,
                            layout:{padding:{top:14,right:4}},
                            interaction:{mode:'index',intersect:false},
                            scales:{
                                x:{grid:{display:false},border:{display:false},ticks:{color:COLORES.texto,maxTicksLimit:8,font:{size:11}}},
                                yTemperatura:{type:'linear',position:'left',grace:'15%',grid:{color:COLORES.linea},border:{display:false},ticks:{color:COLORES.texto,font:{size:11},callback:v=>v+' °C'}},
                                yHumedad:{type:'linear',position:'right',min:0,max:100,grid:{drawOnChartArea:false},border:{display:false},ticks:{color:COLORES.texto,font:{size:11},callback:v=>v+' %'}}
                            },
                            plugins:{
                                legend:{display:false},
                                tooltip:{
                                    backgroundColor:COLORES.tinta,padding:10,cornerRadius:6,boxWidth:8,boxHeight:8,usePointStyle:true,
                                    callbacks:{label:c=>{const v=Number(c.parsed.y);return ` ${c.dataset.label}: ${Number.isFinite(v)?v.toFixed(1):'--'}${c.dataset.yAxisID==='yHumedad'?' %':' °C'}`;}}
                                }
                            }
                        },
                        plugins:[etiquetaUltimoValor]
                    });
                }

                /* Mini gráficas de los indicadores */
                sparkTemp=crearSpark('sparkTemp',COLORES.temp);
                sparkHum=crearSpark('sparkHum',COLORES.hum);

                /* Dona de condición */
                const canvasDona=document.getElementById('condicionChart');
                if(canvasDona){
                    condicionChart=new window.Chart(canvasDona.getContext('2d'),{
                        type:'doughnut',
                        data:{
                            labels:['Estables','Con alerta','Sin lecturas'],
                            datasets:[{
                                data:[{{ $conteoEstable }},{{ $conteoAlerta }},{{ $conteoSinLectura }}],
                                backgroundColor:[COLORES.hum,COLORES.temp,COLORES.sinLectura],
                                borderColor:'#ffffff',borderWidth:3,hoverOffset:4
                            }]
                        },
                        options:{
                            responsive:true,maintainAspectRatio:false,animation:false,cutout:'74%',
                            plugins:{legend:{display:false},tooltip:{backgroundColor:COLORES.tinta,padding:10,cornerRadius:6}}
                        }
                    });
                }
            }

            function formatearDelta(serie,unidad){
                const valores=serie.filter(Number.isFinite);
                if(valores.length<2)return{texto:'--',clase:'pc-badge pc-num'};
                const delta=valores[valores.length-1]-valores[0];
                const signo=delta>0?'+':(delta<0?'−':'±');
                return{texto:`${signo}${Math.abs(delta).toFixed(1)} ${unidad}`,clase:'pc-badge pc-num'};
            }

            function actualizarGraficas(grafica){
                const labels=normalizarArray(grafica?.labels);
                const temperaturas=normalizarArray(grafica?.temperaturas).map(Number);
                const humedades=normalizarArray(grafica?.humedades).map(Number);

                const avisoVacio=document.getElementById('graficaVacia');
                if(avisoVacio)avisoVacio.hidden=labels.length>0;

                if(ambienteChart){
                    ambienteChart.data.labels=labels;
                    ambienteChart.data.datasets[0].data=humedades;
                    ambienteChart.data.datasets[1].data=temperaturas;
                    ambienteChart.update('none');
                }

                [[sparkTemp,temperaturas],[sparkHum,humedades]].forEach(([grafico,serie])=>{
                    if(!grafico)return;
                    grafico.data.labels=serie.map((_,i)=>i);
                    grafico.data.datasets[0].data=serie;
                    grafico.update('none');
                });

                const dTemp=formatearDelta(temperaturas,'°C');
                const dHum=formatearDelta(humedades,'%');
                if(elementos.tempDelta){elementos.tempDelta.textContent=dTemp.texto;elementos.tempDelta.className=dTemp.clase;}
                if(elementos.humDelta){elementos.humDelta.textContent=dHum.texto;elementos.humDelta.className=dHum.clase;}
            }

            function actualizarCondicion(items){
                let estable=0,alerta=0,sinLectura=0;
                (Array.isArray(items)?items:[]).forEach(item=>{
                    const tieneLectura=item.temperatura!==null&&item.temperatura!==undefined;
                    if(!tieneLectura)sinLectura++;
                    else if(Number(item.alertas_abiertas||0)>0)alerta++;
                    else estable++;
                });

                setText('cuentaEstable',estable);
                setText('cuentaAlerta',alerta);
                setText('cuentaSinLectura',sinLectura);
                setText('donutTotal',estable+alerta+sinLectura);

                if(condicionChart){
                    condicionChart.data.datasets[0].data=[estable,alerta,sinLectura];
                    condicionChart.update('none');
                }
            }

            function renderResumenIncubadoras(items){
                const cuerpo=document.getElementById('resumenIncubadorasLive');
                if(!cuerpo)return;

                if(!Array.isArray(items)||!items.length){
                    cuerpo.innerHTML='<tr><td colspan="6" class="pc-empty">No hay incubadoras registradas.</td></tr>';
                    return;
                }

                cuerpo.innerHTML=items.map(item=>{
                    const tieneLectura=item.temperatura!==null&&item.temperatura!==undefined;
                    const hayAlerta=Number(item.alertas_abiertas||0)>0;
                    const humedad=Math.max(0,Math.min(100,Number(item.humedad)||0));

                    const condicion=!tieneLectura
                        ?'<span class="pc-badge">Sin lecturas</span>'
                        :(hayAlerta?'<span class="pc-badge pc-badge--danger">Alerta</span>':'<span class="pc-badge pc-badge--ok">Estable</span>');

                    const medidor=tieneLectura
                        ?`<div class="pc-meter">
                              <span class="pc-meter-track"><span class="pc-meter-fill" style="width:${humedad}%"></span></span>
                              <span class="pc-meter-value pc-num">${escapeHtml(item.humedad)} %</span>
                          </div>`
                        :'—';

                    return `<tr>
                        <td><span class="pc-cell-main">${escapeHtml(item.nombre)}</span><span class="pc-cell-sub">${escapeHtml(item.codigo)}</span></td>
                        <td class="nowrap">${escapeHtml(item.estado)}</td>
                        <td>${condicion}</td>
                        <td class="text-end pc-num">${tieneLectura?escapeHtml(item.temperatura)+' °C':'—'}</td>
                        <td>${medidor}</td>
                        <td class="pc-num">${tieneLectura?escapeHtml(item.fecha):'—'}</td>
                    </tr>`;
                }).join('');
            }

            async function actualizarDashboardTiempoReal(forzar=false){
                if(!forzar&&(peticionActiva||document.hidden))return;
                peticionActiva=true;
                const solicitud=++numeroSolicitud;

                try{
                    const response=await fetch(urlTiempoReal(),{
                        headers:{'Accept':'application/json','Cache-Control':'no-cache'},
                        cache:'no-store'
                    });

                    if(!response.ok)throw new Error(`HTTP ${response.status}`);

                    const data=await response.json();

                    // Si mientras tanto se eligió otra incubadora, esta respuesta ya no sirve
                    if(solicitud!==numeroSolicitud)return;

                    if(!data.ok)throw new Error('No se pudo actualizar el dashboard.');

                    setConnectionState(true);

                    setText('metricUsuariosTotal',data.metricas?.usuarios_total);
                    setText('metricIncubadorasTotal',data.metricas?.incubadoras_total);
                    setText('metricLecturasHoy',data.metricas?.lecturas_hoy);
                    setText('metricAlertasActivas',data.metricas?.alertas_activas);
                    setText('metricLotesTotal',data.metricas?.lotes_total);
                    setText('metricFrascosTotal',data.metricas?.frascos_total);

                    actualizarEstadoAlertas(data.metricas?.alertas_activas);

                    if(data.dht22&&data.dht22.temperatura!==null){
                        const temperatura=Number(data.dht22.temperatura);
                        const humedad=Number(data.dht22.humedad);
                        if(elementos.temperatura&&Number.isFinite(temperatura))elementos.temperatura.textContent=temperatura.toFixed(1);
                        if(elementos.humedad&&Number.isFinite(humedad))elementos.humedad.textContent=humedad.toFixed(1);
                        if(elementos.hora)elementos.hora.textContent=data.dht22.fecha_hora??'--:--:--';
                    }else{
                        // La incubadora seleccionada todavía no tiene lecturas
                        limpiarLecturas();
                    }

                    actualizarGraficas(data.grafica);
                    renderResumenIncubadoras(data.resumen_incubadoras);
                    actualizarCondicion(data.resumen_incubadoras);

                }catch(error){
                    if(solicitud!==numeroSolicitud)return;
                    setConnectionState(false);
                    console.error('Error al actualizar dashboard:',error);
                }finally{
                    if(solicitud===numeroSolicitud){
                        peticionActiva=false;
                        panelMonitoreo?.classList.remove('is-loading');
                        panelMonitoreo?.setAttribute('aria-busy','false');
                    }
                }
            }

            function limpiarLecturas(){
                if(elementos.temperatura)elementos.temperatura.textContent='--';
                if(elementos.humedad)elementos.humedad.textContent='--';
                if(elementos.hora)elementos.hora.textContent='--:--:--';
            }

            /* ---------- Cambio de incubadora ---------- */
            selectorIncubadora?.addEventListener('change',()=>{
                incubadoraSeleccionada=selectorIncubadora.value;

                // Guarda la selección en la URL sin recargar la página
                const url=new URL(window.location.href);
                if(incubadoraSeleccionada){url.searchParams.set('incubadora_id',incubadoraSeleccionada);}else{url.searchParams.delete('incubadora_id');}
                window.history.replaceState(null,'',url);

                limpiarLecturas();
                panelMonitoreo?.classList.add('is-loading');
                panelMonitoreo?.setAttribute('aria-busy','true');

                actualizarDashboardTiempoReal(true);
            });

            function actualizarActuadorVisual(actuador,activo){
                const control=controles[actuador];
                if(!control)return;
                const femenino=['calefaccion','ventilacion'].includes(actuador);

                if(control.switch)control.switch.checked=activo;

                if(control.badge){
                    control.badge.textContent=activo?(femenino?'Encendida':'Encendido'):(femenino?'Apagada':'Apagado');
                    control.badge.className='pc-act-state pc-state '+(activo?'pc-state--ok':'pc-state--off');
                    control.badge.closest('.pc-act')?.classList.toggle('is-on',activo);
                }
            }

            function actualizarModoVisual(){
                if(elementos.modoSwitch)elementos.modoSwitch.checked=modoManual;

                if(elementos.modoBadge){
                    elementos.modoBadge.textContent=modoManual?'Manual':'Automático';
                    elementos.modoBadge.className=modoManual?'pc-badge':'pc-badge pc-badge--ok';
                }

                if(elementos.modoNota){
                    elementos.modoNota.textContent=modoManual
                        ?'En modo manual los actuadores solo responden a las órdenes de este panel. Desactívelo para que el sistema vuelva a regular el microclima.'
                        :'En modo automático el sistema regula los actuadores según las condiciones óptimas de cada especie.';
                }

                ACTUADORES.forEach(actuador=>{
                    if(controles[actuador]?.switch)controles[actuador].switch.disabled=!modoManual;
                });
            }

            async function cargarEstadoActuadores(){
                try{
                    const marca=Date.now();
                    await Promise.all(ACTUADORES.map(async actuador=>{
                        const response=await fetch(`${URL_ESTADOS_ACTUADORES[actuador]}?t=${marca}`,{
                            headers:{'Accept':'application/json','Cache-Control':'no-cache'},
                            cache:'no-store'
                        });
                        if(!response.ok)return;
                        const data=await response.json();
                        if(data.ok)actualizarActuadorVisual(actuador,data.comando==='encender');
                    }));
                    actualizarModoVisual();
                }catch(error){
                    console.error('Error al sincronizar actuadores:',error);
                }
            }

            async function cargarModoPersistido(){
                try{
                    const response=await fetch(`${URL_MODO.show}?t=${Date.now()}`,{
                        headers:{'Accept':'application/json','Cache-Control':'no-cache'},
                        cache:'no-store'
                    });
                    const data=await response.json();
                    if(!response.ok||!data.ok)throw new Error(data.message||'No se pudo obtener el modo.');
                    modoManual=data.modo==='manual';
                    actualizarModoVisual();
                    return true;
                }catch(error){
                    modoManual=false;
                    actualizarModoVisual();
                    console.error('Error al obtener modo:',error);
                    return false;
                }
            }

            async function guardarModoPersistido(modo){
                try{
                    const response=await fetch(URL_MODO.update,{
                        method:'POST',
                        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken},
                        body:JSON.stringify({modo})
                    });
                    const data=await response.json();
                    if(!response.ok||!data.ok)throw new Error(data.message||'No se pudo actualizar el modo.');
                    return true;
                }catch(error){
                    console.error('Error al guardar modo:',error);
                    notify('error','No se pudo cambiar el modo','Se conservará el modo anterior.');
                    return false;
                }
            }

            async function enviarOrdenActuador(actuador,accion){
                try{
                    const response=await fetch(URL_ACTUADORES[actuador],{
                        method:'POST',
                        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken},
                        body:JSON.stringify({accion})
                    });
                    const data=await response.json();
                    if(!response.ok||!data.ok)throw new Error(data.message||'No se pudo ejecutar la orden.');
                    return true;
                }catch(error){
                    console.error(`Error en ${actuador}:`,error);
                    notify('error','No se pudo ejecutar la acción','Verifique la conexión con el dispositivo.');
                    return false;
                }
            }

            elementos.modoSwitch?.addEventListener('change',async event=>{
                const anterior=modoManual;
                modoManual=event.target.checked;
                event.target.disabled=true;
                actualizarModoVisual();

                if(!await guardarModoPersistido(modoManual?'manual':'automatico')){
                    modoManual=anterior;
                    event.target.disabled=false;
                    actualizarModoVisual();
                    return;
                }

                if(!modoManual){
                    await Promise.all(ACTUADORES.map(actuador=>enviarOrdenActuador(actuador,'apagar')));
                    await cargarEstadoActuadores();
                }

                event.target.disabled=false;
                actualizarModoVisual();
            });

            ACTUADORES.forEach(actuador=>{
                controles[actuador]?.switch?.addEventListener('change',async event=>{
                    if(!modoManual){event.target.checked=false;return;}
                    const nuevoEstado=event.target.checked;
                    event.target.disabled=true;
                    const ok=await enviarOrdenActuador(actuador,nuevoEstado?'encender':'apagar');
                    event.target.disabled=false;
                    actualizarActuadorVisual(actuador,ok?nuevoEstado:!nuevoEstado);
                });
            });

            async function iniciarDashboard(){
                iniciarGraficas();
                await cargarModoPersistido();
                await cargarEstadoActuadores();
                actualizarDashboardTiempoReal();

                setInterval(()=>{
                    actualizarDashboardTiempoReal();
                    cargarEstadoActuadores();
                },2000);
            }

            iniciarDashboard();

            document.addEventListener('visibilitychange',()=>{
                if(!document.hidden){
                    actualizarDashboardTiempoReal();
                    cargarEstadoActuadores();
                    cargarModoPersistido();
                }
            });
        });
    </script>
</x-app-layout>
