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
        Schema::table('notacreditos', function (Blueprint $table) {
            $table->unsignedBigInteger('forma_pago_id')->nullable()->after('status');
            $table->foreign('forma_pago_id')->references('id')->on('formapagos');
            $table->decimal('valor_devolucion', 12, 2)->nullable()->after('forma_pago_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notacreditos', function (Blueprint $table) {
            $table->dropForeign(['forma_pago_id']);
            $table->dropColumn(['forma_pago_id', 'valor_devolucion']);
        });
    }
};
