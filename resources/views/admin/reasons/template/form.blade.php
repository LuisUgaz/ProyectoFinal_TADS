<div class="form-group">

    <label for="name">

        Nombre del Motivo <span class="text-danger">*</span>

    </label>

    <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese nombre del motivo"
        value="{{ $reason->name ?? '' }}" required>

</div>

<div class="form-group">

    <label for="description">

        Descripción

    </label>

    <textarea class="form-control" id="description" name="description" rows="4"
        placeholder="Ingrese una descripción del motivo">{{ $reason->description ?? '' }}</textarea>

</div>
