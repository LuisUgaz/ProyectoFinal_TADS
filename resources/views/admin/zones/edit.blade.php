<form action="{{ route('admin.zones.update', $zone->id) }}" method="POST" id="zoneForm">
    @csrf
    @method('PUT')

    @include('admin.zones.template.form')

    <button type="button" class="btn btn-primary" onclick="window.saveZone()">
        <i class="fas fa-save"></i> Actualizar
    </button>

    <button type="button" class="btn btn-danger" data-dismiss="modal">
        <i class="fas fa-times"></i> Cancelar
    </button>
</form>
