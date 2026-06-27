@extends('adminlte::page')

@section('title', 'Cambios')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content')

    <div class="pt-3"></div>

    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary btn-sm float-right" id="btn-nuevo-masivo">
                <i class="fas fa-exchange-alt"></i>
                Nuevo Cambio Masivo
            </button>

            <h4>
                <i class="fas fa-history"></i>
                Cambios de Programaciones
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body border-bottom">
            <div class="row align-items-end">

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Fecha de inicio</label>
                        <input type="date" id="start_date" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Fecha de fin</label>
                        <input type="date" id="end_date" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Tipo de cambio</label>
                        <select id="change_type" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="Turno">Turno</option>
                            <option value="Vehículo">Vehículo</option>
                            <option value="Conductor">Conductor</option>
                            <option value="Ayudantes">Ayudantes</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="d-none d-md-block">&nbsp;</label>

                        <div class="d-flex">
                            <button type="button" id="btn-filtrar" class="btn btn-primary btn-sm flex-fill mr-1">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>

                            <button type="button" id="btn-limpiar" class="btn btn-secondary btn-sm flex-fill ml-1">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm text-nowrap" id="datatable">
                <thead>
                    <tr>
                        <th>Tipo de cambio</th>
                        <th>Fecha cambio</th>
                        <th>Antes</th>
                        <th>Después</th>
                        <th>Realizado por</th>
                        <th>Programación</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="ShowModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Detalle de Cambio
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body"></div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="MassModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exchange-alt"></i>
                        Nuevo Cambio Masivo
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body"></div>

            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        let table;

        $(document).ready(function() {

            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.changes.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.change_type = $('#change_type').val();
                    }
                },
                columns: [{
                        data: 'type_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date_format'
                    },
                    {
                        data: 'previous_value_format'
                    },
                    {
                        data: 'new_value_format'
                    },
                    {
                        data: 'user_name'
                    },
                    {
                        data: 'schedule_info'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
                }
            });

            $('#btn-filtrar').click(function() {
                table.ajax.reload(null, false);
            });

            $('#btn-limpiar').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#change_type').val('');

                table.ajax.reload(null, false);
            });

        });

        $(document).on('click', '.btn-show', function() {

            let id = $(this).attr('id');

            $.ajax({
                url: "{{ route('admin.changes.show', 'id') }}".replace('id', id),
                type: "GET",

                success: function(response) {
                    $('#ShowModal .modal-title').html(
                        '<i class="fas fa-eye"></i> Detalle del Cambio'
                    );

                    $('#ShowModal .modal-body').html(response);

                    $('#ShowModal').modal('show');
                },

                error: function() {
                    Swal.fire(
                        'Error',
                        'No se pudo cargar el detalle del cambio.',
                        'error'
                    );
                }
            });
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let url = $(this).data('url');

            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción eliminará el registro del historial.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed || result.value) {

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
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
                                xhr.responseJSON?.message ?? 'No se pudo eliminar.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $('#btn-nuevo-masivo').click(function() {
            $.get("{{ route('admin.changes.mass.create') }}", function(response) {
                $('#MassModal .modal-body').html(response);
                $('#MassModal').modal('show');
            });
        });

        $(document).on('change', '#mass_change_type', function() {
            let type = $(this).val();

            $('.mass-option').addClass('d-none');
            $('.previous-select').removeAttr('name');
            $('.new-select').removeAttr('name');

            if (type === 'Turno') {
                $('#option-turno-old, #option-turno-new').removeClass('d-none');
                $('#option-turno-old .previous-select').attr('name', 'previous_value_id');
                $('#option-turno-new .new-select').attr('name', 'new_value_id');
            }

            if (type === 'Vehículo') {
                $('#option-vehicle-old, #option-vehicle-new').removeClass('d-none');
                $('#option-vehicle-old .previous-select').attr('name', 'previous_value_id');
                $('#option-vehicle-new .new-select').attr('name', 'new_value_id');
            }

            if (type === 'Conductor') {
                $('#option-driver-old, #option-driver-new').removeClass('d-none');
                $('#option-driver-old .previous-select').attr('name', 'previous_value_id');
                $('#option-driver-new .new-select').attr('name', 'new_value_id');
            }

            if (type === 'Ayudantes') {
                $('#option-helper-old, #option-helper-new').removeClass('d-none');
                $('#option-helper-old .previous-select').attr('name', 'previous_value_id');
                $('#option-helper-new .new-select').attr('name', 'new_value_id');
            }
        });

        $(document).on('change', '#mass_reason_id', function() {
            let description = $(this).find(':selected').data('description') || '';
            $('#mass_reason_description').val(description);
        });

        $(document).on('submit', '#mass-change-form', function(e) {
            e.preventDefault();

            let form = $(this);

            Swal.fire({
                title: '¿Aplicar cambio masivo?',
                text: 'Esta operación afectará a todas las programaciones coincidentes y no se recomienda revertirla manualmente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, aplicar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed || result.value) {

                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),

                        success: function(response) {
                            $('#MassModal').modal('hide');
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
                                'No se pudo aplicar el cambio masivo.',
                                'error'
                            );
                        }
                    });

                }

            });
        });
    </script>
@stop

@section('css')
    <style>
        #ShowModal .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }
    </style>
@stop
