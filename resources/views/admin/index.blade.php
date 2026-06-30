@extends('adminlte::page')

@section('title', 'Dashboard General')

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="dashboard-programacion">

        <div class="dashboard-head">
            <div>
                <h1>Dashboard General</h1>
                <p>Monitoreo y gestion de programaciones en tiempo real</p>
            </div>

            <a href="{{ route('admin.schedules.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-calendar-alt"></i>
                Ir al Modulo de Programacion
            </a>
        </div>

        <div class="row dashboard-stats">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-blue">
                    <div>
                        <strong>{{ $dashboard['total'] }}</strong>
                        <span>Total Programaciones</span>
                    </div>
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-green">
                    <div>
                        <strong>{{ $dashboard['completed'] }}</strong>
                        <span>Programaciones Completas</span>
                    </div>
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-red">
                    <div>
                        <strong>{{ $dashboard['incompleted'] }}</strong>
                        <span>Programaciones Incompletas</span>
                    </div>
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card stat-yellow">
                    <div>
                        <strong>{{ $dashboard['missing_personnel'] }}</strong>
                        <span>Personal Faltante</span>
                    </div>
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="dashboard-filter">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label><i class="fas fa-calendar-day"></i> Fecha:</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>

                <div class="col-md-4">
                    <label><i class="fas fa-clock"></i> Turno:</label>
                    <select name="shift_id" class="form-control">
                        <option value="">Todos los turnos</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" @selected((string) $shiftId === (string) $shift->id)>
                                {{ $shift->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </form>

        <div class="row dashboard-zones">
            @forelse ($dashboard['cards'] as $card)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                    <div class="zone-card {{ $card['is_complete'] ? 'zone-complete' : 'zone-incomplete' }}">
                        <div class="zone-card-header">
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $card['zone'] }}</span>
                            </div>

                            @if ($card['is_complete'])
                                <span class="zone-ok">OK</span>
                            @else
                                <span class="zone-alert"><i class="fas fa-pen"></i></span>
                            @endif
                        </div>

                        <div class="zone-card-body">
                            <div class="zone-info-grid">
                                <div>
                                    <i class="fas fa-clock text-primary"></i>
                                    <small>Turno</small>
                                    <strong>{{ $card['shift'] }}</strong>
                                </div>

                                <div>
                                    <i class="fas fa-truck text-warning"></i>
                                    <small>Vehiculo</small>
                                    <strong>{{ $card['vehicle'] }}</strong>
                                </div>
                            </div>

                            <div class="zone-group">
                                <small>Grupo</small>
                                <strong>{{ $card['group'] }}</strong>
                            </div>

                            <div class="zone-status">
                                @if ($card['is_complete'])
                                    <span class="badge badge-success badge-custom">
                                        <i class="fas fa-check"></i>
                                        Completo
                                    </span>
                                @else
                                    <span class="badge badge-danger badge-custom" title="{{ $card['missing_names'] }}">
                                        <i class="fas fa-exclamation"></i>
                                        Incompleto
                                    </span>
                                @endif
                            </div>

                            <div class="zone-counts">
                                <div class="present-count">
                                    <i class="fas fa-user-check"></i>
                                    <strong>{{ $card['present_count'] }}</strong>
                                    <span>Presentes</span>
                                </div>

                                <div class="missing-count">
                                    <i class="fas fa-user-times"></i>
                                    <strong>{{ $card['missing_count'] }}</strong>
                                    <span>Faltantes</span>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm btn-block btn-open-daily"
                                data-id="{{ $card['id'] }}">
                                <i class="fas fa-eye"></i>
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="dashboard-empty">
                        <i class="fas fa-calendar-times"></i>
                        <strong>No hay programaciones para los filtros seleccionados.</strong>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="dailyEditorModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <form class="modal-content" id="daily-editor-form">
                @csrf

                <div class="modal-header dashboard-modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i>
                        Editor de Programacion
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body dashboard-modal-body">
                    <input type="hidden" id="editor_daily_id">
                    <input type="hidden" id="editor_driver_id">

                    <div class="editor-summary">
                        <div>
                            <small>Zona</small>
                            <strong id="editor_zone">-</strong>
                        </div>
                        <div>
                            <small>Grupo</small>
                            <strong id="editor_group">-</strong>
                        </div>
                        <div>
                            <small>Fecha</small>
                            <strong id="editor_date">-</strong>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="editor-panel panel-blue">
                                <div class="editor-panel-title">
                                    <i class="fas fa-clock"></i>
                                    Cambio de Turno
                                </div>

                                <div class="editor-panel-body">
                                    <label>Nuevo Turno</label>
                                    <select id="editor_shift_id" class="form-control" required></select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="editor-panel panel-green">
                                <div class="editor-panel-title">
                                    <i class="fas fa-truck"></i>
                                    Cambio de Vehiculo
                                </div>

                                <div class="editor-panel-body">
                                    <label>Nuevo Vehiculo</label>
                                    <select id="editor_vehicle_id" class="form-control" required></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="editor-panel panel-teal">
                        <div class="editor-panel-title">
                            <i class="fas fa-users"></i>
                            Cambio de Personal
                        </div>

                        <div class="editor-panel-body">
                            <div class="personnel-editor-row">
                                <div>
                                    <label>Personal Actual</label>
                                    <div class="current-person">
                                        <i class="fas fa-user"></i>
                                        <span id="editor_driver_current">-</span>
                                    </div>
                                </div>

                                <div>
                                    <label>Nuevo Personal</label>
                                    <select id="editor_new_driver_id" class="form-control"></select>
                                </div>
                            </div>

                            <div id="editor_helpers"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        let editorState = null;

        function optionList(items, selectedId, emptyText) {
            let html = emptyText ? `<option value="">${emptyText}</option>` : '';

            items.forEach(item => {
                let selected = String(item.id) === String(selectedId) ? 'selected' : '';
                html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
            });

            return html;
        }

        function personnelOptions(items) {
            if (!items.length) {
                return '<option value="">Sin personal disponible</option>';
            }

            let html = '<option value="">Mantener actual</option>';

            items.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });

            return html;
        }

        function openDailyEditor(id) {
            const url = "{{ route('dashboard.daily.show', ['daily' => '__ID__']) }}".replace('__ID__', id);

            $.get(url, function(response) {
                editorState = response;

                $('#editor_daily_id').val(response.daily.id);
                $('#editor_zone').text(response.daily.zone);
                $('#editor_group').text(response.daily.group);
                $('#editor_date').text(response.daily.date);

                $('#editor_shift_id').html(optionList(response.shifts, response.daily.shift_id));
                $('#editor_vehicle_id').html(optionList(response.vehicles, response.daily.vehicle_id));

                $('#editor_driver_id').val(response.daily.driver_id);
                $('#editor_driver_current').text(
                    response.driver ? `${response.driver.name} (${response.driver.role})` : 'Sin conductor'
                );
                $('#editor_new_driver_id').html(personnelOptions(response.available_drivers));

                let helpersHtml = '';

                response.helpers.forEach((helper, index) => {
                    helpersHtml += `
                        <div class="personnel-editor-row helper-row" data-current-id="${helper.id}">
                            <div>
                                <label>Personal Actual</label>
                                <div class="current-person">
                                    <i class="fas fa-user"></i>
                                    <span>${helper.name} (${helper.role})</span>
                                </div>
                            </div>

                            <div>
                                <label>Nuevo Personal</label>
                                <select class="form-control editor-new-helper">
                                    ${personnelOptions(response.available_helpers)}
                                </select>
                            </div>
                        </div>
                    `;
                });

                if (!helpersHtml) {
                    helpersHtml = `
                        <div class="editor-empty-personnel">
                            Esta programacion no tiene ayudantes asignados.
                        </div>
                    `;
                }

                $('#editor_helpers').html(helpersHtml);
                $('#dailyEditorModal').modal('show');
            }).fail(function() {
                Swal.fire('Error', 'No se pudo cargar el detalle de la programacion.', 'error');
            });
        }

        $(document).on('click', '.btn-open-daily', function() {
            openDailyEditor($(this).data('id'));
        });

        $('#daily-editor-form').on('submit', function(e) {
            e.preventDefault();

            if (!editorState) {
                return;
            }

            const dailyId = $('#editor_daily_id').val();
            const url = "{{ route('dashboard.daily.update', ['daily' => '__ID__']) }}".replace('__ID__', dailyId);

            const driverId = $('#editor_new_driver_id').val() || $('#editor_driver_id').val();
            const helperIds = [];

            $('.helper-row').each(function() {
                const replacement = $(this).find('.editor-new-helper').val();
                helperIds.push(replacement || $(this).data('current-id'));
            });

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    shift_id: $('#editor_shift_id').val(),
                    vehicle_id: $('#editor_vehicle_id').val(),
                    driver_id: driverId,
                    helper_ids: helperIds
                },
                success: function(response) {
                    $('#dailyEditorModal').modal('hide');

                    Swal.fire('Proceso exitoso', response.message, 'success')
                        .then(() => window.location.reload());
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message ?? 'No se pudo actualizar la programacion.',
                        'error'
                    );
                }
            });
        });
    </script>
@stop

@section('css')
    <style>
        .dashboard-programacion {
            padding: 8px 0 18px;
        }

        .dashboard-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        .dashboard-head h1 {
            color: #1f2933;
            font-size: 25px;
            font-weight: 900;
            margin: 0;
        }

        .dashboard-head p {
            color: #667085;
            font-size: 13px;
            font-weight: 700;
            margin: 2px 0 0;
        }

        .dashboard-head .btn {
            border: none;
            border-radius: 7px;
            font-weight: 800;
            padding: 8px 12px;
        }

        .stat-card {
            min-height: 86px;
            border-radius: 7px;
            padding: 16px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(31, 41, 51, 0.13);
        }

        .stat-card strong {
            display: block;
            font-size: 27px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 9px;
        }

        .stat-card span {
            display: block;
            font-size: 12px;
            font-weight: 800;
        }

        .stat-card i {
            font-size: 46px;
            opacity: 0.2;
        }

        .stat-blue {
            background: #068cc2;
        }

        .stat-green {
            background: #28a745;
        }

        .stat-red {
            background: #dc3545;
        }

        .stat-yellow {
            background: #ffc107;
            color: #4b3b00;
        }

        .dashboard-filter {
            background: #ffffff;
            border: 1px solid #e1e5df;
            border-radius: 7px;
            padding: 14px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(31, 41, 51, 0.04);
        }

        .dashboard-filter label {
            color: #1f2933;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .dashboard-filter .form-control,
        .dashboard-filter .btn {
            height: 36px !important;
            border-radius: 7px !important;
            font-size: 13px !important;
            font-weight: 700;
        }

        .zone-card {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #dfe5dc;
            overflow: hidden;
            min-height: 265px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 3px 12px rgba(31, 41, 51, 0.08);
        }

        .zone-card-header {
            min-height: 36px;
            padding: 9px 12px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 900;
        }

        .zone-complete .zone-card-header {
            background: #28a745;
        }

        .zone-incomplete .zone-card-header {
            background: #dc3545;
        }

        .zone-ok,
        .zone-alert {
            background: rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: 900;
        }

        .zone-card-body {
            padding: 13px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .zone-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            text-align: center;
            margin-bottom: 12px;
        }

        .zone-info-grid i {
            display: block;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .zone-info-grid small,
        .zone-group small {
            display: block;
            color: #667085;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .zone-info-grid strong,
        .zone-group strong {
            display: block;
            color: #1f2933;
            font-size: 13px;
            font-weight: 900;
            overflow-wrap: anywhere;
        }

        .zone-group {
            text-align: center;
            margin-bottom: 11px;
        }

        .zone-status {
            text-align: center;
            min-height: 26px;
            margin-bottom: 12px;
        }

        .zone-counts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: auto;
            margin-bottom: 9px;
        }

        .zone-counts div {
            min-height: 46px;
            color: #ffffff;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 900;
        }

        .present-count {
            background: #28a745;
        }

        .missing-count {
            background: #dc3545;
        }

        .zone-card .btn {
            border: none;
            border-radius: 6px;
            font-weight: 800;
        }

        .dashboard-empty {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
            padding: 26px;
            text-align: center;
        }

        .dashboard-empty i {
            display: block;
            font-size: 30px;
            margin-bottom: 9px;
        }

        .dashboard-modal-header {
            background: #0b3d6d !important;
            color: #ffffff !important;
            border-bottom: none !important;
        }

        .dashboard-modal-header .modal-title,
        .dashboard-modal-header .close,
        .dashboard-modal-header .close span {
            color: #ffffff !important;
        }

        .dashboard-modal-body {
            background: #f5f6f4 !important;
        }

        .editor-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .editor-summary div,
        .current-person,
        .editor-empty-personnel {
            background: #ffffff;
            border: 1px solid #dfe5dc;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .editor-summary small {
            display: block;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .editor-summary strong {
            color: #1f2933;
            font-size: 14px;
            font-weight: 900;
        }

        .editor-panel {
            background: #ffffff;
            border: 1px solid #dfe5dc;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .editor-panel-title {
            color: #ffffff;
            padding: 10px 13px;
            font-size: 14px;
            font-weight: 900;
        }

        .panel-blue .editor-panel-title {
            background: #0ea5e9;
        }

        .panel-green .editor-panel-title {
            background: #28a745;
        }

        .panel-teal .editor-panel-title {
            background: #17a2b8;
        }

        .editor-panel-body {
            padding: 13px;
        }

        .personnel-editor-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #edf0eb;
        }

        .personnel-editor-row:first-child {
            padding-top: 0;
        }

        .personnel-editor-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .current-person {
            min-height: 34px;
            display: flex;
            align-items: center;
            gap: 9px;
            color: #1f2933;
            font-size: 13px;
            font-weight: 800;
        }

        .current-person i {
            color: #0ea5e9;
        }

        .editor-empty-personnel {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 767px) {
            .dashboard-head {
                display: block;
            }

            .dashboard-head .btn {
                margin-top: 10px;
                width: 100%;
            }

            .editor-summary,
            .personnel-editor-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop
