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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('metodo_pago_id')->nullable();
            $table->string('description_payment')->nullable();
            $table->string('tipo_card')->nullable();
            $table->string('country_stripe')->nullable();
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('fondos')->nullable();
            $table->integer('cuatro_digitos')->nullable();
            $table->string('fecha_creacion')->nullable();
            $table->string('customer_id_object')->nullable();
            $table->foreign('customer_id_object')->references('objectId')->on('users');
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
        Schema::dropIfExists('cards');
    }
};
