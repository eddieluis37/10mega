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
        Schema::create('alistar_topping_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alistar_toppings_id')->nullable();
            $table->foreign('alistar_toppings_id')->references('id')->on('alistar_toppings');

            $table->unsignedBigInteger('products_id')->nullable();
            $table->foreign('products_id')->references('id')->on('products');

            $table->decimal('kgrequeridos', 18, 2)->nullable();
            $table->decimal('precio_minimo', 18, 2)->nullable();
            $table->decimal('total_venta', 18, 2)->nullable();
            $table->decimal('porc_venta', 18, 2)->nullable();
            $table->decimal('costo_total', 18, 2)->nullable();
            $table->decimal('costo_Kilo', 18, 2)->nullable();
            $table->decimal('utilidad', 18, 2)->nullable();
            $table->decimal('porc_utilidad', 18, 2)->nullable();
            $table->decimal('newstock', 18, 2)->nullable();
            $table->decimal('merma', 18, 2)->nullable();
            $table->decimal('porc_merma', 18, 2)->nullable();
            $table->decimal('cost_transformation', 18, 2)->nullable();

            $table->boolean('status')->parent_select()->default(true)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alistar_topping_details');
    }
};
