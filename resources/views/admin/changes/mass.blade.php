<form id="mass-change-form" action="{{ route('admin.changes.mass.store') }}" method="POST">
    @csrf

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha de inicio <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha de fin <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Zona</label>
                <select name="zone_id" class="form-control">
                    <option value="">Todas las zonas</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Deje vacío para aplicar a todas las zonas.</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Tipo de cambio <span class="text-danger">*</span></label>
                <select name="change_type" id="mass_change_type" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="Turno">Cambio de turno</option>
                    <option value="Vehículo">Cambio de vehículo</option>
                    <option value="Conductor">Cambio de conductor</option>
                    <option value="Ayudantes">Cambio de ayudante</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-turno-old">
            <div class="form-group">
                <label>Turno a reemplazar <span class="text-danger">*</span></label>
                <select class="form-control previous-select">
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-turno-new">
            <div class="form-group">
                <label>Nuevo turno <span class="text-danger">*</span></label>
                <select class="form-control new-select">
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-vehicle-old">
            <div class="form-group">
                <label>Vehículo a reemplazar <span class="text-danger">*</span></label>
                <select class="form-control previous-select">
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-vehicle-new">
            <div class="form-group">
                <label>Nuevo vehículo <span class="text-danger">*</span></label>
                <select class="form-control new-select">
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-driver-old">
            <div class="form-group">
                <label>Conductor a reemplazar <span class="text-danger">*</span></label>
                <select class="form-control previous-select">
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->lastnames }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-driver-new">
            <div class="form-group">
                <label>Nuevo conductor <span class="text-danger">*</span></label>
                <select class="form-control new-select">
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->lastnames }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-helper-old">
            <div class="form-group">
                <label>Ayudante a reemplazar <span class="text-danger">*</span></label>
                <select class="form-control previous-select">
                    @foreach ($helpers as $helper)
                        <option value="{{ $helper->id }}">{{ $helper->names }} {{ $helper->lastnames }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6 d-none mass-option" id="option-helper-new">
            <div class="form-group">
                <label>Nuevo ayudante <span class="text-danger">*</span></label>
                <select class="form-control new-select">
                    @foreach ($helpers as $helper)
                        <option value="{{ $helper->id }}">{{ $helper->names }} {{ $helper->lastnames }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <input type="hidden" name="previous_value_id" id="previous_value_id">
        <input type="hidden" name="new_value_id" id="new_value_id">

        <div class="col-md-6">
            <div class="form-group">
                <label>Motivo <span class="text-danger">*</span></label>
                <select name="reason_id" id="mass_reason_id" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach ($reasons as $reason)
                        <option value="{{ $reason->id }}" data-description="{{ $reason->description }}">
                            {{ $reason->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Detalle del motivo</label>
                <textarea id="mass_reason_description" class="form-control" rows="2" readonly></textarea>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Descripción adicional</label>
                <textarea name="description" class="form-control" rows="2"
                    placeholder="Ingrese un detalle adicional si corresponde..."></textarea>
            </div>
        </div>

    </div>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Esta operación modificará varias programaciones dentro del rango seleccionado.
    </div>

    <div class="text-right">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Aplicar Cambio Masivo
        </button>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Cancelar
        </button>
    </div>
</form>
