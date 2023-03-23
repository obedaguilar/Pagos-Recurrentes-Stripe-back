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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('objectId')->primary();
            $table->string('nombre');
            $table->string('apellidoP')->nullable();
            $table->string('apellidoM')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->unique();
            $table->string('telefono');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('api_token')->nullable();
            // $table->boolean('is_admin')->default(false);
            $table->uuid('user_roles_objectId');
            $table->foreign('user_roles_objectId')->references('objectId')->on('roles');
            $table->boolean('isDeleted')->default(true);
            $table->boolean('isActive')->default(true);
            $table->string('documento')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->rememberToken();
            // $table->uuid('createdBy')->nullable();
            // $table->uuid('updatedBy')->nullable();
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
        Schema::dropIfExists('users');
    }
};
