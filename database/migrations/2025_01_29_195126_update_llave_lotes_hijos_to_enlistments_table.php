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
        Schema::table('enlistments', function (Blueprint $table) {
            $table->foreignId('lote_hijos_id')->nullable()
            ->after('lote_id')->constrained('lotes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enlistments', function (Blueprint $table) {
            $table->dropForeign(['lote_hijos_id']);
            $table->dropColumn('lote_hijos_id');
        });
    }
};
