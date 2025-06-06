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

            $table->unique(['store_id', 'lote_id', 'product_id'], 'inventario_unique');

            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');

            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes');

            $table->unsignedBigInteger('product_id')->nullable(); // Relacionar con los productos
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->decimal('cantidad_inventario_inicial', 10, 2)->default(0)->nullable()->comment('Cantidad inicial que tiene el inventario como stock al iniciar.');
            $table->decimal('cantidad_compra_lote', 10, 2)->default(0)->nullable();
            $table->decimal('cantidad_alistamiento', 10, 2)->default(0)->nullable();
            $table->decimal('cantidad_compra_prod', 10, 2)->default(0)->nullable();
            $table->decimal('cantidad_prod_term', 10, 2)->default(0)->nullable();

            $table->decimal('cantidad_traslado', 10, 2)->default(0)->nullable();


            $table->decimal('cantidad_venta', 10, 2)->default(0)->nullable();
            $table->decimal('cantidad_notacredito', 10, 2)->default(0)->nullable();

            $table->decimal('stock_ideal', 15, 2)->default(0)->nullable()->comment('Cálculo de la suma de la compra compensada + inventario_inicial...');
            $table->decimal('stock_fisico', 15, 2)->default(0)->nullable()->comment('cantidad real en fisico');
            $table->decimal('cantidad_diferencia', 15, 2)->default(0)->nullable()->comment('es el resultado de restar el stock_ideal menos el stock fisico');


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
