<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $zone->name }}</h3>
            </div>

            <div class="card-body">
                <p><strong>Departamento:</strong> {{ $zone->department->name }}</p>
                <p><strong>Provincia:</strong> {{ $zone->province->name }}</p>
                <p><strong>Distrito:</strong> {{ $zone->district->name }}</p>
                <p><strong>Residuos promedio:</strong> {{ $zone->average_waste ?? '-' }}</p>
                <p><strong>Estado:</strong> {{ $zone->status ? 'Activo' : 'Inactivo' }}</p>
                <p><strong>Descripción:</strong> {{ $zone->description ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="mapShow" style="width: 100%; height: 450px;"></div>
    </div>
</div>

<script>
    function initShowZoneMap() {
        let coordinates = @json($zone->coordinates);

        const defaultCenter = {
            lat: -6.7630,
            lng: -79.8366
        };

        let map = new google.maps.Map(document.getElementById("mapShow"), {
            center: defaultCenter,
            zoom: 15,
        });

        if (coordinates && coordinates.length > 0) {
            let polygon = new google.maps.Polygon({
                paths: coordinates,
                map: map
            });

            let bounds = new google.maps.LatLngBounds();

            coordinates.forEach(function(coord) {
                bounds.extend(coord);
            });

            map.fitBounds(bounds);
        }
    }

    if (typeof google === 'undefined') {
        $.getScript(
            "https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initShowZoneMap");
    } else {
        initShowZoneMap();
    }
</script>
