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
        Schema::create('brand_third_third', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_third_id')->constrained('brand_third')->onDelete('cascade');
            $table->foreignId('third_id')->constrained('thirds')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_third_third');
    }
};
