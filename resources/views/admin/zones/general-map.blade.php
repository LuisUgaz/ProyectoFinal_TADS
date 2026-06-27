<div class="zone-general-layout">

    {{-- PANEL IZQUIERDO --}}
    <div class="zone-left-panel">

        {{-- FILTROS --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-filter"></i>
                Filtros de búsqueda
            </div>

            <div class="zone-filter-group">
                <label>Departamento</label>
                <select id="filter_department_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="zone-filter-group">
                <label>Provincia</label>
                <select id="filter_province_id" class="form-control">
                    <option value="">Todas</option>
                </select>
            </div>

            <div class="zone-filter-group mb-0">
                <label>Distrito</label>
                <select id="filter_district_id" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-chart-bar"></i>
                Resumen de zonas
            </div>

            <div class="zone-stats-grid">
                <div class="zone-stat-box">
                    <i class="fas fa-map-marked-alt"></i>
                    <strong id="totalZonesBox">0</strong>
                    <span>Zonas</span>
                </div>

                <div class="zone-stat-box">
                    <i class="fas fa-check-circle"></i>
                    <strong id="activeZonesBox">0</strong>
                    <span>Activas</span>
                </div>

                <div class="zone-stat-box">
                    <i class="fas fa-map-pin"></i>
                    <strong id="totalPointsBox">0</strong>
                    <span>Puntos</span>
                </div>
            </div>
        </div>

        {{-- LEYENDA --}}
        <div class="zone-panel-card">
            <div class="zone-panel-title">
                <i class="fas fa-list"></i>
                Leyenda del mapa
            </div>

            <div id="zonesLegend" class="zone-legend-box">
                <div class="zone-empty-text">
                    Cargando zonas...
                </div>
            </div>
        </div>

    </div>

    {{-- PANEL DERECHO --}}
    <div class="zone-right-panel">

        <div class="zone-map-card">
            <div class="zone-map-title">
                <i class="fas fa-map"></i>
                Visualización general de zonas
            </div>

            <div id="generalZonesMap" class="zone-map-box zone-map-box-lg"></div>

            <div class="zone-map-note">
                <i class="fas fa-info-circle"></i>
                <span id="zoneCounterText">
                    Cada color representa una zona registrada diferente.
                </span>
            </div>
        </div>

    </div>

</div>

<script>
    setTimeout(function() {
        if (window.generalZonesMapInstance) {
            window.generalZonesMapInstance.remove();
            window.generalZonesMapInstance = null;
        }

        let map = L.map('generalZonesMap').setView([-9.19, -75.0152], 6);
        window.generalZonesMapInstance = map;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let colors = [
            '#0874d1',
            '#16a34a',
            '#ef4444',
            '#f59e0b',
            '#0ea5e9',
            '#7c3aed',
            '#f97316',
            '#14b8a6',
            '#db2777',
            '#374151'
        ];

        let allZones = [];
        let zonesLayer = L.featureGroup().addTo(map);

        let defaultDepartmentName = 'Lambayeque';
        let defaultProvinceName = 'Chiclayo';
        let defaultDistrictName = 'José Leonardo Ortiz';

        $.get("{{ route('admin.zones.all-polygons') }}", function(zones) {
            allZones = zones;
            setDefaultLocationFilters();
        });

        $('#filter_department_id').off('change.generalMap').on('change.generalMap', function() {
            let departmentId = $(this).val();

            $('#filter_province_id').html('<option value="">Todas</option>');
            $('#filter_district_id').html('<option value="">Todos</option>');

            if (departmentId) {
                $.get("{{ route('admin.zones.provinces', ':id') }}".replace(':id', departmentId),
                    function(provinces) {
                        provinces.forEach(function(province) {
                            $('#filter_province_id').append(
                                `<option value="${province.id}">${province.name}</option>`
                            );
                        });
                    });
            }

            renderZones();
            centerMapByFilter();
        });

        $('#filter_province_id').off('change.generalMap').on('change.generalMap', function() {
            let provinceId = $(this).val();

            $('#filter_district_id').html('<option value="">Todos</option>');

            if (provinceId) {
                $.get("{{ route('admin.zones.districts', ':id') }}".replace(':id', provinceId),
                    function(districts) {
                        districts.forEach(function(district) {
                            $('#filter_district_id').append(
                                `<option value="${district.id}">${district.name}</option>`
                            );
                        });
                    });
            }

            renderZones();
            centerMapByFilter();
        });

        $('#filter_district_id').off('change.generalMap').on('change.generalMap', function() {
            renderZones();
            centerMapByFilter();
        });

        function setDefaultLocationFilters() {
            let departmentOption = $('#filter_department_id option').filter(function() {
                return $(this).text().trim().toLowerCase() === defaultDepartmentName.toLowerCase();
            });

            if (!departmentOption.length) {
                renderZones();
                return;
            }

            let departmentId = departmentOption.val();

            $('#filter_department_id').val(departmentId);

            $.get("{{ route('admin.zones.provinces', ':id') }}".replace(':id', departmentId), function(
                provinces) {
                $('#filter_province_id').html('<option value="">Todas</option>');
                $('#filter_district_id').html('<option value="">Todos</option>');

                provinces.forEach(function(province) {
                    $('#filter_province_id').append(
                        `<option value="${province.id}">${province.name}</option>`
                    );
                });

                let provinceOption = $('#filter_province_id option').filter(function() {
                    return $(this).text().trim().toLowerCase() === defaultProvinceName
                        .toLowerCase();
                });

                if (!provinceOption.length) {
                    renderZones();
                    return;
                }

                let provinceId = provinceOption.val();

                $('#filter_province_id').val(provinceId);

                $.get("{{ route('admin.zones.districts', ':id') }}".replace(':id', provinceId),
                    function(districts) {
                        $('#filter_district_id').html('<option value="">Todos</option>');

                        districts.forEach(function(district) {
                            $('#filter_district_id').append(
                                `<option value="${district.id}">${district.name}</option>`
                            );
                        });

                        let districtOption = $('#filter_district_id option').filter(function() {
                            return $(this).text().trim().toLowerCase() ===
                                defaultDistrictName.toLowerCase();
                        });

                        if (districtOption.length) {
                            $('#filter_district_id').val(districtOption.val());
                        }

                        renderZones();
                        centerMapByFilter();
                    });
            });
        }

        function getFilteredZones() {
            let departmentId = $('#filter_department_id').val();
            let provinceId = $('#filter_province_id').val();
            let districtId = $('#filter_district_id').val();

            return allZones.filter(function(zone) {
                if (!zone.coordinates || zone.coordinates.length < 3) {
                    return false;
                }

                if (departmentId && zone.department_id != departmentId) {
                    return false;
                }

                if (provinceId && zone.province_id != provinceId) {
                    return false;
                }

                if (districtId && zone.district_id != districtId) {
                    return false;
                }

                return true;
            });
        }

        function renderZones() {
            zonesLayer.clearLayers();

            let zones = getFilteredZones();
            let totalZones = 0;
            let activeZones = 0;
            let totalPoints = 0;
            let legendHtml = '';

            zones.forEach(function(zone, index) {
                totalZones++;
                totalPoints += zone.coordinates.length;

                if (zone.status) {
                    activeZones++;
                }

                let color = colors[index % colors.length];

                let latlngs = zone.coordinates.map(function(point) {
                    return [point.lat, point.lng];
                });

                let polygon = L.polygon(latlngs, {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.25,
                    weight: 3
                }).addTo(zonesLayer);

                polygon.bindPopup(`
                    <div style="text-align:center; min-width:170px;">
                        <div style="font-size:22px; color:${color}; margin-bottom:6px;">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>

                        <h6 style="font-weight:800; margin-bottom:8px;">
                            ${zone.name}
                        </h6>

                        <div style="font-size:13px;">
                            <div style="margin-bottom:4px;">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                                ${zone.district}
                            </div>

                            <div style="margin-bottom:4px;">
                                <i class="fas fa-city text-muted"></i>
                                ${zone.province}
                            </div>

                            <div style="margin-bottom:6px;">
                                <i class="fas fa-trash-alt text-muted"></i>
                                Residuos: ${zone.average_waste ? zone.average_waste + ' kg' : 'N/A'}
                            </div>

                            <span class="badge badge-${zone.status ? 'success' : 'danger'} badge-custom">
                                ${zone.status ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                    </div>
                `);

                polygon.bindTooltip(zone.name, {
                    permanent: false,
                    direction: 'center'
                });

                legendHtml += `
                    <div class="zone-legend-item">
                        <span class="zone-legend-color" style="background:${color};"></span>

                        <div>
                            <strong>${zone.name}</strong>
                            <small>${zone.district}</small>
                        </div>
                    </div>
                `;
            });

            $('#totalZonesBox').text(totalZones);
            $('#activeZonesBox').text(activeZones);
            $('#totalPointsBox').text(totalPoints);

            $('#zoneCounterText').text(totalZones + ' zona(s) encontradas según el filtro seleccionado.');

            $('#zonesLegend').html(
                legendHtml ||
                `<div class="zone-empty-text">
                    No hay zonas registradas para el filtro seleccionado.
                </div>`
            );

            if (zonesLayer.getLayers().length > 0) {
                map.fitBounds(zonesLayer.getBounds(), {
                    padding: [20, 20]
                });
            }
        }

        function centerMapByFilter() {
            let department = $('#filter_department_id option:selected').text().trim();
            let province = $('#filter_province_id option:selected').text().trim();
            let district = $('#filter_district_id option:selected').text().trim();

            let queryParts = [];

            if (district && district !== 'Todos') {
                queryParts.push(district);
            }

            if (province && province !== 'Todas') {
                queryParts.push(province);
            }

            if (department && department !== 'Todos') {
                queryParts.push(department);
            }

            if (queryParts.length === 0) {
                return;
            }

            queryParts.push('Perú');

            $.get('https://nominatim.openstreetmap.org/search', {
                q: queryParts.join(', '),
                format: 'json',
                limit: 1
            }, function(response) {
                if (response.length > 0) {
                    let lat = parseFloat(response[0].lat);
                    let lon = parseFloat(response[0].lon);

                    map.setView(
                        [lat, lon],
                        district !== 'Todos' ? 14 : province !== 'Todas' ? 11 : 8
                    );
                }
            });
        }

        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    }, 300);
</script>
