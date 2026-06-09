<form action="{{ route('admin.zones.store') }}" method="POST" id="zoneForm">
    @csrf

    @include('admin.zones.template.form')

    <button type="button" class="btn btn-primary" onclick="window.saveZone()">
        <i class="fas fa-save"></i> Guardar
    </button>

    <button type="button" class="btn btn-danger" data-dismiss="modal">
        <i class="fas fa-times"></i> Cancelar
    </button>
</form>
