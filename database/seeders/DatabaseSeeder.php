<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Planes;
use Dotenv\Repository\Adapter\WriterInterface;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Planes::create([
            'name'        => 'Monthly',
            'slug'        => 'monthly',
            'stripe_plan' => 'monthly',
            'cost'        => 30,
            'description' => 'Un mes entero de acceso al hosting'
        ]);
        Planes::create([
            'name'        => 'Yearly',
            'slug'        => 'yearly',
            'stripe_plan' => 'yearly',
            'cost'        => 60,
            'description' => 'Un año de acceso al hosting'
        ]);
    }
}
