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
        Schema::create('centro_costo_store', function (Blueprint $table) {
            $table->id();
            // Specify the foreign key for centro_costo_id to reference the centro_costo table
            $table->foreignId('centro_costo_id')
                ->constrained('centro_costo') // Specify the table name here
                ->onDelete('cascade');

            // Specify the foreign key for store_id to reference the stores table (if it exists)
            $table->foreignId('store_id')
                ->constrained() // This will reference the `id` column of the `stores` table
                ->onDelete('cascade');

            $table->decimal('quantity', 18, 2)->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centro_costo_store');
    }
};
