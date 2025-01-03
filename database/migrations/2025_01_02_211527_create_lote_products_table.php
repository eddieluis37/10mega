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
        Schema::create('lote_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lote_id');
            $table->unsignedBigInteger('product_id');
            
            $table->decimal('cantidad', 18, 2)->default(0); 
            $table->decimal('precio', 18, 2)->nullable(); 
            
            $table->timestamps();

            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Evitar duplicados en la tabla pivote
            $table->unique(['lote_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_products');
    }
};
