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
        Schema::create('alistar_toppings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users');

            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete("cascade");

            $table->unsignedBigInteger('lote_id')->nullable();
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete("cascade");

            $table->unsignedBigInteger('lote_hijos_id')->nullable();
            $table->foreign('lote_hijos_id')->references('id')->on('lotes')->onDelete("cascade");

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete("cascade");

            $table->unsignedBigInteger('meatcut_id')->nullable();
            $table->foreign('meatcut_id')->references('id')->on('meatcuts')->onDelete("cascade");

            $table->decimal('cantidad_padre_a_procesar', 18, 2)->default(0);

            $table->decimal('stock_actual_padre', 18, 2)->default(0);
            $table->decimal('nuevo_stock_padre', 18, 2)->default(0);
            $table->decimal('costo_unitario_padre', 18, 2)->default(0);
            $table->decimal('total_costo', 18, 2)->default(0);

            $table->decimal('total_cost_transformation', 18, 2)->nullable();
            $table->enum('inventario', ['pending', 'added'])->default('pending');
            $table->date('fecha_alistamiento');
            $table->date('fecha_cierre')->nullable();

            $table->boolean('status')->parent_select()->default(true)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alistar_toppings');
    }
};
