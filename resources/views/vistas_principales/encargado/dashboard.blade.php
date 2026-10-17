@php
    $user = $user ?? auth()->user();
    $nombre = $user ? explode(' ', trim($user->name))[0] : 'Encargado';
    $rol = $user?->rol_nombre ?? 'Encargado';

    $totalIncubadoras = $totalIncubadoras ?? 0;
    $totalAlertasActivas = $totalAlertasActivas ?? 0;
    $lotesEnSeguimiento = $lotesEnSeguimiento ?? 0;
    $lecturasHoy = $lecturasHoy ?? 0;

    $incubadoras = $incubadoras ?? collect();
    $alertasPrioritarias = $alertasPrioritarias ?? collect();
    $alertasActivasPorIncubadora = $alertasActivasPorIncubadora ?? collect();
    $lecturasRecientes = $lecturasRecientes ?? collect();
    $nombresIncubadoras = $nombresIncubadoras ?? collect();
    $ultimaLectura = $ultimaLectura ?? null;
    $asignaciones = $asignaciones ?? collect();

    // Íconos de línea del sistema (mismo estilo que el menú lateral), sin paquetes externos
    $iconos = [
        'incubadora'  => '<rect x="7" y="7" width="10" height="10" rx="2"/><rect x="10" y="10" width="4" height="4" rx=".5"/><path d="M9 3v2M15 3v2M9 19v2M15 19v2M3 9h2M3 15h2M19 9h2M19 15h2"/>',
        'lecturas'    => '<path d="M4 20h16"/><path d="M7 16v-5M11 16V6M15 16v-7M19 16V4"/>',
        'alerta'      => '<path d="M10.3 4.3 2.9 17.2A2 2 0 0 0 4.6 20h14.8a2 2 0 0 0 1.7-2.8L13.7 4.3a2 2 0 0 0-3.4 0z"/><path d="M12 9.5v4"/><path d="M12 17h.01"/>',
        'monitoreo'   => '<path d="M3 12h4l2.5-6 5 12 2.5-6h4"/>',
        'temperatura' => '<path d="M10 13.5V5a2 2 0 1 1 4 0v8.5a4 4 0 1 1-4 0z"/><path d="M12 16v-5"/>',
        'humedad'     => '<path d="M12 3.5S6 9.9 6 14a6 6 0 0 0 12 0c0-4.1-6-10.5-6-10.5z"/><path d="M9.5 15a2.5 2.5 0 0 0 2.5 2.5"/>',
        'lote'        => '<path d="M12 20v-8"/><path d="M12 12C12 8.5 9.5 6 5 6c0 3.6 2.6 6 7 6z"/><path d="M12 14c0-3 2.3-5 6-5 0 3-2.3 5-6 5z"/>',
        'reloj'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'calendario'  => '<rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'ubicacion'   => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/>',
        'cronometro'  => '<circle cx="12" cy="13.5" r="7.5"/><path d="M12 10v3.5l2 1.5M10 3h4M12 3v3"/>',
        'chevron'     => '<path d="m9 6 6 6-6 6"/>',
        'correcto'    => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12.2 2.4 2.4 4.6-4.8"/>',
        'accesos'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/>',
    ];
    $icono = fn(string $nombreIcono) => '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'.($iconos[$nombreIcono] ?? '').'</svg>';
@endphp

<x-app-layout>

    <style>
        /* =========================================================
           PANEL DEL ENCARGADO
           Mismo sistema visual que el panel del super admin.
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
            --pc-brand-bg: #e8f2f5;
            --pc-icon: #5f7178;
            --pc-ok: #1e7a4f;
            --pc-ok-bg: #e7f4ec;
            --pc-warn: #9a5a06;
            --pc-warn-bg: #fdf3e2;
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

        /* Etiquetas */
        .pc-badge {
            display: inline-flex; align-items: center; height: 22px; padding: 0 8px; border-radius: 6px;
            font-size: .74rem; font-weight: 600; white-space: nowrap; background: var(--pc-neutral-bg); color: var(--pc-text);
        }
        .pc-badge--ok { background: var(--pc-ok-bg); color: var(--pc-ok); }
        .pc-badge--warn { background: var(--pc-warn-bg); color: var(--pc-warn); }
        .pc-badge--danger { background: var(--pc-danger-bg); color: var(--pc-danger); }
        .pc-badge--info { background: var(--pc-brand-bg); color: var(--pc-brand); }

        /* Tarjetas */
        .pc-card { background: var(--pc-surface); border: 1px solid var(--pc-line); border-radius: 12px; box-shadow: 0 1px 2px rgba(24, 38, 44, .04); }
        .pc-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; padding: 16px 20px 0; }
        .pc-card-title { margin: 0; display: flex; align-items: center; gap: 9px; font-size: .98rem; font-weight: 600; color: var(--pc-ink); }
        .pc-card-sub { margin: 4px 0 0; font-size: .8rem; color: var(--pc-muted); }
        .pc-card-body { padding: 16px 20px 20px; }

        /* Indicadores */
        .pc-kpi { padding: 16px 18px; height: 100%; display: flex; flex-direction: column; }
        .pc-kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .pc-kpi-label { white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; font-size: .84rem; font-weight: 500; color: var(--pc-text); }
        .pc-kpi-value { margin-top: 12px; font-size: 1.85rem; font-weight: 600; line-height: 1; color: var(--pc-ink); }
        .pc-kpi-note { margin-top: 10px; font-size: .78rem; color: var(--pc-muted); }
        .pc-kpi.is-alert .pc-kpi-value { color: var(--pc-danger); }
        .pc-kpi.is-alert .pc-kpi-label .icono { color: var(--pc-danger); }

        /* Alertas */
        .pc-alert { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--pc-line-soft); }
        .pc-alert:first-child { padding-top: 4px; }
        .pc-alert:last-child { border-bottom: 0; }
        .pc-alert-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; background: var(--pc-neutral-bg); font-size: 1.15rem; }
        .pc-alert--alto .pc-alert-icon { background: var(--pc-danger-bg); }
        .pc-alert--alto .pc-alert-icon .icono { color: var(--pc-danger); }
        .pc-alert--medio .pc-alert-icon { background: var(--pc-warn-bg); }
        .pc-alert--medio .pc-alert-icon .icono { color: var(--pc-warn); }
        .pc-alert-title { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; }
        .pc-alert-title strong { color: var(--pc-ink); font-weight: 600; }
        .pc-alert-title span { font-size: .84rem; color: var(--pc-text); }
        .pc-alert-msg { margin: 4px 0 6px; font-size: .84rem; color: var(--pc-muted); line-height: 1.5; }
        .pc-alert-meta { display: flex; flex-wrap: wrap; gap: 4px 16px; font-size: .78rem; color: var(--pc-muted); }
        .pc-alert-meta span { display: inline-flex; align-items: center; gap: 5px; }
        .pc-alert-meta .icono { font-size: .95rem; }
        .pc-alert-tags { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }

        /* Lectura actual */
        .pc-reading { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--pc-line-soft); }
        .pc-reading-label { display: inline-flex; align-items: center; gap: 10px; color: var(--pc-text); font-weight: 500; }
        .pc-reading-label .icono { font-size: 1.3rem; }
        .pc-reading--temp .icono { color: var(--pc-temp); }
        .pc-reading--hum .icono { color: var(--pc-hum); }
        .pc-reading-value { font-size: 1.85rem; font-weight: 600; color: var(--pc-ink); line-height: 1; }
        .pc-reading-value small { font-size: .95rem; font-weight: 500; color: var(--pc-muted); margin-left: 3px; }
        .pc-detail { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; font-size: .84rem; }
        .pc-detail span { color: var(--pc-muted); }
        .pc-detail strong { color: var(--pc-ink); font-weight: 600; text-align: right; }

        /* Tablas */
        .pc-table-wrap { overflow-x: auto; margin-top: 12px; }
        .pc-table { width: 100%; border-collapse: collapse; font-size: .86rem; }
        .pc-table th { text-align: left; font-weight: 600; font-size: .78rem; color: var(--pc-muted); padding: 9px 20px; border-bottom: 1px solid var(--pc-line); white-space: nowrap; }
        .pc-table td { padding: 11px 20px; border-bottom: 1px solid var(--pc-line-soft); vertical-align: middle; }
        .pc-table tbody tr:last-child td { border-bottom: 0; }
        .pc-table tbody tr:hover td { background: #fafbfc; }
        .pc-table .text-end { text-align: right; }
        .pc-table td.pc-num, .pc-table td.nowrap, .pc-cell-main { white-space: nowrap; }
        .pc-cell-main { display: block; font-weight: 600; color: var(--pc-ink); }
        .pc-cell-sub { display: block; font-size: .78rem; color: var(--pc-muted); }
        .pc-meter { display: flex; align-items: center; gap: 10px; min-width: 140px; }
        .pc-meter-track { flex: 1; height: 6px; border-radius: 99px; background: var(--pc-line-soft); overflow: hidden; }
        .pc-meter-fill { display: block; height: 100%; border-radius: 99px; background: var(--pc-hum); }
        .pc-meter-value { min-width: 48px; text-align: right; font-weight: 600; color: var(--pc-ink); }

        /* Estado vacío */
        .pc-empty { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 32px 20px; text-align: center; color: var(--pc-muted); }
        .pc-empty .icono { font-size: 1.8rem; }
        .pc-empty strong { color: var(--pc-ink); font-weight: 600; }
        .pc-empty--ok .icono { color: var(--pc-ok); }

        /* Accesos */
        .pc-links { list-style: none; margin: 0; padding: 0; }
        .pc-links a {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: var(--pc-ink); text-decoration: none;
            border-bottom: 1px solid var(--pc-line-soft); font-weight: 500; transition: background-color .15s ease;
        }
        .pc-links li:last-child a { border-bottom: 0; }
        .pc-links a:hover { background: #fafbfc; }
        .pc-links a:focus-visible { outline: 2px solid var(--pc-brand); outline-offset: -2px; }
        .pc-links .icono { font-size: 1.2rem; }
        .pc-links .pc-link-text { flex: 1; }
        .pc-links .pc-link-text small { display: block; font-size: .78rem; font-weight: 400; color: var(--pc-muted); }
        .pc-links .pc-link-count { font-size: .8rem; font-weight: 600; color: var(--pc-muted); }
        .pc-note { margin: 0; padding: 12px 20px 16px; font-size: .78rem; color: var(--pc-muted); line-height: 1.5; border-top: 1px solid var(--pc-line-soft); }

        .pc-btn {
            display: inline-flex; align-items: center; gap: 8px; height: 36px; padding: 0 14px; border-radius: 8px;
            border: 1px solid var(--pc-line); background: var(--pc-surface); color: var(--pc-ink); font-size: .84rem; font-weight: 600; text-decoration: none;
        }
        .pc-btn:hover { background: var(--pc-head); color: var(--pc-ink); }
        .pc-btn .icono { font-size: 1rem; color: var(--pc-ink); }

        @media (max-width: 767.98px) {
            .pc-alert { grid-template-columns: auto minmax(0, 1fr); }
            .pc-alert-tags { grid-column: 2; flex-direction: row; align-items: center; }
        }
        @media (max-width: 575.98px) {
            .pc-card-head, .pc-card-body, .pc-links a, .pc-note { padding-left: 16px; padding-right: 16px; }
            .pc-table th, .pc-table td { padding-left: 16px; padding-right: 16px; }
        }
        @media (prefers-reduced-motion: reduce) { .pc-links a { transition: none; } }
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
                <h1>Mi panel</h1>
                <p>{{ $nombre }}, {{ $rol }}. Incubadoras asignadas, alertas y condiciones del microclima.</p>
            </div>
            <div class="pc-meta">
                <span class="pc-meta-item">{!! $icono('reloj') !!}<span x-text="date"></span><strong class="pc-num" x-text="time">--:--:--</strong></span>
            </div>
        </header>

        {{-- =====================================================
            INDICADORES
        ====================================================== --}}
        <section class="row g-3 mb-3" aria-label="Indicadores">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('incubadora') !!}Mis incubadoras</span>
                    </div>
                    <span class="pc-kpi-value pc-num">{{ $totalIncubadoras }}</span>
                    <span class="pc-kpi-note">Asignaciones vigentes</span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi {{ $totalAlertasActivas > 0 ? 'is-alert' : '' }}">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('alerta') !!}Alertas activas</span>
                        @if($totalAlertasActivas > 0)
                            <span class="pc-badge pc-badge--danger">Atender</span>
                        @else
                            <span class="pc-badge pc-badge--ok">En orden</span>
                        @endif
                    </div>
                    <span class="pc-kpi-value pc-num">{{ $totalAlertasActivas }}</span>
                    <span class="pc-kpi-note">{{ $totalAlertasActivas > 0 ? 'Requieren seguimiento' : 'Sin incidencias en tus incubadoras' }}</span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('lote') !!}Lotes</span>
                    </div>
                    <span class="pc-kpi-value pc-num">{{ $lotesEnSeguimiento }}</span>
                    <span class="pc-kpi-note">En mis incubadoras</span>
                </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <article class="pc-card pc-kpi">
                    <div class="pc-kpi-top">
                        <span class="pc-kpi-label">{!! $icono('lecturas') !!}Lecturas de hoy</span>
                        <span class="pc-badge">Hoy</span>
                    </div>
                    <span class="pc-kpi-value pc-num">{{ $lecturasHoy }}</span>
                    <span class="pc-kpi-note">Registros del sensor</span>
                </article>
            </div>
        </section>

        {{-- =====================================================
            ALERTAS + LECTURA ACTUAL
        ====================================================== --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8" id="alertas-prioritarias">
                <section class="pc-card h-100" aria-labelledby="tituloAlertas">
                    <div class="pc-card-head">
                        <div>
                            <h2 id="tituloAlertas" class="pc-card-title">{!! $icono('alerta') !!}Alertas que requieren atención</h2>
                            <p class="pc-card-sub">Incidencias pendientes o atendidas que siguen activas</p>
                        </div>
                        @if($totalAlertasActivas > 0)
                            <a href="{{ route('encargado.alertas.index') }}" class="pc-btn">Gestionar alertas {!! $icono('chevron') !!}</a>
                        @endif
                    </div>

                    <div class="pc-card-body">
                        @forelse($alertasPrioritarias as $alerta)
                            @php
                                $estadoClave = $alerta->estado?->clave;
                                $estadoClase = match($estadoClave) {
                                    'pendiente' => 'pc-badge pc-badge--warn',
                                    'atendida' => 'pc-badge pc-badge--info',
                                    default => 'pc-badge',
                                };

                                $nivelClave = strtolower($alerta->nivel?->clave ?? '');
                                [$nivelClase, $nivelGrupo] = match($nivelClave) {
                                    'alto', 'alta' => ['pc-badge pc-badge--danger', 'alto'],
                                    'medio', 'media' => ['pc-badge pc-badge--warn', 'medio'],
                                    default => ['pc-badge', 'bajo'],
                                };

                                $tipoClave = $alerta->tipo?->clave;
                                $tipoIcono = match($tipoClave) {
                                    'temperatura' => 'temperatura',
                                    'humedad' => 'humedad',
                                    default => 'alerta',
                                };
                            @endphp

                            <article class="pc-alert pc-alert--{{ $nivelGrupo }}">
                                <span class="pc-alert-icon">{!! $icono($tipoIcono) !!}</span>

                                <div style="min-width:0">
                                    <div class="pc-alert-title">
                                        <strong>{{ $alerta->incubadora?->nombre ?? 'Incubadora' }}</strong>
                                        <span>{{ $alerta->tipo?->nombre ?? 'Alerta' }}: {{ $alerta->lectura_causante }}</span>
                                    </div>

                                    @if($alerta->mensaje)
                                        <p class="pc-alert-msg">{{ $alerta->mensaje }}</p>
                                    @endif

                                    <div class="pc-alert-meta">
                                        @if($alerta->lote)
                                            <span>{!! $icono('lote') !!}{{ $alerta->lote->codigo_lote }}</span>
                                        @endif
                                        <span class="pc-num">{!! $icono('calendario') !!}{{ $alerta->fecha_hora?->format('d/m/Y H:i') }}</span>
                                        <span>{!! $icono('cronometro') !!}{{ $alerta->duracion_incidente }}</span>
                                    </div>
                                </div>

                                <div class="pc-alert-tags">
                                    <span class="{{ $nivelClase }}">{{ $alerta->nivel?->nombre ?? 'Sin nivel' }}</span>
                                    <span class="{{ $estadoClase }}">{{ $alerta->estado?->nombre ?? 'Sin estado' }}</span>
                                </div>
                            </article>
                        @empty
                            <div class="pc-empty pc-empty--ok">
                                {!! $icono('correcto') !!}
                                <strong>Todo en orden</strong>
                                <span>No tienes alertas activas en tus incubadoras.</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="pc-card h-100" aria-labelledby="tituloAmbiente">
                    <div class="pc-card-head">
                        <div>
                            <h2 id="tituloAmbiente" class="pc-card-title">{!! $icono('monitoreo') !!}Lectura más reciente</h2>
                            <p class="pc-card-sub">Último registro de tus incubadoras</p>
                        </div>
                    </div>

                    <div class="pc-card-body">
                        @if($ultimaLectura)
                            <div class="pc-reading pc-reading--temp">
                                <span class="pc-reading-label">{!! $icono('temperatura') !!}Temperatura</span>
                                <span class="pc-reading-value pc-num">{{ number_format((float) $ultimaLectura->temperatura, 1) }}<small>°C</small></span>
                            </div>
                            <div class="pc-reading pc-reading--hum">
                                <span class="pc-reading-label">{!! $icono('humedad') !!}Humedad relativa</span>
                                <span class="pc-reading-value pc-num">{{ number_format((float) $ultimaLectura->humedad, 1) }}<small>%</small></span>
                            </div>

                            <div class="mt-2">
                                <div class="pc-detail">
                                    <span>Incubadora</span>
                                    <strong>{{ $nombresIncubadoras->get($ultimaLectura->incubadora_id, '—') }}</strong>
                                </div>
                                <div class="pc-detail">
                                    <span>Fecha y hora</span>
                                    <strong class="pc-num">{{ $ultimaLectura->fecha_hora?->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        @else
                            <div class="pc-empty">
                                {!! $icono('monitoreo') !!}
                                <span>Todavía no hay lecturas ambientales.</span>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        {{-- =====================================================
            MIS INCUBADORAS
        ====================================================== --}}
        <section class="pc-card mb-3" id="mis-incubadoras" aria-labelledby="tituloIncubadoras">
            <div class="pc-card-head">
                <div>
                    <h2 id="tituloIncubadoras" class="pc-card-title">{!! $icono('incubadora') !!}Mis incubadoras</h2>
                    <p class="pc-card-sub">Equipos bajo tu responsabilidad</p>
                </div>
                <span class="pc-badge pc-num">{{ $totalIncubadoras }} {{ $totalIncubadoras === 1 ? 'asignada' : 'asignadas' }}</span>
            </div>

            @if($incubadoras->isEmpty())
                <div class="pc-empty">
                    {!! $icono('incubadora') !!}
                    <strong>Sin incubadoras asignadas</strong>
                    <span>Actualmente no tienes una asignación vigente.</span>
                </div>
            @else
                <div class="pc-table-wrap">
                    <table class="pc-table">
                        <thead>
                        <tr>
                            <th scope="col">Incubadora</th>
                            <th scope="col">Ubicación</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Condición</th>
                            <th scope="col" class="text-end">Temperatura</th>
                            <th scope="col">Humedad</th>
                            <th scope="col">Última lectura</th>
                            <th scope="col">Asignada desde</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($incubadoras as $incubadora)
                            @php
                                $lectura = $incubadora->ultimaLecturaMicroclima;
                                $alertasIncubadora = (int) $alertasActivasPorIncubadora->get($incubadora->id, 0);
                                $asignacion = $asignaciones->firstWhere('incubadora_id', $incubadora->id);
                                $humedadPct = $lectura ? max(0, min(100, (float) $lectura->humedad)) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <span class="pc-cell-main">{{ $incubadora->nombre }}</span>
                                    <span class="pc-cell-sub">{{ $incubadora->codigo ?? 'Sin código' }}</span>
                                </td>
                                <td>{{ $incubadora->ubicacion ?: 'No registrada' }}</td>
                                <td class="nowrap">{{ $incubadora->estado?->nombre ?? 'Sin estado' }}</td>
                                <td>
                                    @if(!$lectura)
                                        <span class="pc-badge">Sin lecturas</span>
                                    @elseif($alertasIncubadora > 0)
                                        <span class="pc-badge pc-badge--danger pc-num">{{ $alertasIncubadora }} {{ $alertasIncubadora === 1 ? 'alerta' : 'alertas' }}</span>
                                    @else
                                        <span class="pc-badge pc-badge--ok">Estable</span>
                                    @endif
                                </td>
                                <td class="text-end pc-num">{{ $lectura ? number_format((float) $lectura->temperatura, 1).' °C' : '—' }}</td>
                                <td>
                                    @if($lectura)
                                        <div class="pc-meter">
                                            <span class="pc-meter-track"><span class="pc-meter-fill" style="width: {{ $humedadPct }}%"></span></span>
                                            <span class="pc-meter-value pc-num">{{ number_format((float) $lectura->humedad, 1) }} %</span>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="pc-num">{{ $lectura?->fecha_hora?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="pc-num">{{ $asignacion?->fecha_inicio?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- =====================================================
            LECTURAS RECIENTES + ACCESOS
        ====================================================== --}}
        <div class="row g-3">
            <div class="col-12 col-xl-8" id="lecturas-recientes">
                <section class="pc-card h-100" aria-labelledby="tituloLecturas">
                    <div class="pc-card-head">
                        <div>
                            <h2 id="tituloLecturas" class="pc-card-title">{!! $icono('lecturas') !!}Lecturas recientes</h2>
                            <p class="pc-card-sub">Últimos registros ambientales de tus incubadoras</p>
                        </div>
                    </div>

                    @if($lecturasRecientes->isEmpty())
                        <div class="pc-empty">
                            {!! $icono('lecturas') !!}
                            <span>No hay lecturas registradas.</span>
                        </div>
                    @else
                        <div class="pc-table-wrap">
                            <table class="pc-table">
                                <thead>
                                <tr>
                                    <th scope="col">Incubadora</th>
                                    <th scope="col" class="text-end">Temperatura</th>
                                    <th scope="col">Humedad</th>
                                    <th scope="col">Fecha y hora</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lecturasRecientes as $lectura)
                                    @php $humedadPct = max(0, min(100, (float) $lectura->humedad)); @endphp
                                    <tr>
                                        <td class="nowrap"><span class="pc-cell-main">{{ $nombresIncubadoras->get($lectura->incubadora_id, 'Incubadora') }}</span></td>
                                        <td class="text-end pc-num">{{ number_format((float) $lectura->temperatura, 1) }} °C</td>
                                        <td>
                                            <div class="pc-meter">
                                                <span class="pc-meter-track"><span class="pc-meter-fill" style="width: {{ $humedadPct }}%"></span></span>
                                                <span class="pc-meter-value pc-num">{{ number_format((float) $lectura->humedad, 1) }} %</span>
                                            </div>
                                        </td>
                                        <td class="pc-num">{{ $lectura->fecha_hora?->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="pc-card h-100" aria-labelledby="tituloAccesos">
                    <div class="pc-card-head pb-3">
                        <div>
                            <h2 id="tituloAccesos" class="pc-card-title">{!! $icono('accesos') !!}Accesos rápidos</h2>
                            <p class="pc-card-sub">Herramientas para tu trabajo diario</p>
                        </div>
                    </div>

                    <ul class="pc-links" style="border-top: 1px solid var(--pc-line-soft);">
                        <li>
                            <a href="{{ route('encargado.alertas.index') }}">
                                {!! $icono('alerta') !!}
                                <span class="pc-link-text">Mis alertas<small>Atender y resolver incidencias</small></span>
                                @if($totalAlertasActivas > 0)
                                    <span class="pc-badge pc-badge--danger pc-num">{{ $totalAlertasActivas }}</span>
                                @endif
                                {!! $icono('chevron') !!}
                            </a>
                        </li>
                        <li>
                            <a href="#mis-incubadoras">
                                {!! $icono('incubadora') !!}
                                <span class="pc-link-text">Mis incubadoras<small>Estado de cada equipo</small></span>
                                <span class="pc-link-count pc-num">{{ $totalIncubadoras }}</span>
                                {!! $icono('chevron') !!}
                            </a>
                        </li>
                        <li>
                            <a href="#lecturas-recientes">
                                {!! $icono('lecturas') !!}
                                <span class="pc-link-text">Lecturas recientes<small>Registros del sensor</small></span>
                                {!! $icono('chevron') !!}
                            </a>
                        </li>
                        <li>
                            <a href="#alertas-prioritarias">
                                {!! $icono('monitoreo') !!}
                                <span class="pc-link-text">Incidencias activas<small>Resumen en este panel</small></span>
                                {!! $icono('chevron') !!}
                            </a>
                        </li>
                    </ul>

                    <p class="pc-note">Este panel muestra solo la información de las incubadoras que tienes asignadas actualmente.</p>
                </section>
            </div>
        </div>
    </div>

</x-app-layout>
