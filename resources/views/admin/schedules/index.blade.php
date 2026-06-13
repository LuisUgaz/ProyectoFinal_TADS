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
            <button class="btn btn-info btn-sm">
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
                        <th>Grupo</th>
                        <th>Zona</th>
                        <th>Turno</th>
                        <th>Vehículo</th>
                        <th>Conductor</th>
                        <th>Ayudantes</th>
                        <th>Periodo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Nueva Programación -->
<div class="modal fade" id="modal-schedule" tabindex="-1" role="dialog" aria-labelledby="modalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalScheduleLabel"><i class="fas fa-calendar-plus"></i> Nueva Programación</h5>
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
                                <label>Grupo de Personal (Plantilla)</label>
                                <select name="personnel_group_id" id="personnel_group_id" class="form-control select2" style="width: 100%" required>
                                    <option value="">Seleccione un grupo...</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Zona</label>
                                <select name="zone_id" id="zone_id" class="form-control select2" style="width: 100%">
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Turno</label>
                                <select name="shift_id" id="shift_id" class="form-control select2" style="width: 100%">
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Vehículo</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-control select2" style="width: 100%">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Días de la Semana</label>
                                <div class="d-flex flex-wrap border p-1 rounded bg-white">
                                    @foreach(['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá', 'Do'] as $day)
                                        <div class="custom-control custom-checkbox mr-2">
                                            <input class="custom-control-input workday-checkbox" type="checkbox" name="workdays[]" id="day_{{ $day }}" value="{{ $day }}">
                                            <label for="day_{{ $day }}" class="custom-control-label font-weight-normal">{{ $day }}</label>
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
                        <div class="col-md-4 text-center">
                            <label class="font-weight-bold text-muted small text-uppercase">Ayudante 1</label>
                            <input type="hidden" name="helper_ids[]" id="helper_id_1">
                            <div id="helper-card-1">
                                <div class="p-3 border rounded bg-light text-muted small">Sin asignar</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="font-weight-bold text-muted small text-uppercase">Ayudante 2</label>
                            <input type="hidden" name="helper_ids[]" id="helper_id_2">
                            <div id="helper-card-2">
                                <div class="p-3 border rounded bg-light text-muted small">Sin asignar</div>
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
@stop

@section('js')
<script>
$(function() {
    let table = $('#schedules-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("admin.schedules.index") }}',
        columns: [
            { data: 'group_name', name: 'group_name' },
            { data: 'zone_name', name: 'zone_name' },
            { data: 'shift_name', name: 'shift_name' },
            { data: 'vehicle_plate', name: 'vehicle_plate' },
            { data: 'driver_name', name: 'driver_name' },
            { data: 'helpers_names', name: 'helpers_names' },
            { data: 'date_range', name: 'date_range' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        }
    });

    $('.select2').select2({ theme: 'bootstrap4' });

    function createPersonnelCard(id, name, type) {
        if (!id) return '';
        return `
            <div class="card card-outline card-secondary mb-2 shadow-sm animate__animated animate__fadeIn">
                <div class="card-body p-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="mb-0 font-weight-bold">${name}</h6>
                            <small class="text-muted text-uppercase small">${type}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function updatePersonnelCards(group) {
        if (!group) {
            $('#driver-card').html('<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>');
            $('#helper-card-1').html('<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>');
            $('#helper-card-2').html('<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>');
            return;
        }

        // Conductor
        if (group.driver) {
            $('#driver_id').val(group.driver.id);
            $('#driver-card').html(createPersonnelCard(group.driver.id, `${group.driver.names} ${group.driver.lastnames}`, 'Conductor'));
        }

        // Ayudante 1
        if (group.helpers && group.helpers.length > 0) {
            let h1 = group.helpers[0].personnel;
            $('#helper_id_1').val(h1.id);
            $('#helper-card-1').html(createPersonnelCard(h1.id, `${h1.names} ${h1.lastnames}`, 'Ayudante 1'));
        } else {
            $('#helper-card-1').html('<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>');
        }

        // Ayudante 2
        if (group.helpers && group.helpers.length > 1) {
            let h2 = group.helpers[1].personnel;
            $('#helper_id_2').val(h2.id);
            $('#helper-card-2').html(createPersonnelCard(h2.id, `${h2.names} ${h2.lastnames}`, 'Ayudante 2'));
        } else {
            $('#helper-card-2').html('<div class="p-3 border rounded bg-light text-muted small text-center">Sin asignar</div>');
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
    });

    $('#personnel_group_id').on('change', function() {
        let groupId = $(this).val();
        if (!groupId || $('#schedule_id').val()) return;

        $.get(`/admin/personnel-groups/${groupId}`, function(group) {
            $('#zone_id').val(group.zone_id).trigger('change');
            $('#shift_id').val(group.shift_id).trigger('change');
            $('#vehicle_id').val(group.vehicle_id).trigger('change');

            $('.workday-checkbox').prop('checked', false);
            group.workdays.forEach(wd => {
                let shortDay = wd.day.substring(0, 2);
                if (wd.day == 'Miércoles') shortDay = 'Mi';
                if (wd.day == 'Sábado') shortDay = 'Sá';
                if (wd.day == 'Domingo') shortDay = 'Do';
                $(`.workday-checkbox[value="${shortDay}"]`).prop('checked', true);
            });

            $('#btn-save').prop('disabled', true);
            updatePersonnelCards(group);
        });
    });

    $('input, select').on('change', function() {
        $('#btn-save').prop('disabled', true);
    });

    $('#btn-validate').on('click', function() {
        let formData = $('#schedule-form').serialize();
        
        Swal.fire({
            title: 'Validando...',
            text: 'Espere un momento mientras verificamos la disponibilidad.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '{{ route("admin.schedules.validate-availability") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                Swal.close();
                
                let hasErrors = response.errors && response.errors.length > 0;
                let hasWarnings = response.warnings && response.warnings.length > 0;

                if (!hasErrors) {
                    let message = 'No se encontraron conflictos bloqueantes. Ya puede guardar.';
                    let icon = 'success';
                    let html = '';

                    if (hasWarnings) {
                        icon = 'info';
                        message = 'La programación es válida, pero tenga en cuenta lo siguiente:';
                        html = '<ul class="text-left">';
                        response.warnings.forEach(item => {
                            html += `<li>${item}</li>`;
                        });
                        html += '</ul>';
                    }

                    Swal.fire({
                        title: hasWarnings ? 'Aviso' : '¡Éxito!',
                        text: hasWarnings ? undefined : message,
                        html: html || undefined,
                        icon: icon
                    });
                    
                    $('#btn-save').prop('disabled', false);
                } else {
                    let list = '<ul class="text-left">';
                    response.errors.forEach(item => {
                        list += `<li class="text-danger"><strong>ERROR:</strong> ${item}</li>`;
                    });
                    if (hasWarnings) {
                        response.warnings.forEach(item => {
                            list += `<li class="text-muted"><strong>AVISO:</strong> ${item}</li>`;
                        });
                    }
                    list += '</ul>';
                    
                    Swal.fire({
                        title: 'Conflictos Detectados',
                        html: list,
                        icon: 'warning'
                    });
                    $('#btn-save').prop('disabled', true);
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'Hubo un problema al validar la disponibilidad.', 'error');
            }
        });
    });

    $('#schedule-form').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let id = $('#schedule_id').val();
        let url = id ? `/admin/schedules/${id}` : '{{ route("admin.schedules.store") }}';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#modal-schedule').modal('hide');
                table.ajax.reload();
                Swal.fire('Guardado', 'La programación se ha registrado correctamente.', 'success');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let message = 'Hubo un error al guardar.';
                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $('#schedule-form')[0].reset();
        $('#schedule_id').val(id);
        
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.get(`/admin/schedules/${id}`, function(schedule) {
            Swal.close();
            $('#modalScheduleLabel').html('<i class="fas fa-edit"></i> Modificar Programación');
            
            $('#personnel_group_id').val(schedule.personnel_group_id).trigger('change');
            $('#start_date').val(schedule.start_date.split('T')[0]);
            $('#end_date').val(schedule.end_date.split('T')[0]);
            $('#zone_id').val(schedule.zone_id).trigger('change');
            $('#shift_id').val(schedule.shift_id).trigger('change');
            $('#vehicle_id').val(schedule.vehicle_id).trigger('change');
            $('#driver_id').val(schedule.driver_id).trigger('change');
            $('#notes').val(schedule.notes);

            let helperIds = schedule.helpers.map(h => h.id);
            $('#helper_ids').val(helperIds).trigger('change');

            $('.workday-checkbox').prop('checked', false);
            schedule.workdays.forEach(wd => {
                // Mapeo robusto de nombre completo a abreviatura
                let dayMap = {
                    'Lunes': 'Lu',
                    'Martes': 'Ma',
                    'Miércoles': 'Mi',
                    'Jueves': 'Ju',
                    'Viernes': 'Vi',
                    'Sábado': 'Sá',
                    'Domingo': 'Do'
                };
                let valToCheck = dayMap[wd.day] || wd.day;
                $(`.workday-checkbox[value="${valToCheck}"]`).prop('checked', true);
            });

            $('#btn-save').prop('disabled', false); // Permitir guardar en edición
            $('#modal-schedule').modal('show');
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let deleteUrl = '{{ route("admin.schedules.index") }}/' + id;
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            type: 'warning', // v8 usa 'type' en lugar de 'icon'
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) { // v8 usa .value en lugar de .isConfirmed
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: { 
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Respuesta Exitosa:', response);
                        table.ajax.reload();
                        Swal.fire('Eliminado', 'La programación ha sido eliminada.', 'success');
                    },
                    error: function(xhr) {
                        console.error('Error Completo:', xhr);
                        let errorMsg = 'Error desconocido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMsg = xhr.responseText.substring(0, 100);
                        }
                        Swal.fire('Error del Servidor', errorMsg, 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-history', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Cargando Historial...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.get(`/admin/schedules/${id}`, function(schedule) {
            Swal.close();
            // Por ahora mostramos los detalles básicos como "historial"
            let content = `
                <div class="text-left">
                    <p><strong>ID:</strong> ${schedule.id}</p>
                    <p><strong>Fecha de Creación:</strong> ${new Date(schedule.created_at).toLocaleString()}</p>
                    <p><strong>Última Actualización:</strong> ${new Date(schedule.updated_at).toLocaleString()}</p>
                    <p><strong>Estado:</strong> ${schedule.status}</p>
                    <p><strong>Notas:</strong> ${schedule.notes || 'Sin notas'}</p>
                </div>
            `;
            Swal.fire({
                title: 'Detalles de la Programación',
                html: content,
                icon: 'info'
            });
        });
    });
});
</script>
@stop
