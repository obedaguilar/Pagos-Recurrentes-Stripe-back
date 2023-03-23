<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'objectId' => '067e6162-3b6f-4ae2-a171-2470b63dff00',
                'name' => 'SuperAdmin',
            ],
            [
                'objectId' => '8adf4c04-3f10-40cb-aefa-f57f93440c5b',
                'name' => 'Admin',
            ],
            [
                'objectId' => '77f8c365-d2e3-471f-9f5a-98dfb702c000',
                'name' => 'Cliente',
            ],

        ]);
    }
}
