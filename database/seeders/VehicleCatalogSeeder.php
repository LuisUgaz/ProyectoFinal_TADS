<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleColor;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Blanco', 'code' => '#FFFFFF', 'description' => 'Blanco estándar'],
            ['name' => 'Negro', 'code' => '#000000', 'description' => 'Negro obsidiana'],
            ['name' => 'Gris Plata', 'code' => '#C0C0C0', 'description' => 'Gris metalizado'],
            ['name' => 'Rojo', 'code' => '#FF0000', 'description' => 'Rojo brillante'],
            ['name' => 'Azul', 'code' => '#0000FF', 'description' => 'Azul municipal'],
        ];

        foreach ($colors as $color) {
            VehicleColor::updateOrCreate(
                ['name' => $color['name']],
                $color
            );
        }

        $types = [
            ['name' => 'Camión recolector', 'description' => 'Vehículo principal de recolección de basura'],
            ['name' => 'Compactador', 'description' => 'Vehículo con sistema de compactado de residuos'],
            ['name' => 'Volquete', 'description' => 'Vehículo de carga pesada para escombros'],
            ['name' => 'Camioneta', 'description' => 'Vehículo de supervisión y transporte de personal'],
            ['name' => 'Motofurgón', 'description' => 'Vehículo ligero para recolección en pasajes estrechos'],
            ['name' => 'Cisterna', 'description' => 'Vehículo para transporte de agua y riego de áreas verdes'],
        ];

        foreach ($types as $type) {
            VehicleType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }

        $brands = [
            [
                'name' => 'Toyota',
                'description' => 'Marca japonesa reconocida por su durabilidad',
                'models' => [
                    ['name' => 'Hilux', 'code' => 'TOY-HIL-01', 'description' => 'Camioneta 4x4'],
                    ['name' => 'Dyna', 'code' => 'TOY-DYN-02', 'description' => 'Camión de carga ligera'],
                ],
            ],
            [
                'name' => 'Volvo',
                'description' => 'Fabricante sueco líder en vehículos pesados',
                'models' => [
                    ['name' => 'FH16', 'code' => 'VOL-FH-03', 'description' => 'Camión recolector pesado'],
                    ['name' => 'FMX', 'code' => 'VOL-FMX-04', 'description' => 'Vehículo para construcción'],
                ],
            ],
            [
                'name' => 'Mercedes-Benz',
                'description' => 'Calidad y potencia alemana',
                'models' => [
                    ['name' => 'Atego', 'code' => 'MB-ATE-05', 'description' => 'Camión recolector mediano'],
                    ['name' => 'Actros', 'code' => 'MB-ACT-06', 'description' => 'Vehículo de gran tonelaje'],
                ],
            ],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::updateOrCreate(
                ['name' => $brandData['name']],
                ['description' => $brandData['description']]
            );

            foreach ($brandData['models'] as $modelData) {
                $brand->models()->updateOrCreate(
                    ['code' => $modelData['code']],
                    [
                        'name' => $modelData['name'],
                        'description' => $modelData['description'],
                    ]
                );
            }
        }
    }
}