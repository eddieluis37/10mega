<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddIndexesToSaleDetailsAndInventarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->index(['store_id', 'lote_id', 'product_id'], 'idx_sd_store_lote_prod');
        });
        Schema::table('inventarios', function (Blueprint $table) {
            $table->index(['store_id', 'lote_id', 'product_id'], 'idx_inv_store_lote_prod');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropIndex('idx_sd_store_lote_prod');
        });
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropIndex('idx_inv_store_lote_prod');
        });
    }
}