@extends('adminlte::page')

@section('title', 'Grupos de Personal')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content')

    <div class="pt-3"></div>

    <div class="card">

        <div class="card-header">
            <button type="button" class="btn btn-primary btn-sm float-right" id="btn-nuevo">
                <i class="fas fa-plus"></i>
                Nuevo Grupo
            </button>

            <h4>
                <i class="fas fa-users"></i>
                Lista de Grupos de Personal
            </h4>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm" id="datatable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Zona</th>
                        <th>Turno</th>
                        <th>Vehículo</th>
                        <th>Conductor</th>
                        <th>Ayudantes</th>
                        <th>Días de Trabajo</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

    <div class="modal fade" id="FormModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Formulario de Grupo</h5>

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
        $(document).ready(function() {

            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: false,
                autoWidth: false,
                ajax: "{{ route('admin.personnel-groups.index') }}",

                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'zone'
                    },
                    {
                        data: 'shift'
                    },
                    {
                        data: 'vehicle'
                    },
                    {
                        data: 'driver'
                    },
                    {
                        data: 'helpers',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'workdays'
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at_format'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],

                columnDefs: [{
                        width: '70px',
                        targets: 0
                    },
                    {
                        width: '60px',
                        targets: 1
                    },
                    {
                        width: '70px',
                        targets: 2
                    },
                    {
                        width: '70px',
                        targets: 3
                    },
                    {
                        width: '170px',
                        targets: 4
                    },
                    {
                        width: '210px',
                        targets: 5
                    },
                    {
                        width: '90px',
                        targets: 6
                    },
                    {
                        width: '75px',
                        targets: 7
                    },
                    {
                        width: '65px',
                        targets: 8
                    },
                    {
                        width: '75px',
                        targets: 9
                    }
                ],

                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
                }
            });

        });

        $('#btn-nuevo').click(function() {
            $.ajax({
                url: "{{ route('admin.personnel-groups.create') }}",
                type: "GET",

                success: function(response) {
                    $('#FormModal .modal-title').html(
                        '<i class="fas fa-users"></i> Nuevo Grupo de Personal'
                    );

                    $('#FormModal .modal-body').html(response);
                    $('#FormModal').modal("show");

                    $('#FormModal form').on("submit", function(e) {
                        e.preventDefault();
                        enviarFormulario(this);
                    });
                }
            });
        });

        $(document).on('click', '.btn-editar', function() {

            let id = $(this).attr("id");

            $.ajax({
                url: "{{ route('admin.personnel-groups.edit', 'id') }}".replace('id', id),
                type: "GET",

                success: function(response) {
                    $('#FormModal .modal-title').html(
                        '<i class="fas fa-pen"></i> Modificar Grupo'
                    );

                    $('#FormModal .modal-body').html(response);
                    $('#FormModal').modal("show");

                    $('#FormModal form').on("submit", function(e) {
                        e.preventDefault();
                        enviarFormulario(this);
                    });
                }
            });
        });

        function enviarFormulario(formulario) {

            let form = $(formulario);
            let formData = new FormData(formulario);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,

                success: function(response) {
                    $('#FormModal').modal("hide");
                    refreshTable();

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
                        xhr.responseText ??
                        'Error interno del servidor',
                        'error'
                    );
                }
            });
        }

        $(document).on('click', '.btn-delete', function(e) {

            e.preventDefault();

            let url = $(this).data('url');

            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción es irreversible',
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
                            refreshTable();

                            Swal.fire(
                                'Proceso exitoso',
                                response.message,
                                'success'
                            );
                        },

                        error: function(xhr) {
                            Swal.fire(
                                'Error',
                                xhr.responseJSON.message,
                                'error'
                            );
                        }
                    });
                }
            });
        });

        function refreshTable() {
            $('#datatable').DataTable().ajax.reload(null, false);
        }
    </script>
@stop

@section('css')
    <style>
        #datatable {
            width: 100% !important;
            table-layout: fixed;
            font-size: 13px;
        }

        #datatable th,
        #datatable td {
            vertical-align: middle !important;
            white-space: normal !important;
            word-wrap: break-word;
        }

        #datatable th {
            text-align: center;
        }

        #datatable td:nth-child(1),
        #datatable td:nth-child(2),
        #datatable td:nth-child(3),
        #datatable td:nth-child(4),
        #datatable td:nth-child(7),
        #datatable td:nth-child(8),
        #datatable td:nth-child(9),
        #datatable td:nth-child(10) {
            text-align: center;
        }

        #datatable td:nth-child(5),
        #datatable th:nth-child(5) {
            width: 170px;
            min-width: 170px;
            max-width: 170px;
            white-space: nowrap !important;
            text-align: left;
        }

        /* Ayudantes */
        #datatable td:nth-child(6) {
            width: 210px;
            max-width: 210px;
            text-align: center;
        }

        .helper-item {
            padding: 2px 0;
            line-height: 1.35;
            border-bottom: 1px dashed #e0e0e0;
        }

        .helper-item:last-child {
            border-bottom: none;
        }

        /* Días de trabajo */
        #datatable td:nth-child(7) {
            width: 90px;
            max-width: 90px;
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(2, 34px);
            justify-content: center;
            gap: 2px;
        }

        .day-badge {
            display: inline-block;
            width: 34px;
            padding: 2px 0;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            border-radius: 4px;
        }

        #datatable td:nth-child(9),
        #datatable th:nth-child(9) {
            width: 65px;
            min-width: 65px;
            max-width: 65px;
            text-align: center;
            line-height: 1.3;
        }

        /* Acciones */
        #datatable td:nth-child(10) {
            width: 75px;
            max-width: 75px;
            white-space: nowrap !important;
        }
    </style>
@stop
