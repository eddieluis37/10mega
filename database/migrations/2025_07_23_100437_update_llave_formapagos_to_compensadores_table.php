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
        Schema::table('compensadores', function (Blueprint $table) {
            $table->foreignId('formapago_id')->nullable()
                ->after('thirds_id')->constrained('formapagos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compensadores', function (Blueprint $table) {
            $table->dropForeign(['formapago_id']);
            $table->dropColumn('formapago_id');
        });
    }
};
