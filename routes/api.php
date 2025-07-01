<?php

use App\Http\Controllers\producto\productoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/productos', [productoController::class, 'getProductos']);

//Route::get('products', [ProductController::class, 'index']);



Route::middleware('check_api_key')
     ->get('products', [ProductController::class, 'index']);




/* 
// Pagos de clientes y proveedores
Route::post('/customer-payment', [PaymentController::class, 'customerPayment']);
Route::post('/supplier-payment', [PaymentController::class, 'supplierPayment']);

// Apertura y cierre de caja
Route::post('/caja/open', [CajaController::class, 'openCaja']);
Route::post('/caja/close', [CajaController::class, 'closeCaja']);
 */