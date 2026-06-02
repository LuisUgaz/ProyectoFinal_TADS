<form action="{{ isset($vehicle) ? route('admin.vehicles.update', $vehicle->id) : route('admin.vehicles.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($vehicle))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="code">Código Interno</label>
                <input type="text" name="code" id="code" class="form-control" placeholder="VEH-001"
                    value="{{ isset($vehicle) ? $vehicle->code : '' }}">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="name">Nombre / Alias</label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="Camión Recolector #1" value="{{ isset($vehicle) ? $vehicle->name : '' }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="plate">Placa</label>
                <input type="text" name="plate" id="plate" class="form-control" placeholder="ABC-123"
                    value="{{ isset($vehicle) ? $vehicle->plate : '' }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="brand_model_id">Marca y Modelo</label>
                <select name="brand_model_id" id="brand_model_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach ($models as $model)
                        <option value="{{ $model->id }}"
                            {{ isset($vehicle) && $vehicle->brand_model_id == $model->id ? 'selected' : '' }}>
                            {{ $model->brand->name }} - {{ $model->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="vehicle_type_id">Tipo de Vehículo</label>
                <select name="vehicle_type_id" id="vehicle_type_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}"
                            {{ isset($vehicle) && $vehicle->vehicle_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="vehicle_color_id">Color</label>
                <select name="vehicle_color_id" id="vehicle_color_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach ($colors as $color)
                        <option value="{{ $color->id }}"
                            {{ isset($vehicle) && $vehicle->vehicle_color_id == $color->id ? 'selected' : '' }}>
                            {{ $color->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="year">Año</label>
                <input type="number" name="year" id="year" class="form-control" placeholder="2024"
                    value="{{ isset($vehicle) ? $vehicle->year : date('Y') }}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="load_capacity">Cap. Carga (Tn)</label>
                <input type="number" step="0.01" name="load_capacity" id="load_capacity" class="form-control"
                    placeholder="0.00" value="{{ isset($vehicle) ? $vehicle->load_capacity : '' }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="fuel_capacity">Cap. Comb. (L)</label>
                <input type="number" step="0.01" name="fuel_capacity" id="fuel_capacity" class="form-control"
                    placeholder="0.00" value="{{ isset($vehicle) ? $vehicle->fuel_capacity : '' }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="passenger_capacity">Cap. Pasajeros</label>
                <input type="number" name="passenger_capacity" id="passenger_capacity" class="form-control"
                    placeholder="0" value="{{ isset($vehicle) ? $vehicle->passenger_capacity : '' }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="compaction_capacity">Cap. Compactación (Tn)</label>
                <input type="number" step="0.01" name="compaction_capacity" id="compaction_capacity"
                    class="form-control" placeholder="0.00"
                    value="{{ isset($vehicle) ? $vehicle->compaction_capacity : '' }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="mileage">Kilometraje (Km)</label>
                <input type="number" name="mileage" id="mileage" class="form-control" placeholder="0"
                    value="{{ isset($vehicle) ? $vehicle->mileage : '0' }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="status">Estado</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Activo" {{ isset($vehicle) && $vehicle->status == 'Activo' ? 'selected' : '' }}>
                        Activo</option>
                    <option value="Mantenimiento"
                        {{ isset($vehicle) && $vehicle->status == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento
                    </option>
                    <option value="Inactivo"
                        {{ isset($vehicle) && $vehicle->status == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="engine_number">Número de Motor</label>
                <input type="text" name="engine_number" id="engine_number" class="form-control"
                    placeholder="Opcional" value="{{ isset($vehicle) ? $vehicle->engine_number : '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="chassis_number">Número de Chasis</label>
                <input type="text" name="chassis_number" id="chassis_number" class="form-control"
                    placeholder="Opcional" value="{{ isset($vehicle) ? $vehicle->chassis_number : '' }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Descripción / Notas Adicionales</label>
        <textarea name="description" id="description" class="form-control" rows="2"
            placeholder="Detalles técnicos, estado general, etc.">{{ isset($vehicle) ? $vehicle->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="images">Imágenes del Vehículo</label>
        <div class="custom-file">
            <input type="file" name="images[]" id="images" class="custom-file-input" multiple
                accept="image/*">
            <label class="custom-file-label" for="images">Seleccionar imágenes...</label>
        </div>
    </div>

    @if (isset($vehicle) && $vehicle->images->count() > 0)
        <div class="form-group">
            <label>Imágenes Actuales</label>
            <div class="row">
                @foreach ($vehicle->images as $image)
                    <div class="col-md-3 mb-2">
                        <div class="card shadow-sm">
                            <img src="{{ asset('storage/' . $image->path) }}" class="card-img-top"
                                style="height: 100px; object-fit: cover;">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Vehículo
        </button>
    </div>
</form>

<script>
    // Mostrar nombre del archivo en el input custom-file
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        if (this.files.length > 1) {
            fileName = this.files.length + ' archivos seleccionados';
        }
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
