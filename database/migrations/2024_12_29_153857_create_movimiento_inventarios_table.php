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
            $table->enum('tipo', ['compensadores', 'desposteres', 'venta', 'traslado'])->default('compensadores'); //Tipo de movimiento (compra, traslado, alistamiento, etc.).

            $table->foreignId('compensador_id')->nullable()->constrained('compensadores')->onDelete('set null');
            $table->foreignId('desposteres_id')->nullable()->constrained('desposteres')->onDelete('set null');

            //La relación entre movimiento_inventarios y compensadores permite rastrear información específica como facturas.

            //$table->unsignedBigInteger('entidad_referencia_id');

            $table->unsignedBigInteger('store_origen_id')->nullable();
            $table->foreign('store_origen_id')->references('id')->on('stores');

            $table->unsignedBigInteger('store_destino_id')->nullable();
            $table->foreign('store_destino_id')->references('id')->on('stores');

            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade'); //Relación Lotes - Productos - Categorías

            $table->unsignedBigInteger('product_id')->nullable(); // Relacionar con un producto específico
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->decimal('cantidad', 18, 2)->default(0)->nullable(); // Cantidad afectada.
            $table->decimal('costo_unitario', 18, 2)->default(0)->nullable(); // Costo unitario promedio.
            $table->decimal('total', 18, 2)->default(0)->nullable(); // Valor total del movimiento..

            $table->dateTime('fecha')->nullable();

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
