<div class="row">
    <div class="col-md-6">
        <p><strong>Personal:</strong><br>{{ $vacation->personnel->names }} {{ $vacation->personnel->lastnames }}</p>
        <p><strong>DNI:</strong><br>{{ $vacation->personnel->dni }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Estado:</strong><br>
            @php
                $badges = ['Pendiente' => 'warning', 'Aprobada' => 'success', 'Rechazada' => 'danger'];
                $color = $badges[$vacation->status] ?? 'secondary';
            @endphp
            <span class="badge badge-{{ $color }}">{{ $vacation->status }}</span>
        </p>
        <p><strong>Días Solicitados:</strong><br>{{ $vacation->requested_days }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Fecha de Inicio:</strong><br>{{ $vacation->start_date->format('d/m/Y') }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Fecha de Fin:</strong><br>{{ $vacation->end_date->format('d/m/Y') }}</p>
    </div>
    <div class="col-md-12">
        <hr>
        <p><strong>Notas:</strong><br>{{ $vacation->notes ?: 'Sin notas adicionales' }}</p>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            Días disponibles para el año {{ $vacation->start_date->year }}: <strong>{{ $availableDays }} días</strong>
        </div>
    </div>
</div>
<div class="text-right mt-3">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
