<form action="{{ isset($type) ? route('admin.vehicle-types.update', $type->id) : route('admin.vehicle-types.store') }}"
    method="POST">
    @csrf
    @if (isset($type))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name">Nombre del Tipo de Vehículo *</label>
        <input type="text" name="name" id="name" class="form-control"
            placeholder="Ingrese nombre del tipo de vehículo" value="{{ isset($type) ? $type->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea name="description" id="description" class="form-control" rows="3"
            placeholder="Ingrese una descripción del tipo de vehículo">{{ isset($type) ? $type->description : '' }}</textarea>
    </div>

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar
        </button>
    </div>
</form>
