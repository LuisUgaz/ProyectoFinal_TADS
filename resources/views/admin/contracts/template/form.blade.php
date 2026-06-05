<div class="row">
    <div class="col-md-12 form-group">
        <label for="personnel_id">Personal *</label>
        <select name="personnel_id" id="personnel_id" class="form-control select2" required>
            <option value="">Busque por DNI, nombres o apellidos del personal</option>
            @foreach ($personnels as $person)
                <option value="{{ $person->id }}"
                    {{ isset($contract) && $contract->personnel_id == $person->id ? 'selected' : '' }}>
                    {{ $person->dni }} - {{ $person->names }} {{ $person->lastnames }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">
            Seleccione al personal para continuar con el contrato
        </small>
    </div>

    <div class="col-md-6 form-group">
        <label for="type">Tipo de Contrato *</label>
        <select name="type" id="type" class="form-control" required>
            <option value="Permanente" {{ isset($contract) && $contract->type == 'Permanente' ? 'selected' : '' }}>
                Permanente
            </option>
            <option value="Nombrado" {{ isset($contract) && $contract->type == 'Nombrado' ? 'selected' : '' }}>
                Nombrado
            </option>
            <option value="Temporal" {{ isset($contract) && $contract->type == 'Temporal' ? 'selected' : '' }}>
                Temporal
            </option>
        </select>
    </div>

    <div class="col-md-6 form-group">
        <label for="salary">Salario *</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">S/</span>
            </div>
            <input type="number" name="salary" id="salary" class="form-control" step="0.01" min="0"
                value="{{ $contract->salary ?? '' }}" required placeholder="0.00">
        </div>
    </div>

    <div class="col-md-6 form-group">
        <label for="start_date">Fecha de Inicio *</label>
        <input type="date" name="start_date" id="start_date" class="form-control"
            value="{{ isset($contract) ? $contract->start_date->format('Y-m-d') : '' }}" required>
    </div>

    <div class="col-md-6 form-group">
        <label for="end_date">Fecha de Fin</label>
        <input type="date" name="end_date" id="end_date" class="form-control"
            value="{{ isset($contract) && $contract->end_date ? $contract->end_date->format('Y-m-d') : '' }}">
    </div>

    <div class="col-md-12 form-group">
        <label for="probation_period">Periodo de Prueba (meses)</label>
        <input type="text" name="probation_period" id="probation_period" class="form-control"
            value="{{ $contract->probation_period ?? '' }}" placeholder="Ej: 3 meses">
        <small class="text-muted">
            Periodo de prueba para contrato PERMANENTE
        </small>
    </div>

    <div class="col-md-12 form-group">
        <div class="custom-control custom-switch">
            <input type="hidden" name="is_active" value="0">

            <input type="checkbox" class="custom-control-input"
                id="{{ isset($contract) ? 'is_active_edit' : 'is_active' }}" name="is_active" value="1"
                {{ !isset($contract) || $contract->is_active ? 'checked' : '' }}>

            <label class="custom-control-label" for="{{ isset($contract) ? 'is_active_edit' : 'is_active' }}">
                Contrato Activo
            </label>
        </div>

        <small class="text-muted">
            Si se marca como activo, se desactivarán otros contratos previos de esta persona.
        </small>
    </div>
</div>
