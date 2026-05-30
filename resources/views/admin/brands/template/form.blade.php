<div class="form-group">
    <label>Nombre</label>
    <input type="text"
           name="name"
           class="form-control"
           value="{{ $brand->name ?? '' }}"
           required>
</div>

<div class="form-group">
    <label>Descripción</label>
    <textarea name="description"
              class="form-control"
              rows="3"
              required>{{ $brand->description ?? '' }}</textarea>
</div>

<div class="form-group">
    <label>Logo</label>

    <input type="file"
           name="logo"
           class="form-control-file"
           accept="image/*">

    @isset($brand)
        @if($brand->logo)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$brand->logo) }}"
                     width="80">
            </div>
        @endif
    @endisset
</div>