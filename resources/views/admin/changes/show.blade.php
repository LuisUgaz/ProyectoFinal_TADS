<div class="change-detail-clean">

    <div class="change-summary">
        <div>
            <span class="summary-label">Resumen del cambio</span>
            <h6>
                Programación #{{ $change->schedule_id }}
            </h6>
        </div>

        <span class="change-type-pill">
            {{ $change->change_type ?? 'Cambio' }}
        </span>
    </div>

    <div class="change-comparison">

        <div class="change-panel">
            <div class="change-panel-title">
                <i class="fas fa-history"></i>
                Registro anterior
            </div>

            <div class="change-panel-body">
                <span class="field-label">Tipo de cambio</span>
                <p>{{ $change->change_type ?? 'Cambio' }}</p>

                <span class="field-label">Valor anterior</span>
                <p class="value-text">{{ $change->previous_value ?? 'Sin registro' }}</p>
            </div>
        </div>

        <div class="change-panel">
            <div class="change-panel-title">
                <i class="fas fa-check-circle"></i>
                Registro actualizado
            </div>

            <div class="change-panel-body">
                <span class="field-label">Tipo de cambio</span>
                <p>{{ $change->change_type ?? 'Cambio' }}</p>

                <span class="field-label">Valor nuevo</span>
                <p class="value-text">{{ $change->new_value ?? 'Sin registro' }}</p>
            </div>
        </div>

    </div>

    <div class="change-info-clean">

        <div class="change-info-title">
            <i class="fas fa-info-circle"></i>
            Información del cambio
        </div>

        <div class="info-grid">

            <div class="info-item">
                <span>Programación</span>
                <strong>#{{ $change->schedule_id }}</strong>
            </div>

            <div class="info-item">
                <span>Zona</span>
                <strong>{{ $change->schedule?->zone?->name ?? 'Sin zona' }}</strong>
            </div>

            <div class="info-item">
                <span>Motivo</span>
                <strong>{{ $change->reason?->name ?? 'Sin motivo' }}</strong>
            </div>

            <div class="info-item">
                <span>Realizado por</span>
                <strong>{{ $change->user?->name ?? 'Administrador' }}</strong>
            </div>

            <div class="info-item">
                <span>Fecha</span>
                <strong>{{ $change->created_at->format('d/m/Y H:i') }}</strong>
            </div>

        </div>

        <div class="detail-box">
            <span>Detalle adicional</span>
            <p>{{ $change->description ?? 'Sin detalle adicional.' }}</p>
        </div>

    </div>

</div>

<style>
    .change-detail-clean {
        font-size: 14px;
    }

    .change-summary {
        background: #ffffff;
        border: 1px solid #dfe5dc;
        border-left: 5px solid #78b82a;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .summary-label {
        display: block;
        color: #6c757d;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .change-summary h6 {
        margin: 0;
        font-weight: 800;
        color: #001f3f;
    }

    .change-type-pill {
        background: #1f1f1f;
        color: #ffffff;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .change-comparison {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 14px;
    }

    .change-panel {
        background: #ffffff;
        border: 1px solid #dfe5dc;
        border-radius: 10px;
        overflow: hidden;
    }

    .change-panel-title {
        background: #f5f6f4;
        border-bottom: 1px solid #dfe5dc;
        padding: 10px 14px;
        color: #001f3f;
        font-weight: 800;
    }

    .change-panel-title i {
        color: #78b82a;
        margin-right: 5px;
    }

    .change-panel-body {
        padding: 14px;
    }

    .field-label {
        display: block;
        color: #6c757d;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .change-panel-body p {
        margin-bottom: 14px;
        color: #001f3f;
    }

    .value-text {
        font-weight: 800;
        font-size: 16px;
    }

    .change-info-clean {
        background: #ffffff;
        border: 1px solid #dfe5dc;
        border-radius: 10px;
        overflow: hidden;
    }

    .change-info-title {
        background: #f5f6f4;
        border-bottom: 1px solid #dfe5dc;
        padding: 10px 14px;
        color: #001f3f;
        font-weight: 800;
    }

    .change-info-title i {
        color: #78b82a;
        margin-right: 5px;
    }

    .info-grid {
        padding: 14px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .info-item {
        background: #f8f9f7;
        border: 1px solid #edf0eb;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .info-item span,
    .detail-box span {
        display: block;
        color: #6c757d;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-item strong {
        color: #001f3f;
        font-weight: 800;
    }

    .detail-box {
        margin: 0 14px 14px 14px;
        background: #f8f9f7;
        border: 1px solid #edf0eb;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .detail-box p {
        margin: 0;
        color: #001f3f;
    }

    @media (max-width: 768px) {

        .change-comparison,
        .info-grid {
            grid-template-columns: 1fr;
        }

        .change-summary {
            display: block;
        }

        .change-type-pill {
            display: inline-block;
            margin-top: 10px;
        }
    }
</style>
