<form action="{{ route('admin.vacations.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Personal</label>
                <select name="personnel_id" id="personnel_id" class="form-control select2" required>
                    <option value="">Seleccione al personal</option>
                    @foreach($personnels as $p)
                        <option value="{{ $p->id }}">{{ $p->dni }} - {{ $p->names }} {{ $p->lastnames }}</option>
                    @endforeach
                </select>
                <small id="days-info" class="form-text text-muted"></small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha de inicio</label>
                <input type="date" name="start_date" id="start_date_form" class="form-control" required min="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Días solicitados</label>
                <input type="number" name="requested_days" id="requested_days" class="form-control" required min="1" max="30">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Fecha de fin (Calculada)</label>
                <input type="text" id="end_date_display" class="form-control" readonly placeholder="Se calculará automáticamente">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Notas adicionales</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Opcional..."></textarea>
            </div>
        </div>
    </div>

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Solicitud</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#personnel_id').change(function() {
            let id = $(this).val();
            if (id) {
                let year = new Date().getFullYear();
                $.get("{{ route('admin.vacations.personnel-info') }}", { personnel_id: id, year: year }, function(data) {
                    $('#days-info').text('Días usados: ' + data.used_days + ' | Días disponibles: ' + data.available_days);
                    $('#requested_days').attr('max', data.available_days);
                });
            } else {
                $('#days-info').text('');
            }
        });

        $('#start_date_form, #requested_days').on('change input', function() {
            let startStr = $('#start_date_form').val();
            let days = parseInt($('#requested_days').val());

            if (startStr && days > 0) {
                let start = new Date(startStr + 'T00:00:00');
                start.setDate(start.getDate() + days - 1);
                
                let day = ("0" + start.getDate()).slice(-2);
                let month = ("0" + (start.getMonth() + 1)).slice(-2);
                let year = start.getFullYear();
                
                $('#end_date_display').val(day + '/' + month + '/' + year);
            } else {
                $('#end_date_display').val('');
            }
        });
    });
</script>
