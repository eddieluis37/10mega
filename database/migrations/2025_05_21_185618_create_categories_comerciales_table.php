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
        Schema::create('categories_comerciales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('subcategory_comerciales_id')->nullable();
            $table->foreign('subcategory_comerciales_id')->references('id')->on('subcategory_comerciales');         

            $table->string('name',255);
            $table->string('image',100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_comerciales');
    }
};
