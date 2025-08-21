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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();        
            $table->unsignedBigInteger('category_id')->nullable(); // Restricción de categoría para el lote
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->string('codigo', 50, 0)->unique()->nullable();
            $table->decimal('costo', 18, 2)->default(0)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('status',['0','1','2','3','4','5'])->default('1'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
