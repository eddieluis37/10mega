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
        Schema::create('notacredito_inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('notacredito_id')->constrained();
            $table->foreignId('notacredito_detail_id')->constrained();
            $table->foreignId('movimiento_inventario_id')->constrained('movimiento_inventarios');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('store_id')->constrained();
            $table->foreignId('lote_id')->constrained();
            $table->decimal('quantity',10,2);
            $table->decimal('unit_cost',12,2);
            $table->decimal('total_cost',12,2);
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notacredito_inventory_movements');
    }
};
