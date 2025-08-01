<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------|
| Ordenar las rutas dentro del grupo de middleware a veces puede afectar el comportamiento de redirección o el modo en que se carga la vista, 
| especialmente si se aplican otras rutas que modifican el flujo de autenticación.|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

use App\Http\Livewire\PartialReturn;
use App\Http\Controllers\Panel\ReportesController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExportCxcController;
use App\Http\Controllers\ExportOrderController;
use App\Http\Controllers\ExportSalesXProdController;
use App\Http\Controllers\ExportSalesXsCController;
use App\Http\Controllers\TodosController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\BeneficiopollosController;
use App\Http\Livewire\CashoutController;
use App\Http\Livewire\CategoriesController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\Component1;
use App\Http\Livewire\Dash;
use App\Http\Livewire\DesposterController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\PosController;
use App\Http\Livewire\PrecioAgreementsController;
use App\Http\Livewire\ProductsController;
use App\Http\Livewire\MeatcutsController;
use App\Http\Livewire\ReportsController;
use App\Http\Livewire\ReportsOrdersController;
use App\Http\Livewire\CuentasporcobrarsController;
use App\Http\Livewire\ReportsSalesXProdController;
use App\Http\Livewire\ReportsSalesXsCController;
use App\Http\Livewire\RolesController;
use App\Http\Livewire\Select2;
use App\Http\Livewire\ThirdsController;
use App\Http\Livewire\UsersController;
use App\Http\Controllers\ExportComprasXProdController;

/*************** SIN LIVWWIRE **********************/

use App\Http\Controllers\caja\cajaController;

use App\Http\Controllers\res\desposteresController;
use App\Http\Controllers\res\beneficioresController;
use App\Http\Controllers\cerdo\despostecerdoController;
use App\Http\Controllers\cerdo\beneficiocerdoController;

use App\Http\Controllers\FormapagoController;
use App\Http\Controllers\ParametrocontableController;
use App\Http\Controllers\sale\saleController;

use App\Http\Controllers\compensado\compensadoController;
use App\Http\Controllers\alistamiento\alistamientoController;
use App\Http\Controllers\alistartopping\alistartoppingController;
use App\Http\Controllers\pollo\beneficiopolloController;
use App\Http\Controllers\pollo\despostepolloController;
use App\Http\Controllers\inventory\CargarVentasController;
use App\Http\Controllers\inventory\CentroCostoProductController;
use App\Http\Controllers\inventory\CargueProductTerminadosController;
use App\Http\Controllers\CentroCostoProdController;
use App\Http\Controllers\AsignarPreciosProdController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandCrudController;
use App\Http\Controllers\caja\cajasalidaefectivoController;
use App\Http\Controllers\caja\pdfCierreCajaController;
use App\Http\Controllers\caja\pdfSalidaefectivoController;
use App\Http\Controllers\caja\resumenDiarioController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\compensado\ordencomprapdfCompensadoController;
use App\Http\Controllers\producto\productoController;
use App\Http\Controllers\compensado\pdfCompensadoController;
use App\Http\Controllers\faster\fasterController;
use App\Http\Controllers\transfer\transferController;
use App\Http\Controllers\workshop\workshopController;

use App\Http\Controllers\costo\costoController;
use App\Http\Controllers\cuentasporcobrar\cuentasporcobrarController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\DragDropController;
use App\Http\Controllers\excelAnalisisKGController;
use App\Http\Controllers\excelAnalisisUtilidadController;
use App\Http\Controllers\ExcelConsolidadoVentasController;
use App\Http\Controllers\inventario\inventarioController;
use App\Http\Controllers\inventory\inventoryController;

use App\Http\Controllers\inventory\diaryController;
use App\Http\Controllers\inventory\mensualController;
use App\Http\Controllers\listaprecio\listaprecioController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportReciboCajaController;

use App\Http\Controllers\ImportStockFisicoController;
use App\Http\Controllers\inventario\porcentrocostoController;
use App\Http\Controllers\inventario\siporcentrocostoController;
use App\Http\Controllers\inventory\inventoryUtilidadHistoricoController;
use App\Http\Controllers\LossController;
use App\Http\Controllers\notacredito\notacreditoController;
use App\Http\Controllers\notacredito\pdfNotacreditoController;
use App\Http\Controllers\notadebito\notadebitoController;
use App\Http\Controllers\notadebito\pdfNotadebitoController;
use App\Http\Controllers\order\orderController;
use App\Http\Controllers\order\pdfOrderController;
use App\Http\Controllers\recibodecaja\pdfRecibodecajaController;
use App\Http\Controllers\recibodecaja\recibodecajaController;
use App\Http\Controllers\res\pdfLoteController;
use App\Http\Controllers\sale\exportFacturaController;
use App\Http\Controllers\pollo\utilidadpolloController;
use App\Http\Controllers\reportes\reportecompraprodController;
use App\Http\Controllers\reportes\reportecompraproveedorController;
use App\Http\Controllers\reportes\reporteventaprodclientController;
use App\Http\Controllers\reportes\reporteventaprodController;
use App\Http\Controllers\reportes\reportecomprarequeridaController;
use App\Http\Controllers\reportes\reporteasignarpreciosController;
use App\Http\Controllers\ProductLoteController;
use App\Http\Controllers\sale\exportDespachoController;
use App\Http\Controllers\sale\exportRemisionController;
use App\Http\Controllers\ReporteCierreCajaController;
use App\Http\Controllers\reportes\reporteajusteinventariosController;
use App\Http\Controllers\RestaurantOrderController;
use App\Http\Controllers\sale\exportComandaController;
use App\Http\Controllers\sale\saleautoservicioController;
use App\Http\Controllers\sale\salebarController;
use App\Http\Controllers\sale\saleparrillaController;
use App\Http\Controllers\transfer\exportTransferController;
use App\Http\Controllers\UserController;

Route::get('export-users', [UserController::class, 'export'])->name('users.export');

Route::get('caja-salida-efectivo', [cajasalidaefectivoController::class, 'index'])->name('cajasalidaefectivo.index');
Route::get('show-cse', [cajasalidaefectivoController::class, 'show'])->name('cajasalidaefectivo.show');
Route::post('csesave', [cajasalidaefectivoController::class, 'store'])->middleware('auth')->name('caja_salida_efectivo.store');
Route::get('caja-salida-efectivo/pdfFormatopos/{id}', [pdfSalidaefectivoController::class, 'pdfFormatopos'])->name('caja-salida-efectivo.pdfFormatopos');


Route::middleware(['auth'])->group(function () {
    Route::resource('dishes', DishController::class)->middleware('can:viewAny,App\Models\Dish');
    Route::resource('combos', ComboController::class)->middleware('can:viewAny,App\Models\Combo');
    Route::resource('restaurant-orders', RestaurantOrderController::class)->middleware('can:viewAny,App\Models\RestaurantOrder');
    Route::resource('losses', LossController::class)->middleware('can:viewAny,App\Models\Loss');
});




/************************************************* */

Route::get('/reporte-detalle-recibo/{id}', [ReportReciboCajaController::class, 'show'])->name('reportrecibocaja.show');

// Ruta para obtener los registros de cuentas_por_cobrars vía AJAX
Route::get('/getClientPayments', [recibodecajaController::class, 'getClientPayments'])->name('reciboCaja.getClientPayments');

Route::get('/reporte-cierre-caja/{id}', [ReporteCierreCajaController::class, 'show'])->name('reporte.cierre');



Route::get('/obtener-valores-producto', [transferController::class, 'obtenerValoresProducto'])->name('transfer.obtener-valores-producto');
Route::get('/obtener-valores-producto-destino', [transferController::class, 'obtenerValoresProductoDestino'])->name('transfer.obtener-valores-producto-destino');


Route::post('/inventario/inicial', [InventarioController::class, 'registrarInicial'])->name('inventario.inicial');
Route::get('/inventario/inicial', [InventarioController::class, 'showInventarioInicialForm'])->name('inventario.inicial.form');


//Route::get('/admin/posts', 'Admin\PostsController@index')->name('admin.posts.index');
Route::get('/admin/posts', [App\Http\Controllers\Admin\PostsController::class, 'index'])->name('admin.posts.index');

//Route::post('/admin/posts/store', 'Admin\PostsController@store')->name('admin.posts.store');
Route::post('/admin/posts/store', [App\Http\Controllers\Admin\PostsController::class, 'store'])->name('admin.posts.store');


//Route::post('/admin/posts/{postId}/update', 'Admin\PostsController@update')->name('admin.posts.update');
Route::post('/admin/posts{postId}/update', [App\Http\Controllers\Admin\PostsController::class, 'update'])->name('admin.posts.update');

//Route::delete('/admin/posts/{postId}/delete', 'Admin\PostsController@delete')->name('admin.posts.delete');
Route::delete('/admin/posts{postId}/delete', [App\Http\Controllers\Admin\PostsController::class, 'delete'])->name('admin.posts.delete');

Route::get('/', function () {
    return view('auth.login');
});

Route::get('prueba', function () {
    return view('livewire.beneficiores.prueba');
});

//Auth::routes();

Auth::routes(['register' => false]); // deshabilitamos el registro de nuevos users

Route::get('/home', Dash::class);


Route::get('/libros', function () {
    // return view('welcome');
    return view('book');
});

Route::resource('books', BooksController::class);
//Route::resource('beneficiocerdos', BeneficiocerdosController::class);

Route::get('/roles/{role}', 'RoleController@show');

Route::get('reportes', [ReportesController::class, 'index'])->name('reportes.index');


/* TERCEROS */

Route::middleware(['auth', 'can:acceder_terceros'])->group(function () {
    Route::get('thirds', ThirdsController::class);
});

Route::middleware(['auth', 'can:acceder_brand'])->group(function () {
    // Rutas para el CRUD de marcas (sin relaciones)
    Route::resource('brand-crud', BrandCrudController::class)
        ->parameters(['brand-crud' => 'brand']);

    // Rutas para el módulo que relaciona una marca con proveedores
    Route::resource('brands', BrandController::class)->parameters([
        'brands' => 'brandThird'
    ]);
});


Route::middleware(['auth', 'can:acceder_inventario_stockfisico'])->group(function () {
    Route::post('/updateCcpInventory', [CentroCostoProductController::class, 'updateCcpInventory'])->name('inventory.updateCcpInventory999');
    Route::get('inventory/centro_costo_products', [CentroCostoProductController::class, 'index'])->name('inventory.showccp');

    Route::get('reportes/ventas_por_productos', [reporteventaprodController::class, 'index'])->name('reportes.ventas_por_productos');
    Route::get('reportes/ajuste_de_inventarios', [reporteajusteinventariosController::class, 'index'])->name('reportes.ajuste_de_inventarios');

    Route::get('showReportVentasPorProd', [reporteventaprodController::class, 'show'])->name('showReportVentasPorProd.showlist');
});

Route::get('cuentas_por_cobrar', [cuentasporcobrarController::class, 'index'])->name('cuentas_por_cobrar');

/*****************************ORDENES DE PEDIDOS******************************************/
Route::middleware(['auth', 'can:acceder_orders'])->group(function () {
    Route::get('orders', [orderController::class, 'index'])->name('order.index');
    Route::get('showOrder', [orderController::class, 'show'])->name('order.showOrder');
    Route::post('ordersave', [orderController::class, 'store'])->name('order.save');
    Route::get('/getDireccionesByCliente/{cliente_id}', [orderController::class, 'getDireccionesByCliente'])->name('order.getDireccionesByCliente');
    Route::get('order/create/{id}', [orderController::class, 'create'])->name('order.create');
    Route::get('abrirOrden/{id}', [orderController::class, 'reopen'])->name('order.reopen');
    Route::get('delivered/{id}', [orderController::class, 'delivered'])->name('order.delivered');
    Route::post('ordersavedetail', [orderController::class, 'savedetail'])->name('order.savedetail');
    Route::post('orderById', [orderController::class, 'editOrder'])->name('order.editOrder');    // order_details 
    Route::get('downOrder/{id}', [orderController::class, 'destroy'])->name('order.destroy');
    Route::post('orderdown', [orderController::class, 'destroyDetail'])->name('order.down');
    Route::get('/order-obtener-valores', [orderController::class, 'obtenerValores'])->name('order.order-obtener-valores');
    Route::post('order/create/registrar_order/{id}', [orderController::class, 'storeOrder'])->name('order.saveOrder');
    Route::get('order/showPDFOrder/{id}', [pdfOrderController::class, 'showPDFOrder'])->name('order.showPDFOrder');
    Route::get('/order-edit/{id}', [orderController::class, 'edit'])->name('order.edit'); // informacion basica inicial de la orden
});

// Proteger todas las rutas dentro del modulo de cargue de productos terminados
/*****************************CARGUE DE PRODUCTOS TERMINADOS*******************************************/
Route::middleware(['auth', 'can:acceder_cargue_productos_term'])->group(function () {
    Route::post('lotesave', [CargueProductTerminadosController::class, 'storelote'])->name('lote.save');
    Route::post('productlotesave', [CargueProductTerminadosController::class, 'productlote'])->name('productlote.save');
    Route::get('/lote-data', [CargueProductTerminadosController::class, 'getLoteData']);
    Route::get('inventory/cargue_products_terminados', [CargueProductTerminadosController::class, 'index'])->name('inventory.showcpt');
    Route::get('showCptInventory', [CargueProductTerminadosController::class, 'show'])->name('inventory.show-cpt');
    Route::delete('/product-lote/{id}', [ProductLoteController::class, 'destroy']);
    Route::post('/updateCptInventory', [CargueProductTerminadosController::class, 'updateCptInventory'])->name('inventory.updateCptInventory999');

    Route::get('/sincronizar-product-lote', [CargueProductTerminadosController::class, 'sincronizarProductLote'])->name('sincronizar.product.lote');

    Route::get('showCcpInventory', [CentroCostoProductController::class, 'show'])->name('inventory.show-ccp');

    Route::get('totales', [inventoryController::class, 'totales'])->name('inventory.totales');

    Route::post('cargarInventariohist', [inventoryController::class, 'cargarInventariohist'])->name('cargarInventariohist');
});

/*****************************BENEFICIO-RES*******************************************/

// Proteger todas las rutas dentro del modulo de compra_lote
Route::middleware(['auth', 'can:acceder_compra_lote'])->group(function () {
    Route::get('beneficiores', [beneficioresController::class, 'index'])->name('beneficiores.index');
    Route::get('showbeneficiores', [beneficioresController::class, 'show'])->name('beneficiores.showlist');
    Route::get('get_plantasacrificio_by_id', [beneficioresController::class, 'get_plantasacrificio_by_id'])->name('get_plantasacrificio_by_id');
    Route::post('savebeneficiores', [beneficioresController::class, 'store'])->name('beneficiores.save');
    Route::get('/edit/{id}', [beneficioresController::class, 'edit'])->name('beneficiores.edit');
    Route::get('downbeneficiores/{id}', [beneficioresController::class, 'destroy'])->name('beneficiores.destroy');
    Route::get('beneficiores/pdfLote/{id}', [pdfLoteController::class, 'pdfLote']);

    /*****************************DESPOSTE-RES******************************************/
    Route::get('desposteres', [desposteresController::class, 'index'])->name('desposteres.index');
    Route::get('desposteres/{id}', [desposteresController::class, 'create']);
    Route::post('/desposteresUpdate', [desposteresController::class, 'update']);
    Route::post('/downdesposter', [desposteresController::class, 'destroy']);
    Route::post('cargarInventario', [desposteresController::class, 'cargarInventario'])->name('desposteres.show');

    /********************************* BENEFICIO AVES******************************/
    Route::get('beneficioaves', [beneficiopolloController::class, 'index'])->name('beneficioaves.index');
    Route::get('get_plantasacrificiopollo_by_id', [beneficiopolloController::class, 'get_plantasacrificiopollo_by_id'])->name('get_plantasacrificiopollo_by_id');
    Route::post('savebeneficioaves', [beneficiopolloController::class, 'store'])->name('beneficioaves.save');
    Route::get('/beneficioavesedit/{id}', [beneficiopolloController::class, 'edit'])->name('beneficioaves.edit');
    Route::get('showbeneficioaves', [beneficiopolloController::class, 'show'])->name('beneficioaves.showlist');

    /*****************************UTILIDAD-AVES******************************************/
    Route::get('utilidadaves/{id}', [utilidadpolloController::class, 'create'])->name('utilidadaves.create');
    Route::post('/utilidadavesUpdate', [utilidadpolloController::class, 'update'])->name('utilidadaves.update');
    Route::post('/downutilidadave', [utilidadpolloController::class, 'destroy'])->name('utilidadaves.destroy');

    /*****************************DESPOSTE-AVES******************************************/
    Route::get('desposteaves/{id}', [despostepolloController::class, 'create'])->name('desposteaves.create');
    Route::post('/desposteavesUpdate', [despostepolloController::class, 'update'])->name('desposteaves.update');
    Route::post('/downdesposteave', [despostepolloController::class, 'destroy'])->name('desposteaves.destroy');
    Route::post('cargarInventarioa', [despostepolloController::class, 'cargarInventarioaves'])->name('desposteaves.show');

    /*****************************DESPOSTE-CERDO******************************************/
    Route::get('despostecerdo', [despostecerdoController::class, 'index'])->name('despostecerdo.index');
    Route::get('despostecerdo/{id}', [despostecerdoController::class, 'create']);
    Route::post('/despostecerdoUpdate', [despostecerdoController::class, 'update']);
    Route::post('/downdespostec', [despostecerdoController::class, 'destroy']);
    Route::post('cargarInventarioc', [despostecerdoController::class, 'cargarInventariocerdo'])->name('despostecerdo.show');

    /**BENEFICIO CERDO */
    Route::get('beneficiocerdo', [beneficiocerdoController::class, 'index'])->name('beneficiocerdo.index');
    Route::get('showbeneficiocerdo', [beneficiocerdoController::class, 'show'])->name('beneficiocerdo.showlist');
    Route::get('get_plantasacrificiocerdo_by_id', [beneficiocerdoController::class, 'get_plantasacrificiocerdo_by_id'])->name('get_plantasacrificiocerdo_by_id');
    Route::post('savebeneficiocerdo', [beneficiocerdoController::class, 'store'])->name('beneficiocerdo.save');
    Route::get('/beneficiocerdoedit/{id}', [beneficiocerdoController::class, 'edit'])->name('beneficiocerdo.edit');
    Route::get('downbeneficiocerdo/{id}', [beneficiocerdoController::class, 'destroy'])->name('beneficiocerdo.destroy');
});

// Proteger todas las rutas dentro del modulo de compra_productos
/*****************************COMPRAS-COMPENSADOS****************************************** */
Route::middleware(['auth', 'can:acceder_compra_productos'])->group(function () {
    Route::get('compensado', [compensadoController::class, 'index'])->name('compensado.index');
    
    Route::get('compensado/create_order/{id}', [compensadoController::class, 'create_order'])->name('compensado.create_order');
    
    Route::get('compensado/create/{id}', [compensadoController::class, 'create'])->name('compensado.create');
    
    Route::get('showlistcompensado', [compensadoController::class, 'show'])->name('compensado.showlist');
    Route::post('getproductos', [compensadoController::class, 'getproducts'])->name('compensado.getproductos');
    Route::get('/compensado/search-products', [compensadoController::class, 'searchProducts'])->name('compensado.search-products');
    Route::post('compensadosave', [compensadoController::class, 'store'])->name('compensado.save');
    
    Route::post('compensadosavedetailorder', [compensadoController::class, 'savedetail_order'])->name('compensado.savedetail_order');
    
    Route::post('compensadosavedetail', [compensadoController::class, 'savedetail'])->name('compensado.savedetail');


    Route::post('compensadodown', [compensadoController::class, 'destroy'])->name('compensado.down');
    Route::post('compensadogetById', [compensadoController::class, 'edit'])->name('compensado.ById');

    Route::post('compensadogetByIdOrder', [compensadoController::class, 'editOrder'])->name('compensado.ByIdOrder');
     Route::post('compensadoByIdOrder', [compensadoController::class, 'editCompensadoorder'])->name('compensado.editCompensadoorder');


    Route::post('compensadoById', [compensadoController::class, 'editCompensado'])->name('compensado.editCompensado');
    Route::post('/downmaincompensado', [compensadoController::class, 'destroyCompensado'])->name('compensado.downCompensado');
    Route::post('compensadoInvres', [compensadoController::class, 'cargarInventariocr'])->name('compensado.cargarInventariocr');

    Route::get('compensado/ordencomprapdfCompensado/{id}', [ordencomprapdfCompensadoController::class, 'ordencomprapdfCompensado']);
    Route::get('compensado/pdfCompensado/{id}', [pdfCompensadoController::class, 'pdfCompensado']);
});

// Proteger todas las rutas dentro del modulo de alistamiento
Route::middleware(['auth', 'can:acceder_alistamiento'])->group(function () {
    /**ALISTAMIENTO*/
    Route::get('alistamiento', [alistamientoController::class, 'index'])->name('alistamiento.index');
    Route::post('alistamientosave', [alistamientoController::class, 'store'])->name('alistamiento.save');
    Route::get('showalistamiento', [alistamientoController::class, 'show'])->name('alistamiento.showlist');
    Route::get('alistamiento/create/{id}', [alistamientoController::class, 'create'])->name('alistamiento.create');
    Route::post('getproductos', [alistamientoController::class, 'getproducts'])->name('alistamiento.getproductos');
    Route::post('alistamientosavedetail', [alistamientoController::class, 'savedetail'])->name('alistamiento.savedetail');
    Route::post('/alistamientoUpdate', [alistamientoController::class, 'updatedetail'])->name('alistamiento.update');
    Route::post('alistamientodown', [alistamientoController::class, 'destroy'])->name('alistamiento.down');
    Route::post('alistamientoById', [alistamientoController::class, 'editAlistamiento'])->name('alistamiento.edit');
    Route::post('getproductospadre', [alistamientoController::class, 'getProductsCategoryPadre'])->name('alistamiento.getproductospadre');
    Route::post('/downmmainalistamiento', [alistamientoController::class, 'destroyAlistamiento'])->name('alistamiento.downAlistamiento');
    Route::post('/downmmainalistamiento', [alistamientoController::class, 'destroyAlistamiento'])->name('alistamiento.downAlistamiento');
    Route::post('alistamientoAddShoping', [alistamientoController::class, 'add_shopping'])->name('alistamiento.addShopping');

    Route::get('/get-lotes/{storeId}', [alistamientoController::class, 'getLotes'])->name('get.lotes');
    Route::get('/get-productos/{loteId}', [alistamientoController::class, 'getProductos'])->name('get.productos');
});

// Proteger todas las rutas dentro del modulo de alistamiento
Route::middleware(['auth', 'can:acceder_alistamiento'])->group(function () {
    /**ALISTAMIENTO*/
    Route::get('alistartopping', [alistartoppingController::class, 'index'])->name('alistartopping.index');
    Route::post('alistartoppingsave', [alistartoppingController::class, 'store'])->name('alistartopping.save');
    Route::get('showalistartopping', [alistartoppingController::class, 'show'])->name('alistartopping.showlist');
    Route::get('alistartopping/create/{id}', [alistartoppingController::class, 'create'])->name('alistartopping.create');
    Route::post('alistargetproductos', [alistartoppingController::class, 'getproducts'])->name('alistartopping.getproductos');
    Route::post('alistartoppingsavedetail', [alistartoppingController::class, 'savedetail'])->name('alistartopping.savedetail');
    Route::post('/alistartoppingUpdate', [alistartoppingController::class, 'updatedetail'])->name('alistartopping.update');
    Route::post('alistartoppingdown', [alistartoppingController::class, 'destroy'])->name('alistartopping.down');
    Route::post('alistartoppingById', [alistartoppingController::class, 'editAlistamiento'])->name('alistartopping.edit');
    Route::post('getproductospadre', [alistartoppingController::class, 'getProductsCategoryPadre'])->name('alistartopping.getproductospadre');
    Route::post('/downmmainalistartopping', [alistartoppingController::class, 'destroyAlistamiento'])->name('alistartopping.downAlistamiento');
    Route::post('/downmmainalistartopping', [alistartoppingController::class, 'destroyAlistamiento'])->name('alistartopping.downAlistamiento');
    Route::post('alistartoppingAddShoping', [alistartoppingController::class, 'add_shopping'])->name('alistartopping.addShopping');

    Route::get('/get-lotes/{storeId}', [alistartoppingController::class, 'getLotes'])->name('get.lotes');
    Route::get('/get-productos/{loteId}', [alistartoppingController::class, 'getProductos'])->name('get.productos');

    Route::get('/alistartopping/search/{store}', [alistartoppingController::class, 'search'])->name('alistartopping.search');
});

// Proteger todas las rutas dentro del modulo de traslados
Route::middleware(['auth', 'can:acceder_traslado'])->group(function () {
    /***** TRANSFER ******** */


    Route::get('transfer', [transferController::class, 'index'])->name('transfer.index');
    Route::post('transfersave', [transferController::class, 'store'])->name('transfer.save');
    Route::get('showtransfer', [transferController::class, 'show'])->name('transfer.showlist');
    Route::get('transfer/create/{id}', [transferController::class, 'create'])->name('transfer.create');

    Route::get('/transfer/get-products-by-lote', [transferController::class, 'getProductsByLote']);

    Route::get('/transfer/search', [transferController::class, 'search'])->name('transfer.search');

    Route::post('getproductos', [transferController::class, 'getproducts'])->name('transfer.getproductos');
    Route::post('productsbycostcenterdest', [transferController::class, 'ProductsByCostcenterDest'])->name('transfer.productsbycostcenterdest');
    Route::post('getproductsbycostcenterorigin', [transferController::class, 'getProductsByCostcenterOrigin'])->name('transfer.getproductsbycostcenterorigin');

    Route::post('transfersavedetail', [transferController::class, 'savedetail'])->name('transfer.savedetail');
    Route::post('/transferUpdate', [transferController::class, 'updatedetail'])->name('transfer.update');
    Route::post('transferdown', [transferController::class, 'destroy'])->name('transfer.down');
    Route::post('transferById', [transferController::class, 'editTransfer'])->name('transfer.edit');
    Route::post('productospadre', [transferController::class, 'getProductsCategoryPadre'])->name('transfer.productospadre');
    Route::post('/downmmaintransfer', [transferController::class, 'destroyTransfer'])->name('transfer.downAlistamiento');
    Route::post('transferAddShoping', [transferController::class, 'add_shopping'])->name('transfer.addShopping');
    Route::get('transfer/showTransfer/{id}', [exportTransferController::class, 'showTransfer'])->name('transfer.showTransfer');
});



// Proteger todas las rutas dentro del modulo de ventas
/*****************************VENTAS******************************************/
Route::middleware(['auth', 'can:acceder_ventas'])->group(function () {
    Route::post('sale/partial_return', [App\Http\Controllers\sale\saleController::class, 'partialReturn'])->name('sale.partial_return');
    Route::get('sale{saleId}/delete', [SaleController::class, 'delete'])->name('sale.delete');
    Route::get('sale{ventaId}/edit', [SaleController::class, 'edit'])->name('sale.edit');
    Route::post('sale/{ventaId}', [SaleController::class, 'update'])->name('sale.update');
    Route::post('getproductosv', [SaleController::class, 'getproducts'])->name('sale.getproductos');

    Route::get('sales', [saleController::class, 'index'])->name('sale.index');
    Route::post('ventasave', [saleController::class, 'store'])->name('sale.save');
    Route::get('showlistVentas', [saleController::class, 'show'])->name('sale.showlistVentas');
    Route::post('store-venta-mostrador', [saleController::class, 'storeVentaMostrador'])->name('sale.storeVentaMostrador');

    Route::post('salesavedetail', [saleController::class, 'savedetail'])->name('sale.savedetail');
    Route::post('saleById', [saleController::class, 'editCompensado'])->name('sale.editCompensado');
    Route::post('ventadown', [saleController::class, 'destroy'])->name('sale.down');
    Route::post('/destroyVenta', [saleController::class, 'destroyVenta'])->name('sale.destroyVenta');

    Route::get('sale/create/{id}', [saleController::class, 'create'])->name('sale.create');

    Route::get('/sa-obtener-precios-producto', [saleController::class, 'SaObtenerPreciosProducto'])->name('sale.sa-obtener-precios-producto');

    Route::get('sale/create/registrar_pago/{id}', [saleController::class, 'create_reg_pago'])->name('sale.registrar_pago');
    Route::post('sale/create/registrar_pago/{id}', [saleController::class, 'storeRegistroPago'])->name('pago.save');

    Route::get('sale/showFactura/{id}', [exportFacturaController::class, 'showFactura'])->name('sale.showFactura');
    Route::get('sale/showDespacho/{id}', [exportDespachoController::class, 'showDespacho'])->name('sale.showDespacho');
    Route::get('sale/showRemision/{id}', [exportRemisionController::class, 'showRemision'])->name('sale.showRemision');

    Route::get('/cargar-inventario-masivo', [saleController::class, 'cargarInventarioMasivo'])->name('cargar.inventario.masivo');

    Route::get('/products/search', [saleController::class, 'search'])->name('products.search');

    // Ruta para cargar la vista del formulario de devolución parcial
    // Esta ruta redirige a una vista donde se muestra el detalle de la venta
    // y permite digitar la cantidad a devolver para cada producto.
    Route::get('/sale/partial-return-form/{id}', [saleController::class, 'partialreturnform'])->name('sales.partialReturnForm');
    // Ruta para procesar la devolución parcial (se envían los datos mediante AJAX)
    // Route::post('/sale/{saleId}/annul', [saleController::class, 'annulSale']); // esta funcionalidad de anulacion no realiza devolucion de dinero

    Route::get('/getDireccionesByClienteSale/{cliente_id}', [saleController::class, 'getDireccionesByClienteSale'])->name('sale.getDireccionesByClienteSale');
});

/* VENTAS PARRILLA Tipo 2 = POS MOSTRADOR, Tpo 3 = DOMICILIO */
Route::middleware(['auth', 'can:acceder_venta_parrilla'])->group(function () {
    Route::get('sales_parrilla', [saleController::class, 'index_parrilla'])->name('sale.index_parrilla');
    Route::get('showParrillaVentas', [saleController::class, 'showParrilla'])->name('sale.showParrilla');
    Route::post('ventasave_parrilla', [saleController::class, 'store_parrilla'])->name('sale.save_parrilla');
    Route::post('store-parrilla-mostrador', [saleController::class, 'storeParrillaMostrador'])->name('sale.storeParrillaMostrador');
    Route::get('sale_parrilla/create/{id}', [saleController::class, 'create_parrilla'])->name('sale.create_parrilla');
    Route::get('/products/search/parrilla', [saleparrillaController::class, 'search'])->name('products.search_parrilla');
    Route::get('sale_parrilla/create/registrar_pago/{id}', [saleController::class, 'create_reg_pago'])->name('sale.registrar_pago');
    Route::post('sale_parrilla/create/registrar_pago/{id}', [saleController::class, 'storeRegistroPago'])->name('pago.save');
    Route::get('sale/showComanda/{id}', [exportComandaController::class, 'showComanda'])->name('sale.showComanda');
});

/* VENTAS AUTOSERVICIO Tipo 4 = POS MOSTRADOR, Tipo 5 = DOMICILIO */
Route::middleware(['auth', 'can:acceder_venta_autoservicio'])->group(function () {
    Route::get('sales_autoservicio', [saleautoservicioController::class, 'index_autoservicio'])->name('sale.index_autoservicio');
    Route::get('showAutoservicioVentas', [saleautoservicioController::class, 'showAutoservicio'])->name('sale.showAutoservicio');
    Route::post('ventasave_autoservicio', [saleautoservicioController::class, 'store_autoservicio'])->name('sale.store_autoservicio');
    Route::get('sale_autoservicio/create/{id}', [saleautoservicioController::class, 'create_autoservicio'])->name('sale.create_autoservicio');
    Route::get('/products/search/autoservicio', [saleautoservicioController::class, 'search'])->name('products.search_autoservicio');
    Route::get('sale_autoservicio/create/registrar_pago/{id}', [saleautoservicioController::class, 'create_reg_pago'])->name('sale.registrar_pago');
    Route::post('sale_autoservicio/create/registrar_pago/{id}', [saleautoservicioController::class, 'storeRegistroPago'])->name('pago.save');
    Route::post('store-autoservicio-mostrador', [saleautoservicioController::class, 'storeAutoservicioMostrador'])->name('sale.storeAutoservicioMostrador');
});


/* VENTAS BAR Tipo 6 = POS MOSTRADOR, Tipo 7 = DOMICILIO */
Route::middleware(['auth', 'can:acceder_venta_bar'])->group(function () {
    Route::get('sales_bar', [salebarController::class, 'index_bar'])->name('sale.index_bar');
    Route::get('showBarVentas', [salebarController::class, 'showBar'])->name('sale.showBar');
    Route::post('ventasave_bar', [salebarController::class, 'store_bar'])->name('sale.store_bar');
    Route::get('sale_bar/create/{id}', [salebarController::class, 'create_bar'])->name('sale.create_bar');
    Route::get('/products/search/bar', [salebarController::class, 'search'])->name('products.search_bar');
    Route::get('sale_bar/create/registrar_pago/{id}', [salebarController::class, 'create_reg_pago'])->name('sale.registrar_pago');
    Route::post('sale_bar/create/registrar_pago/{id}', [salebarController::class, 'storeRegistroPago'])->name('pago.save');
    Route::post('store-bar-mostrador', [salebarController::class, 'storeBarMostrador'])->name('sale.storeBarMostrador');
});

Route::middleware(['auth', 'can:acceder_inventario'])->group(function () {
    /*****************************INVENTORY****************************************** */
    Route::get('inventory/diary', [diaryController::class, 'index'])->name('inventory.diary');
    Route::get('inventory/consolidado', [inventoryController::class, 'index'])->name('inventory.consolidado');
    Route::get('showinventory', [diaryController::class, 'show'])->name('inventory.showlist');

    /*****************************INVENTORIO NUEVO****************************************** */
    Route::get('inventario/cierre', [inventarioController::class, 'index'])->name('inventario.cierre');
    Route::get('showInventarioCierre', [inventarioController::class, 'showInvcierre'])->name('inventario.showInventarioCierre');
    Route::get('getLotes', [inventarioController::class, 'getLotes'])->name('inventario.getLotes');
    Route::get('getAllLotes', [inventarioController::class, 'getAllLotes'])->name('inventario.getAllLotes');


    Route::get('inventario/por_centro_costo', [porcentrocostoController::class, 'index'])->name('inventario.por_centro_costo');
    Route::get('showPorCentroCosto', [porcentrocostoController::class, 'showPorCentroCosto'])->name('inventario.showPorCentroCosto');

    Route::get('inventario/si_por_centro_costo', [siporcentrocostoController::class, 'SiIndex'])->name('inventario.SiIndex');
    Route::get('SishowPorCentroCosto', [siporcentrocostoController::class, 'SishowPorCentroCosto'])->name('inventario.SishowPorCentroCosto');
    

    Route::get('getStores', [porcentrocostoController::class, 'getStores'])->name('inventario.getStores');
    Route::get('getAllStores', [porcentrocostoController::class, 'getAllStores'])->name('inventario.getAllStores');


    /*****************************INVENTORY-HISTORICO-KG****************************************** */
    Route::get('inventory/showhistorico', [inventoryController::class, 'showhistorico'])->name('inventory.showhistorico');
    Route::get('inventory/consolidado_historico', [inventoryController::class, 'indexhistorico'])->name('inventory.consolidadohistorico');
    Route::get('totaleshist', [inventoryController::class, 'totaleshist'])->name('inventory.totaleshist');

    /*****************************INVENTORY-HISTORICO-UTILIDAD*******************************************/
    Route::get('inventory/showhistutilidad', [inventoryUtilidadHistoricoController::class, 'showhistutilidad'])->name('inventory.showhistutilidad');
    Route::get('inventory/consolidado_histutilidad', [inventoryUtilidadHistoricoController::class, 'indexhistutilidad'])->name('inventory.consolidadohistutilidad');
    Route::get('totaleshistutilidad', [inventoryUtilidadHistoricoController::class, 'totaleshistutilidad'])->name('inventory.totaleshistutilidad');

    /*****************************CARGAR-VENTAS*******************************************/
    Route::get('inventory/cargar_ventas', [CargarVentasController::class, 'index'])->name('inventory.showcvc');
    Route::get('showCargarVentasInv', [CargarVentasController::class, 'show'])->name('inventory.showCargarVentas');
    Route::post('/updateCVInv', [CargarVentasController::class, 'updateCVInv'])->name('inventory.updateCVInv');
});



Route::middleware(['auth', 'can:acceder_productos'])->group(function () {
    /**PRODUCTOS SIN LIVEWIRE**/
    Route::get('producto', [productoController::class, 'index'])->name('producto.index');
    Route::post('productosave', [productoController::class, 'store'])->name('producto.save');
    Route::get('showproducto', [productoController::class, 'show'])->name('producto.showproducto');
    Route::get('producto/create/{id}', [productoController::class, 'create'])->name('producto.create');
    Route::post('producto/create/{id}', [productoController::class, 'storeCierreCaja'])->name('producto.cierre');
    Route::get('producto/showReciboCaja/{id}', [productoController::class, 'showReciboCaja'])->name('producto.showReciboProducto');
    Route::get('/producto-edit/{id}', [productoController::class, 'edit'])->name('producto.edit');

    Route::get('/productos/select2', [productoController::class, 'select2'])->name('producto.select2');
});

Route::middleware(['auth', 'can:acceder_lista_de_precio'])->group(function () {
    /*****************************LISTA_DE_PRECIO******************************************/
    Route::get('lista_de_precio', [listaprecioController::class, 'index'])->name('lista_de_precio.index');
    Route::get('showListaPrecio', [listaprecioController::class, 'show'])->name('lista_de_precio.showListaPrecio');

    Route::post('lista_de_preciosave', [listaprecioController::class, 'store'])->name('lista_de_precio.save');
    Route::get('lista_de_precio/create/{id}', [listaprecioController::class, 'create'])->name('lista_de_precio.create');
    Route::get('lista_de_precio{lista_de_precioId}/delete', [listaprecioController::class, 'delete'])->name('lista_de_precio.delete');
    Route::get('lista_de_precio{lista_de_precioId}/edit', [listaprecioController::class, 'edit'])->name('lista_de_precio.edit');
    Route::post('lista_de_precio/{lista_de_precioId}', [listaprecioController::class, 'update'])->name('lista_de_precio.update');
});

Route::group(['middleware' => [('auth')]], function () {

    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])
        ->name('users.profile')
        ->middleware('auth');

    Route::get('categories', CategoriesController::class)->name('categories');
    Route::get('users', UsersController::class);
    Route::get('roles', RolesController::class);
    Route::get('permisos', PermisosController::class);
    Route::get('asignar', AsignarController::class);
    // Route::get('products', ProductsController::class);
    Route::get('meatcuts', MeatcutsController::class);
    Route::get('pos', PosController::class);
    Route::get('coins', CoinsController::class);
    Route::get('reports', ReportsController::class);
    Route::get('reports_orders', ReportsOrdersController::class);
    Route::get('report_sales_x_sc', ReportsSalesXsCController::class);
    Route::get('report_sales_x_prod', ReportsSalesXProdController::class);
    Route::get('cashout', CashoutController::class);
    Route::get('dash', Dash::class)->name('dash');

    Route::get('precio_agreements', PrecioAgreementsController::class);
    //Route::get('beneficiores', BeneficioresController::class);
    Route::get('beneficiopollos', BeneficiopollosController::class);
    //Route::get('desposteres/{id}', DesposteresController::class);

    /**beneficiores*/
    Route::resource('desposter', DesposterController::class);

    /*desposteres* */
    Route::post('desposteresAdd', [DesposteresController::class, 'store']);
    Route::get('getdesposter/{id}', [DesposteresController::class, 'getdesposter']);
    Route::get('downdesposter/{id}/{beneficioId}', [DesposteresController::class, 'destroy']);

    //reportes PDF
    Route::get('report/pdf/{user}/{type}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
    Route::get('report/pdf/{user}/{type}', [ExportController::class, 'reportPDF']);


    //reportes EXCEL
    Route::get('report/excel/{user}/{type}/{f1}/{f2}', [ExportController::class, 'reporteExcel']);
    Route::get('report/excel/{user}/{type}', [ExportController::class, 'reporteExcel']);

    Route::post('storepollo', [BeneficiopollosController::class, 'storepollo'])->name('storepollo');

    Route::post('storem', [DesposterController::class, 'storem'])->name('storem');

    /************************* CUENTAS POR COBRAR ********************************** */
    Route::get('cuentasporcobrars', CuentasporcobrarsController::class);

    //reportes PDF
    Route::get('reportCxc/pdf/{user}/{type}/{f1}/{f2}', [ExportCxcController::class, 'reportPDF']);
    Route::get('reportCxc/pdf/{user}/{type}', [ExportCxcController::class, 'reportPDF']);

    //reportes EXCEL
    Route::get('reportCxc/excel/{user}/{type}/{f1}/{f2}', [ExportCxcController::class, 'reporteExcel']);
    Route::get('reportCxc/excel/{user}/{type}', [ExportCxcController::class, 'reporteExcel']);

    //reportes Ordenes de pedidos
    Route::get('report_order/excel/{f1}/{f2}', [ExportSalesXsCController::class, 'reporteExcel']);

    //reportes Ventas x Subcentro de costos
    Route::get('report_sales_x_sc/excel/{f1}/{f2}', [ExportSalesXsCController::class, 'reporteExcel']);

    //reportes Ventas x Productos
    Route::get('report_sales_x_prod/excel/{f1}/{f2}', [ExportSalesXProdController::class, 'reporteExcel']);

    //Route::post('citywithstatecountry', [CityController::class, 'citywithstatecountry'])->name('citywithstatecountry');

    //reportes Compras x productos
    //Route::get('report_compras_x_prod/excel/', [reportecompraprodController::class, 'reporteExcel']);



    Route::get('report_compras_x_prod/excel/{f1}/{f2}', [reportecompraprodController::class, 'reporteExcel']);
    //Route::get('report_compras_x_prod', [reportecompraprodController::class, 'reporteExcel']);
    //Route::get('export-to-excel', 'TuControlador@exportToExcel')->name('exportToExcel');
    //Route::get('/report_compras_x_prod/excel', [reportecompraprodController::class, 'reporteExcel'])->name('report_compras_x_prod.excel');   

    Route::get('todos', TodosController::class . '@index')->name('todos');

    Route::post('todos', TodosController::class . '@store');

    Route::get('/todos/{id}', [TodosController::class, 'show'])->name('todos-edit');

    Route::patch('/todos/{id}', [TodosController::class, 'update'])->name('todos-update');

    Route::delete('/todos/{id}', [TodosController::class, 'destroy'])->name('todos-destroy');

    /************************* RUTAS SIN LIVEWIRE ********************************** */
    Route::get('centro_costo_prod', [CentroCostoProdController::class, 'index'])->name('ccpShow');
    Route::get('showCcpSwitch', [CentroCostoProdController::class, 'show'])->name('showCcpSwitch');
    Route::post('/updateCcpSwitch', [CentroCostoProdController::class, 'updateCcpSwitch'])->name('updateCcpSwitch');

    /************************* RUTAS ASIGNAR PRECIOS A PRODUCTOS ********************************** */
    Route::get('asignar_precios_prod', [AsignarPreciosProdController::class, 'index'])->name('APPShow');
    Route::get('showAPPSwitch', [AsignarPreciosProdController::class, 'show'])->name('showAPPSwitch');
    Route::post('/updateAPPSwitch', [AsignarPreciosProdController::class, 'updateAPPSwitch'])->name('updateAPPSwitch');
    Route::get('report_asignar_precios_prod/excel/{f1}/{f2}', [reporteasignarpreciosController::class, 'reporteExcel']);

    /************************* REPORTES NUEVOS ********************************** */

    Route::get('reportes/ventas_por_productos_clientes', [reporteventaprodclientController::class, 'index'])->name('reportes.ventas_por_productos_clientes');
    Route::get('reportVentasPorProdClient', [reporteventaprodclientController::class, 'show'])->name('ReportVentasPorProdClient.showli');



    Route::get('reportes/compras_por_productos', [reportecompraprodController::class, 'index'])->name('reportes.compras_por_productos');
    Route::get('reportes/compras_requeridas', [reportecomprarequeridaController::class, 'index'])->name('reportes.compras_requeridas');
    Route::get('showReportComprasPorProd', [reportecompraprodController::class, 'show'])->name('showReportComprasPorProd.showlist');
    Route::get('showReportComprasRequeridas', [reportecomprarequeridaController::class, 'show'])->name('showReportComprasRequeridas.showlist');

    Route::get('reportes/compras_por_proveedores', [reportecompraproveedorController::class, 'index'])->name('reportes.compras_por_proveedores');
    Route::get('showReportComprasPorProvee', [reportecompraproveedorController::class, 'show'])->name('showReportComprasPorProvee.showlist');

    Route::get('showReportAjusteDeInv', [reporteajusteinventariosController::class, 'show'])->name('showReportAjusteDeInv.showajustedeinv');


     Route::get('showCuentasPorCobrar', [cuentasporcobrarController::class, 'show'])->name('showCuentasPorCobrar');






    /***CAJA*/
    Route::get('caja', [cajaController::class, 'index'])->name('caja.index');
    Route::post('cajasave', [cajaController::class, 'store'])->name('caja.save');
    Route::get('showcaja', [cajaController::class, 'show'])->name('caja.showcaja');
    Route::get('caja/create/{id}', [cajaController::class, 'create'])->name('caja.create');
    Route::post('caja/create/{id}', [cajaController::class, 'storeCierreCaja'])->name('caja.cierre');
    Route::get('caja/showReciboCaja/{id}', [cajaController::class, 'showReciboCaja'])->name('caja.showRecibo');

    Route::get('caja/reportecierre/{id}', [cajaController::class, 'reportecierre'])->name('caja.reportecierre');

    // Route::get('caja/pdfCierreCaja/{id}', [pdfCierreCajaController::class, 'pdfCierreCaja']);
    Route::get('caja/pdfCierreCaja/{id}', [pdfCierreCajaController::class, 'pdfCierreCaja'])->name('caja.pdfCierre');
    Route::get('caja/resumenDiario/{id}', [resumenDiarioController::class, 'resumenDiario'])->name('caja.resumenDiario');





    /** TALLER ***/
    Route::get('workshop', [workshopController::class, 'index'])->name('workshop.index');
    Route::post('workshopsave', [workshopController::class, 'store'])->name('workshop.save');
    Route::get('showworkshop', [workshopController::class, 'show'])->name('workshop.showlist');
    Route::get('workshop/create/{id}', [workshopController::class, 'create'])->name('workshop.create');
    Route::post('getproductos', [workshopController::class, 'getproducts'])->name('workshop.getproductos');
    Route::post('workshopsavedetail', [workshopController::class, 'savedetail'])->name('workshop.savedetail');
    Route::post('/workshopUpdate', [workshopController::class, 'updatedetail'])->name('workshop.update');
    Route::post('workshopdown', [workshopController::class, 'destroy'])->name('workshop.down');
    Route::post('workshopById', [workshopController::class, 'editWorkshop'])->name('workshop.edit');
    Route::post('getproductospadre', [workshopController::class, 'getProductsCategoryPadre'])->name('workshop.getproductospadre');
    Route::post('/downmmainworkshop', [workshopController::class, 'destroyWorkshop'])->name('workshop.downAlistamiento');
    Route::post('workshopAddShoping', [workshopController::class, 'add_shopping'])->name('workshop.addShopping');

    Route::post('afectarCostos', [workshopController::class, 'afectarCostos'])->name('afectarCostos.show');


    /***** FASTER ******** */
    Route::get('faster', [fasterController::class, 'index'])->name('faster.index');
    Route::post('fastersave', [fasterController::class, 'store'])->name('faster.save');
    Route::get('showfaster', [fasterController::class, 'show'])->name('faster.showlist');
    Route::get('faster/create/{id}', [fasterController::class, 'create'])->name('faster.create');
    Route::post('getproductos', [fasterController::class, 'getproducts'])->name('faster.getproductos');
    Route::post('fastersavedetail', [fasterController::class, 'savedetail'])->name('faster.savedetail');
    Route::post('/fasterUpdate', [fasterController::class, 'updatedetail'])->name('faster.update');
    Route::post('fasterdown', [fasterController::class, 'destroy'])->name('faster.down');
    Route::post('fasterById', [fasterController::class, 'editFaster'])->name('faster.edit');
    Route::post('getproductospadre', [fasterController::class, 'getProductsCategoryPadre'])->name('faster.getproductospadre');
    Route::post('/downmmainfaster', [fasterController::class, 'destroyFaster'])->name('faster.downAlistamiento');
    Route::post('fasterAddShoping', [fasterController::class, 'add_shopping'])->name('faster.addShopping');

    /**COSTO*/
    Route::get('costo', [costoController::class, 'index'])->name('costo.index');
    Route::get('showcosto', [costoController::class, 'show'])->name('costo.showlist');
    Route::get('costo/create/{id}', [costoController::class, 'create'])->name('costo.create');


    /***** FORMAS DE PAGO ******** */
    Route::get('formapago', [FormapagoController::class, 'index'])->name('formapago.index');
    Route::post('formapagosave', [FormapagoController::class, 'store'])->name('formapago.save');
    Route::get('formapago{formapagoId}/delete', [FormapagoController::class, 'delete'])->name('formapago.delete');
    Route::get('formapago{formapagoId}/edit', [FormapagoController::class, 'edit'])->name('formapago.edit');
    Route::post('formapago/{formapagoId}', [FormapagoController::class, 'update'])->name('formapago.update');


    /***** PARAMETROS CONTABLES*********/
    Route::get('parametrocontable', [ParametrocontableController::class, 'index'])->name('parametrocontable.index');
    Route::post('parametrocontablesave', [ParametrocontableController::class, 'store'])->name('parametrocontable.save');
    Route::get('parametrocontable{parametrocontableId}/delete', [ParametrocontableController::class, 'delete'])->name('parametrocontable.delete');
    Route::get('parametrocontable{parametrocontableId}/edit', [ParametrocontableController::class, 'edit'])->name('parametrocontable.edit');
    Route::post('parametrocontable/{parametrocontableId}', [ParametrocontableController::class, 'update'])->name('parametrocontable.update');



    /*****************************RECIBO DE CAJAS******************************************/
    Route::post('/recibodecajas', [recibodecajaController::class, 'payment'])->name('recibodecajas.payment');
    Route::get('recibodecajas', [recibodecajaController::class, 'index'])->name('recibodecaja.index');
    Route::get('showlistRecibodecajas', [recibodecajaController::class, 'show'])->name('recibodecaja.showlistRecibodecajas');
    Route::post('recibodecajasave', [recibodecajaController::class, 'store'])->name('recibodecaja.save');
    Route::get('recibodecaja/create/{id}', [recibodecajaController::class, 'create'])->name('recibodecaja.create');
    Route::get('/obtener-valores', [recibodecajaController::class, 'obtenerValores'])->name('recibodecaja.obtener-valores');
    Route::post('gurdarrecibodecaja', [recibodecajaController::class, 'gurdarrecibodecaja'])->name('recibodecaja.gurdarrecibodecaja');
    Route::get('recibodecaja/showRecibodecaja/{id}', [pdfRecibodecajaController::class,  'showRecibodecaja'])->name('recibodecaja.showRecibodecaja');
    Route::get('recibodecaja/showFormatopos/{id}', [pdfRecibodecajaController::class,  'showFormatopos'])->name('recibodecaja.showFormatopos');

    Route::get('/facturasByCliente/{cliente_id}', [recibodecajaController::class, 'facturasByCliente'])->name('recibodecaja.facturasByCliente');

    /* Route::post('registroPagoSave', [saleController::class, 'storeRegistroPago'])->name('pago.save'); */



    /* 
    Route::post('notacreditosavedetail', [notacreditoController::class, 'savedetail'])->name('notacredito.savedetail');
    Route::get('/obtener-precios-producto', [notacreditoController::class, 'obtenerPreciosProducto'])->name('notacredito.obtener-precios-producto');
    Route::post('notacreditoById', [notacreditoController::class, 'editNotacredito'])->name('notacredito.editNotacredito');
    Route::post('notacredito/create/registrar_notacredito/{id}', [notacreditoController::class, 'storeNotacredito'])->name('notacredito2.save');
    Route::get('notacredito/showNotacredito/{id}', [pdfNotacreditoController::class, 'showNotacredito']);
    Route::post('downnotacredito', [notacreditoController::class, 'destroy'])->name('notacredito.down');     
    
  


     /*****************************NOTA_CREDITO******************************************/

    Route::get('notacredito', [notacreditoController::class, 'index'])->name('notacredito.index');
    Route::get('showNotacredito', [notacreditoController::class, 'show'])->name('notacredito.showNotacredito');
    Route::post('notacreditosave', [notacreditoController::class, 'store'])->name('notacredito.save');
    Route::get('notacredito/create/{id}', [notacreditoController::class, 'create'])->name('notacredito.create');
    Route::post('notacreditosavedetail', [notacreditoController::class, 'savedetail'])->name('notacredito.savedetail');
    Route::get('/nc-obtener-precios-producto', [notacreditoController::class, 'NCObtenerPreciosProducto'])->name('notacredito.nc-obtener-precios-producto');
    Route::post('notacreditoById', [notacreditoController::class, 'editNotacredito'])->name('notacredito.editNotacredito');
    Route::post('notacredito/create/registrar_notacredito/{id}', [notacreditoController::class, 'storeNotacredito'])->name('notacredito2.save');
    Route::get('notacredito/showNotacredito/{id}', [pdfNotacreditoController::class, 'showNotacredito']);
    Route::post('downnotacredito', [notacreditoController::class, 'destroy'])->name('notacredito.down');

    Route::get('/getFacturasByCliente/{cliente_id}', [notacreditoController::class, 'getFacturasByCliente'])->name('notacredito.getFacturasByCliente');

    /*****************************NOTA_DEBITO******************************************/

    Route::get('notadebito', [notadebitoController::class, 'index'])->name('notadebito.index');
    Route::get('showNotadebito', [notadebitoController::class, 'show'])->name('notadebito.showNotadebito');
    Route::post('notadebitosave', [notadebitoController::class, 'store'])->name('notadebito.save');
    Route::get('notadebito/create/{id}', [notadebitoController::class, 'create'])->name('notadebito.create');
    Route::post('notadebitosavedetail', [notadebitoController::class, 'savedetail'])->name('notadebito.savedetail');
    Route::get('/obtener-precios-producto', [notadebitoController::class, 'obtenerPreciosProducto'])->name('notadebito.obtener-precios-producto');
    Route::post('notadebitoById', [notadebitoController::class, 'editNotacredito'])->name('notadebito.editNotadebito');
    Route::post('notadebito/create/registrar_notadebito/{id}', [notadebitoController::class, 'storeNotadebito'])->name('notadebito2.save');
    Route::get('notadebito/showNotacredito/{id}', [pdfNotadebitoController::class, 'showNotadebito']);



    Route::post('/drag-drop', [DragDropController::class, 'handleDragDrop'])->name('drag-drop.handleDragDrop');
    Route::get('/drag', [DragDropController::class, 'showDragView'])->name('drag.showDragView');

    // Route::get('/descargar-reporte', 'App\Http\Controllers\ReportController@downloadExcel');
    Route::get('/descargar-reporte', [ReportController::class, 'downloadExcel'])->name('descargar-reporte');

    Route::get('/import', [ImportStockFisicoController::class, 'import'])->name('import');

    /*****************************Reportes de Exceles******************************************/

    Route::get('/excel-analisis-kg', [excelAnalisisKGController::class, 'exportToExcel'])->name('excel-analisis-kg');
    Route::get('/excel-analisis-utilidad', [excelAnalisisUtilidadController::class, 'exportToExcel'])->name('excel-analisis-utilidad');

    Route::get('/excel-consolidado-ventas', [ExcelConsolidadoVentasController::class, 'downloadExcel'])->name('excel-consolidado-ventas');
});

require __DIR__ . '/admin.php';

Route::get('conte', Component1::class);
Route::get('conte2', function () {
    return view('contenedor');
});

//rutas utils
Route::get('select2', Select2::class);


/* Route::view('/', 'welcome'); */
Route::view('/examples/basic', 'examples.basic');
Route::view('/examples/custom-component', 'examples.custom-component');
Route::view('/examples/as-form-input', 'examples.as-form-input');
Route::view('/examples/livewire', 'examples.livewire');
Route::view('/examples/livewire/drag-drop-multiple-targets', 'examples.livewire-drag-drop-multiple-targets');
Route::view('/examples/customization', 'examples.customization');
Route::view('/examples/drag-drop', 'examples.drag-drop');
Route::view('/examples/drag-drop-nested', 'examples.drag-drop-nested');
Route::view('/examples/disable-drop-sort', 'examples.disable-drop-sort');
