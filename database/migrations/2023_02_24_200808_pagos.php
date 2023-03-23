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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_cliente')->nullable();
            $table->string('email_cliente')->nullable();
            $table->string('status_pago')->nullable();
            $table->string('id_pago')->nullable();
            $table->string('amount')->nullable();
            $table->string('suscripcion')->nullable();
            $table->string('stripe_id_plan')->nullable();
            $table->string('plan_name')->nullable();
            $table->string('fecha_pago')->nullable();
            $table->string('fecha_inicio');
            $table->string('fecha_vencimiento');
            // $table->foreign('stripe_id_plan')->references('stripe_id')->on('plans');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
