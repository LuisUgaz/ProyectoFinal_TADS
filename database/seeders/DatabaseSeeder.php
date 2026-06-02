<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VehicleColor;
use App\Models\VehicleType;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Vehicle;
use App\Models\PersonnelType;
use App\Models\Personnel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuario Administrador
        User::factory()->create([
            'name' => 'Administrador TADS',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Colores de Vehículos
        $colors = [
            ['name' => 'Blanco', 'code' => '#FFFFFF', 'description' => 'Blanco estándar'],
            ['name' => 'Negro', 'code' => '#000000', 'description' => 'Negro obsidiana'],
            ['name' => 'Gris Plata', 'code' => '#C0C0C0', 'description' => 'Gris metalizado'],
            ['name' => 'Rojo', 'code' => '#FF0000', 'description' => 'Rojo brillante'],
            ['name' => 'Azul', 'code' => '#0000FF', 'description' => 'Azul municipal'],
        ];
        foreach ($colors as $color) {
            VehicleColor::create($color);
        }

        // 3. Tipos de Vehículo
        $types = [
            ['name' => 'Camión recolector', 'description' => 'Vehículo principal de recolección de basura'],
            ['name' => 'Compactador', 'description' => 'Vehículo con sistema de compactado de residuos'],
            ['name' => 'Volquete', 'description' => 'Vehículo de carga pesada para escombros'],
            ['name' => 'Camioneta', 'description' => 'Vehículo de supervisión y transporte de personal'],
        ];
        foreach ($types as $type) {
            VehicleType::create($type);
        }

        // 4. Marcas y Modelos
        $brands = [
            [
                'name' => 'Toyota',
                'description' => 'Marca japonesa reconocida por su durabilidad',
                'models' => [
                    ['name' => 'Hilux', 'code' => 'TOY-HIL-01', 'description' => 'Camioneta 4x4'],
                    ['name' => 'Dyna', 'code' => 'TOY-DYN-02', 'description' => 'Camión de carga ligera'],
                ]
            ],
            [
                'name' => 'Volvo',
                'description' => 'Fabricante sueco líder en vehículos pesados',
                'models' => [
                    ['name' => 'FH16', 'code' => 'VOL-FH-03', 'description' => 'Camión recolector pesado'],
                    ['name' => 'FMX', 'code' => 'VOL-FMX-04', 'description' => 'Vehículo para construcción'],
                ]
            ],
            [
                'name' => 'Mercedes-Benz',
                'description' => 'Calidad y potencia alemana',
                'models' => [
                    ['name' => 'Atego', 'code' => 'MB-ATE-05', 'description' => 'Camión recolector mediano'],
                    ['name' => 'Actros', 'code' => 'MB-ACT-06', 'description' => 'Vehículo de gran tonelaje'],
                ]
            ],
        ];
        // 4. Marcas y Modelos
        foreach ($brands as $bData) {
            $brand = Brand::updateOrCreate(
                ['name' => $bData['name']],
                ['description' => $bData['description']]
            );

            foreach ($bData['models'] as $mData) {
                $brand->models()->updateOrCreate(
                    ['code' => $mData['code']],
                    [
                        'name' => $mData['name'],
                        'description' => $mData['description']
                    ]
                );
            }
        }

        // 5. Vehículos (Generación de 5 vehículos de prueba)
        $allModels = BrandModel::all();
        $allTypes = VehicleType::all();
        $allColors = VehicleColor::all();

        $plates = ['V1C-789', 'A5T-456', 'M9B-123', 'X4D-001', 'P2R-555'];

        for ($i = 0; $i < 5; $i++) {
            Vehicle::create([
                'brand_model_id' => $allModels->random()->id,
                'vehicle_type_id' => $allTypes->random()->id,
                'vehicle_color_id' => $allColors->random()->id,
                'plate' => $plates[$i],
                'year' => rand(2015, 2024),
                'engine_number' => 'ENG-' . rand(100000, 999999),
                'chassis_number' => 'CHS-' . rand(100000, 999999),
                'mileage' => rand(1000, 50000),
                'status' => 'Activo'
            ]);
        }

        // 6. Tipo de personal
        $personnelTypes = [
            [
                'name' => 'Conductor',
                'description' => 'Personal autorizado para conducir vehículos'
            ],
            [
                'name' => 'Ayudante',
                'description' => 'Personal de apoyo en la recolección'
            ],
        ];

        foreach ($personnelTypes as $type) {
            PersonnelType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }

        // 7. Personal
        $conductor = PersonnelType::where('name', 'Conductor')->first();
        $ayudante = PersonnelType::where('name', 'Ayudante')->first();

        Personnel::updateOrCreate(
            ['dni' => '87654321'],
            [
                'personnel_type_id' => $ayudante->id,
                'names' => 'Pedro Fernando',
                'lastnames' => 'Montenegro Quispe',
                'birthdate' => '1995-09-16',
                'phone' => '987654321',
                'email' => 'pedro.montenegro@gmail.com',
                'status' => 'Activo',
                'password' => Hash::make('654321'),
                'address' => 'Av. Grau 456',
                'photo_path' => null,
                'license_path' => null,
            ]
        );

    }
}
