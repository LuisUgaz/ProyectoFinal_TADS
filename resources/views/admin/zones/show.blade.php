<div class="zone-show-layout">

    {{-- PANEL IZQUIERDO --}}
    <div class="zone-left-panel">

        {{-- RESUMEN DE LA ZONA --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-map-marker-alt"></i>
                {{ $zone->name }}
            </div>

            <div class="zone-stats-grid">
                <div class="zone-stat-box">
                    <i class="fas fa-map-pin"></i>
                    <strong>{{ is_array($zone->coordinates) ? count($zone->coordinates) : 0 }}</strong>
                    <span>Puntos</span>
                </div>

                <div class="zone-stat-box">
                    <i class="fas fa-trash-alt"></i>
                    <strong>
                        {{ $zone->average_waste ? number_format($zone->average_waste, 2) : 'N/A' }}
                    </strong>
                    <span>Residuos</span>
                </div>

                <div class="zone-stat-box">
                    <i class="fas fa-ruler-combined"></i>
                    <strong id="zoneAreaBox">-</strong>
                    <span>Área</span>
                </div>
            </div>

            <div class="zone-info-list">
                <div class="zone-info-item">
                    <label>Departamento</label>
                    <span>{{ $zone->department->name ?? '-' }}</span>
                </div>

                <div class="zone-info-item">
                    <label>Provincia</label>
                    <span>{{ $zone->province->name ?? '-' }}</span>
                </div>

                <div class="zone-info-item">
                    <label>Distrito</label>
                    <span>{{ $zone->district->name ?? '-' }}</span>
                </div>

                <div class="zone-info-item">
                    <label>Estado</label>
                    <span>
                        @if ($zone->status)
                            <span class="badge badge-success badge-custom">Activo</span>
                        @else
                            <span class="badge badge-danger badge-custom">Inactivo</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- DESCRIPCIÓN --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-align-left"></i>
                Descripción
            </div>

            <p class="zone-description">
                {{ $zone->description ?: 'Sin descripción registrada.' }}
            </p>
        </div>

        {{-- COORDENADAS --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-list-ol"></i>
                Coordenadas del perímetro
            </div>

            <div class="zone-coordinates-table">
                <table>
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
                                <td colspan="3">
                                    No hay coordenadas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- PANEL DERECHO --}}
    <div class="zone-right-panel">

        <div class="zone-map-card">
            <div class="zone-map-title">
                <i class="fas fa-map"></i>
                Visualización del perímetro
            </div>

            <div id="showZoneMap" class="zone-map-box"></div>

            <div class="zone-map-note">
                <i class="fas fa-info-circle"></i>
                El área sombreada corresponde al perímetro registrado de la zona.
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

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
                color: '#0874d1',
                fillColor: '#0874d1',
                fillOpacity: 0.25,
                weight: 3
            }).addTo(map);

            let turfPoints = coordinates.map(function(point) {
                return [point.lng, point.lat];
            });

            turfPoints.push(turfPoints[0]);

            let turfPolygon = turf.polygon([turfPoints]);
            let areaM2 = turf.area(turfPolygon);
            let areaKm2 = areaM2 / 1000000;

            let areaText = areaKm2 >= 0.01 ?
                areaKm2.toFixed(2) + ' km²' :
                areaM2.toFixed(2) + ' m²';

            $('#zoneAreaBox').html(areaText);

            polygon.bindPopup(`
                <div style="text-align:center; min-width:170px;">
                    <div style="font-size:22px; color:#0874d1; margin-bottom:6px;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>

                    <h6 style="font-weight:800; margin-bottom:8px;">
                        {{ $zone->name }}
                    </h6>

                    <div style="font-size:13px;">
                        <div style="margin-bottom:4px;">
                            <i class="fas fa-map-marker-alt text-muted"></i>
                            {{ $zone->district->name ?? '-' }}
                        </div>

                        <div>
                            <i class="fas fa-trash-alt text-muted"></i>
                            Residuos: {{ $zone->average_waste ? $zone->average_waste . ' kg' : 'N/A' }}
                        </div>
                    </div>
                </div>
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
