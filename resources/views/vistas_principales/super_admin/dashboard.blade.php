<x-app-layout>
    @php
        $usuario=auth()->user();
        $nombreCorto=$usuario?explode(' ',trim($usuario->name))[0]:'Usuario';
        $rolNombre=strtoupper($usuario?->rol_nombre??'Superadministrador');
        $incubadoraTiempoRealId=$incubadoraActualId??optional($incubadoras->first())->id;
    @endphp

    <div class="container-fluid py-4 px-3 px-lg-4 dashboard-page"
         x-data="{
            time:'',date:'',greeting:'',
            init(){this.updateClock();setInterval(()=>this.updateClock(),1000)},
            updateClock(){
                const now=new Date();
                this.time=new Intl.DateTimeFormat('es-MX',{timeZone:'America/Mexico_City',hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false}).format(now);
                this.date=new Intl.DateTimeFormat('es-MX',{timeZone:'America/Mexico_City',weekday:'long',day:'2-digit',month:'long'}).format(now);
                const parts=new Intl.DateTimeFormat('es-MX',{timeZone:'America/Mexico_City',hour:'2-digit',hour12:false}).formatToParts(now);
                let hour=Number(parts.find(p=>p.type==='hour')?.value||0);
                if(hour===24)hour=0;
                this.greeting=hour<12?'Buenos días':(hour<19?'Buenas tardes':'Buenas noches');
            }
         }">

        <section class="dashboard-hero mb-4">
            <div class="row align-items-center g-4">
                <div class="col-12 col-xl-8">
                    <h1 class="dashboard-title mb-2"><span x-text="greeting">Bienvenido</span>, {{ $nombreCorto }}</h1>
                    <p class="text-secondary mb-0 dashboard-description">Supervisa el microclima, las incubadoras, alertas, sensores y actuadores desde un solo panel.</p>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="dashboard-clock">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dashboard-clock-icon"><i class="bi bi-clock"></i></div>
                            <div class="flex-grow-1">
                                <span class="small text-secondary text-capitalize d-block" x-text="date"></span>
                                <strong class="dashboard-clock-time" x-text="time">--:--:--</strong>
                            </div>
                            <div class="text-end">
                                <span id="connectionBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
                                    <span class="status-dot status-dot-muted me-1"></span>Conectando
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="dashboard-kpi h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="dashboard-kpi-label">Usuarios</span>
                                <div id="metricUsuariosTotal" class="dashboard-kpi-value">{{ $usuariosTotal }}</div>
                                <span class="dashboard-kpi-caption">Cuentas registradas</span>
                            </div>
                            <div class="dashboard-kpi-icon bg-primary-subtle text-primary"><i class="bi bi-people"></i></div>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="dashboard-kpi h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="dashboard-kpi-label">Incubadoras</span>
                                <div id="metricIncubadorasTotal" class="dashboard-kpi-value">{{ $incubadorasTotal }}</div>
                                <span class="dashboard-kpi-caption">Equipos registrados</span>
                            </div>
                            <div class="dashboard-kpi-icon bg-success-subtle text-success"><i class="bi bi-cpu"></i></div>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="dashboard-kpi h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="dashboard-kpi-label">Lecturas hoy</span>
                                <div id="metricLecturasHoy" class="dashboard-kpi-value">{{ $lecturasHoy }}</div>
                                <span class="dashboard-kpi-caption">Registros ambientales</span>
                            </div>
                            <div class="dashboard-kpi-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-activity"></i></div>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="dashboard-kpi h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="dashboard-kpi-label">Alertas activas</span>
                                <div id="metricAlertasActivas" class="dashboard-kpi-value">{{ $alertasActivas }}</div>
                                <div id="metricAlertasEstado" class="dashboard-kpi-caption mt-1">
                                    @if($alertasActivas>0)
                                        <span class="text-danger fw-semibold">Requiere atención</span>
                                    @else
                                        <span class="text-success fw-semibold">Sin incidencias</span>
                                    @endif
                                </div>
                            </div>
                            <div class="dashboard-kpi-icon bg-danger-subtle text-danger"><i class="bi bi-bell"></i></div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <div class="card dashboard-panel h-100">
                        <div class="card-header dashboard-panel-header">
                            <div>
                                <h5 class="mb-1 fw-bold text-brand-dark">Monitoreo ambiental</h5>
                                <span class="small text-secondary">Temperatura y humedad en tiempo real</span>
                            </div>
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                <span class="status-dot status-dot-success me-1"></span>En vivo
                            </span>
                        </div>

                        <div class="card-body p-3 p-lg-4">
                            <div class="row g-4">
                                <div class="col-12 col-lg-6">
                                    <div class="dashboard-chart-card h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <span class="small text-secondary d-block">Temperatura</span>
                                                <strong class="text-brand-dark">Comportamiento térmico</strong>
                                            </div>
                                            <div class="dashboard-chart-icon text-warning-emphasis bg-warning-subtle"><i class="bi bi-thermometer-half"></i></div>
                                        </div>
                                        <div class="dashboard-chart-container"><canvas id="temperaturaChart"></canvas></div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="dashboard-chart-card h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <span class="small text-secondary d-block">Humedad relativa</span>
                                                <strong class="text-brand-dark">Comportamiento hídrico</strong>
                                            </div>
                                            <div class="dashboard-chart-icon text-info-emphasis bg-info-subtle"><i class="bi bi-droplet-half"></i></div>
                                        </div>
                                        <div class="dashboard-chart-container"><canvas id="humedadChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card dashboard-panel h-100">
                        <div class="card-header dashboard-panel-header">
                            <div>
                                <h5 class="fw-bold text-brand-dark mb-1">Sensor ambiental</h5>
                                <span class="small text-secondary">Lectura actual DHT22</span>
                            </div>
                            <i class="bi bi-broadcast-pin fs-4 text-success"></i>
                        </div>

                        <div class="card-body p-4">
                            <div class="sensor-live-value mb-3">
                                <div class="sensor-live-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-thermometer-half"></i></div>
                                <div>
                                    <span class="small text-secondary d-block">Temperatura</span>
                                    <div><strong id="dht22Temp" class="sensor-live-number">--</strong> <span class="fw-bold text-secondary">°C</span></div>
                                </div>
                            </div>

                            <div class="sensor-live-value mb-4">
                                <div class="sensor-live-icon bg-info-subtle text-info-emphasis"><i class="bi bi-droplet-half"></i></div>
                                <div>
                                    <span class="small text-secondary d-block">Humedad relativa</span>
                                    <div><strong id="dht22Hum" class="sensor-live-number">--</strong> <span class="fw-bold text-secondary">%</span></div>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-secondary">Última actualización</span>
                                    <span id="dht22Time" class="small fw-semibold font-monospace text-brand-dark">--:--:--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="row g-4">
                <div class="col-12 col-xl-7">
                    <div class="card dashboard-panel h-100">
                        <div class="card-header dashboard-panel-header">
                            <div>
                                <h5 class="fw-bold text-brand-dark mb-1">Estado de incubadoras</h5>
                                <span class="small text-secondary">Resumen operativo actualizado automáticamente</span>
                            </div>

                            <div class="d-flex gap-3 text-end">
                                <div><span class="small text-secondary d-block">Lotes</span><strong id="metricLotesTotal" class="text-brand-dark">{{ $lotesTotal }}</strong></div>
                                <div><span class="small text-secondary d-block">Frascos</span><strong id="metricFrascosTotal" class="text-brand-dark">{{ $frascosTotal }}</strong></div>
                            </div>
                        </div>

                        <div class="card-body p-3 p-lg-4">
                            <div id="resumenIncubadorasLive" class="row g-3">
                                @forelse($resumenIncubadoras as $item)
                                    @php
                                        $inc=$item['incubadora'];
                                        $lectura=$item['ultima_lectura'];
                                        $alertas=$item['alertas_abiertas'];
                                    @endphp

                                    <div class="col-12 col-md-6">
                                        <article class="incubator-summary h-100">
                                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                                <div>
                                                    <h6 class="fw-bold text-brand-dark mb-1">{{ $inc->nombre }}</h6>
                                                    <span class="small text-secondary">{{ $inc->codigo }}</span>
                                                </div>
                                                @if($alertas>0)
                                                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Alerta</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Estable</span>
                                                @endif
                                            </div>

                                            <div class="incubator-detail"><span>Estado</span><strong>{{ $inc->estado->nombre??'Sin estado' }}</strong></div>

                                            @if($lectura)
                                                <div class="incubator-detail"><span>Temperatura</span><strong>{{ $lectura->temperatura }} °C</strong></div>
                                                <div class="incubator-detail"><span>Humedad</span><strong>{{ $lectura->humedad }} %</strong></div>
                                                <div class="incubator-detail"><span>Última lectura</span><strong class="small">{{ \Carbon\Carbon::parse($lectura->fecha_hora)->format('d/m/Y H:i') }}</strong></div>
                                            @else
                                                <div class="small text-secondary fst-italic mt-3">Sin lecturas registradas.</div>
                                            @endif
                                        </article>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="dashboard-empty-state">
                                            <i class="bi bi-inboxes fs-2 mb-2"></i>
                                            <span>No hay incubadoras registradas.</span>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="card dashboard-panel h-100">
                        <div class="card-header dashboard-panel-header">
                            <div>
                                <h5 class="fw-bold text-brand-dark mb-1">Control de microclima</h5>
                                <span class="small text-secondary">Gestión manual y automática de actuadores</span>
                            </div>
                            <span id="modoBadge" class="badge bg-success-subtle text-success-emphasis rounded-pill">Automático</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="control-row mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="control-icon bg-primary-subtle text-primary"><i class="bi bi-sliders"></i></div>
                                    <div>
                                        <strong class="d-block text-brand-dark">Modo de operación</strong>
                                        <span id="modoLabel" class="small text-secondary">Modo automático activo</span>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input id="modoSwitch" class="form-check-input dashboard-switch" type="checkbox" role="switch" aria-label="Cambiar modo de operación">
                                </div>
                            </div>

                            <div class="control-row mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="control-icon bg-info-subtle text-info-emphasis"><i class="bi bi-cloud-fog2"></i></div>
                                    <div>
                                        <strong class="d-block text-brand-dark">Generador de niebla</strong>
                                        <span id="nieblaLabel" class="small text-secondary">Apagado</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="nieblaBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Apagado</span>
                                    <div class="form-check form-switch m-0">
                                        <input id="nieblaSwitch" class="form-check-input dashboard-switch" type="checkbox" role="switch" disabled aria-label="Controlar generador de niebla">
                                    </div>
                                </div>
                            </div>

                            <div class="control-row mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="control-icon bg-danger-subtle text-danger"><i class="bi bi-thermometer-high"></i></div>
                                    <div>
                                        <strong class="d-block text-brand-dark">Calefacción</strong>
                                        <span id="calefaccionLabel" class="small text-secondary">Apagada</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="calefaccionBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Apagada</span>
                                    <div class="form-check form-switch m-0">
                                        <input id="calefaccionSwitch" class="form-check-input dashboard-switch" type="checkbox" role="switch" disabled aria-label="Controlar calefacción">
                                    </div>
                                </div>
                            </div>

                            <div class="control-row mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="control-icon bg-primary-subtle text-primary"><i class="bi bi-fan"></i></div>
                                    <div>
                                        <strong class="d-block text-brand-dark">Ventilación</strong>
                                        <span id="ventilacionLabel" class="small text-secondary">Apagada</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="ventilacionBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Apagada</span>
                                    <div class="form-check form-switch m-0">
                                        <input id="ventilacionSwitch" class="form-check-input dashboard-switch" type="checkbox" role="switch" disabled aria-label="Controlar ventilación">
                                    </div>
                                </div>
                            </div>

                            <div class="control-row mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="control-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-lightbulb"></i></div>
                                    <div>
                                        <strong class="d-block text-brand-dark">Iluminación</strong>
                                        <span id="ledLabel" class="small text-secondary">Apagado</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span id="ledBadge" class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">Apagado</span>
                                    <div class="form-check form-switch m-0">
                                        <input id="ledSwitch" class="form-check-input dashboard-switch" type="checkbox" role="switch" disabled aria-label="Controlar iluminación">
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-control-info">
                                <i class="bi bi-info-circle me-2"></i>
                                En modo automático, los actuadores son administrados por el sistema. Activa el modo manual para controlarlos directamente.
                            </div>

                            <div class="border-top mt-4 pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-secondary">Nivel de acceso</span>
                                    <span class="badge bg-dark-subtle text-dark-emphasis rounded-pill">{{ $rolNombre }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded',()=>{
            const INCUBADORA_ID=@json($incubadoraTiempoRealId);
            const URL_TIEMPO_REAL_BASE=@json(route('super_admin.dashboard.tiempo-real'));
            const URL_TIEMPO_REAL=INCUBADORA_ID?`${URL_TIEMPO_REAL_BASE}?incubadora_id=${encodeURIComponent(INCUBADORA_ID)}`:URL_TIEMPO_REAL_BASE;

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

            const csrfToken=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const elementos={
                temperatura:document.getElementById('dht22Temp'),
                humedad:document.getElementById('dht22Hum'),
                hora:document.getElementById('dht22Time'),
                modoSwitch:document.getElementById('modoSwitch'),
                modoLabel:document.getElementById('modoLabel'),
                modoBadge:document.getElementById('modoBadge'),
                connectionBadge:document.getElementById('connectionBadge'),

                nieblaSwitch:document.getElementById('nieblaSwitch'),
                nieblaLabel:document.getElementById('nieblaLabel'),
                nieblaBadge:document.getElementById('nieblaBadge'),

                calefaccionSwitch:document.getElementById('calefaccionSwitch'),
                calefaccionLabel:document.getElementById('calefaccionLabel'),
                calefaccionBadge:document.getElementById('calefaccionBadge'),

                ventilacionSwitch:document.getElementById('ventilacionSwitch'),
                ventilacionLabel:document.getElementById('ventilacionLabel'),
                ventilacionBadge:document.getElementById('ventilacionBadge'),

                ledSwitch:document.getElementById('ledSwitch'),
                ledLabel:document.getElementById('ledLabel'),
                ledBadge:document.getElementById('ledBadge')
            };

            const controles={
                niebla:{switch:elementos.nieblaSwitch,label:elementos.nieblaLabel,badge:elementos.nieblaBadge},
                calefaccion:{switch:elementos.calefaccionSwitch,label:elementos.calefaccionLabel,badge:elementos.calefaccionBadge},
                ventilacion:{switch:elementos.ventilacionSwitch,label:elementos.ventilacionLabel,badge:elementos.ventilacionBadge},
                luz:{switch:elementos.ledSwitch,label:elementos.ledLabel,badge:elementos.ledBadge}
            };

            let temperaturaChart=null;
            let humedadChart=null;
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
                elementos.connectionBadge.className=online
                    ?'badge bg-success-subtle text-success-emphasis rounded-pill'
                    :'badge bg-danger-subtle text-danger-emphasis rounded-pill';
                elementos.connectionBadge.innerHTML=online
                    ?'<span class="status-dot status-dot-success me-1"></span>En línea'
                    :'<span class="status-dot status-dot-danger me-1"></span>Sin conexión';
            }

            function notify(type,title,message){
                if(typeof window.microseedAlert==='function'){
                    window.microseedAlert(type,title,message);
                    return;
                }

                if(window.Swal){
                    window.Swal.fire({icon:type,title,text:message,confirmButtonText:'Aceptar'});
                    return;
                }

                alert(message);
            }

            function actualizarEstadoAlertas(totalAlertas){
                const contenedor=document.getElementById('metricAlertasEstado');
                if(!contenedor)return;

                contenedor.innerHTML=Number(totalAlertas||0)>0
                    ?'<span class="text-danger fw-semibold">Requiere atención</span>'
                    :'<span class="text-success fw-semibold">Sin incidencias</span>';
            }

            function iniciarGraficas(){
                if(!window.Chart)return;

                const commonOptions={
                    responsive:true,
                    maintainAspectRatio:false,
                    animation:false,
                    interaction:{mode:'index',intersect:false},
                    scales:{
                        x:{grid:{display:false},ticks:{color:'#6c757d',maxTicksLimit:7,font:{size:10}}},
                        y:{beginAtZero:false,grid:{color:'rgba(108,117,125,.12)'},ticks:{color:'#6c757d',font:{size:10}}}
                    },
                    plugins:{
                        legend:{display:false},
                        tooltip:{backgroundColor:'#144255',padding:10,cornerRadius:8,displayColors:false}
                    }
                };

                const canvasTemp=document.getElementById('temperaturaChart');
                if(canvasTemp){
                    const ctx=canvasTemp.getContext('2d');
                    const gradient=ctx.createLinearGradient(0,0,0,260);
                    gradient.addColorStop(0,'rgba(234,179,8,.30)');
                    gradient.addColorStop(1,'rgba(234,179,8,0)');

                    temperaturaChart=new window.Chart(ctx,{
                        type:'line',
                        data:{labels:[],datasets:[{
                                label:'Temperatura (°C)',
                                data:[],
                                borderColor:'#eab308',
                                backgroundColor:gradient,
                                borderWidth:2,
                                pointRadius:2,
                                pointHoverRadius:5,
                                tension:.35,
                                fill:true
                            }]},
                        options:commonOptions
                    });
                }

                const canvasHum=document.getElementById('humedadChart');
                if(canvasHum){
                    const ctx=canvasHum.getContext('2d');
                    const gradient=ctx.createLinearGradient(0,0,0,260);
                    gradient.addColorStop(0,'rgba(59,180,156,.30)');
                    gradient.addColorStop(1,'rgba(59,180,156,0)');

                    humedadChart=new window.Chart(ctx,{
                        type:'line',
                        data:{labels:[],datasets:[{
                                label:'Humedad (%)',
                                data:[],
                                borderColor:'#3bb49c',
                                backgroundColor:gradient,
                                borderWidth:2,
                                pointRadius:2,
                                pointHoverRadius:5,
                                tension:.35,
                                fill:true
                            }]},
                        options:commonOptions
                    });
                }
            }

            function actualizarGraficas(grafica){
                const labels=normalizarArray(grafica?.labels);
                const temperaturas=normalizarArray(grafica?.temperaturas).map(Number);
                const humedades=normalizarArray(grafica?.humedades).map(Number);

                if(temperaturaChart){
                    temperaturaChart.data.labels=labels;
                    temperaturaChart.data.datasets[0].data=temperaturas;
                    temperaturaChart.update('none');
                }

                if(humedadChart){
                    humedadChart.data.labels=labels;
                    humedadChart.data.datasets[0].data=humedades;
                    humedadChart.update('none');
                }
            }

            function renderResumenIncubadoras(items){
                const contenedor=document.getElementById('resumenIncubadorasLive');
                if(!contenedor)return;

                if(!Array.isArray(items)||!items.length){
                    contenedor.innerHTML='<div class="col-12"><div class="dashboard-empty-state"><i class="bi bi-inboxes fs-2 mb-2"></i><span>No hay incubadoras registradas.</span></div></div>';
                    return;
                }

                contenedor.innerHTML=items.map(item=>{
                    const badge=Number(item.alertas_abiertas||0)>0
                        ?'<span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Alerta</span>'
                        :'<span class="badge bg-success-subtle text-success-emphasis rounded-pill">Estable</span>';

                    const lectura=item.temperatura!==null
                        ?`<div class="incubator-detail"><span>Temperatura</span><strong>${escapeHtml(item.temperatura)} °C</strong></div>
                           <div class="incubator-detail"><span>Humedad</span><strong>${escapeHtml(item.humedad)} %</strong></div>
                           <div class="incubator-detail"><span>Última lectura</span><strong class="small">${escapeHtml(item.fecha)}</strong></div>`
                        :'<div class="small text-secondary fst-italic mt-3">Sin lecturas registradas.</div>';

                    return `<div class="col-12 col-md-6">
                        <article class="incubator-summary h-100">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h6 class="fw-bold text-brand-dark mb-1">${escapeHtml(item.nombre)}</h6>
                                    <span class="small text-secondary">${escapeHtml(item.codigo)}</span>
                                </div>
                                ${badge}
                            </div>
                            <div class="incubator-detail"><span>Estado</span><strong>${escapeHtml(item.estado)}</strong></div>
                            ${lectura}
                        </article>
                    </div>`;
                }).join('');
            }

            async function actualizarDashboardTiempoReal(){
                if(peticionActiva||document.hidden)return;

                peticionActiva=true;

                try{
                    const separator=URL_TIEMPO_REAL.includes('?')?'&':'?';
                    const response=await fetch(`${URL_TIEMPO_REAL}${separator}t=${Date.now()}`,{
                        headers:{'Accept':'application/json','Cache-Control':'no-cache'},
                        cache:'no-store'
                    });

                    if(!response.ok)throw new Error(`HTTP ${response.status}`);

                    const data=await response.json();
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
                    }

                    actualizarGraficas(data.grafica);
                    renderResumenIncubadoras(data.resumen_incubadoras);

                }catch(error){
                    setConnectionState(false);
                    console.error('Error al actualizar dashboard:',error);
                }finally{
                    peticionActiva=false;
                }
            }

            function actualizarActuadorVisual(actuador,activo){
                const control=controles[actuador];
                if(!control)return;

                const femenino=['calefaccion','ventilacion'].includes(actuador);
                const encendido=femenino?'Encendida':'Encendido';
                const apagado=femenino?'Apagada':'Apagado';

                if(control.switch)control.switch.checked=activo;
                if(control.label)control.label.textContent=activo?encendido:apagado;

                if(control.badge){
                    control.badge.textContent=activo?encendido:apagado;
                    control.badge.className=activo
                        ?'badge bg-success-subtle text-success-emphasis rounded-pill'
                        :'badge bg-secondary-subtle text-secondary-emphasis rounded-pill';
                }
            }

            function actualizarModoVisual(){
                if(elementos.modoSwitch)elementos.modoSwitch.checked=modoManual;
                if(elementos.modoLabel)elementos.modoLabel.textContent=modoManual?'Modo manual activo':'Modo automático activo';

                if(elementos.modoBadge){
                    elementos.modoBadge.textContent=modoManual?'Manual':'Automático';
                    elementos.modoBadge.className=modoManual
                        ?'badge bg-warning-subtle text-warning-emphasis rounded-pill'
                        :'badge bg-success-subtle text-success-emphasis rounded-pill';
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

                        if(data.ok){
                            actualizarActuadorVisual(
                                actuador,
                                data.comando==='encender'
                            );
                        }
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
                        headers:{
                            'Content-Type':'application/json',
                            'Accept':'application/json',
                            'X-CSRF-TOKEN':csrfToken
                        },
                        body:JSON.stringify({modo})
                    });

                    const data=await response.json();

                    if(!response.ok||!data.ok)throw new Error(data.message||'No se pudo actualizar el modo.');

                    return true;

                }catch(error){
                    console.error('Error al guardar modo:',error);
                    notify('error','No se pudo cambiar el modo','El modo anterior se conservará.');
                    return false;
                }
            }

            async function enviarOrdenActuador(actuador,accion){
                try{
                    const response=await fetch(URL_ACTUADORES[actuador],{
                        method:'POST',
                        headers:{
                            'Content-Type':'application/json',
                            'Accept':'application/json',
                            'X-CSRF-TOKEN':csrfToken
                        },
                        body:JSON.stringify({accion})
                    });

                    const data=await response.json();

                    if(!response.ok||!data.ok)throw new Error(data.message||'No se pudo ejecutar la orden.');

                    return true;

                }catch(error){
                    console.error(`Error en ${actuador}:`,error);
                    notify('error','No se pudo ejecutar la acción','Verifica la conexión con el dispositivo.');
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
                    if(!modoManual){
                        event.target.checked=false;
                        return;
                    }

                    const nuevoEstado=event.target.checked;
                    event.target.disabled=true;

                    const ok=await enviarOrdenActuador(
                        actuador,
                        nuevoEstado?'encender':'apagar'
                    );

                    event.target.disabled=false;
                    actualizarActuadorVisual(
                        actuador,
                        ok?nuevoEstado:!nuevoEstado
                    );
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
