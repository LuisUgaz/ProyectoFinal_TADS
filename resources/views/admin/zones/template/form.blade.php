<div class="form-group">
    <label>Nombre de la Zona <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $zone->name ?? '') }}"
        placeholder="Ingrese nombre de la zona" required>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Departamento <span class="text-danger">*</span></label>
        <select name="department_id" class="form-control" required>
            @foreach ($departments as $id => $name)
                <option value="{{ $id }}"
                    {{ old('department_id', $zone->department_id ?? ($defaultDepartment->id ?? '')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label>Provincia <span class="text-danger">*</span></label>
        <select name="province_id" class="form-control" required>
            @foreach ($provinces as $id => $name)
                <option value="{{ $id }}"
                    {{ old('province_id', $zone->province_id ?? ($defaultProvince->id ?? '')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-4">
        <label>Distrito <span class="text-danger">*</span></label>
        <select name="district_id" class="form-control" required>
            @foreach ($districts as $id => $name)
                <option value="{{ $id }}"
                    {{ old('district_id', $zone->district_id ?? ($defaultDistrict->id ?? '')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label>Descripción</label>
    <textarea name="description" class="form-control" rows="3" placeholder="Ingrese una descripción de la zona">{{ old('description', $zone->description ?? '') }}</textarea>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Residuos Promedio (kg)</label>
        <input type="number" step="0.01" name="average_waste" class="form-control"
            value="{{ old('average_waste', $zone->average_waste ?? '') }}" placeholder="Ej: 150.50">
        <small class="text-muted">Cantidad promedio de residuos en kilogramos por día</small>
    </div>

    <div class="form-group col-md-6">
        <label>Estado</label>
        <select name="status" class="form-control" required>
            <option value="1" {{ old('status', $zone->status ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('status', $zone->status ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
</div>

<input type="hidden" name="coordinates" id="coordinates" value='@json(old('coordinates', $zone->coordinates ?? null))'>

<div class="form-group mb-2">
    <label>Coordenadas del Perímetro <span class="text-danger">*</span></label>
</div>

<div id="coordinatesRows"></div>

<div class="mb-2">
    <button type="button" class="btn btn-primary btn-sm" id="btnAddCoordinate">
        <i class="fas fa-plus"></i> Agregar Coordenada
    </button>

    <button type="button" class="btn btn-warning btn-sm" id="btnClearPolygon">
        <i class="fas fa-undo"></i> Limpiar Mapa y Coordenadas
    </button>
</div>

<small class="text-muted d-block mb-3">
    Mínimo 3 coordenadas para definir un perímetro.
</small>

<div class="form-group mb-2">
    <label>Mapa interactivo de la zona</label>
</div>

<div id="zoneMap" style="width: 100%; height: 420px;" class="mb-1"></div>

<small class="text-muted d-block mb-3">
    Dibuja o ajusta el perímetro directamente en el mapa.
</small>
