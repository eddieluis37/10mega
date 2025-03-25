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
        Schema::create('tabla_inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_cycle_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->integer('quantity_at_close');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabla_inventory_histories');
    }
};
