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
        Schema::create('movimiento_inventarios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['compensadores', 'Venta', 'Traslado'])->default('compensadores');
            $table->unsignedBigInteger('entidad_referencia_id');
            
            $table->unsignedBigInteger('bodega_origen_id')->nullable();
            $table->foreign('bodega_origen_id')->references('id')->on('stores');

            $table->unsignedBigInteger('bodega_destino_id')->nullable();
            $table->foreign('bodega_destino_id')->references('id')->on('stores');

            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes');
            $table->dateTime('fecha')->nullable();

            $table->decimal('cantidad', 18, 2)->default(0)->nullable();
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};
