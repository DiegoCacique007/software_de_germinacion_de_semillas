@php
    use Illuminate\Support\Facades\Route;

    $title=$title??'Módulo';
    $subtitle=$subtitle??'Administración del módulo';
    $items=collect($items??[]);
    $routeBase=$routeBase??'';
    $entitySingular=$entitySingular??'Registro';
    $entityPlural=$entityPlural??'Registros';
    $columns=$columns??[];
    $fields=$fields??[];

    $hasFileField=collect($fields)->contains(fn($field)=>($field['type']??'text')==='file');

    $allowCreate=$canCreate??true;
    $allowEdit=$canEdit??true;
    $allowDelete=$canDelete??true;
    $allowShow=$canShow??true;
    $showAsPage=$showAsPage??false;

    $hasStoreRoute=$routeBase!==''&&Route::has($routeBase.'.store');
    $hasUpdateRoute=$routeBase!==''&&Route::has($routeBase.'.update');
    $hasDestroyRoute=$routeBase!==''&&Route::has($routeBase.'.destroy');
    $hasShowRoute=$routeBase!==''&&Route::has($routeBase.'.show');

    $canStore=$allowCreate&&$hasStoreRoute;
    $canUpdate=$allowEdit&&$hasUpdateRoute;
    $canDestroy=$allowDelete&&$hasDestroyRoute;
    $canShow=(bool)$allowShow;
    $showWithRoute=$canShow&&$showAsPage&&$hasShowRoute;
    $hasActions=$canShow||$canUpdate||$canDestroy;

    $emptyForm=collect($fields)->mapWithKeys(fn($field)=>[$field['name']=>''])->toArray();

    $oldForm=collect($fields)->mapWithKeys(function($field){
        $type=$field['type']??'text';
        return [$field['name']=>in_array($type,['password','file'],true)?'':old($field['name'],'')];
    })->toArray();

    $longFields=collect($fields)->filter(fn($field)=>($field['type']??'text')==='textarea')->pluck('name')->toArray();

    $isLongColumn=function($column)use($longFields){
        if(($column['wrap']??false)===true)return true;
        $key=strtolower((string)($column['key']??''));
        if(in_array($key,$longFields,true))return true;
        foreach(['observacion','descripcion','mensaje','detalle','nota','comentario','contenido','direccion'] as $word)if(str_contains($key,$word))return true;
        return false;
    };

    $formatValue=function($value,$key){
        if($key==='activo')return(bool)$value?'Activo':'Inactivo';
        if($value instanceof \DateTimeInterface)return $value->format('d/m/Y H:i');
        return blank($value)&&$value!==0&&$value!=='0'?'—':(string)$value;
    };

    $formatImageUrl=function($value){
        if(blank($value))return null;
        $value=(string)$value;
        if(str_starts_with($value,'http://')||str_starts_with($value,'https://'))return $value;
        if(str_starts_with($value,'/storage/'))return asset(ltrim($value,'/'));
        if(str_starts_with($value,'storage/'))return asset($value);
        return asset('storage/'.ltrim($value,'/'));
    };

    $resolveFieldIcon=function($column){
        $key=strtolower((string)($column['key']??''));
        $label=strtolower((string)($column['label']??''));
        $text=$key.' '.$label;

        if(str_contains($text,'incubadora'))return'bi-box-seam';
        if(str_contains($text,'frasco'))return'bi-cup';
        if(str_contains($text,'lote'))return'bi-layers';
        if(str_contains($text,'especie'))return'bi-flower1';
        if(str_contains($text,'semilla'))return'bi-circle';
        if(str_contains($text,'germin'))return'bi-flower2';
        if(str_contains($text,'estado'))return'bi-check-circle';
        if(str_contains($text,'nivel'))return'bi-exclamation-triangle';
        if(str_contains($text,'fecha'))return'bi-calendar3';
        if(str_contains($text,'duracion'))return'bi-stopwatch';
        if(str_contains($text,'origen'))return'bi-cpu';
        if(str_contains($text,'lectura'))return'bi-activity';
        if(str_contains($text,'inicio'))return'bi-calendar-check';
        if(str_contains($text,'fin'))return'bi-calendar-x';
        if(str_contains($text,'seguimiento'))return'bi-clipboard-check';
        if(str_contains($text,'observacion'))return'bi-chat-left-text';
        if(str_contains($text,'descripcion'))return'bi-card-text';
        if(str_contains($text,'mensaje'))return'bi-chat-square-text';
        if(str_contains($text,'usuario')||str_contains($text,'encargado')||str_contains($text,'registr')||str_contains($text,'atendida por'))return'bi-person';
        if(str_contains($text,'altura'))return'bi-arrows-vertical';
        if(str_contains($text,'porcentaje'))return'bi-percent';
        if(str_contains($text,'temperatura'))return'bi-thermometer-half';
        if(str_contains($text,'humedad'))return'bi-droplet';
        if(str_contains($text,'archivo')||str_contains($text,'foto')||str_contains($text,'evidencia'))return'bi-image';
        if(str_contains($text,'posicion'))return'bi-geo-alt';
        if(str_contains($text,'codigo'))return'bi-upc-scan';
        if(str_contains($text,'cantidad'))return'bi-123';

        return'bi-info-circle';
    };

    $isStatusColumn=function($column){
        $key=strtolower((string)($column['key']??''));
        $label=strtolower((string)($column['label']??''));
        return str_contains($key,'estado')||str_contains($label,'estado');
    };

    $isLevelColumn=function($column){
        $key=strtolower((string)($column['key']??''));
        $label=strtolower((string)($column['label']??''));
        return str_contains($key,'nivel')||str_contains($label,'nivel');
    };

    $statusBadgeClass=function($value){
        return match(strtolower(trim((string)$value))){
            'pendiente'=>'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'atendida'=>'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            'resuelta'=>'bg-success-subtle text-success-emphasis border border-success-subtle',
            'activo','activa'=>'bg-success-subtle text-success-emphasis border border-success-subtle',
            'inactivo','inactiva'=>'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default=>'bg-brand-soft text-brand-primary border border-brand-subtle',
        };
    };

    $statusIcon=function($value){
        return match(strtolower(trim((string)$value))){
            'pendiente'=>'bi-clock-history',
            'atendida'=>'bi-person-check',
            'resuelta'=>'bi-check-circle-fill',
            'activo','activa'=>'bi-check-circle',
            'inactivo','inactiva'=>'bi-dash-circle',
            default=>'bi-info-circle',
        };
    };

    $levelBadgeClass=function($value){
        return match(strtolower(trim((string)$value))){
            'bajo','baja'=>'bg-success-subtle text-success-emphasis border border-success-subtle',
            'medio','media'=>'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'alto','alta'=>'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            default=>'bg-light text-secondary border',
        };
    };

    $levelIcon=function($value){
        return match(strtolower(trim((string)$value))){
            'bajo','baja'=>'bi-arrow-down-circle',
            'medio','media'=>'bi-dash-circle',
            'alto','alta'=>'bi-exclamation-triangle-fill',
            default=>'bi-info-circle',
        };
    };
@endphp

<x-app-layout>

    <style>
        .crud-table-wrapper{width:100%;overflow-x:auto;overflow-y:visible;scrollbar-width:thin;scrollbar-color:#8fbcc3 #eef7f5}
        .crud-table-wrapper::-webkit-scrollbar{height:8px}.crud-table-wrapper::-webkit-scrollbar-track{background:#eef7f5;border-radius:10px}.crud-table-wrapper::-webkit-scrollbar-thumb{background:#8fbcc3;border-radius:10px}.crud-table-wrapper::-webkit-scrollbar-thumb:hover{background:#5c9eaa}
        .crud-table-readable{width:max-content!important;min-width:100%!important;table-layout:auto!important}.crud-table-readable th{white-space:nowrap!important;vertical-align:middle;min-width:120px}.crud-table-readable td{vertical-align:middle}
        .crud-column-normal{white-space:nowrap!important;min-width:130px!important;max-width:none!important;overflow-wrap:normal!important;word-break:normal!important}.crud-column-short{white-space:nowrap!important;min-width:90px!important;width:90px!important;text-align:center}.crud-column-medium{white-space:nowrap!important;min-width:160px!important}.crud-column-long{white-space:normal!important;min-width:260px!important;max-width:340px!important;line-height:1.45!important;overflow-wrap:break-word!important;word-break:normal!important}
        .crud-column-image{white-space:nowrap!important;min-width:110px!important;width:110px!important;text-align:center}.crud-column-actions{white-space:nowrap!important;min-width:120px!important;width:120px!important;text-align:center}.crud-cell-content{display:block;width:100%;white-space:inherit;word-break:inherit;overflow-wrap:inherit}.crud-image-thumb{width:70px;height:55px;object-fit:cover;display:block}

        .crud-view-modal{border:0;background:#fff}.crud-view-header{position:relative;padding:24px 28px 15px;text-align:center;background:#fff}.crud-view-close{position:absolute;top:15px;right:15px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;border:0;border-radius:12px;background:transparent;color:#88969d;font-size:19px;transition:.18s}.crud-view-close:hover{color:#1c607a;background:#eef8f6;transform:rotate(4deg)}
        .crud-view-icon{width:56px;height:56px;display:flex;align-items:center;justify-content:center;margin:0 auto 13px;border-radius:17px;color:#1c607a;background:linear-gradient(145deg,#edf9f7,#e2f4f1);border:1px solid rgba(59,180,156,.24);font-size:25px;box-shadow:0 6px 16px rgba(33,106,115,.07)}.crud-view-title{margin:0;color:#173f50;font-size:24px;font-weight:800;letter-spacing:-.025em}.crud-view-subtitle{margin:6px auto 0;color:#7b8a93;font-size:14px;font-weight:500}
        .crud-view-body{padding:16px 23px 9px;background:#fff}.crud-view-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.crud-view-card{position:relative;min-width:0;min-height:96px;padding:16px 17px;border:1px solid #dce8e8;border-radius:16px;background:linear-gradient(145deg,#fbfdfd,#f7faf9);overflow:hidden;transition:.18s}.crud-view-card::after{content:'';position:absolute;left:0;top:18px;bottom:18px;width:3px;border-radius:0 4px 4px 0;background:transparent;transition:.18s}.crud-view-card:hover{transform:translateY(-2px);border-color:rgba(59,180,156,.38);background:#fff;box-shadow:0 8px 22px rgba(22,70,84,.075)}.crud-view-card:hover::after{background:#3bb49c}.crud-view-card-full{grid-column:1/-1}
        .crud-view-card-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px}.crud-view-label{min-width:0;display:flex;align-items:center;gap:9px;color:#1c607a;font-size:13px;font-weight:750}.crud-view-label-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.crud-view-field-icon{width:29px;height:29px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:9px;color:#267b86;background:#eaf7f5;border:1px solid rgba(59,180,156,.15);font-size:13px;transition:.18s}.crud-view-card:hover .crud-view-field-icon{color:#fff;background:#3b9f94;transform:scale(1.03)}
        .crud-view-index{flex-shrink:0;color:#a1adb2;font-size:9px;font-weight:800;letter-spacing:.08em;line-height:1;padding:4px 6px;border:1px solid #e2eaea;border-radius:6px;background:#fbfcfc}.crud-view-value{padding-left:38px;color:#294753;font-size:15px;font-weight:550;line-height:1.5;white-space:pre-line;overflow-wrap:break-word}.crud-view-value-status{display:inline-flex;align-items:center;gap:7px;width:auto;margin-left:38px;padding:6px 11px;border:1px solid rgba(59,180,156,.18);border-radius:999px;color:#226d72;background:#eef9f7;font-size:13px;font-weight:700}.crud-view-value-status::before{content:'';width:7px;height:7px;border-radius:999px;background:#3bb49c;box-shadow:0 0 0 3px rgba(59,180,156,.10)}
        .crud-view-empty{padding-left:38px;color:#98a5ac;font-size:14px;font-style:italic}.crud-view-image-button{display:block;margin-left:38px;padding:0;border:0;background:transparent;cursor:zoom-in}.crud-view-image{display:block;width:155px;height:105px;object-fit:cover;border:1px solid #d7e5e4;border-radius:12px;box-shadow:0 5px 14px rgba(15,23,42,.07);transition:.2s}.crud-view-image-button:hover .crud-view-image{transform:scale(1.025);box-shadow:0 9px 22px rgba(15,23,42,.12)}.crud-view-footer{display:flex;justify-content:center;padding:19px 24px 24px;background:#fff}.crud-view-close-btn{min-width:122px;transition:.18s}.crud-view-close-btn:hover{transform:translateY(-1px)}
        .crud-image-modal-content{background:#fff}.crud-image-modal-body{padding:22px;text-align:center;background:#f7fbfa}.crud-image-modal-preview{max-height:72vh;object-fit:contain;border-radius:14px}

        @media(max-width:767.98px){
            .crud-table-readable th{min-width:110px}.crud-column-normal{min-width:125px!important}.crud-column-medium{min-width:150px!important}.crud-column-long{min-width:230px!important;max-width:280px!important}
            .crud-view-header{padding:21px 18px 12px}.crud-view-body{padding:14px 15px 8px}.crud-view-grid{grid-template-columns:1fr;gap:12px}.crud-view-card-full{grid-column:auto}.crud-view-card{min-height:auto;padding:15px}.crud-view-title{font-size:21px}.crud-view-value,.crud-view-empty{padding-left:38px}.crud-view-value-status,.crud-view-image-button{margin-left:38px}.crud-view-footer{padding:16px 18px 20px}
        }
    </style>

    <div class="container-fluid py-4 px-3 px-lg-4" x-data="crudModule()" x-init="init()" x-cloak>

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
            <div><h1 class="h2 fw-bold text-brand-primary mb-1">{{ $title }}</h1><p class="text-secondary mb-0">{{ $subtitle }}</p></div>

            @if($canStore)
                <button type="button" @click="openCreateModal()" class="btn btn-brand d-inline-flex align-items-center justify-content-center gap-2 px-4 py-2 rounded-3">
                    <i class="bi bi-plus-lg fs-5"></i><span>Nuevo {{ $entitySingular }}</span>
                </button>
            @endif
        </div>

        @if($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold"><i class="bi bi-exclamation-triangle-fill fs-5"></i><span>Corrige los siguientes errores:</span></div>
                <ul class="mb-0 ps-3 small">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

            <div class="card-header bg-light border-bottom border-light-subtle py-3 px-3 px-sm-4">
                <div class="row g-3 align-items-center justify-content-between">

                    <div class="col-12 col-md-auto d-flex flex-wrap align-items-center gap-2 gap-sm-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 small fw-bold text-secondary">Mostrar</label>
                            <select x-model="perPage" class="form-select form-select-sm w-auto fw-bold text-brand-primary rounded-3 border-secondary-subtle">
                                <option value="10">10</option><option value="20">20</option><option value="50">50</option><option value="100">100</option>
                            </select>
                            <span class="small fw-bold text-secondary">registros</span>
                        </div>

                        <span class="badge rounded-pill bg-brand-soft text-brand-primary px-3 py-2 border border-brand-subtle d-inline-flex align-items-center gap-2">
                        <span class="badge-dot bg-brand-accent"></span><span><span x-text="filteredRows.length"></span> resultados</span>
                    </span>
                    </div>

                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-brand-primary ps-3 rounded-start-3"><i class="bi bi-search"></i></span>
                            <input type="search" x-model="search" placeholder="Buscar {{ strtolower($entityPlural) }}..." class="form-control border-start-0 ps-1 rounded-end-3 py-2">
                            <button type="button" x-show="search.length>0" x-cloak @click="clearSearch()" title="Limpiar búsqueda" class="btn btn-outline-secondary border-start-0 border-end-0"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-body p-0">
                <div class="crud-table-wrapper">
                    <table class="table table-hover align-middle mb-0 crud-table-readable">

                        <thead class="crud-table-header">
                        <tr>
                            @foreach($columns as $column)
                                @php
                                    $headerType=$column['type']??'text';
                                    $headerLong=$isLongColumn($column);
                                    $headerClass=$headerType==='image'?'crud-column-image':($headerLong?'crud-column-long':(($column['size']??'')==='short'?'crud-column-short':(($column['size']??'')==='medium'?'crud-column-medium':'crud-column-normal')));
                                @endphp
                                <th class="px-3 px-sm-4 py-3 {{ $headerClass }}">{{ $column['label'] }}</th>
                            @endforeach

                            @if($hasActions)<th class="px-3 px-sm-4 py-3 crud-column-actions">Acciones</th>@endif
                        </tr>
                        </thead>

                        <tbody x-ref="recordsContainer">
                        @forelse($items as $item)

                            @php
                                $showPayload=collect($columns)->map(function($column)use($item,$formatValue,$formatImageUrl,$resolveFieldIcon,$isLongColumn,$isStatusColumn,$isLevelColumn){
                                    $value=data_get($item,$column['key']);
                                    $type=$column['type']??'text';

                                    return [
                                        'label'=>$column['label'],
                                        'type'=>$type,
                                        'value'=>$formatValue($value,$column['key']),
                                        'url'=>$type==='image'?$formatImageUrl($value):null,
                                        'icon'=>$resolveFieldIcon($column),
                                        'full'=>$isLongColumn($column),
                                        'status'=>$isStatusColumn($column),
                                        'level'=>$isLevelColumn($column),
                                    ];
                                })->values()->all();

                                $editPayload=[
                                    'action'=>$canUpdate?route($routeBase.'.update',$item):'',
                                    'fields'=>collect($fields)->mapWithKeys(function($field)use($item){
                                        $name=$field['name'];
                                        $key=$field['edit_key']??$name;
                                        $type=$field['type']??'text';
                                        $value=in_array($type,['password','file'],true)?'':data_get($item,$key,'');

                                        if(is_bool($value))$value=$value?'1':'0';

                                        if($value instanceof \DateTimeInterface){
                                            $value=match($type){
                                                'date'=>$value->format('Y-m-d'),
                                                'datetime-local'=>$value->format('Y-m-d\TH:i'),
                                                default=>$value->format('Y-m-d H:i:s'),
                                            };
                                        }

                                        return[$name=>(string)$value];
                                    })->toArray(),
                                ];

                                $searchText=collect($columns)->map(function($column)use($item,$formatValue){
                                    if(($column['type']??'text')==='image')return'';
                                    return$formatValue(data_get($item,$column['key']),$column['key']);
                                })->implode(' ');
                            @endphp

                            <tr class="item-row crud-table-row" data-search="{{ $searchText }}">

                                @foreach($columns as $column)
                                    @php
                                        $value=data_get($item,$column['key']);
                                        $displayValue=$formatValue($value,$column['key']);
                                        $columnType=$column['type']??'text';
                                        $isLong=$isLongColumn($column);
                                        $imageUrl=$columnType==='image'?$formatImageUrl($value):null;
                                        $columnClass='crud-column-normal';

                                        if($columnType==='image')$columnClass='crud-column-image';
                                        elseif($isLong)$columnClass='crud-column-long';
                                        elseif(($column['size']??'')==='short')$columnClass='crud-column-short';
                                        elseif(($column['size']??'')==='medium')$columnClass='crud-column-medium';
                                    @endphp

                                    <td class="px-3 px-sm-4 py-3 text-secondary {{ $columnClass }}">

                                        @if($columnType==='image')

                                            @if($imageUrl)
                                                <button type="button" class="border-0 bg-transparent p-0 d-inline-block" @click="openImageModal(@js($imageUrl))" title="Ver fotografía">
                                                    <img src="{{ $imageUrl }}" alt="Fotografía" class="crud-image-thumb rounded-3 border shadow-sm">
                                                </button>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif

                                        @elseif($column['key']==='activo')

                                            <span class="badge rounded-pill {{ (bool)$value?'bg-brand-soft text-brand-primary border border-brand-subtle':'bg-light text-secondary border' }}">{{ $displayValue }}</span>

                                        @elseif($isStatusColumn($column))

                                            <span class="badge rounded-pill px-3 py-2 fw-semibold {{ $statusBadgeClass($displayValue) }}">
                                            <i class="bi {{ $statusIcon($displayValue) }} me-1"></i>{{ $displayValue }}
                                        </span>

                                        @elseif($isLevelColumn($column))

                                            <span class="badge rounded-pill px-3 py-2 fw-semibold {{ $levelBadgeClass($displayValue) }}">
                                            <i class="bi {{ $levelIcon($displayValue) }} me-1"></i>{{ $displayValue }}
                                        </span>

                                        @else

                                            <div class="crud-cell-content">{{ $displayValue }}</div>

                                        @endif

                                    </td>
                                @endforeach

                                @if($hasActions)
                                    <td class="px-3 px-sm-4 py-3 crud-column-actions">
                                        <div class="d-inline-flex align-items-center justify-content-center gap-1">

                                            @if($canShow)
                                                @if($showWithRoute)
                                                    <a href="{{ route($routeBase.'.show',$item) }}" title="Ver datos" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1"><i class="bi bi-eye"></i></a>
                                                @else
                                                    <button type="button" @click="openShowModal(@js($showPayload))" title="Ver datos" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1"><i class="bi bi-eye"></i></button>
                                                @endif
                                            @endif

                                            @if($canUpdate)
                                                <button type="button" @click="openEditModal(@js($editPayload))" title="Editar" class="btn btn-sm btn-outline-success rounded-2 px-2 py-1"><i class="bi bi-pencil"></i></button>
                                            @endif

                                            @if($canDestroy)
                                                <form action="{{ route($routeBase.'.destroy',$item) }}" method="POST" class="d-inline" @submit.prevent="askDeleteConfirmation($event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Eliminar" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                @endif

                            </tr>

                        @empty
                            <tr><td colspan="{{ count($columns)+($hasActions?1:0) }}" class="px-4 py-5 text-center text-muted">No hay {{ strtolower($entityPlural) }} registrados.</td></tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>

                <div x-show="filteredRows.length===0&&rows.length>0" x-cloak class="py-5 text-center text-muted">
                    <i class="bi bi-search fs-3 text-secondary mb-2 d-block"></i>No se encontraron resultados para la búsqueda.
                </div>
            </div>

            <div x-show="rows.length>0&&totalPages>1" x-cloak class="card-footer bg-light border-top border-light-subtle d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 py-3 px-3 px-sm-4">
                <span class="small text-secondary">Página <strong class="text-brand-primary" x-text="currentPage"></strong> de <strong class="text-brand-primary" x-text="totalPages"></strong></span>

                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{'disabled':currentPage===1}">
                        <button type="button" class="page-link rounded-start-2" @click="previousPage()" :disabled="currentPage===1"><i class="bi bi-chevron-left me-1"></i>Anterior</button>
                    </li>
                    <li class="page-item" :class="{'disabled':currentPage===totalPages}">
                        <button type="button" class="page-link rounded-end-2" @click="nextPage()" :disabled="currentPage===totalPages">Siguiente<i class="bi bi-chevron-right ms-1"></i></button>
                    </li>
                </ul>
            </div>

        </div>

        @if($canShow&&!$showWithRoute)
            <div x-show.important="showModalOpen" x-cloak x-transition.opacity.duration.180ms class="modal fade show d-block crud-modal-backdrop" tabindex="-1" role="dialog" aria-modal="true" @keydown.escape.window="closeShowModal()" @click.self="closeShowModal()">

                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" @click.outside="closeShowModal()">
                    <div class="modal-content shadow-lg rounded-4 overflow-hidden crud-view-modal">

                        <div class="crud-view-header">
                            <button type="button" class="crud-view-close" @click="closeShowModal()" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button>
                            <div class="crud-view-icon"><i class="bi bi-eye"></i></div>
                            <h4 class="crud-view-title">Ver {{ $entitySingular }}</h4>
                            <p class="crud-view-subtitle">Consulta la información completa del registro seleccionado.</p>
                        </div>

                        <div class="modal-body crud-view-body">
                            <div class="crud-view-grid">

                                <template x-for="(field,index) in selectedShow" :key="index">
                                    <div class="crud-view-card" :class="field.type==='image'||field.full||(field.value&&field.value.length>110)?'crud-view-card-full':''">

                                        <div class="crud-view-card-header">
                                            <div class="crud-view-label">
                                                <span class="crud-view-field-icon"><i class="bi" :class="field.icon"></i></span>
                                                <span class="crud-view-label-text" x-text="field.label"></span>
                                            </div>
                                            <span class="crud-view-index" x-text="String(index+1).padStart(2,'0')"></span>
                                        </div>

                                        <template x-if="field.type==='image'&&field.url">
                                            <button type="button" class="crud-view-image-button" @click="openImageModal(field.url)" title="Ampliar fotografía">
                                                <img :src="field.url" alt="Imagen del registro" class="crud-view-image">
                                            </button>
                                        </template>

                                        <template x-if="field.type==='image'&&!field.url">
                                            <div class="crud-view-empty">Sin fotografía disponible</div>
                                        </template>

                                        <template x-if="field.type!=='image'&&(field.status||field.level)">
                                            <div class="crud-view-value-status" x-text="field.value"></div>
                                        </template>

                                        <template x-if="field.type!=='image'&&!field.status&&!field.level">
                                            <div class="crud-view-value" x-text="field.value"></div>
                                        </template>

                                    </div>
                                </template>

                            </div>
                        </div>

                        <div class="crud-view-footer">
                            <button type="button" class="btn btn-brand-outline crud-view-close-btn px-4 py-2 rounded-3" @click="closeShowModal()"><i class="bi bi-x-circle me-2"></i>Cerrar</button>
                        </div>

                    </div>
                </div>

            </div>
        @endif

        <div x-show.important="imageModalOpen" x-cloak class="modal fade show d-block crud-modal-backdrop" tabindex="-1" role="dialog" aria-modal="true" @keydown.escape.window="closeImageModal()" @click.self="closeImageModal()">

            <div class="modal-dialog modal-dialog-centered modal-lg" @click.outside="closeImageModal()">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden crud-image-modal-content">

                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                        <div><h5 class="fw-bold text-brand-dark mb-0">Vista de fotografía</h5><small class="text-secondary">Evidencia registrada en el sistema</small></div>
                        <button type="button" class="btn-close" @click="closeImageModal()" aria-label="Cerrar"></button>
                    </div>

                    <div class="crud-image-modal-body"><img :src="selectedImage" alt="Fotografía de evidencia" class="img-fluid crud-image-modal-preview"></div>

                    <div class="modal-footer border-top-0 justify-content-end px-4 pb-4 pt-2">
                        <button type="button" class="btn btn-brand-outline px-4 py-2 rounded-3" @click="closeImageModal()"><i class="bi bi-x-circle me-2"></i>Cerrar</button>
                    </div>

                </div>
            </div>

        </div>

        @if($canStore||$canUpdate)
            <div x-show.important="formModalOpen" x-cloak class="modal fade show d-block crud-modal-backdrop" tabindex="-1" role="dialog" aria-modal="true" @keydown.escape.window="closeFormModal()" @click.self="closeFormModal()">

                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" @click.outside="closeFormModal()">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden position-relative">

                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" @click="closeFormModal()" aria-label="Cerrar"></button>

                        <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 text-center d-flex flex-column align-items-center">
                            <div class="crud-modal-icon mb-3"><i class="bi fs-3" :class="formMode==='create'?'bi-plus-circle':'bi-pencil-square'"></i></div>
                            <h4 class="modal-title fw-bold text-brand-dark" x-text="formMode==='create'?'Nuevo {{ $entitySingular }}':'Editar {{ $entitySingular }}'"></h4>
                            <p class="text-secondary small mb-0 mt-1" x-text="formMode==='create'?'Ingresa la información necesaria para registrar un nuevo elemento.':'Modifica la información del registro seleccionado.'"></p>
                        </div>

                        <form :action="formAction" method="POST" @submit.prevent="askFormConfirmation($event)" @if($hasFileField) enctype="multipart/form-data" @endif>

                            @csrf

                            <input type="hidden" name="_method" value="PUT" :disabled="formMode!=='edit'">
                            <input type="hidden" name="_crud_mode" :value="formMode">
                            <input type="hidden" name="_crud_edit_action" :value="formMode==='edit'?formAction:''">

                            <div class="modal-body px-4 py-3">
                                <div class="row g-3">

                                    @foreach($fields as $field)
                                        @php
                                            $fieldName=$field['name'];
                                            $fieldType=$field['type']??'text';
                                            $required=$field['required']??false;
                                            $requiredCreate=$field['required_create']??false;
                                            $fullWidth=in_array($fieldType,['textarea','file'],true);
                                        @endphp

                                        <div class="{{ $fullWidth?'col-12':'col-12 col-md-6' }}">

                                            <label for="{{ $fieldName }}" class="form-label small fw-bold text-brand-primary mb-1">
                                                {{ $field['label'] }}
                                                @if($required)<span class="text-danger">*</span>
                                                @elseif($requiredCreate)<span x-show="formMode==='create'" class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @if($fieldType==='textarea')

                                                <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" x-model="formData['{{ $fieldName }}']" rows="{{ $field['rows']??3 }}" @if($required) required @elseif($requiredCreate) x-bind:required="formMode==='create'" @endif class="form-control rounded-3 @error($fieldName) is-invalid @enderror"></textarea>

                                            @elseif($fieldType==='select')

                                                <select id="{{ $fieldName }}" name="{{ $fieldName }}" x-model="formData['{{ $fieldName }}']" @if($required) required @elseif($requiredCreate) x-bind:required="formMode==='create'" @endif class="form-select rounded-3 @error($fieldName) is-invalid @enderror">
                                                    <option value="">Seleccione una opción</option>
                                                    @foreach(($field['options']??[]) as $option)
                                                        <option value="{{ data_get($option,$field['option_value']??'id') }}">{{ data_get($option,$field['option_label']??'nombre') }}</option>
                                                    @endforeach
                                                </select>

                                            @elseif($fieldType==='file')

                                                <input id="{{ $fieldName }}" type="file" name="{{ $fieldName }}" accept="{{ $field['accept']??'image/*' }}" @if($required) required @elseif($requiredCreate) x-bind:required="formMode==='create'" @endif class="microseed-file-input form-control rounded-3 @error($fieldName) is-invalid @enderror">
                                                @if(!empty($field['help']))<div class="form-text small">{{ $field['help'] }}</div>@endif

                                            @else

                                                <input id="{{ $fieldName }}" type="{{ $fieldType }}" name="{{ $fieldName }}" x-model="formData['{{ $fieldName }}']" @if(isset($field['step'])) step="{{ $field['step'] }}" @endif @if(isset($field['min'])) min="{{ $field['min'] }}" @endif @if(isset($field['max'])) max="{{ $field['max'] }}" @endif @if(!empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif @if($required) required @elseif($requiredCreate) x-bind:required="formMode==='create'" @endif class="form-control rounded-3 @error($fieldName) is-invalid @enderror">

                                            @endif

                                            @error($fieldName)<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror

                                        </div>
                                    @endforeach

                                </div>
                            </div>

                            <div class="modal-footer border-top-0 justify-content-center gap-3 pb-4 pt-2">
                                <button type="button" @click="closeFormModal()" class="btn btn-secondary px-4 py-2 rounded-3 fw-semibold">Cancelar</button>
                                <button type="submit" class="btn btn-brand px-4 py-2 rounded-3" x-text="formMode==='create'?'Guardar registro':'Guardar cambios'"></button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        @endif

    </div>

    <script>
        function crudModule(){
            return{
                search:'',perPage:10,currentPage:1,rows:[],filteredRows:[],showModalOpen:false,selectedShow:[],imageModalOpen:false,selectedImage:'',formModalOpen:false,formMode:'create',formAction:'',formData:@js($emptyForm),

                init(){
                    this.$nextTick(()=>{this.loadRows();this.updateTable();});
                    this.$watch('search',()=>{this.currentPage=1;this.updateTable();});
                    this.$watch('perPage',()=>{this.currentPage=1;this.updateTable();});

                    const hasErrors=@js($errors->any()),hasFormPermissions=@js($canStore||$canUpdate);

                    if(hasErrors&&hasFormPermissions){
                        const oldMode=@js(old('_crud_mode','create'));
                        this.formMode=oldMode==='edit'?'edit':'create';
                        this.formAction=this.formMode==='edit'?@js(old('_crud_edit_action','')):@js($canStore?route($routeBase.'.store'):'');
                        this.formData=@js($oldForm);
                        this.formModalOpen=true;
                    }
                },

                loadRows(){
                    if(!this.$refs.recordsContainer){this.rows=[];return;}
                    this.rows=Array.from(this.$refs.recordsContainer.querySelectorAll('.item-row'));
                },

                updateTable(){
                    const term=this.search.trim().toLowerCase();
                    this.filteredRows=this.rows.filter(row=>(row.dataset.search??'').toLowerCase().includes(term));

                    const amount=Number(this.perPage),pages=Math.max(1,Math.ceil(this.filteredRows.length/amount));
                    if(this.currentPage>pages)this.currentPage=pages;

                    const start=(this.currentPage-1)*amount,end=start+amount;
                    this.rows.forEach(row=>row.style.display='none');
                    this.filteredRows.slice(start,end).forEach(row=>row.style.display='');
                },

                get totalPages(){return Math.max(1,Math.ceil(this.filteredRows.length/Number(this.perPage)));},

                previousPage(){if(this.currentPage<=1)return;this.currentPage--;this.updateTable();},
                nextPage(){if(this.currentPage>=this.totalPages)return;this.currentPage++;this.updateTable();},
                clearSearch(){this.search='';this.currentPage=1;this.updateTable();},

                openShowModal(data){this.selectedShow=data??[];this.showModalOpen=true;},
                closeShowModal(){this.showModalOpen=false;this.selectedShow=[];},

                openImageModal(url){if(!url)return;this.selectedImage=url;this.imageModalOpen=true;},
                closeImageModal(){this.imageModalOpen=false;this.selectedImage='';},

                openCreateModal(){
                    this.formMode='create';
                    this.formAction=@js($canStore?route($routeBase.'.store'):'');
                    this.formData=@js($emptyForm);
                    this.resetFileInputs();
                    this.formModalOpen=true;
                },

                openEditModal(payload){
                    this.formMode='edit';
                    this.formAction=payload.action??'';
                    this.formData={...@js($emptyForm),...(payload.fields??{})};
                    this.resetFileInputs();
                    this.formModalOpen=true;
                },

                closeFormModal(){
                    this.formModalOpen=false;
                    this.formMode='create';
                    this.formAction='';
                    this.formData=@js($emptyForm);
                    this.resetFileInputs();
                },

                resetFileInputs(){
                    this.$nextTick(()=>document.querySelectorAll('.microseed-file-input').forEach(input=>input.value=''));
                },

                async askFormConfirmation(event){
                    event.preventDefault();

                    const form=event.currentTarget;
                    if(!form.checkValidity()){form.reportValidity();return;}

                    if(this.formMode==='edit'){
                        if(typeof window.microseedConfirmEdit==='function'){
                            const result=await window.microseedConfirmEdit();
                            if(result?.isConfirmed)form.submit();
                        }else form.submit();

                        return;
                    }

                    if(typeof window.microseedConfirmCreate==='function'){
                        const result=await window.microseedConfirmCreate();
                        if(result?.isConfirmed)form.submit();
                    }else form.submit();
                },

                async askDeleteConfirmation(event){
                    event.preventDefault();

                    const form=event.currentTarget;

                    if(typeof window.microseedConfirmDelete==='function'){
                        const result=await window.microseedConfirmDelete();
                        if(result?.isConfirmed)form.submit();
                    }else form.submit();
                }
            };
        }
    </script>

</x-app-layout>
