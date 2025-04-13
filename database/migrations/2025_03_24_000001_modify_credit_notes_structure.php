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
        // Actualizaciones adicionales para seguimiento de Notas de Crédito
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedTinyInteger('credit_notes_count')->default(0)
                ->comment('Contador de notas de crédito asociadas (máximo permitido: 2)');
            $table->enum('credit_note_status', ['none', 'partial', 'full'])
                ->default('none')
                ->comment('Estado de las notas de crédito: none = sin notas, partial = devolución parcial, full = venta anulada');
        });

        // 2. Modify the notacreditos table to better handle inventory returns
        Schema::table('notacreditos', function (Blueprint $table) {
            $table->enum('return_type', ['full_cancellation', 'partial_return'])
                ->after('tipo')
                ->comment('Tipo de devolución: full_cancellation = anulación total, partial_return = devolución parcial');
            $table->boolean('inventory_processed')->default(false)
                ->comment('Indica si el inventario fue ajustado para esta nota de crédito')
                ->after('return_type');
            $table->unsignedTinyInteger('credit_note_sequence')->default(1)
                ->comment('Secuencia de la nota de crédito para la venta (1 o 2)')
                ->after('inventory_processed');
        });

        // 3. Enhance notacredito_details to better track inventory items
    /*     Schema::table('notacredito_details', function (Blueprint $table) {
            // Add reference to original sale_detail_id
            $table->unsignedBigInteger('sale_detail_id')->nullable()->after('product_id');
            $table->foreign('sale_detail_id')->references('id')->on('sale_details');

            // Add store and lote information (copied from sale_details)
            $table->unsignedBigInteger('store_id')->nullable()->after('sale_detail_id');
            $table->foreign('store_id')->references('id')->on('stores');

            $table->unsignedBigInteger('lote_id')->nullable()->after('store_id');
            $table->foreign('lote_id')->references('id')->on('lotes');

            $table->unsignedBigInteger('inventario_id')->nullable()->after('lote_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

            // Add a field to track if this detail has been processed in inventory
            $table->boolean('inventory_processed')->default(false)
                ->comment('Flag to track if inventory has been updated for this specific item');
        }); */

        // 4. Create a new table to track credit note inventory movements
        Schema::create('notacredito_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notacredito_id')->constrained();
            $table->foreignId('notacredito_detail_id')->constrained();
            $table->foreignId('movimiento_inventario_id')->constrained('movimiento_inventarios');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('store_id')->constrained();
            $table->foreignId('lote_id')->constrained();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notacredito_inventory_movements');

        Schema::table('notacredito_details', function (Blueprint $table) {
            $table->dropForeign(['sale_detail_id']);
            $table->dropForeign(['store_id']);
            $table->dropForeign(['lote_id']);
            $table->dropForeign(['inventario_id']);
            $table->dropColumn(['sale_detail_id', 'store_id', 'lote_id', 'inventario_id', 'inventory_processed']);
        });

        Schema::table('notacreditos', function (Blueprint $table) {
            $table->dropColumn(['return_type', 'inventory_processed', 'credit_note_sequence']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['credit_notes_count', 'credit_note_status']);
        });
    }
};
