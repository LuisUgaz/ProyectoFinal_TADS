<div class="form-group">
    <label>Personal *</label>

    <select name="personnel_id" id="personnel_id" class="form-control select2" required>
        <option value="">Busque por DNI, nombres o apellidos del personal</option>

        @foreach ($personnels as $person)
            <option value="{{ $person->id }}"
                {{ isset($attendance) && $attendance->personnel_id == $person->id ? 'selected' : '' }}>
                {{ $person->dni }} - {{ $person->names }} {{ $person->lastnames }}
            </option>
        @endforeach
    </select>

    <small class="text-muted">
        Seleccione al personal para consultar sus registros del día
    </small>
</div>

<div id="personnel-info-box" class="card border-secondary mb-3">
    <div class="card-header bg-light">
        <strong>
            <i class="fas fa-history text-info"></i>
            Registros de asistencia del día
        </strong>
    </div>

    <div class="card-body" id="personnel-empty-state">
        <div class="text-center text-muted py-3">
            <i class="fas fa-search fa-2x mb-2"></i>
            <p class="mb-0">
                Seleccione un personal para visualizar sus registros de asistencia del día.
            </p>
        </div>
    </div>

    <div class="card-body d-none" id="personnel-loaded-state">

        <div id="records-list" class="mb-2 text-muted">
            No hay registros para esta fecha.
        </div>

        <div id="attendance-message" class="alert py-2 mb-0"></div>
    </div>
</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>Fecha *</label>
            <input type="date" name="date" class="form-control"
                value="{{ isset($attendance) ? $attendance->date->format('Y-m-d') : now()->format('Y-m-d') }}" required>
            <small class="text-muted">
                Seleccione la fecha de asistencia
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Hora *</label>
            <input type="time" name="time" class="form-control"
                value="{{ isset($attendance) ? \Carbon\Carbon::parse($attendance->time)->format('H:i') : now()->format('H:i') }}"
                required>
            <small class="text-muted">
                Seleccione la hora de registro
            </small>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>Tipo de Marcación *</label>

            <input type="text" id="type_preview" class="form-control"
                value="{{ isset($attendance) ? $attendance->type : 'Ingreso' }}" disabled>

            <small class="text-muted">
                El sistema asigna el tipo según los registros del día
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Estado *</label>
            <select name="status" class="form-control" required>
                <option value="Presente"
                    {{ isset($attendance) && $attendance->status == 'Presente' ? 'selected' : '' }}>
                    Presente
                </option>

                <option value="Ausente" {{ isset($attendance) && $attendance->status == 'Ausente' ? 'selected' : '' }}>
                    Ausente
                </option>
            </select>
            <small class="text-muted">
                Seleccione el estado de asistencia
            </small>
        </div>
    </div>

</div>

<div class="form-group">
    <label>Notas adicionales</label>
    <textarea name="notes" class="form-control" rows="3" placeholder="Ingrese notas adicionales sobre la asistencia">{{ $attendance->notes ?? '' }}</textarea>
</div>

<div class="alert alert-info py-2">
    <i class="fas fa-info-circle"></i>
    El turno se asignará automáticamente según la hora cuando el módulo de Turnos esté implementado.
</div>

<script>
    function loadPersonnelDayInfo() {
        let personnelId = $('#personnel_id').val();
        let date = $('input[name="date"]').val();

        if (!personnelId || !date) {
            $('#personnel-empty-state').removeClass('d-none');
            $('#personnel-loaded-state').addClass('d-none');

            $('#type_preview').val('Ingreso');
            $('#FormModal button[type="submit"]').prop('disabled', false);
            return;
        }

        $.ajax({
            url: "{{ route('admin.attendances.personnel-day-info') }}",
            type: "GET",
            data: {
                personnel_id: personnelId,
                date: date
            },
            success: function(response) {
                $('#personnel-empty-state').addClass('d-none');
                $('#personnel-loaded-state').removeClass('d-none');

                let recordsHtml = '';

                if (response.records.length === 0) {
                    recordsHtml = '<span class="text-muted">No hay registros para esta fecha.</span>';
                } else {
                    response.records.forEach(function(record) {
                        let badge = record.type === 'Ingreso' ? 'success' : 'info';

                        recordsHtml += `
                            <div class="attendance-record-item">
                                <div>
                                    <strong>${record.type}</strong>
                                    <small class="text-muted d-block">${record.time} - ${record.status}</small>
                                </div>
                            </div>
                        `;
                    });
                }

                $('#records-list').html(recordsHtml);

                if (response.can_register) {
                    $('#type_preview').val(response.next_type);

                    if (response.next_type === 'Ingreso') {
                        $('#attendance-message')
                            .removeClass('alert-success alert-warning alert-danger')
                            .addClass('alert-info')
                            .html('<i class="fas fa-info-circle"></i> ' + response.message);
                    } else {
                        $('#attendance-message')
                            .removeClass('alert-success alert-warning alert-danger')
                            .addClass('alert-warning')
                            .html('<i class="fas fa-info-circle"></i> ' + response.message);
                    }

                    $('#FormModal button[type="submit"]').prop('disabled', false);
                } else {
                    $('#type_preview').val('Registro completo');

                    $('#attendance-message')
                        .removeClass('alert-success alert-info alert-warning')
                        .addClass('alert-danger')
                        .html('<i class="fas fa-lock"></i> ' + response.message);

                    $('#FormModal button[type="submit"]').prop('disabled', true);
                }
            }
        });
    }

    $('#personnel_id').on('change', loadPersonnelDayInfo);
    $('input[name="date"]').on('change', loadPersonnelDayInfo);

    loadPersonnelDayInfo();
</script>
