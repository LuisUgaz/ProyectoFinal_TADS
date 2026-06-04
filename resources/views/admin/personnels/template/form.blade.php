<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de Personal *</label>
            <select name="personnel_type_id" id="personnel_type_id" class="form-control" required>
                <option value="">
                    Seleccione
                </option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}"
                        {{ isset($personnel) && $personnel->personnel_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>DNI *</label>
            <input type="text" name="dni" maxlength="8" class="form-control" value="{{ $personnel->dni ?? '' }}"
                placeholder="Ingrese DNI" required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Estado *</label>
            <select name="status" class="form-control" required>
                <option value="Activo" {{ isset($personnel) && $personnel->status == 'Activo' ? 'selected' : '' }}>
                    Activo
                </option>
                <option value="Inactivo" {{ isset($personnel) && $personnel->status == 'Inactivo' ? 'selected' : '' }}>
                    Inactivo
                </option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nombres *</label>
            <input type="text" name="names" class="form-control" value="{{ $personnel->names ?? '' }}"
                placeholder="Ingrese nombres" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Apellidos *</label>
            <input type="text" name="lastnames" class="form-control" value="{{ $personnel->lastnames ?? '' }}"
                placeholder="Ingrese apellidos" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de Nacimiento *</label>
            <input type="date" name="birthdate" class="form-control" value="{{ $personnel->birthdate ?? '' }}"
                min="{{ now()->subYears(55)->format('Y-m-d') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}"
                required>
            <small class="text-muted">
                Edad permitida: 18 a 55 años.
            </small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="phone" class="form-control" value="{{ $personnel->phone ?? '' }}"
                placeholder="Ingrese teléfono">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" class="form-control" value="{{ $personnel->email ?? '' }}"
                placeholder="Ingrese email" required>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Dirección *</label>
    <input type="text" name="address" class="form-control" value="{{ $personnel->address ?? '' }}"
        placeholder="Ingrese dirección" required>
</div>

<div class="form-group">
    <label>Contraseña *</label>
    <input type="password" name="password" class="form-control"
        placeholder="{{ isset($personnel) ? 'Dejar vacío para mantener contraseña actual' : 'Ingrese contraseña' }}"
        {{ isset($personnel) ? '' : 'required' }}>
    @isset($personnel)
        <small class="text-muted">
            Si deja este campo vacío, se conservará la contraseña actual.
        </small>
    @endisset
</div>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Foto de Perfil</label>
            <input type="file" name="photo_path" class="form-control-file" accept="image/*">
            @isset($personnel)
                <div class="mt-2">
                    @if ($personnel->photo_path)
                        <img src="{{ asset('storage/' . $personnel->photo_path) }}" class="img-thumbnail" width="120">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center border rounded"
                            style="width:120px;height:120px;">
                            <i class="fas fa-image fa-2x text-muted"></i>
                        </div>
                    @endif
                </div>
            @endisset
        </div>
    </div>

    <div class="col-md-6" id="license_container" style="display:none;">
        <div class="form-group">
            <label>Licencia de Conducir *</label>
            <input type="file" name="license_path" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
            <small class="text-muted">
                Obligatorio para conductores.
            </small>
        </div>
    </div>
</div>

<script>
    function toggleLicenseField() {

        let selectedText = $('#personnel_type_id option:selected')
            .text()
            .trim()
            .toLowerCase();

        if (selectedText === 'conductor') {
            $('#license_container').show();

            @if (!isset($personnel) || (isset($personnel) && !$personnel->license_path))
                $('input[name="license_path"]').attr('required', true);
            @endif

        } else {
            $('#license_container').hide();
            $('input[name="license_path"]').removeAttr('required');
        }
    }

    toggleLicenseField();

    $('#personnel_type_id').change(function() {
        toggleLicenseField();
    });
</script>
