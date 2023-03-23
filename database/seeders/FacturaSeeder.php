<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('facturas_clientes')->insert([
            'num_factura' => 1,
            'fecha_factura' => date('Y-m-d H:i:s'),
            'mes_factura' => 'Febrero',
            'nombre_cliente' => 'Obed Aguilar Vazquez',
            'user_invoice_objectId' => '1edb49bd-c452-6650-926b-d8bbc1d2c488',
            'email_cliente' => 'obedaguilarv@hotmail.com',
            'nombre_factura' => 'Factura 1',
            'url_factura' => 'http://localhost:8080/uploads/public/acusecancelacion.pdf',
        ]);

    }
}
