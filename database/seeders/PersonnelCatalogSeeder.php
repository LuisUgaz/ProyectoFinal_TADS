<?php

namespace Database\Seeders;

use App\Models\PersonnelType;
use Illuminate\Database\Seeder;

class PersonnelCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Conductor',
                'description' => 'Personal autorizado para conducir vehículos',
            ],
            [
                'name' => 'Ayudante',
                'description' => 'Personal de apoyo en la recolección',
            ],
        ];

        foreach ($types as $type) {
            PersonnelType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}