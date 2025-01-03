<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCompensadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compensadores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users');

            $table->unsignedBigInteger('centrocosto_id')->nullable();
            $table->foreign('centrocosto_id')->references('id')->on('centro_costo');
            
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');

            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes');

            $table->unsignedBigInteger('thirds_id')->nullable();
            $table->foreign('thirds_id')->references('id')->on('thirds');

          
            $table->string('factura')->nullable();
            
            $table->date('fecha_ingreso')->nullable();

            $table->date('fecha_compensado')->nullable();
            $table->date('fecha_cierre')->nullable();

            $table->boolean('status')->parent_select()->default(false)->nullable();            
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
        Schema::dropIfExists('compensadores');
    }
}
