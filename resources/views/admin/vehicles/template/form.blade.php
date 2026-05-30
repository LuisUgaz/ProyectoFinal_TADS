<form action="{{ isset($vehicle) ? route('admin.vehicles.update', $vehicle->id) : route('admin.vehicles.store') }}"
      method="POST">
    @csrf
    @if(isset($vehicle))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="plate">Placa</label>
                <input type="text" name="plate" id="plate" class="form-control" placeholder="ABC-123" 
                       value="{{ isset($vehicle) ? $vehicle->plate : '' }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="brand_model_id">Marca y Modelo</label>
                <select name="brand_model_id" id="brand_model_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}" {{ (isset($vehicle) && $vehicle->brand_model_id == $model->id) ? 'selected' : '' }}>
                            {{ $model->brand->name }} - {{ $model->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="vehicle_type_id">Tipo de Vehículo</label>
                <select name="vehicle_type_id" id="vehicle_type_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ (isset($vehicle) && $vehicle->vehicle_type_id == $type->id) ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="vehicle_color_id">Color</label>
                <select name="vehicle_color_id" id="vehicle_color_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ (isset($vehicle) && $vehicle->vehicle_color_id == $color->id) ? 'selected' : '' }}>
                            {{ $color->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="year">Año de Fabricación</label>
                <input type="number" name="year" id="year" class="form-control" placeholder="2024"
                       value="{{ isset($vehicle) ? $vehicle->year : date('Y') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="mileage">Kilometraje</label>
                <input type="number" name="mileage" id="mileage" class="form-control" placeholder="0"
                       value="{{ isset($vehicle) ? $vehicle->mileage : '0' }}" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="engine_number">Número de Motor</label>
                <input type="text" name="engine_number" id="engine_number" class="form-control" placeholder="Opcional"
                       value="{{ isset($vehicle) ? $vehicle->engine_number : '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="chassis_number">Número de Chasis</label>
                <input type="text" name="chassis_number" id="chassis_number" class="form-control" placeholder="Opcional"
                       value="{{ isset($vehicle) ? $vehicle->chassis_number : '' }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="status">Estado</label>
        <select name="status" id="status" class="form-control" required>
            <option value="Activo" {{ (isset($vehicle) && $vehicle->status == 'Activo') ? 'selected' : '' }}>Activo</option>
            <option value="Mantenimiento" {{ (isset($vehicle) && $vehicle->status == 'Mantenimiento') ? 'selected' : '' }}>Mantenimiento</option>
            <option value="Inactivo" {{ (isset($vehicle) && $vehicle->status == 'Inactivo') ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Vehículo
        </button>
    </div>
</form>
