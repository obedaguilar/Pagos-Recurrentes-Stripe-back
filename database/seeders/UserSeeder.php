<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                [
                    'objectId' => '1ec7e12f-58fe-64f8-ab97-2cf05d694255',
                    'nombre' => 'admin',
                    'apellidoP' => 'superadmin',
                    'apellidoM' => 'superadmin',
                    'password' => Hash::make('1234567890'),
                    'email' => 'admin@iknesoft.com',
                    'telefono' => '1234567890',
                    'user_roles_objectId' => '067e6162-3b6f-4ae2-a171-2470b63dff00',
                    'isDeleted' => false,
                    'isActive' => true,

                ],
            ]);

        $uuid = '1ec7e14f-57fe-64f1-ab46-7cf95d694235';
        $user = User::create(
            [
            'objectId' => '1ec7e14f-57fe-64f1-ab46-7cf95d694235',
            'nombre' => 'Cliente',
            'apellidoP' => 'ClienteA',
            'apellidoM' => 'ClienteB',
            'password' => Hash::make('1234567890'),
            'email' => 'cliente@iknesoft.com',
            'telefono' => '1234567890',
            'user_roles_objectId' => config('app.rolesCall.cliente'),
            'isDeleted' => false,
            'isActive' => true,

            ]
        );
    }
}
