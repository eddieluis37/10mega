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
        Schema::create('promotion_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained();

            $table->unsignedBigInteger('centrocosto_id')->nullable();
            $table->foreign('centrocosto_id')->references('id')->on('centro_costo');

            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores');          

            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade'); //Relación Lotes - Productos - Categorías

            $table->unsignedBigInteger('inventario_id')->nullable();
            $table->foreign('inventario_id')->references('id')->on('inventarios'); 

            $table->foreignId('product_id')->constrained();
            $table->decimal('quantity', 8, 2)->default(0)->nullable();
            $table->decimal('porc_desc', 10, 2)->default(0)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->date('fecha_final')->nullable();
            $table->time('hora_final')->nullable();

            $table->string('observacion')->nullable();
            $table->enum('status', ['0', '1', '2', '3', '4', '5'])->default('0');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_details');
    }
};
