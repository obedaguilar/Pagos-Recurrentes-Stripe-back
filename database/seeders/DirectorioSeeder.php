<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('directorios')->insert(
            [
                [
                    'nombre_completo' => 'Vanessa Martinez',
                    'direccion' => 'Calle 1',
                    'telefono' => '1234567898',
                    'email' => 'test1@hotmail.com',

                ],
                [
                    'nombre_completo' => 'Pedro Martinez',
                    'direccion' => 'Calle 2',
                    'telefono' => '1234567887',
                    'email' => 'test2@hotmail.com',

                ],
                [
                    'nombre_completo' => 'Pablo Martinez',
                    'direccion' => 'Calle 3',
                    'telefono' => '1234567876',
                    'email' => 'test3@hotmail.com',

                ],
                [
                    'nombre_completo' => 'Juan Martinez',
                    'direccion' => 'Calle 4',
                    'telefono' => '1234567825',
                    'email' => 'test4@hotmail.com',

                ],
                [
                    'nombre_completo' => 'Simon Martinez',
                    'direccion' => 'Calle 5',
                    'telefono' => '1234567834',
                    'email' => 'test5@hotmail.com',

                ],
                [
                    'nombre_completo' => 'Magdalena Martinez',
                    'direccion' => 'Calle 6',
                    'telefono' => '1234567845',
                    'email' => 'test6@hotmail.com',

                ],

            ]

        );
    }
}
