<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users');

            $table->unsignedBigInteger('bodega_origen_id')->nullable();
            $table->foreign('bodega_origen_id')->references('id')->on('stores');

            $table->unsignedBigInteger('bodega_destino_id')->nullable();
            $table->foreign('bodega_destino_id')->references('id')->on('stores');

            $table->decimal('cantidad_total_traslado', 18, 2)->nullable();

            $table->enum('inventario', ['pending', 'added'])->default('pending');
            $table->date('fecha_tranfer');
            $table->date('fecha_cierre')->nullable();

            $table->boolean('status')->parent_select()->default(true)->nullable();          
   
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
        Schema::dropIfExists('transfers');
    }
}
