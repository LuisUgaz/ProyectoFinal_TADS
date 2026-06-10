<div class="row">
    <div class="col-md-4">

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt"></i> {{ $zone->name }}
                </h5>
            </div>

            <div class="card-body">

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2 bg-light">
                            <i class="fas fa-map-pin text-primary"></i>
                            <h5 class="mb-0 mt-1">
                                {{ is_array($zone->coordinates) ? count($zone->coordinates) : 0 }}
                            </h5>
                            <small class="text-muted">Puntos</small>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border rounded p-2 bg-light">
                            <i class="fas fa-trash-alt text-warning"></i>
                            <h5 class="mb-0 mt-1">
                                {{ $zone->average_waste ? $zone->average_waste . ' kg' : 'N/A' }}
                            </h5>
                            <small class="text-muted">Residuos</small>
                        </div>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-2" style="font-size: 0.9rem;">
                    <tr>
                        <th>Departamento:</th>
                        <td>{{ $zone->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Provincia:</th>
                        <td>{{ $zone->province->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Distrito:</th>
                        <td>{{ $zone->district->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            @if ($zone->status)
                                <span class="badge badge-success badge-custom">Activo</span>
                            @else
                                <span class="badge badge-danger badge-custom">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <hr>

                <h6>
                    Descripción
                </h6>

                <p class="text-muted mb-0">
                    {{ $zone->description ?: 'Sin descripción registrada.' }}
                </p>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-light">
                <strong>
                    <i class="fas fa-list-ol"></i>
                    Coordenadas del perímetro
                </strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 240px;">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Latitud</th>
                                <th>Longitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($zone->coordinates ?? [] as $index => $coordinate)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ isset($coordinate['lat']) ? number_format($coordinate['lat'], 6) : '-' }}
                                    </td>

                                    <td>
                                        {{ isset($coordinate['lng']) ? number_format($coordinate['lng'], 6) : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        No hay coordenadas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <strong>
                    Visualización del perímetro en el mapa
                </strong>
            </div>

            <div class="card-body">
                <div id="showZoneMap"
                    style="width: 100%; height: 530px; border: 1px solid #dee2e6; border-radius: 4px;">
                </div>

                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i>
                    El área sombreada corresponde al perímetro registrado de la zona.
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        let coordinates = @json($zone->coordinates ?? []);

        let map = L.map('showZoneMap', {
            fullscreenControl: false
        }).setView([-6.7630, -79.8366], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (coordinates && coordinates.length >= 3) {
            let latlngs = coordinates.map(function(point) {
                return [point.lat, point.lng];
            });

            let polygon = L.polygon(latlngs, {
                color: '#007bff',
                fillColor: '#007bff',
                fillOpacity: 0.25,
                weight: 3
            }).addTo(map);

            polygon.bindPopup(`
                <strong>{{ $zone->name }}</strong><br>
                <i class="fas fa-map-marker-alt"></i> {{ $zone->district->name ?? '-' }}<br>
                <i class="fas fa-map-pin"></i> ${coordinates.length} puntos
            `).openPopup();

            map.fitBounds(polygon.getBounds(), {
                padding: [20, 20]
            });
        }

        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    }, 300);
</script>
