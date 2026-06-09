@extends('adminlte::page')

@section('title', 'Zonas')

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('content')

    <div class="pt-3"></div>

    <div class="card">
        <div class="card-header">
            <button type="button" class="btn btn-primary btn-sm float-right" id="btnNuevo">
                <i class="fas fa-plus"></i> Nueva Zona
            </button>

            <h4>
                <i class="fas fa-map-marker-alt"></i>
                Lista de Zonas
            </h4>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm text-nowrap" id="datatable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Distrito</th>
                        <th>Provincia</th>
                        <th>Departamento</th>
                        <th>Descripción</th>
                        <th>Coordenadas</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Formulario de Zona</h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        let table;

        $(document).ready(function() {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                order: [
                    [0, 'asc']
                ],
                ajax: "{{ route('admin.zones.index') }}",
                columns: [{
                        data: "name",
                        name: "zones.name"
                    },
                    {
                        data: "district",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "province",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "department",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "description",
                        name: "zones.description"
                    },
                    {
                        data: "coordinates_status",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "status_label",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "created_at_formatted",
                        name: "zones.created_at"
                    },
                    {
                        data: "actions",
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });

        $('#btnNuevo').on('click', function() {
            $.get("{{ route('admin.zones.create') }}", function(response) {
                $('#modalTitle').html('Nueva Zona');
                $('#formModal .modal-body').html(response);
                $('#formModal').modal('show');
            });
        });

        $(document).on('click', '.btn-editar', function() {
            let id = $(this).attr('id');

            $.get("{{ route('admin.zones.edit', ':id') }}".replace(':id', id), function(response) {
                $('#modalTitle').html('Editar Zona');
                $('#formModal .modal-body').html(response);
                $('#formModal').modal('show');
            });
        });

        $('#formModal').on('shown.bs.modal', function() {
            if ($('#zoneMap').length) {
                initZoneLeafletMap();
            }
        });

        $(document).on('click', '.btn-ver-mapa', function() {
            let id = $(this).attr('id');

            $.get("{{ route('admin.zones.show', ':id') }}".replace(':id', id), function(response) {
                $('#modalTitle').html('Mapa de la Zona');
                $('#formModal .modal-body').html(response);
                $('#formModal').modal('show');
            });
        });

        window.saveZone = function() {
            let form = $('#zoneForm');
            let coordinates = $('#coordinates').val();

            if (!coordinates || coordinates === 'null' || coordinates === '[]') {
                Swal.fire('Error', 'Debe dibujar y cerrar el perímetro antes de guardar.', 'error');
                return;
            }

            let formData = new FormData(form[0]);
            formData.set('coordinates', coordinates);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#formModal').modal('hide');
                    $('#datatable').DataTable().ajax.reload(null, false);
                    Swal.fire('Correcto', response.message, 'success');
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        Swal.fire('Error', errors, 'error');
                    } else {
                        Swal.fire('Error', xhr.responseJSON?.message ?? 'No se pudo guardar.', 'error');
                    }
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
                            $('#datatable').DataTable().ajax.reload(null, false);

                            Swal.fire(
                                'Proceso exitoso',
                                response.message,
                                'success'
                            );
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON;

                            Swal.fire(
                                'Ocurrió un error',
                                response ? response.message : 'No se pudo eliminar',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        function initZoneLeafletMap() {
            if (window.zoneLeafletMap) {
                window.zoneLeafletMap.remove();
                window.zoneLeafletMap = null;
            }

            window.zoneLeafletMap = L.map('zoneMap').setView([-6.7630, -79.8366], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(window.zoneLeafletMap);

            let drawnItems = new L.FeatureGroup();
            window.zoneLeafletMap.addLayer(drawnItems);

            let polygonDrawer = new L.Draw.Polygon(window.zoneLeafletMap, {
                allowIntersection: false,
                showArea: true,
                shapeOptions: {
                    color: 'red'
                }
            });

            let drawControl = new L.Control.Draw({
                draw: {
                    polygon: {
                        allowIntersection: false,
                        showArea: true,
                        shapeOptions: {
                            color: 'red'
                        }
                    },
                    marker: false,
                    circle: false,
                    rectangle: false,
                    polyline: false,
                    circlemarker: false
                },
                edit: {
                    featureGroup: drawnItems,
                    remove: true
                }
            });

            window.zoneLeafletMap.addControl(drawControl);

            let savedCoordinates = $('#coordinates').val();

            if (savedCoordinates && savedCoordinates !== 'null') {
                try {
                    let coords = JSON.parse(savedCoordinates);

                    if (coords.length > 0) {
                        let latlngs = coords.map(c => [c.lat, c.lng]);
                        let polygon = L.polygon(latlngs, {
                            color: 'red'
                        });
                        drawnItems.addLayer(polygon);
                        window.zoneLeafletMap.fitBounds(polygon.getBounds());
                    }
                } catch (e) {}
            }

            $('#btnDrawPolygon').off('click').on('click', function() {
                polygonDrawer.enable();
            });

            $('#btnClearPolygon').off('click').on('click', function() {
                drawnItems.clearLayers();
                $('#coordinates').val('');
            });

            window.zoneLeafletMap.on(L.Draw.Event.CREATED, function(event) {
                drawnItems.clearLayers();
                let layer = event.layer;
                drawnItems.addLayer(layer);
                saveCoordinates(layer);
            });

            window.zoneLeafletMap.on(L.Draw.Event.EDITED, function(event) {
                event.layers.eachLayer(function(layer) {
                    saveCoordinates(layer);
                });
            });

            window.zoneLeafletMap.on(L.Draw.Event.DELETED, function() {
                $('#coordinates').val('');
            });

            function saveCoordinates(layer) {
                let latlngs = layer.getLatLngs()[0];

                let coordinates = latlngs.map(function(point) {
                    return {
                        lat: point.lat,
                        lng: point.lng
                    };
                });

                $('#coordinates').val(JSON.stringify(coordinates));
            }

            setTimeout(function() {
                window.zoneLeafletMap.invalidateSize();
            }, 300);
        }
    </script>
@endsection
