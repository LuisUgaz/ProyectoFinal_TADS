<div class="form-group">

    <label for="name">

        Nombre

    </label>

    <input type="text"
           class="form-control"
           id="name"
           name="name"
           placeholder="Nombre del motivo"
           value="{{ $reason->name ?? '' }}"
           required>

</div>

<div class="form-group">

    <label for="description">

        Descripción

    </label>

    <textarea class="form-control"
              id="description"
              name="description"
              rows="4"
              placeholder="Ingrese una descripción">{{ $reason->description ?? '' }}</textarea>

</div>