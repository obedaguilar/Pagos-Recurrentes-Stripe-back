<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas_clientes', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_factura')->nullable();
            $table->string('mes_factura')->nullable();
            $table->string('nombre_cliente')->nullable();
            $table->uuid('user_invoice_objectId');
            $table->string('email_cliente')->nullable();
            $table->string('nombre_factura')->nullable();
            $table->string('url_factura')->nullable();
            $table->foreign('user_invoice_objectId')->references('objectId')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas_clientes');
    }
};
