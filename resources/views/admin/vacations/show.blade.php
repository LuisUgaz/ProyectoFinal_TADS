<div class="vacation-show-body">

    <div class="vacation-detail-card">
        <div class="vacation-detail-title">
            <i class="fas fa-user"></i>
            Datos del personal
        </div>

        <div class="vacation-detail-grid">
            <div>
                <label>DNI</label>
                <span>{{ $vacation->personnel->dni }}</span>
            </div>

            <div>
                <label>Personal</label>
                <span>
                    {{ $vacation->personnel->names }}
                    {{ $vacation->personnel->lastnames }}
                </span>
            </div>
        </div>
    </div>

    <div class="vacation-detail-card">
        <div class="vacation-detail-title">
            <i class="fas fa-calendar-alt"></i>
            Información de la solicitud
        </div>

        <div class="vacation-detail-grid">
            <div>
                <label>Fecha de inicio</label>
                <span>{{ $vacation->start_date->format('d/m/Y') }}</span>
            </div>

            <div>
                <label>Fecha de fin</label>
                <span>{{ $vacation->end_date->format('d/m/Y') }}</span>
            </div>

            <div>
                <label>Días solicitados</label>
                <span>{{ $vacation->requested_days }} días</span>
            </div>

            <div>
                <label>Estado</label>
                <span>
                    @php
                        $badges = [
                            'Pendiente' => 'warning',
                            'Aprobada' => 'success',
                            'Rechazada' => 'danger',
                        ];

                        $color = $badges[$vacation->status] ?? 'secondary';
                    @endphp

                    <span class="badge badge-{{ $color }} badge-custom">
                        {{ $vacation->status }}
                    </span>
                </span>
            </div>

            <div>
                <label>Año correspondiente</label>
                <span>{{ $vacation->start_date->year }}</span>
            </div>

            <div>
                <label>Días disponibles</label>
                <span>{{ $availableDays }} días</span>
            </div>
        </div>
    </div>

    <div class="vacation-detail-card">
        <div class="vacation-detail-title">
            <i class="fas fa-align-left"></i>
            Notas
        </div>

        <p class="vacation-description">
            {{ $vacation->notes ?: 'Sin notas adicionales' }}
        </p>
    </div>

    <div class="vacation-alert">
        <i class="fas fa-info-circle"></i>
        Las vacaciones solo aplican para personal con contrato activo de tipo
        <strong>Permanente</strong> o <strong>Nombrado</strong>.
    </div>

    <div class="text-right mt-3">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

</div>
