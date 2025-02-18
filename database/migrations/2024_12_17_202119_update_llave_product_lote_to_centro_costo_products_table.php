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
        Schema::table('centro_costo_products', function (Blueprint $table) {
            $table->foreignId('product_lote_id')->nullable()
            ->after('products_id')->constrained('product_lote')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centro_costo_products', function (Blueprint $table) {
            $table->dropForeign(['product_lote_id']);
            $table->dropColumn('product_lote_id');
        });
    }
};
