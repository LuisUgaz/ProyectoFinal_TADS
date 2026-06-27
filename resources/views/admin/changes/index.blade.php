@extends('adminlte::page')

@section('title', 'Cambios')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content')

    <div class="pt-3"></div>

    <div class="card">
        <div class="card-header">
            <h4>
                <i class="fas fa-history"></i>
                Historial de Cambios
            </h4>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm text-nowrap" id="datatable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Programación</th>
                        <th>Zona</th>
                        <th>Motivo</th>
                        <th>Registrado por</th>
                        <th width="80">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="ShowModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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

@stop

@section('js')
    <script>
        $(document).ready(function() {

            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                order: [
                    [0, 'desc']
                ],
                ajax: "{{ route('admin.changes.index') }}",
                columns: [{
                        data: 'date_format'
                    },
                    {
                        data: 'schedule_info'
                    },
                    {
                        data: 'zone_name'
                    },
                    {
                        data: 'reason_name'
                    },
                    {
                        data: 'user_name'
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
    </script>
@stop
