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
        Schema::create('subscripciones', function (Blueprint $table) {
            $table->id();
            $table->string('id_suscripcion');
            $table->string('title_suscripcion');
            // $table->string('current_period_start');
            $table->string('customer_email')->nullable();
            $table->string('customer');
            $table->boolean('status_factura');
            $table->string('customer_id_object_subs');
            $table->foreign('customer_id_object_subs')->references('objectId')->on('users');

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
        Schema::dropIfExists('subscripciones');
    }
};
