<?php

namespace Database\Seeders;


use App\Models\Levels_products;
use App\Models\Subcentrocosto;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {

    $this->call(DenominationSeeder::class);
    $this->call(CategorySeeder::class);
    $this->call(MeatcutSeeder::class);
    $this->call(UnitofmeasureSeeder::class);
    $this->call(Levels_productSeeder::class);

    $this->call(UserSeeder::class);
    $this->call(Type_identificationSeeder::class);
    $this->call(Type_regimen_ivaSeeder::class);
    $this->call(OfficeSeeder::class);
    $this->call(ProvinceSeeder::class);
    $this->call(AgreementSeeder::class);
    $this->call(ThirdSeeder::class);
    $this->call(BrandSeeder::class);
   // $this->call(BrandThirdSeeder::class);
 //   $this->call(ProductSeeder::class);
    $this->call(Precio_agreementSeeder::class);
    $this->call(SacrificioSeeder::class);

    $this->call(SacrificiocerdoSeeder::class);
    $this->call(SacrificiospolloSeeder::class);
    // $this->call(BeneficiocerdoSeeder::class);          
    // $this->call(DespostereSeeder::class);
    $this->call(CentrocostoSeeder::class);
    $this->call(LoteSeeder::class);

  //  $this->call(Centro_costo_productSeeder::class);

    $this->call(Nicho_mercadoSeeder::class);
    // $this->call(Nicho_mercado_centro_costo_productSeeder::class);
 //   $this->call(Nicho_mercado_productSeeder::class);
    $this->call(FormapagoSeeder::class);
    $this->call(Categorias_contableSeeder::class);
    $this->call(Clases_contableSeeder::class);
    $this->call(Relaciones_contableSeeder::class);
    $this->call(ParametrocontableSeeder::class);
    $this->call(SubcentrocostoSeeder::class);

    $this->call(StoreSeeder::class);

   // $this->call(CajaSeeder::class);
    $this->call(SalesSeeder::class);
    $this->call(Sales_detailSeeder::class);
    // $this->call(SaleFormaPagosSeederSeeder::class);
    $this->call(ListaprecioSeeder::class);
    $this->call(ListapreciodetalleSeeder::class);
    // $this->call(NotacreditoSeeder::class);
    //$this->call(RecibodecajaSeeder::class);
    $this->call(Cuentas_por_cobrarSeeder::class);

    $this->call(ProductLoteSeeder::class);

    $this->call(ProductStoreSeeder::class);
    //  $this->call(CompensadoresSeeder::class);
    //  $this->call(BeneficioreSeeder::class);
    //  $this->call(InventarioSeeder::class);
    //  $this->call(Movimiento_inventarioSeeder::class);
    // $this->call(LoteProductSeeder::class);
    $this->call(PermissionsSeeder::class);
    $this->call(StoreUserSeeder::class);
  }
}
