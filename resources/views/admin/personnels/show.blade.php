<div class="modal-header personnel-show-header">
    <h5 class="modal-title">
        <i class="fas fa-user"></i>
        Información del Personal
    </h5>

    <button type="button" class="close" data-dismiss="modal">
        <span>&times;</span>
    </button>
</div>

<div class="modal-body personnel-show-body">

    <div class="row">

        {{-- FOTO --}}
        <div class="col-lg-4">

            <div class="personnel-photo-card">

                <h6>Foto del personal</h6>

                @if ($personnel->photo_path)
                    <div class="personnel-main-photo">
                        <img src="{{ asset('storage/' . $personnel->photo_path) }}" alt="Foto del personal">
                    </div>
                @else
                    <div class="personnel-empty-photo">
                        <i class="fas fa-user-slash"></i>
                        <span>Sin foto registrada</span>
                    </div>
                @endif

            </div>

        </div>

        {{-- INFORMACIÓN --}}
        <div class="col-lg-8">

            {{-- DATOS PERSONALES --}}
            <div class="personnel-detail-card">

                <div class="personnel-detail-title">
                    <i class="fas fa-id-card"></i>
                    Datos personales
                </div>

                <div class="personnel-detail-grid">

                    <div>
                        <label>DNI</label>
                        <span>{{ $personnel->dni }}</span>
                    </div>

                    <div>
                        <label>Tipo de personal</label>
                        <span>{{ $personnel->type->name ?? '-' }}</span>
                    </div>

                    <div>
                        <label>Nombres</label>
                        <span>{{ $personnel->names }}</span>
                    </div>

                    <div>
                        <label>Apellidos</label>
                        <span>{{ $personnel->lastnames }}</span>
                    </div>

                    <div>
                        <label>Fecha de nacimiento</label>
                        <span>{{ \Carbon\Carbon::parse($personnel->birthdate)->format('d/m/Y') }}</span>
                    </div>

                    <div>
                        <label>Estado</label>
                        <span>
                            @if ($personnel->status == 'Activo')
                                <span class="badge badge-success badge-custom">Activo</span>
                            @else
                                <span class="badge badge-danger badge-custom">Inactivo</span>
                            @endif
                        </span>
                    </div>

                    @if (strtolower($personnel->type->name ?? '') == 'conductor')
                        <div class="personnel-full">
                            <label>N° de licencia</label>
                            <span>{{ $personnel->license_number ?: 'No registrada' }}</span>
                        </div>
                    @endif

                </div>

            </div>

            {{-- CONTACTO --}}
            <div class="personnel-detail-card">

                <div class="personnel-detail-title">
                    <i class="fas fa-address-book"></i>
                    Contacto
                </div>

                <div class="personnel-detail-grid">

                    @if ($personnel->phone)
                        <div>
                            <label>Teléfono</label>
                            <span>{{ $personnel->phone }}</span>
                        </div>
                    @endif

                    <div>
                        <label>Correo electrónico</label>
                        <span>{{ $personnel->email }}</span>
                    </div>

                    @if ($personnel->address)
                        <div class="personnel-full">
                            <label>Dirección</label>
                            <span>{{ $personnel->address }}</span>
                        </div>
                    @endif

                </div>

            </div>


        </div>

    </div>

</div>
