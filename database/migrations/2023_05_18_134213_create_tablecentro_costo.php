<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCentroCosto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centro_costo', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('direccion', 150, 0)->nullable(); 
            $table->boolean('status')->parent_select()->default(true)->nullable();
            $table->string('prefijo', 50, 0)->nullable();
            $table->string('resolucion_dian', 50, 0)->nullable();            
            $table->integer('desde')->nullable();
            $table->integer('hasta')->nullable();
            $table->dateTime('fecha_inicial')->nullable();
            $table->dateTime('fecha_final')->nullable();
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
        Schema::dropIfExists('centro_costo');
    }
}
