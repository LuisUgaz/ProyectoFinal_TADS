<?php

namespace Database\Seeders;

use App\Models\Personnel;
use App\Models\PersonnelType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        $conductorType = PersonnelType::where('name', 'Conductor')->first();
        $ayudanteType = PersonnelType::where('name', 'Ayudante')->first();

        $personnelData = [
            [
                'dni' => '12345678',
                'type_id' => $conductorType->id,
                'names' => 'Juan Alberto',
                'lastnames' => 'Ramos Soto',
                'birthdate' => '1985-05-20',
                'phone' => '955443322',
                'email' => 'juan.ramos@gmail.com',
                'address' => 'Calle Las Flores 123',
                'salary' => 2500.00,
                'contract_type' => 'Nombrado',
            ],
            [
                'dni' => '44556677',
                'type_id' => $conductorType->id,
                'names' => 'Carlos Mario',
                'lastnames' => 'Vargas Llosa',
                'birthdate' => '1990-12-10',
                'phone' => '999888777',
                'email' => 'carlos.vargas@gmail.com',
                'address' => 'Urb. Los Pinos B-12',
                'salary' => 2800.00,
                'contract_type' => 'Temporal',
            ],
            [
                'dni' => '22334455',
                'type_id' => $conductorType->id,
                'names' => 'Ricardo',
                'lastnames' => 'Gomez Tapia',
                'birthdate' => '1988-04-14',
                'phone' => '933445566',
                'email' => 'ricardo.gomez@gmail.com',
                'address' => 'Dirección de prueba 22334455',
                'salary' => 2600.00,
                'contract_type' => 'Nombrado',
            ],
            [
                'dni' => '33445566',
                'type_id' => $conductorType->id,
                'names' => 'Roberto',
                'lastnames' => 'Sánchez Diaz',
                'birthdate' => '1987-08-22',
                'phone' => '944556677',
                'email' => 'roberto.sanchez@gmail.com',
                'address' => 'Dirección de prueba 33445566',
                'salary' => 2700.00,
                'contract_type' => 'Permanente',
            ],
            [
                'dni' => '44556688',
                'type_id' => $conductorType->id,
                'names' => 'Luis Alberto',
                'lastnames' => 'Perez Prado',
                'birthdate' => '1986-11-30',
                'phone' => '955667788',
                'email' => 'luis.perez@gmail.com',
                'address' => 'Dirección de prueba 44556688',
                'salary' => 2550.00,
                'contract_type' => 'Nombrado',
            ],
            [
                'dni' => '55667788',
                'type_id' => $conductorType->id,
                'names' => 'Miguel Angel',
                'lastnames' => 'Rodriguez Franco',
                'birthdate' => '1984-02-18',
                'phone' => '966778899',
                'email' => 'miguel.rodriguez@gmail.com',
                'address' => 'Dirección de prueba 55667788',
                'salary' => 2650.00,
                'contract_type' => 'Permanente',
            ],

            [
                'dni' => '87654321',
                'type_id' => $ayudanteType->id,
                'names' => 'Pedro Fernando',
                'lastnames' => 'Montenegro Quispe',
                'birthdate' => '1995-09-16',
                'phone' => '987654321',
                'email' => 'pedro.montenegro@gmail.com',
                'address' => 'Av. Grau 456',
                'salary' => 1200.00,
                'contract_type' => 'Permanente',
            ],
            [
                'dni' => '11223344',
                'type_id' => $ayudanteType->id,
                'names' => 'Maria Elena',
                'lastnames' => 'Paz Soldan',
                'birthdate' => '1998-03-25',
                'phone' => '912345678',
                'email' => 'maria.paz@gmail.com',
                'address' => 'Jr. Junin 789',
                'salary' => 1100.00,
                'contract_type' => 'Temporal',
            ],
            [
                'dni' => '66778899',
                'type_id' => $ayudanteType->id,
                'names' => 'Jorge',
                'lastnames' => 'Castro Ruiz',
                'birthdate' => '1996-07-12',
                'phone' => '977889900',
                'email' => 'jorge.castro@gmail.com',
                'address' => 'Dirección de prueba 66778899',
                'salary' => 1300.00,
                'contract_type' => 'Permanente',
            ],
            [
                'dni' => '77889900',
                'type_id' => $ayudanteType->id,
                'names' => 'Raul',
                'lastnames' => 'Mendoza Lima',
                'birthdate' => '1997-06-10',
                'phone' => '988990011',
                'email' => 'raul.mendoza@gmail.com',
                'address' => 'Dirección de prueba 77889900',
                'salary' => 1350.00,
                'contract_type' => 'Nombrado',
            ],
            [
                'dni' => '88990011',
                'type_id' => $ayudanteType->id,
                'names' => 'Fernando',
                'lastnames' => 'Soto Mayor',
                'birthdate' => '1993-09-19',
                'phone' => '999001122',
                'email' => 'fernando.soto@gmail.com',
                'address' => 'Dirección de prueba 88990011',
                'salary' => 1250.00,
                'contract_type' => 'Permanente',
            ],
            [
                'dni' => '99001122',
                'type_id' => $ayudanteType->id,
                'names' => 'Andres',
                'lastnames' => 'Cueva Bravo',
                'birthdate' => '1994-01-25',
                'phone' => '900112233',
                'email' => 'andres.cueva@gmail.com',
                'address' => 'Dirección de prueba 99001122',
                'salary' => 1400.00,
                'contract_type' => 'Nombrado',
            ],
            [
                'dni' => '10112233',
                'type_id' => $ayudanteType->id,
                'names' => 'Sebastian',
                'lastnames' => 'Vela Ortiz',
                'birthdate' => '1999-05-11',
                'phone' => '911223344',
                'email' => 'sebastian.vela@gmail.com',
                'address' => 'Dirección de prueba 10112233',
                'salary' => 1200.00,
                'contract_type' => 'Permanente',
            ],
            [
                'dni' => '20223344',
                'type_id' => $ayudanteType->id,
                'names' => 'Kevin',
                'lastnames' => 'Rojas Peña',
                'birthdate' => '1998-10-07',
                'phone' => '922334455',
                'email' => 'kevin.rojas@gmail.com',
                'address' => 'Dirección de prueba 20223344',
                'salary' => 1150.00,
                'contract_type' => 'Temporal',
            ],
            [
                'dni' => '30334455',
                'type_id' => $ayudanteType->id,
                'names' => 'Diego',
                'lastnames' => 'Torres Luna',
                'birthdate' => '1997-03-29',
                'phone' => '933445566',
                'email' => 'diego.torres@gmail.com',
                'address' => 'Dirección de prueba 30334455',
                'salary' => 1100.00,
                'contract_type' => 'Temporal',
            ],
            [
                'dni' => '40445566',
                'type_id' => $ayudanteType->id,
                'names' => 'Mateo',
                'lastnames' => 'Silva Cruz',
                'birthdate' => '1996-12-13',
                'phone' => '944556677',
                'email' => 'mateo.silva@gmail.com',
                'address' => 'Dirección de prueba 40445566',
                'salary' => 1100.00,
                'contract_type' => 'Temporal',
            ],
        ];

        foreach ($personnelData as $data) {
            $personnel = Personnel::updateOrCreate(
                ['dni' => $data['dni']],
                [
                    'personnel_type_id' => $data['type_id'],
                    'names' => $data['names'],
                    'lastnames' => $data['lastnames'],
                    'birthdate' => $data['birthdate'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'status' => 'Activo',
                    'password' => Hash::make($data['dni']),
                    'address' => $data['address'],
                    'photo_path' => null,
                    'license_number' => $data['type_id'] == $conductorType->id ? 'C' . $data['dni'] : null,
                ]
            );

            $personnel->contracts()->updateOrCreate(
                [
                    'personnel_id' => $personnel->id,
                    'is_active' => true,
                ],
                [
                    'type' => $data['contract_type'],
                    'start_date' => now()->subMonths(6)->format('Y-m-d'),
                    'end_date' => $data['contract_type'] === 'Temporal'
                        ? now()->addMonths(6)->format('Y-m-d')
                        : null,
                    'salary' => $data['salary'],
                    'probation_period' => '3 meses',
                    'is_active' => true,
                ]
            );
        }
    }
}