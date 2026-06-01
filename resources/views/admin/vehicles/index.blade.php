@extends('adminlte::page')

@section('title', 'Vehículos')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content')
<div class="pt-3"></div>

<div class="card">
    <div class="card-header">
        <button type="button" class="btn btn-primary btn-sm float-right" id="btn-nuevo">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </button>
        <h4><i class="fas fa-truck"></i> Lista de Vehículos</h4>
    </div>

    <div class="card-body">
        <table class="table table-striped table-hover" id="datatable">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Placa</th>
                    <th>Marca y Modelo</th>
                    <th>Tipo</th>
                    <th>Color</th>
                    <th>Año</th>
                    <th>Estado</th>
                    <th width="20">Editar</th>
                    <th width="20">Eliminar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="FormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Formulario de Vehículo</h5>
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
        ajax: "{{ route('admin.vehicles.index') }}",
        columns: [
            { data: "code", defaultContent: "N/A" },
            { data: "plate" },
            { data: "full_model" },
            { data: "type_name" },
            { data: "color_info" },
            { data: "year" },
            { data: "status" },
            { data: "edit", orderable: false, searchable: false },
            { data: "delete", orderable: false, searchable: false },
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json',
        },
    });
});

$('#btn-nuevo').click(function() {
    $.ajax({
        url: "{{ route('admin.vehicles.create') }}",
        type: "GET",
        success: function(response) {
            $('#FormModal .modal-title').html('<i class="fas fa-truck"></i> Nuevo Vehículo');
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
        url: "{{ route('admin.vehicles.edit', 'id') }}".replace('id', id),
        type: "GET",
        success: function(response) {
            $('#FormModal .modal-title').html('<i class="fas fa-pen"></i> Modificar Vehículo');
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
            Swal.fire('Proceso exitoso', response.message, 'success');
        },
        error: function(xhr) {
            let response = xhr.responseJSON;
            Swal.fire('Ocurrió un error', response.message, 'error');
        }
    });
}

$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    let url = $(this).data('url');

    Swal.fire({
        title: "¿Está seguro de eliminar?",
        text: "Esta acción es irreversible",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
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
                    Swal.fire('Proceso exitoso', response.message, 'success');
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    Swal.fire('Ocurrió un error', response ? response.message : 'No se pudo eliminar', 'error');
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
