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

            $table->foreignId('compensador_id')->nullable()->constrained('compensadores')->onDelete('set null');

            //La relación entre movimiento_inventarios y compensadores permite rastrear información específica como facturas.

            //$table->unsignedBigInteger('entidad_referencia_id');

            $table->unsignedBigInteger('store_origen_id')->nullable();
            $table->foreign('store_origen_id')->references('id')->on('stores');

            $table->unsignedBigInteger('store_destino_id')->nullable();
            $table->foreign('store_destino_id')->references('id')->on('stores');

            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade'); //Relación Lotes - Productos - Categorías

            /*   Cada lote está relacionado con un producto, y ese producto está vinculado a una categoría.
             Esto asegura que cada lote esté indirectamente asociado a una categoría.
           Integridad referencial: Uso de onDelete('cascade') para que las eliminaciones en categorías o 
           productos afecten a lotes relacionados. */

            //  $table->unsignedBigInteger('lote_id')->nullable();
            //  $table->foreign('lote_id')->references('id')->on('lotes');

            $table->unsignedBigInteger('product_id')->nullable(); // Relacionar con un producto específico
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

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
