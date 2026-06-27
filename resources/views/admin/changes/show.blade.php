<div class="change-detail">

    <div class="alert alert-info">
        <strong>Resumen:</strong>
        Cambio realizado en la programación #{{ $change->schedule_id }}.
    </div>

    <div class="row">

        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <strong>
                        <i class="fas fa-arrow-left"></i>
                        Valores Anteriores
                    </strong>
                </div>

                <div class="card-body">
                    <p>
                        <strong>Turno:</strong><br>
                        {{ $change->old_shift ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Vehículo:</strong><br>
                        {{ $change->old_vehicle ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Conductor:</strong><br>
                        {{ $change->old_driver ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Ayudantes:</strong><br>
                        {{ $change->old_helpers ?? 'Sin registro' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <strong>
                        <i class="fas fa-arrow-right"></i>
                        Valores Nuevos
                    </strong>
                </div>

                <div class="card-body">
                    <p>
                        <strong>Turno:</strong><br>
                        {{ $change->new_shift ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Vehículo:</strong><br>
                        {{ $change->new_vehicle ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Conductor:</strong><br>
                        {{ $change->new_driver ?? 'Sin registro' }}
                    </p>

                    <p>
                        <strong>Ayudantes:</strong><br>
                        {{ $change->new_helpers ?? 'Sin registro' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="card mt-2">
        <div class="card-header">
            <strong>
                <i class="fas fa-info-circle"></i>
                Información del Cambio
            </strong>
        </div>

        <div class="card-body">
            <p>
                <strong>Motivo:</strong>
                {{ $change->reason?->name ?? 'Reprogramación' }}
            </p>

            <p>
                <strong>Detalle:</strong><br>
                {{ $change->description ?? 'Sin detalle adicional.' }}
            </p>

            <p>
                <strong>Realizado por:</strong>
                {{ $change->user?->name ?? 'Administrador' }}
            </p>

            <p>
                <strong>Fecha:</strong>
                {{ $change->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

</div>
