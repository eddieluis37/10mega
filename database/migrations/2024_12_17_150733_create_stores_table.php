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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            // Specify the foreign key for centro_costo_id to reference the centro_costo table
            $table->foreignId('centrocosto_id')->nullable()
                ->constrained('centro_costo') // Specify the table name here
                ->onDelete('cascade');
            $table->string('name');
            $table->string('description', 150, 0)->nullable(); // podria cambiar a tipo text
            $table->boolean('status')->parent_select()->default(true)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
