<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('caja_salida_efectivo', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('caja_id');           
            $table->foreign('caja_id')->references('id')->on('cajas');

            $table->decimal('vr_efectivo', 12, 0)->default(0)->nullable();
            
            $table->longText('concepto');

            $table->dateTime('fecha_hora_salida')->nullable();
            
            $table->unsignedBigInteger('third_id')->nullable();           
            $table->foreign('third_id')->references('id')->on('thirds'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_salida_efectivo');
    }
};
