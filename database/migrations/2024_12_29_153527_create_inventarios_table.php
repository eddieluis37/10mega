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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes');
            $table->unsignedBigInteger('product_id')->nullable(); // Relacionar con los productos
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            $table->decimal('cantidad_inicial', 18, 2)->default(0)->nullable(); // Cantidad inicial al inicio del período.
            $table->decimal('cantidad_final', 18, 2)->default(0)->nullable(); // Cantidad al cierre del período.

            $table->decimal('costo_unitario', 18, 2)->default(0)->nullable(); // Costo unitario promedio.
            $table->decimal('costo_total', 18, 2)->default(0)->nullable(); // Costo total del inventario final.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
