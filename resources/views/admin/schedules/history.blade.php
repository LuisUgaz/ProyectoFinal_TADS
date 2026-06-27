@if ($schedule->changes->count() > 0)

    <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover">
            <thead class="bg-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Antes</th>
                    <th>Después</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($schedule->changes->sortByDesc('created_at') as $change)
                    <tr>
                        <td>{{ $change->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-warning badge-custom">
                                {{ $change->change_type ?? 'Cambio' }}
                            </span>
                        </td>
                        <td>{{ $change->previous_value ?? 'Sin registro' }}</td>
                        <td>{{ $change->new_value ?? 'Sin registro' }}</td>
                        <td>
                            <strong>{{ $change->reason?->name ?? 'Sin motivo' }}</strong>

                            @if ($change->description)
                                <br>
                                <small class="text-muted">
                                    {{ $change->description }}
                                </small>
                            @endif
                        </td>
                        <td>{{ $change->user?->name ?? 'Administrador' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle"></i>
        Esta programación aún no tiene cambios registrados.
    </div>

@endif
