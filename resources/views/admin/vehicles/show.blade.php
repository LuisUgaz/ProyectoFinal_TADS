<div class="modal-header vehicle-show-header">
    <h5 class="modal-title">
        <i class="fas fa-car-side"></i>
        Información del Vehículo
    </h5>

    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body vehicle-show-body">
    <div class="row">

        <div class="col-lg-4">
            <div class="vehicle-gallery-card">
                <h6>Galería del vehículo</h6>

                @php
                    $profileImage = $vehicle->images->where('is_profile', true)->first() ?? $vehicle->images->first();
                @endphp

                @if ($profileImage)
                    <div class="vehicle-main-photo">
                        <img id="vehicleMainImage" src="{{ asset('storage/' . $profileImage->path) }}"
                            alt="Imagen del vehículo">
                    </div>
                @else
                    <div class="vehicle-empty-photo">
                        <i class="fas fa-image"></i>
                        <span>Sin imágenes</span>
                    </div>
                @endif

                @if ($vehicle->images->count() > 0)
                    <div class="vehicle-mini-gallery">
                        @foreach ($vehicle->images as $image)
                            <div class="vehicle-thumb-item">
                                <img src="{{ asset('storage/' . $image->path) }}"
                                    class="vehicle-thumb {{ $profileImage && $image->id == $profileImage->id ? 'active' : '' }}"
                                    data-image="{{ asset('storage/' . $image->path) }}" alt="Imagen del vehículo">

                                @if ($image->is_profile)
                                    <small class="vehicle-main-label">Principal</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-8">

            <div class="vehicle-detail-card">
                <div class="vehicle-detail-title">
                    <i class="fas fa-id-card"></i> Datos generales
                </div>

                <div class="vehicle-detail-grid">
                    <div>
                        <label>Placa</label>
                        <span>{{ $vehicle->plate ?? '-' }}</span>
                    </div>

                    @if ($vehicle->code)
                        <div>
                            <label>Código</label>
                            <span>{{ $vehicle->code }}</span>
                        </div>
                    @endif

                    @if ($vehicle->name)
                        <div>
                            <label>Nombre</label>
                            <span>{{ $vehicle->name }}</span>
                        </div>
                    @endif

                    <div>
                        <label>Estado</label>
                        <span>
                            @if ($vehicle->status == 'Activo')
                                <span class="badge badge-success badge-custom">Activo</span>
                            @elseif ($vehicle->status == 'Inactivo')
                                <span class="badge badge-danger badge-custom">Inactivo</span>
                            @elseif ($vehicle->status == 'Mantenimiento')
                                <span class="badge badge-warning badge-custom">Mantenimiento</span>
                            @else
                                <span class="badge badge-secondary badge-custom">{{ $vehicle->status ?? '-' }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="vehicle-detail-card">
                <div class="vehicle-detail-title">
                    <i class="fas fa-car"></i> Características
                </div>

                <div class="vehicle-detail-grid">
                    <div>
                        <label>Marca</label>
                        <span>{{ $vehicle->model->brand->name ?? '-' }}</span>
                    </div>

                    <div>
                        <label>Modelo</label>
                        <span>{{ $vehicle->model->name ?? '-' }}</span>
                    </div>

                    <div>
                        <label>Tipo</label>
                        <span>{{ $vehicle->type->name ?? '-' }}</span>
                    </div>

                    <div>
                        <label>Color</label>
                        <span>
                            @if ($vehicle->color)
                                <span class="vehicle-color-dot"
                                    style="background: {{ $vehicle->color->code }};"></span>
                                {{ $vehicle->color->name }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                    <div>
                        <label>Año</label>
                        <span>{{ $vehicle->year ?? '-' }}</span>
                    </div>

                    <div>
                        <label>Kilometraje</label>
                        <span>{{ $vehicle->mileage ?? '-' }} km</span>
                    </div>
                </div>
            </div>

            @if ($vehicle->load_capacity || $vehicle->fuel_capacity || $vehicle->compaction_capacity || $vehicle->passenger_capacity)
                <div class="vehicle-detail-card">
                    <div class="vehicle-detail-title">
                        <i class="fas fa-weight-hanging"></i> Capacidades
                    </div>

                    <div class="vehicle-detail-grid">
                        @if ($vehicle->load_capacity)
                            <div>
                                <label>Carga</label>
                                <span>{{ $vehicle->load_capacity }} Tn</span>
                            </div>
                        @endif

                        @if ($vehicle->fuel_capacity)
                            <div>
                                <label>Combustible</label>
                                <span>{{ $vehicle->fuel_capacity }} L</span>
                            </div>
                        @endif

                        @if ($vehicle->compaction_capacity)
                            <div>
                                <label>Compactación</label>
                                <span>{{ $vehicle->compaction_capacity }} Tn</span>
                            </div>
                        @endif

                        @if ($vehicle->passenger_capacity)
                            <div>
                                <label>Pasajeros</label>
                                <span>{{ $vehicle->passenger_capacity }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($vehicle->engine_number || $vehicle->chassis_number)
                <div class="vehicle-detail-card">
                    <div class="vehicle-detail-title">
                        <i class="fas fa-tools"></i> Identificación técnica
                    </div>

                    <div class="vehicle-detail-grid">
                        @if ($vehicle->engine_number)
                            <div>
                                <label>Número de motor</label>
                                <span>{{ $vehicle->engine_number }}</span>
                            </div>
                        @endif

                        @if ($vehicle->chassis_number)
                            <div>
                                <label>Número de chasis</label>
                                <span>{{ $vehicle->chassis_number }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($vehicle->description)
                <div class="vehicle-detail-card">
                    <div class="vehicle-detail-title">
                        <i class="fas fa-align-left"></i> Descripción
                    </div>

                    <p class="vehicle-description">
                        {{ $vehicle->description }}
                    </p>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
    $('.vehicle-thumb').on('click', function() {
        let imageUrl = $(this).data('image');

        $('#vehicleMainImage').attr('src', imageUrl);
        $('.vehicle-thumb').removeClass('active');
        $(this).addClass('active');
    });
</script>
