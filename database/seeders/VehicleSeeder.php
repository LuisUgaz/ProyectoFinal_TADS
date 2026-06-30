<?php

namespace Database\Seeders;

use App\Models\BrandModel;
use App\Models\Vehicle;
use App\Models\VehicleColor;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $models = BrandModel::all();
        $types = VehicleType::all();
        $colors = VehicleColor::all();

        $vehicles = [
            [
                'plate' => 'V1C-789',
                'code' => 'CR-001',
                'name' => 'Recolector Norte 01',
                'year' => 2021,
                'mileage' => 28450,
                'load_capacity' => 8.00,
                'fuel_capacity' => 120.00,
                'compaction_capacity' => 6.50,
                'passenger_capacity' => 3,
                'description' => 'Camión recolector asignado a rutas urbanas del sector norte.',
            ],
            [
                'plate' => 'A5T-456',
                'code' => 'CR-002',
                'name' => 'Recolector Centro 02',
                'year' => 2020,
                'mileage' => 35680,
                'load_capacity' => 7.50,
                'fuel_capacity' => 110.00,
                'compaction_capacity' => 6.00,
                'passenger_capacity' => 3,
                'description' => 'Unidad para recolección domiciliaria en zonas céntricas.',
            ],
            [
                'plate' => 'M9B-123',
                'code' => 'CP-003',
                'name' => 'Compactador Sur 03',
                'year' => 2022,
                'mileage' => 19800,
                'load_capacity' => 9.00,
                'fuel_capacity' => 130.00,
                'compaction_capacity' => 7.20,
                'passenger_capacity' => 3,
                'description' => 'Vehículo compactador para rutas de alta generación de residuos.',
            ],
            [
                'plate' => 'X4D-001',
                'code' => 'CR-004',
                'name' => 'Recolector Oeste 04',
                'year' => 2019,
                'mileage' => 42100,
                'load_capacity' => 7.00,
                'fuel_capacity' => 105.00,
                'compaction_capacity' => 5.80,
                'passenger_capacity' => 3,
                'description' => 'Unidad operativa para rutas alternas y zonas de difícil acceso.',
            ],
            [
                'plate' => 'P2R-555',
                'code' => 'VF-005',
                'name' => 'Volquete Apoyo 05',
                'year' => 2018,
                'mileage' => 48750,
                'load_capacity' => 10.00,
                'fuel_capacity' => 140.00,
                'compaction_capacity' => 0.00,
                'passenger_capacity' => 2,
                'description' => 'Volquete de apoyo para traslado de residuos voluminosos.',
            ],
            [
                'plate' => 'B7Y-888',
                'code' => 'CR-006',
                'name' => 'Recolector Reserva 06',
                'year' => 2023,
                'mileage' => 12200,
                'load_capacity' => 8.50,
                'fuel_capacity' => 125.00,
                'compaction_capacity' => 6.80,
                'passenger_capacity' => 2,
                'description' => 'Unidad de reserva para reprogramaciones o contingencias.',
            ],
            [
                'plate' => 'C9K-222',
                'code' => 'CM-007',
                'name' => 'Camioneta Supervisión 07',
                'year' => 2021,
                'mileage' => 24400,
                'load_capacity' => 1.20,
                'fuel_capacity' => 70.00,
                'compaction_capacity' => 0.00,
                'passenger_capacity' => 4,
                'description' => 'Camioneta destinada a supervisión de rutas y apoyo operativo.',
            ],
            [
                'plate' => 'Z1X-999',
                'code' => 'MF-008',
                'name' => 'Motofurgón Pasajes 08',
                'year' => 2022,
                'mileage' => 9800,
                'load_capacity' => 0.80,
                'fuel_capacity' => 25.00,
                'compaction_capacity' => 0.00,
                'passenger_capacity' => 2,
                'description' => 'Motofurgón para recolección en calles estrechas y pasajes.',
            ],
        ];

        foreach ($vehicles as $index => $vehicle) {
            Vehicle::updateOrCreate(
                ['plate' => $vehicle['plate']],
                [
                    'code' => $vehicle['code'],
                    'name' => $vehicle['name'],
                    'brand_model_id' => $models->get($index % $models->count())->id,
                    'vehicle_type_id' => $types->get($index % $types->count())->id,
                    'vehicle_color_id' => $colors->get($index % $colors->count())->id,
                    'year' => $vehicle['year'],
                    'engine_number' => 'ENG-' . $vehicle['code'],
                    'chassis_number' => 'CHS-' . $vehicle['code'],
                    'mileage' => $vehicle['mileage'],
                    'status' => 'Activo',
                    'load_capacity' => $vehicle['load_capacity'],
                    'fuel_capacity' => $vehicle['fuel_capacity'],
                    'compaction_capacity' => $vehicle['compaction_capacity'],
                    'passenger_capacity' => $vehicle['passenger_capacity'],
                    'description' => $vehicle['description'],
                ]
            );
        }
    }
}