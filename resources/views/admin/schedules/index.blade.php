@extends('adminlte::page')

@section('title', 'Programaciones')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)

@section('content')
    <div class="pt-4"></div>

    <div class="card">
        <div class="card-header">
            <div class="float-right">
                <button type="button" class="btn btn-primary btn-sm" id="btn-nueva-programacion">
                    <i class="fas fa-plus"></i> Nueva Programación
                </button>
                <button type="button" class="btn btn-info btn-sm" id="btn-programacion-masiva">
                    <i class="fas fa-layer-group"></i> Programación Masiva
                </button>
            </div>
            <h4><i class="fas fa-calendar-check"></i> Lista de Programaciones</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="schedules-table" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Grupo</th>
                            <th>Zona</th>
                            <th>Turno</th>
                            <th>Vehículo</th>
                            <th>Conductor</th>
                            <th>Ayudantes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Nueva Programación -->
    <div class="modal fade" id="modal-schedule" tabindex="-1" role="dialog" aria-labelledby="modalScheduleLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalScheduleLabel"><i class="fas fa-calendar-plus"></i> Nueva Programación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="schedule-form">
                    @csrf
                    <input type="hidden" name="id" id="schedule_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grupo de Personal <span class="text-danger">*</span></label>

                                    <input type="hidden" name="personnel_group_id" id="personnel_group_id">

                                    <div class="position-relative">
                                        <input type="text" id="group_search_input" class="form-control pr-5"
                                            placeholder="Busque o seleccione un grupo..." autocomplete="off" required>

                                        <button type="button" id="clear_group_search" class="btn btn-sm text-muted"
                                            style="position:absolute; right:8px; top:50%; transform:translateY(-50%); display:none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div id="group_results" class="list-group shadow-sm d-none"
                                        style="position:absolute; z-index:9999; left:15px; right:15px; max-height:220px; overflow-y:auto;">

                                        @foreach ($groups as $group)
                                            <button type="button"
                                                class="list-group-item list-group-item-action group-option"
                                                data-id="{{ $group->id }}" data-text="{{ $group->name }}">
                                                {{ $group->name }}
                                            </button>
                                        @endforeach

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Inicio <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Fin <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Zona <span class="text-danger">*</span></label>

                                    <select name="zone_id_visual" id="zone_id" class="form-control select2"
                                        style="width: 100%" disabled>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="zone_id" id="zone_id_hidden">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Turno <span class="text-danger">*</span></label>

                                    <select name="shift_id_visual" id="shift_id" class="form-control select2"
                                        style="width: 100%" disabled>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="shift_id" id="shift_id_hidden">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehículo <span class="text-danger">*</span></label>

                                    <select name="vehicle_id_visual" id="vehicle_id" class="form-control select2"
                                        style="width: 100%" disabled>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->plate }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="vehicle_id" id="vehicle_id_hidden">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Días de la Semana <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap border p-1 rounded bg-white">
                                        @foreach (['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá', 'Do'] as $day)
                                            <div class="custom-control custom-checkbox mr-2">
                                                <input class="custom-control-input workday-checkbox" type="checkbox"
                                                    name="workdays[]" id="day_{{ $day }}"
                                                    value="{{ $day }}" onclick="return false;">
                                                <label for="day_{{ $day }}"
                                                    class="custom-control-label font-weight-normal">{{ $day }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4 text-center">
                                <label class="font-weight-bold text-muted small text-uppercase">Conductor</label>
                                <input type="hidden" name="driver_id" id="driver_id">
                                <div id="driver-card">
                                    <div class="p-3 border rounded bg-light text-muted small">Sin asignar</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="font-weight-bold text-muted small text-uppercase d-block text-center">
                                    Ayudantes
                                </label>

                                <div class="row" id="helpers-cards-container">
                                    <div class="col-md-12">
                                        <div class="p-3 border rounded bg-light text-muted small text-center">
                                            Sin ayudantes asignados
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notas / Observaciones</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" id="btn-validate" class="btn btn-info">
                            <i class="fas fa-shield-alt"></i> Validar Disponibilidad
                        </button>
                        <button type="submit" id="btn-save" class="btn btn-success" disabled>
                            <i class="fas fa-save"></i> Guardar Programación
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Programación Masiva -->
    <div class="modal fade" id="modal-mass" tabindex="-1" role="dialog" aria-labelledby="modalMassLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalMassLabel"><i class="fas fa-layer-group"></i> Nueva Programación
                        Masiva</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" id="mass_start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" id="mass_end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Filtrar por Turno</label>
                                <input type="hidden" id="mass_shift_id" value="">

                                <div class="position-relative">
                                    <input type="text" id="mass_shift_search_input" class="form-control pr-5"
                                        placeholder="Busque o seleccione un turno..." autocomplete="off">

                                    <button type="button" id="clear_mass_shift_search" class="btn btn-sm text-muted"
                                        style="position:absolute; right:8px; top:50%; transform:translateY(-50%); display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div id="mass_shift_results" class="list-group shadow-sm d-none"
                                    style="position:absolute; z-index:99999; left:0; right:0; max-height:220px; overflow-y:auto;">

                                    <button type="button"
                                        class="list-group-item list-group-item-action mass-shift-option" data-id=""
                                        data-text="Todos los turnos">
                                        Todos los turnos
                                    </button>

                                    @foreach ($shifts as $shift)
                                        <button type="button"
                                            class="list-group-item list-group-item-action mass-shift-option"
                                            data-id="{{ $shift->id }}" data-text="{{ $shift->name }}">
                                            {{ $shift->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="feriados-alerta" class="alert alert-warning d-none">
                        <i class="fas fa-calendar-day"></i> Feriados detectados en el rango: <span
                            id="feriados-lista"></span>
                    </div>

                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-primary" id="btn-previsualizar-masiva">
                            <i class="fas fa-search"></i> Previsualizar Grupos
                        </button>
                    </div>

                    <div id="previsualizacion-container" class="d-none">
                        <hr>
                        <h5><i class="fas fa-list"></i> Grupos a Programar</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th width="40"><input type="checkbox" id="mass-select-all"></th>
                                        <th>Grupo de Personal</th>
                                        <th>Configuración (Turno/Vehículo)</th>
                                        <th>Asignación de Personal</th>
                                        <th>Validación / Avisos</th>
                                    </tr>
                                </thead>
                                <tbody id="mass-preview-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success d-none" id="btn-confirmar-masiva">
                        <i class="fas fa-check-circle"></i> Confirmar y Registrar Programación
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Modificar Programación (Rediseñado según 4ta Imagen) -->
    <div class="modal fade" id="modal-edit-schedule" tabindex="-1" role="dialog"
        aria-labelledby="modalEditScheduleLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditScheduleLabel"><i class="fas fa-edit"></i> Modificar
                        Programación de Servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit-schedule-form">
                    @csrf
                    <input type="hidden" name="schedule_id" id="edit_schedule_id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Columna Turnos -->
                            <div class="col-md-4">
                                <div class="card card-outline card-primary h-100">
                                    <div class="card-header">
                                        <h3 class="card-title text-primary font-weight-bold">TURNOS</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-4">
                                            <label class="text-muted small">Turno Actual:</label>
                                            <div id="current_shift_display"
                                                class="font-weight-bold h5 text-dark border-bottom pb-2">-</div>
                                        </div>
                                        <div class="form-group">
                                            <label>Cambiar Turno a:</label>
                                            <select name="shift_id" id="edit_shift_id" class="form-control select2"
                                                style="width: 100%">
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Días Programados</label>
                                            <div class="d-flex flex-wrap border p-2 rounded bg-light"
                                                id="edit-workdays-container">
                                                <!-- Se llena vía JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Vehículo -->
                            <div class="col-md-4">
                                <div class="card card-outline card-success h-100">
                                    <div class="card-header">
                                        <h3 class="card-title text-success font-weight-bold">VEHÍCULO</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-4">
                                            <label class="text-muted small">Vehículo Actual:</label>
                                            <div id="current_vehicle_display"
                                                class="font-weight-bold h5 text-dark border-bottom pb-2">-</div>
                                        </div>
                                        <div class="form-group">
                                            <label>Cambiar Vehículo a:</label>
                                            <select name="vehicle_id" id="edit_vehicle_id" class="form-control select2"
                                                style="width: 100%">
                                                @foreach ($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate }} -
                                                        {{ $vehicle->brand?->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="edit-vehicle-preview" class="mt-2 p-3 text-center">
                                            <i class="fas fa-truck fa-3x text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Personal (Solo Permanente/Nombrado con Asistencia) -->
                            <div class="col-md-4">
                                <div class="card card-outline card-info h-100">
                                    <div class="card-header">
                                        <h3 class="card-title text-info font-weight-bold">PERSONAL</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Conductor (Permanente/Nombrado)</label>
                                            <select name="driver_id" id="edit_driver_id"
                                                class="form-control select2-eligible" style="width: 100%"></select>
                                        </div>
                                        <div class="form-group">
                                            <label>Ayudante 1</label>
                                            <select name="helper_ids[]" id="edit_helper_1"
                                                class="form-control select2-eligible" style="width: 100%"></select>
                                        </div>
                                        <div class="form-group">
                                            <label>Ayudante 2</label>
                                            <select name="helper_ids[]" id="edit_helper_2"
                                                class="form-control select2-eligible" style="width: 100%"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group position-relative">

                                    <label class="text-danger font-weight-bold">
                                        Motivo del cambio <span class="text-danger">*</span>
                                    </label>

                                    <input type="hidden" name="reason_id" id="edit_reason_id" required>

                                    <div class="position-relative">

                                        <input type="text" id="edit_reason_search" class="form-control pr-5"
                                            placeholder="Busque o seleccione un motivo..." autocomplete="off" required>

                                        <button type="button" id="clear_reason_search" class="btn btn-sm text-muted"
                                            style="position:absolute; right:8px; top:50%; transform:translateY(-50%); display:none;">
                                            <i class="fas fa-times"></i>
                                        </button>

                                    </div>

                                    <div id="reason_results" class="list-group shadow-sm d-none"
                                        style="position:absolute; z-index:9999; left:0; right:0; max-height:180px; overflow-y:auto;">

                                        @foreach ($reasons as $reason)
                                            <button type="button"
                                                class="list-group-item list-group-item-action reason-option"
                                                data-id="{{ $reason->id }}" data-name="{{ $reason->name }}"
                                                data-description="{{ $reason->description }}">

                                                {{ $reason->name }}

                                            </button>
                                        @endforeach

                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        Descripción del motivo
                                    </label>
                                    <textarea id="edit_reason_description" class="form-control" rows="2" readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                Observaciones adicionales
                            </label>
                            <textarea name="reason" id="edit_reason" class="form-control" rows="2"
                                placeholder="Ingrese una observación adicional si es necesario..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-warning font-weight-bold">
                            <i class="fas fa-save"></i> Aplicar Modificaciones
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Detalle Diario -->
    <div class="modal fade" id="modal-daily" tabindex="-1" role="dialog" aria-labelledby="modalDailyLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalDailyLabel"><i class="fas fa-calendar-day"></i> Seguimiento de
                        Programación Diaria</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Turno</th>
                                    <th>Vehículo</th>
                                    <th>Conductor</th>
                                    <th>Ayudantes</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="daily-tbody">
                                <!-- Se llena vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-history" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-history"></i>
                        Historial de Cambios
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="history-content"></div>

            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(function() {
            $('#edit_reason_search').on('keyup focus', function() {

                let value = $(this).val().toLowerCase();
                let hasResults = false;

                $('.reason-option').each(function() {

                    let text = $(this).data('name').toLowerCase();

                    if (text.includes(value)) {
                        $(this).removeClass('d-none');
                        hasResults = true;
                    } else {
                        $(this).addClass('d-none');
                    }

                });

                if (hasResults) {
                    $('#reason_results').removeClass('d-none');
                } else {
                    $('#reason_results').addClass('d-none');
                }

            });

            $(document).on('click', '.reason-option', function() {

                $('#edit_reason_id').val($(this).data('id'));
                $('#edit_reason_search').val($(this).data('name'));
                $('#edit_reason_description').val($(this).data('description') || '');

                $('#reason_results').addClass('d-none');
                $('#clear_reason_search').show();

            });

            $('#edit_reason_search').on('input', function() {

                $('#edit_reason_id').val('');
                $('#edit_reason_description').val('');

                if ($(this).val().trim() !== '') {
                    $('#clear_reason_search').show();
                } else {
                    $('#clear_reason_search').hide();
                }

            });

            $('#clear_reason_search').on('click', function() {

                $('#edit_reason_id').val('');
                $('#edit_reason_search').val('');
                $('#edit_reason_description').val('');

                $('#reason_results').addClass('d-none');
                $('#clear_reason_search').hide();

            });

            $(document).on('click', function(e) {

                if (!$(e.target).closest('#edit_reason_search, #reason_results').length) {
                    $('#reason_results').addClass('d-none');
                }

            });

            let table = $('#schedules-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.schedules.index') }}',
                columns: [{
                        data: 'date_format',
                        name: 'date'
                    },
                    {
                        data: 'group_name',
                        name: 'group_name'
                    },
                    {
                        data: 'zone_name',
                        name: 'zone_name'
                    },
                    {
                        data: 'shift_name',
                        name: 'shift_name'
                    },
                    {
                        data: 'vehicle_plate',
                        name: 'vehicle_plate'
                    },
                    {
                        data: 'driver_name',
                        name: 'driver_name'
                    },
                    {
                        data: 'helpers_names',
                        name: 'helpers_names'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });

            $(document).on('click', '.btn-daily', function() {
                let id = $(this).data('id');
                $('#modal-daily').attr('data-active-id', id);

                Swal.fire({
                    title: 'Cargando detalle...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.get(`/admin/schedules/${id}`, function(schedule) {
                    Swal.close();
                    let html = '';

                    if (schedule.dailies && schedule.dailies.length > 0) {
                        schedule.dailies.forEach(d => {
                            let helpers = d.helpers.map(h => h.names + ' ' + h.lastnames)
                                .join('<br>');
                            let statusBadge = '';
                            if (d.status == 'pendiente') statusBadge =
                                '<span class="badge badge-secondary">Pendiente</span>';
                            else if (d.status == 'completado') statusBadge =
                                '<span class="badge badge-success">Completado</span>';
                            else if (d.status == 'reprogramado') statusBadge =
                                '<span class="badge badge-warning">Reprogramado</span>';
                            else if (d.status == 'cancelado') statusBadge =
                                '<span class="badge badge-danger">Cancelado</span>';
                            else statusBadge =
                                `<span class="badge badge-info">${d.status}</span>`;

                            let dateStr = d.date;
                            if (typeof dateStr === 'string' && dateStr.includes('T')) {
                                dateStr = dateStr.split('T')[0];
                            }
                            let parts = dateStr.split('-');
                            let formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;

                            html += `
                        <tr>
                            <td class="font-weight-bold">${formattedDate}</td>
                            <td>${d.shift ? d.shift.name : 'N/A'}</td>
                            <td>${d.vehicle ? d.vehicle.plate : 'N/A'}</td>
                            <td>${d.driver ? d.driver.names + ' ' + d.driver.lastnames : 'N/A'}</td>
                            <td><small>${helpers || 'Sin ayudantes'}</small></td>
                            <td class="text-center">${statusBadge}</td>
                            <td>
                                <button class="btn btn-xs btn-primary btn-change-status" data-id="${d.id}" data-status="${d.status}" title="Cambiar Estado"><i class="fas fa-sync"></i></button>
                            </td>
                        </tr>
                    `;
                        });
                    } else {
                        html =
                            '<tr><td colspan="7" class="text-center">No hay registros diarios para esta programación.</td></tr>';
                    }

                    $('#daily-tbody').html(html);
                    $('#modal-daily').modal('show');
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'No se pudo cargar el detalle diario.', 'error');
                });
            });

            $(document).on('click', '.btn-change-status', function() {
                let id = $(this).data('id');
                let currentStatus = $(this).data('status');

                Swal.fire({
                    title: 'Cambiar Estado',
                    input: 'select',
                    inputOptions: {
                        'pendiente': 'Pendiente',
                        'completado': 'Completado',
                        'cancelado': 'Cancelado'
                    },
                    inputValue: currentStatus,
                    showCancelButton: true,
                    confirmButtonText: 'Actualizar',
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.value) {
                        $.post(`/admin/schedules/daily/${id}/status`, {
                            _token: '{{ csrf_token() }}',
                            status: result.value
                        }, function() {
                            Swal.fire('Actualizado',
                                'El estado se ha actualizado correctamente.', 'success');
                            let activeId = $('#modal-daily').attr('data-active-id');
                            if (activeId) {
                                $('.btn-daily[data-id="' + activeId + '"]').click();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Cargando datos de edición...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.get(`/admin/schedules/${id}/edit`, function(response) {
                    Swal.close();
                    let s = response.schedule;
                    let eligibleDrivers = response.eligibleDrivers;
                    let eligibleHelpers = response.eligibleHelpers;
                    let currentDriver = response.current_driver;
                    let currentHelpers = response.current_helpers;

                    // Limpiar y configurar modal de edición
                    $('#edit_schedule_id').val(s.id);
                    $('#edit_reason').val('');
                    $('#edit_reason_id').val('');
                    $('#edit_reason_search').val('');
                    $('#edit_reason_description').val('');
                    $('#reason_results').addClass('d-none');
                    $('#clear_reason_search').hide();

                    // Visualización de Datos Actuales
                    $('#current_shift_display').text(response.current_shift_name || 'No definido');
                    $('#current_vehicle_display').text(response.current_vehicle_plate ||
                        'No definido');

                    // Configurar Combos de Cambio
                    $('#edit_shift_id').val(s.shift_id).trigger('change');
                    $('#edit_vehicle_id').val(s.vehicle_id).trigger('change');

                    // --- Lógica de CONDUCTORES ---
                    let driverOptions = '<option value="">Seleccione conductor...</option>';
                    let addedDriverIds = new Set();

                    // 1. Agregar explícitamente al Conductor Actual (Independientemente de filtros)
                    if (currentDriver) {
                        driverOptions +=
                            `<option value="${currentDriver.id}">${currentDriver.names} ${currentDriver.lastnames} (ACTUAL)</option>`;
                        addedDriverIds.add(currentDriver.id);
                    }

                    // 2. Agregar conductores elegibles (sin grupo, contrato ok, asistencia ok)
                    eligibleDrivers.forEach(p => {
                        if (!addedDriverIds.has(p.id)) {
                            driverOptions +=
                                `<option value="${p.id}">${p.names} ${p.lastnames}</option>`;
                            addedDriverIds.add(p.id);
                        }
                    });
                    $('#edit_driver_id').html(driverOptions).val(s.driver_id).trigger('change');

                    // --- Lógica de AYUDANTES ---
                    let helperOptions = '<option value="">Seleccione ayudante...</option>';
                    let addedHelperIds = new Set();

                    // 1. Agregar explícitamente a los Ayudantes Actuales (Independientemente de filtros)
                    if (currentHelpers && currentHelpers.length > 0) {
                        currentHelpers.forEach(h => {
                            if (!addedHelperIds.has(h.id)) {
                                helperOptions +=
                                    `<option value="${h.id}">${h.names} ${h.lastnames} (ACTUAL)</option>`;
                                addedHelperIds.add(h.id);
                            }
                        });
                    }

                    // 2. Agregar ayudantes elegibles (sin grupo, contrato ok, asistencia ok)
                    eligibleHelpers.forEach(p => {
                        if (!addedHelperIds.has(p.id)) {
                            helperOptions +=
                                `<option value="${p.id}">${p.names} ${p.lastnames}</option>`;
                            addedHelperIds.add(p.id);
                        }
                    });

                    $('#edit_helper_1').html(helperOptions);
                    $('#edit_helper_2').html(helperOptions);

                    if (s.helpers && s.helpers.length > 0) $('#edit_helper_1').val(s.helpers[0].id)
                        .trigger('change');
                    if (s.helpers && s.helpers.length > 1) $('#edit_helper_2').val(s.helpers[1].id)
                        .trigger('change');

                    // Mostrar días programados
                    let daysHtml = '';
                    if (s.workdays) {
                        s.workdays.forEach(wd => {
                            daysHtml +=
                                `<span class="badge badge-info m-1">${wd.day}</span>`;
                        });
                    }
                    $('#edit-workdays-container').html(daysHtml ||
                        '<span class="text-muted small">No definidos</span>');

                    $('#modal-edit-schedule').modal('show');
                }).fail(function() {
                    Swal.close();
                    Swal.fire('Error', 'No se pudieron cargar los datos para modificar.', 'error');
                });
            });

            $('#edit-schedule-form').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_schedule_id').val();

                $.ajax({
                    url: `/admin/schedules/${id}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function() {
                        $('#modal-edit-schedule').modal('hide');
                        table.ajax.reload();
                        Swal.fire(
                            'Actualizado',
                            'La programación fue modificada y el cambio quedó registrado en el historial.',
                            'success'
                        );
                    }
                });
            });

            // --- PROGRAMACION MASIVA ---
            let massPreviewData = [];

            $('#btn-programacion-masiva').on('click', function() {
                $('#mass-preview-tbody').empty();
                $('#previsualizacion-container').addClass('d-none');
                $('#btn-confirmar-masiva').addClass('d-none');
                $('#feriados-alerta').addClass('d-none');
                $('#modal-mass').modal('show');
            });

            $('#btn-previsualizar-masiva').on('click', function() {
                let start = $('#mass_start_date').val();
                let end = $('#mass_end_date').val();
                let shift = $('#mass_shift_id').val();

                if (!start || !end) {
                    Swal.fire('Atención', 'Seleccione un rango de fechas.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Analizando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post('{{ route('admin.schedules.preview-mass') }}', {
                    _token: '{{ csrf_token() }}',
                    start_date: start,
                    end_date: end,
                    shift_id: shift
                }, function(response) {
                    Swal.close();

                    if (response.holidays.length > 0) {
                        let fList = response.holidays.map(h =>
                            `${h.description} (${new Date(h.date + 'T00:00:00').toLocaleDateString()})`
                        ).join(', ');
                        $('#feriados-lista').text(fList);
                        $('#feriados-alerta').removeClass('d-none');
                    } else {
                        $('#feriados-alerta').addClass('d-none');
                    }

                    let html = '';
                    massPreviewData = response.preview;

                    response.preview.forEach((item, index) => {
                        let group = item.group;
                        let avail = item.availability;
                        let statusBadge = avail.valid ?
                            '<span class="badge badge-success"><i class="fas fa-check"></i> Válido</span>' :
                            '<span class="badge badge-danger"><i class="fas fa-times"></i> Conflicto</span>';
                        let alertClass = avail.valid ? '' : 'table-warning';
                        let messages = '';

                        if (avail.errors.length > 0) {
                            messages =
                                `<div class="text-danger small font-weight-bold mt-1">${avail.errors.map(e => `• ${e}`).join('<br>')}</div>`;
                        }

                        let personnelOptions = '<option value="">Seleccione...</option>';
                        response.personnels.forEach(p => {
                            personnelOptions +=
                                `<option value="${p.id}">${p.names} ${p.lastnames}</option>`;
                        });

                        html += `
                    <tr data-index="${index}" class="${alertClass}">
                        <td class="text-center align-middle">
                            <input type="checkbox" class="group-select-checkbox" data-index="${index}">
                        </td>
                        <td class="align-middle">
                            <div class="font-weight-bold">${group.name}</div>
                            <div class="text-muted small">${group.zone.name}</div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge badge-light border d-block mb-1">${group.shift.name}</span>
                            <span class="small text-muted"><i class="fas fa-truck"></i> ${group.vehicle.plate}</span>
                        </td>
                        <td>
                            <div class="input-group input-group-sm mb-1">
                                <div class="input-group-prepend"><span class="input-group-text bg-light border-0"><i class="fas fa-user-tie"></i></span></div>
                                <select class="form-control select2-mass driver-select" data-index="${index}">${personnelOptions}</select>
                            </div>
                            <div class="input-group input-group-sm mb-1">
                                <div class="input-group-prepend"><span class="input-group-text bg-light border-0"><i class="fas fa-user-friends"></i></span></div>
                                <select class="form-control select2-mass helper1-select" data-index="${index}">${personnelOptions}</select>
                            </div>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text bg-light border-0"><i class="fas fa-user-friends"></i></span></div>
                                <select class="form-control select2-mass helper2-select" data-index="${index}">${personnelOptions}</select>
                            </div>
                        </td>
                        <td class="align-middle">${statusBadge}${messages}</td>
                    </tr>
                `;
                    });

                    $('#mass-preview-tbody').html(html);
                    $('#mass-select-all').on('change', function() {
                        $('.group-select-checkbox').prop('checked', $(this).is(':checked'));
                    });
                    $('.select2-mass').select2({
                        theme: 'bootstrap4',
                        dropdownParent: $('#modal-mass')
                    });

                    response.preview.forEach((item, index) => {
                        let tr = $(`tr[data-index="${index}"]`);
                        tr.find('.driver-select').val(item.group.driver_id).trigger(
                            'change');
                        if (item.group.helpers[0]) tr.find('.helper1-select').val(item.group
                            .helpers[0].personnel_id).trigger('change');
                        if (item.group.helpers[1]) tr.find('.helper2-select').val(item.group
                            .helpers[1].personnel_id).trigger('change');
                    });

                    $('#previsualizacion-container').removeClass('d-none');
                    $('#btn-confirmar-masiva').removeClass('d-none');
                });
            });

            $('#btn-confirmar-masiva').on('click', function() {
                let groupsToStore = [];
                let hasErrors = false;
                let selectedCount = 0;

                $('#mass-preview-tbody tr').each(function() {
                    if ($(this).find('.group-select-checkbox').is(':checked')) {
                        selectedCount++;
                        let index = $(this).data('index');
                        let item = massPreviewData[index];
                        if ($(this).hasClass('table-warning')) hasErrors = true;

                        groupsToStore.push({
                            group_id: item.group.id,
                            zone_id: item.group.zone_id,
                            shift_id: item.group.shift_id,
                            vehicle_id: item.group.vehicle_id,
                            driver_id: $(this).find('.driver-select').val(),
                            helper_ids: [
                                $(this).find('.helper1-select').val(),
                                $(this).find('.helper2-select').val()
                            ].filter(id => id !== "")
                        });
                    }
                });

                if (selectedCount === 0) {
                    Swal.fire('Atención', 'Debe seleccionar al menos un grupo de la lista.', 'warning');
                    return;
                }

                if (hasErrors) {
                    Swal.fire({
                        title: '¿Continuar?',
                        text: 'Ha seleccionado grupos con avisos. ¿Desea registrarlos?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, registrar'
                    }).then((result) => {
                        if (result.value) executeMassStore(groupsToStore);
                    });
                } else {
                    executeMassStore(groupsToStore);
                }
            });

            function executeMassStore(groups) {
                Swal.fire({
                    title: 'Registrando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.post('{{ route('admin.schedules.store-mass') }}', {
                    _token: '{{ csrf_token() }}',
                    start_date: $('#mass_start_date').val(),
                    end_date: $('#mass_end_date').val(),
                    groups: groups
                }, function() {
                    Swal.close();
                    $('#modal-mass').modal('hide');
                    table.ajax.reload();
                    Swal.fire('¡Éxito!', 'Programaciones registradas.', 'success');
                });
            }

            function createPersonnelCard(id, name, type) {
                if (!id) return '';
                return `
            <div class="card card-outline card-secondary mb-2 shadow-sm animate__animated animate__fadeIn">
                <div class="card-body p-2">
                    <div class="row align-items-center">
                        <div class="col-auto"><div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;"><i class="fas fa-user text-white"></i></div></div>
                        <div class="col"><h6 class="mb-0 font-weight-bold">${name}</h6><small class="text-muted text-uppercase small">${type}</small></div>
                    </div>
                </div>
            </div>
        `;
            }

            function updatePersonnelCards(group) {
                let form = $('#schedule-form');

                form.find('#driver_id').val('');
                form.find('input[name="helper_ids[]"]').remove();

                form.find('#driver-card').html(
                    '<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>'
                );

                $('#helpers-cards-container').html(`
        <div class="col-md-12">
            <div class="p-3 border rounded bg-light text-muted small text-center">
                Sin ayudantes asignados
            </div>
        </div>
    `);

                if (!group) return;

                if (group.driver) {
                    form.find('#driver_id').val(group.driver.id);

                    form.find('#driver-card').html(
                        createPersonnelCard(
                            group.driver.id,
                            `${group.driver.names} ${group.driver.lastnames}`,
                            'Conductor'
                        )
                    );
                }

                if (group.helpers && group.helpers.length > 0) {
                    let helpersHtml = '';

                    group.helpers.forEach(function(detail, index) {
                        let helper = detail.personnel;

                        if (helper) {
                            form.append(`
                    <input type="hidden" name="helper_ids[]" value="${helper.id}">
                `);

                            helpersHtml += `
                    <div class="col-md-6">
                        ${createPersonnelCard(
                            helper.id,
                            `${helper.names} ${helper.lastnames}`,
                            `Ayudante ${index + 1}`
                        )}
                    </div>
                `;
                        }
                    });

                    $('#helpers-cards-container').html(helpersHtml);
                }
            }

            $('#btn-nueva-programacion').on('click', function() {
                $('#schedule-form')[0].reset();
                $('#schedule_id').val('');
                $('.select2').val('').trigger('change');
                $('.workday-checkbox').prop('checked', false);
                updatePersonnelCards(null);
                $('#btn-save').prop('disabled', true);
                $('#modalScheduleLabel').html('<i class="fas fa-calendar-plus"></i> Nueva Programación');
                $('#modal-schedule').modal('show');

                $('#group_search_input').val('');
                $('#personnel_group_id').val('');
                $('#group_results').addClass('d-none');
                $('#clear_group_search').hide();

                $('#zone_id_hidden').val('');
                $('#shift_id_hidden').val('');
                $('#vehicle_id_hidden').val('');
            });

            function cargarDatosGrupo(groupId) {
                if (!groupId || $('#schedule_id').val()) return;

                $.get("{{ route('admin.personnel-groups.index') }}/" + groupId, function(group) {
                    let form = $('#schedule-form');

                    form.find('#zone_id').val(group.zone_id).trigger('change');
                    form.find('#shift_id').val(group.shift_id).trigger('change');
                    form.find('#vehicle_id').val(group.vehicle_id).trigger('change');

                    $('#zone_id_hidden').val(group.zone_id);
                    $('#shift_id_hidden').val(group.shift_id);
                    $('#vehicle_id_hidden').val(group.vehicle_id);

                    form.find('.workday-checkbox').prop('checked', false);

                    if (group.workdays) {
                        group.workdays.forEach(wd => {
                            let shortDay = wd.day.substring(0, 2);

                            if (wd.day == 'Miércoles') shortDay = 'Mi';
                            if (wd.day == 'Sábado') shortDay = 'Sá';
                            if (wd.day == 'Domingo') shortDay = 'Do';

                            form.find(`.workday-checkbox[value="${shortDay}"]`).prop('checked',
                                true);
                        });
                    }

                    $('#btn-save').prop('disabled', true);
                    updatePersonnelCards(group);
                });
            }

            $('#group_search_input').on('keyup focus', function() {
                let value = $(this).val().toLowerCase();
                let hasResults = false;

                $('.group-option').each(function() {
                    let text = $(this).data('text').toLowerCase();

                    if (text.includes(value)) {
                        $(this).removeClass('d-none');
                        hasResults = true;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (hasResults) {
                    $('#group_results').removeClass('d-none');
                } else {
                    $('#group_results').addClass('d-none');
                }
            });

            $(document).on('click', '.group-option', function() {
                let groupId = $(this).data('id');
                let groupText = $(this).data('text');

                $('#personnel_group_id').val(groupId);
                $('#group_search_input').val(groupText);
                $('#group_results').addClass('d-none');
                $('#clear_group_search').show();

                cargarDatosGrupo(groupId);
            });

            $('#group_search_input').on('input', function() {
                $('#personnel_group_id').val('');

                $('#zone_id').val('').trigger('change');
                $('#shift_id').val('').trigger('change');
                $('#vehicle_id').val('').trigger('change');

                $('#zone_id_hidden').val('');
                $('#shift_id_hidden').val('');
                $('#vehicle_id_hidden').val('');

                $('.workday-checkbox').prop('checked', false);
                updatePersonnelCards(null);
                $('#btn-save').prop('disabled', true);

                if ($(this).val().trim() !== '') {
                    $('#clear_group_search').show();
                } else {
                    $('#clear_group_search').hide();
                }
            });

            $('#clear_group_search').on('click', function() {
                $('#personnel_group_id').val('');
                $('#group_search_input').val('');
                $('#group_results').addClass('d-none');
                $('#clear_group_search').hide();

                $('#zone_id').val('').trigger('change');
                $('#shift_id').val('').trigger('change');
                $('#vehicle_id').val('').trigger('change');

                $('#zone_id_hidden').val('');
                $('#shift_id_hidden').val('');
                $('#vehicle_id_hidden').val('');

                $('.workday-checkbox').prop('checked', false);
                updatePersonnelCards(null);
                $('#btn-save').prop('disabled', true);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#group_search_input, #group_results').length) {
                    $('#group_results').addClass('d-none');
                }
            });

            $('input, select').on('change', function() {
                $('#btn-save').prop('disabled', true);
            });

            $('#btn-validate').on('click', function() {
                let formData = $('#schedule-form').serialize();
                Swal.fire({
                    title: 'Validando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '{{ route('admin.schedules.validate-availability') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        if (!response.errors || response.errors.length === 0) {
                            Swal.fire('¡Éxito!', 'La programación es válida.', 'success');
                            $('#btn-save').prop('disabled', false);
                        } else {
                            Swal.fire('Conflicto', response.errors.join('<br>'), 'warning');
                            $('#btn-save').prop('disabled', true);
                        }
                    }
                });
            });

            $('#schedule-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('admin.schedules.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        $('#modal-schedule').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Guardado', 'Registrado con éxito.', 'success');
                    }
                });
            });

            $(document).on('click', '.btn-history', function() {

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/schedules/${id}/history`,
                    type: 'GET',

                    success: function(response) {
                        $('#history-content').html(response);
                        $('#modal-history').modal('show');
                    },

                    error: function() {
                        Swal.fire(
                            'Error',
                            'No se pudo cargar el historial de la programación.',
                            'error'
                        );
                    }
                });

            });

            $(document).on('click', '.btn-finish', function() {

                let id = $(this).data('id');

                Swal.fire({
                    title: '¿Finalizar programación?',
                    text: 'Esta acción marcará la programación como finalizada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {

                    if (result.value || result.isConfirmed) {

                        $.ajax({
                            url: `/admin/schedules/${id}/finish`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();

                                Swal.fire(
                                    'Proceso exitoso',
                                    response.message,
                                    'success'
                                );
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error',
                                    xhr.responseJSON?.message ??
                                    'No se pudo finalizar.',
                                    'error'
                                );
                            }
                        });

                    }

                });

            });

            $(document).on('click', '.btn-finish-daily', function() {

                let id = $(this).data('id');

                Swal.fire({
                    title: '¿Finalizar este día?',
                    text: 'Solo se marcará como completada esta programación diaria.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {

                    if (result.isConfirmed || result.value) {

                        $.ajax({
                            url: `/admin/schedules/daily/${id}/finish`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);

                                Swal.fire(
                                    'Proceso exitoso',
                                    response.message,
                                    'success'
                                );
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error',
                                    xhr.responseJSON?.message ??
                                    'No se pudo finalizar.',
                                    'error'
                                );
                            }
                        });

                    }

                });

            });

            $(document).on('click', '.btn-delete-daily', function() {

                let id = $(this).data('id');

                Swal.fire({
                    title: '¿Eliminar programación?',
                    text: 'Se eliminará solo esta programación diaria.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {

                    if (result.isConfirmed || result.value) {

                        $.ajax({
                            url: `/admin/schedules/daily/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload(null, false);

                                Swal.fire(
                                    'Proceso exitoso',
                                    response.message,
                                    'success'
                                );
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error',
                                    xhr.responseJSON?.message ??
                                    'No se pudo eliminar.',
                                    'error'
                                );
                            }
                        });

                    }

                });

            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `{{ route('admin.schedules.index') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                table.ajax.reload();
                                Swal.fire('Eliminado',
                                    'La programación ha sido eliminada.', 'success');
                            }
                        });
                    }
                });
            });

            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // ===============================
            // VALIDACIÓN FECHAS PROGRAMACIÓN
            // ===============================
            function actualizarFechaFin() {
                let inicio = $('#start_date').val();

                if (inicio) {
                    $('#end_date').attr('min', inicio);

                    if ($('#end_date').val() && $('#end_date').val() < inicio) {
                        $('#end_date').val('');
                    }
                } else {
                    $('#end_date').removeAttr('min');
                }
            }

            $('#start_date').on('change input', actualizarFechaFin);
            actualizarFechaFin();


            // ===============================
            // VALIDACIÓN FECHAS MASIVAS
            // ===============================
            function actualizarFechaFinMasiva() {
                let inicio = $('#mass_start_date').val();

                if (inicio) {
                    $('#mass_end_date').attr('min', inicio);

                    if ($('#mass_end_date').val() && $('#mass_end_date').val() < inicio) {
                        $('#mass_end_date').val('');
                    }
                } else {
                    $('#mass_end_date').removeAttr('min');
                }
            }

            $('#mass_start_date').on('change input', actualizarFechaFinMasiva);
            actualizarFechaFinMasiva();

            // ===============================
            // BUSCADOR TURNO MASIVO
            // ===============================
            $('#mass_shift_search_input').on('keyup focus', function() {
                let value = $(this).val().toLowerCase();
                let hasResults = false;

                $('.mass-shift-option').each(function() {
                    let text = $(this).data('text').toLowerCase();

                    if (text.includes(value)) {
                        $(this).removeClass('d-none');
                        hasResults = true;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                if (hasResults) {
                    $('#mass_shift_results').removeClass('d-none');
                } else {
                    $('#mass_shift_results').addClass('d-none');
                }
            });

            $(document).on('click', '.mass-shift-option', function() {
                $('#mass_shift_id').val($(this).data('id'));
                $('#mass_shift_search_input').val($(this).data('text'));
                $('#mass_shift_results').addClass('d-none');
                $('#clear_mass_shift_search').show();
            });

            $('#mass_shift_search_input').on('input', function() {
                $('#mass_shift_id').val('');

                if ($(this).val().trim() !== '') {
                    $('#clear_mass_shift_search').show();
                } else {
                    $('#clear_mass_shift_search').hide();
                }
            });

            $('#clear_mass_shift_search').on('click', function() {
                $('#mass_shift_id').val('');
                $('#mass_shift_search_input').val('');
                $('#mass_shift_results').addClass('d-none');
                $('#clear_mass_shift_search').hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#mass_shift_search_input, #mass_shift_results').length) {
                    $('#mass_shift_results').addClass('d-none');
                }
            });

        });
    </script>
@stop
