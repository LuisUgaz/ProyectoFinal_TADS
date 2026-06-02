<form action="{{ route('admin.contracts.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-md-12 form-group">
            <label for="personnel_id">Personal</label>
            <select name="personnel_id" id="personnel_id" class="form-control select2" required>
                <option value="">Seleccione al personal...</option>
                @foreach($personnels as $person)
                    <option value="{{ $person->id }}">
                        {{ $person->dni }} - {{ $person->names }} {{ $person->lastnames }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="type">Tipo de Contrato</label>
            <select name="type" id="type" class="form-control" required>
                <option value="Permanente">Permanente</option>
                <option value="Nombrado">Nombrado</option>
                <option value="Temporal">Temporal</option>
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="salary">Salario</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">S/</span>
                </div>
                <input type="number" name="salary" id="salary" class="form-control" step="0.01" min="0" required>
            </div>
        </div>

        <div class="col-md-6 form-group">
            <label for="start_date">Fecha de Inicio</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="col-md-6 form-group">
            <label for="end_date">Fecha de Fin (Opcional)</label>
            <input type="date" name="end_date" id="end_date" class="form-control">
        </div>

        <div class="col-md-12 form-group">
            <label for="probation_period">Periodo de Prueba</label>
            <input type="text" name="probation_period" id="probation_period" class="form-control" placeholder="Ej: 3 meses">
        </div>

        <div class="col-md-12 form-group">
            <div class="custom-control custom-switch">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                <label class="custom-control-label" for="is_active">Contrato Activo</label>
            </div>
            <small class="text-muted">Si se marca como activo, se desactivarán otros contratos previos de esta persona.</small>
        </div>
    </div>

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Contrato</button>
    </div>
</form>
