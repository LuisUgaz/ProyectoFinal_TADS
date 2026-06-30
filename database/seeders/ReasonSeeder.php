<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name' => 'Reprogramación', 'description' => 'Cambio de programación por necesidades operativas.'],
            ['name' => 'Avería del vehículo', 'description' => 'El vehículo asignado presenta fallas mecánicas.'],
            ['name' => 'Mantenimiento preventivo', 'description' => 'Vehículo fuera de servicio por mantenimiento programado.'],
            ['name' => 'Mantenimiento correctivo', 'description' => 'Vehículo enviado a reparación por avería.'],
            ['name' => 'Falta de personal', 'description' => 'Ausencia de personal por vacaciones, descanso o renuncia.'],
            ['name' => 'Descanso médico', 'description' => 'Cambio debido a incapacidad temporal del trabajador.'],
            ['name' => 'Vacaciones', 'description' => 'Reemplazo temporal del personal por vacaciones.'],
            ['name' => 'Capacitación', 'description' => 'El personal asiste a una capacitación institucional.'],
            ['name' => 'Emergencia', 'description' => 'Cambio por una situación de emergencia no planificada.'],
            ['name' => 'Evento municipal', 'description' => 'Modificación de rutas por actividades organizadas por la municipalidad.'],
            ['name' => 'Condiciones climáticas', 'description' => 'Reprogramación debido a lluvias u otros fenómenos climáticos.'],
            ['name' => 'Incremento de residuos', 'description' => 'Refuerzo temporal por aumento de residuos sólidos.'],
            ['name' => 'Solicitud vecinal', 'description' => 'Cambio realizado por solicitud de los vecinos.'],
            ['name' => 'Accidente de tránsito', 'description' => 'Vehículo involucrado en un accidente durante la jornada.'],
            ['name' => 'Otros', 'description' => 'Motivo no contemplado en las categorías anteriores.'],
        ];

        foreach ($reasons as $reason) {
            Reason::updateOrCreate(
                ['name' => $reason['name']],
                ['description' => $reason['description']]
            );
        }
    }
}