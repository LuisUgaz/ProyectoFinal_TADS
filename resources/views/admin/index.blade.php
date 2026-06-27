@extends('adminlte::page')

@section('title', 'Panel Principal')

@section('content_header')
@stop

@section('content')
    <div class="dashboard-rsu">

        {{-- ENCABEZADO PRINCIPAL --}}
        <section class="rsu-hero-simple">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <span class="rsu-kicker">
                        <i class="fas fa-city mr-2"></i>
                        Municipalidad Distrital de José Leonardo Ortiz
                    </span>

                    <h1>Sistema de Gestión de Residuos Sólidos Urbanos</h1>

                    <p>
                        Plataforma web para administrar vehículos, personal, zonas y programación
                        del servicio de recolección de residuos sólidos urbanos.
                    </p>
                </div>

                <div class="col-lg-4 col-md-5 text-center">
                    <img src="{{ asset('img/logo-jlo.png') }}" class="rsu-logo-dashboard"
                        alt="Municipalidad Distrital de José Leonardo Ortiz">
                </div>
            </div>
        </section>

        {{-- TÍTULO DE MÓDULOS --}}
        <div class="rsu-section-title">
            <h3>Módulos principales</h3>
        </div>

        {{-- MÓDULOS --}}
        <div class="row rsu-modules-row">

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('admin.vehicles.index') }}" class="rsu-module-card rsu-card-blue">
                    <div class="rsu-card-content">
                        <span class="rsu-card-label">Módulo 01</span>
                        <h4>Vehículos</h4>
                        <p>Colores, marcas, modelos, tipos de vehículos y vehículos.</p>
                        <i class="fas fa-truck-moving rsu-card-icon"></i>
                    </div>
                    <div class="rsu-card-footer">
                        Acceder <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('admin.personnels.index') }}" class="rsu-module-card rsu-card-green">
                    <div class="rsu-card-content">
                        <span class="rsu-card-label">Módulo 02</span>
                        <h4>Personal</h4>
                        <p>Tipos de personal, personal, contratos, asistencias y vacaciones.</p>
                        <i class="fas fa-users-cog rsu-card-icon"></i>
                    </div>
                    <div class="rsu-card-footer">
                        Acceder <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="{{ route('admin.schedules.index') }}" class="rsu-module-card rsu-card-yellow">
                    <div class="rsu-card-content">
                        <span class="rsu-card-label">Módulo 03</span>
                        <h4>Programación</h4>
                        <p>Turnos, zonas, feriados, grupos de personal y programación.</p>
                        <i class="fas fa-route rsu-card-icon"></i>
                    </div>
                    <div class="rsu-card-footer">
                        Acceder <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <a href="#" onclick="return false;" class="rsu-module-card rsu-card-red rsu-card-disabled">
                    <div class="rsu-card-content">
                        <span class="rsu-card-label">Módulo 04</span>
                        <h4>Cambios</h4>
                        <p>Motivos y cambios.</p>
                        <i class="fas fa-exchange-alt rsu-card-icon"></i>
                    </div>
                    <div class="rsu-card-footer">
                        Próximamente <i class="fas fa-clock ml-1"></i>
                    </div>
                </a>
            </div>

        </div>
    </div>
@stop
