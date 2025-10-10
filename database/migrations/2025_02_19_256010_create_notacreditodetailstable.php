<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotacreditoDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('notacredito_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('notacredito_id')->nullable();
            $table->unsignedBigInteger('sale_detail_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('lote_id')->nullable();

            // cantidades / precios con precisión suficiente
            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('unit_price', 18, 4)->default(0);
            $table->decimal('unit_bruto', 18, 4)->default(0);
            $table->decimal('unit_descuento', 18, 4)->default(0);
            $table->decimal('unit_neto', 18, 4)->default(0);
            $table->decimal('unit_iva', 18, 4)->default(0);
            $table->decimal('unit_otro_impuesto', 18, 4)->default(0);
            $table->decimal('unit_impoconsumo', 18, 4)->default(0);
            $table->decimal('unit_total', 18, 4)->default(0);

            // Totales (moneda)
            $table->decimal('total_bruto', 18, 2)->default(0);
            $table->decimal('descuento_total', 18, 2)->default(0);
            $table->decimal('neto_total', 18, 2)->default(0);
            $table->decimal('iva_total', 18, 2)->default(0);
            $table->decimal('otro_impuesto_total', 18, 2)->default(0);
            $table->decimal('impoconsumo_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            // desglose descuentos
            $table->decimal('descuento_pct_amount', 18, 2)->default(0);
            $table->decimal('descuento_fijo_prorrateado', 18, 2)->default(0);
            $table->decimal('descuento_global_prorrateado', 18, 2)->default(0);

            // taxable_base y rounding_adjustment (añadidas ya aquí)
            $table->decimal('taxable_base', 18, 2)->default(0);
            $table->decimal('rounding_adjustment', 18, 2)->default(0);

            // tasas (snapshot)
            $table->decimal('descuento_pct', 8, 4)->nullable();
            $table->decimal('porc_iva', 8, 4)->nullable();
            $table->decimal('porc_otro_impuesto', 8, 4)->nullable();
            $table->decimal('porc_impoconsumo', 8, 4)->nullable();

            $table->json('breakdown')->nullable();

            $table->timestamps();

            // índices y FK (opcional)
            $table->index('notacredito_id');
            $table->index('sale_detail_id');
            $table->index(['product_id', 'store_id']);
            // si quieres las FK activas:
            // $table->foreign('notacredito_id')->references('id')->on('notacreditos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notacredito_details');
    }
}
