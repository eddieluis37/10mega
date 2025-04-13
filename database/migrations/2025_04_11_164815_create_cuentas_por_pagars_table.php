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
        Schema::create('cuentas_por_pagars', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            // Se asume que los proveedores están en la tabla "thirds"
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('id')->on('thirds');

            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending');
            $table->date('fecha_factura')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('monto_total',12,2)->default(0);
            $table->decimal('monto_pendiente',12,2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_pagars');
    }
};
