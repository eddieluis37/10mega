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
        Schema::table('beneficiores', function (Blueprint $table) {
            $table->foreignId('lotes_id')->nullable()
            ->after('products_id')->constrained('lotes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiores', function (Blueprint $table) {
            $table->dropForeign(['lotes_id']);
            $table->dropColumn('lotes_id');
        });
    }
};
