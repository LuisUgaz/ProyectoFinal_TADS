<div class="form-group">
    <label for="name">Nombre</label>

    <input type="text"
           id="name"
           name="name"
           class="form-control"
           value="{{ $type->name ?? '' }}"
           required>
</div>

<div class="form-group">
    <label for="description">Descripción</label>

    <textarea id="description"
              name="description"
              class="form-control"
              rows="3">{{ $type->description ?? '' }}</textarea>
</div>