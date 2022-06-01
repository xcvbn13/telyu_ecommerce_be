<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login',[AuthController::class, 'login']);
Route::post('/register',[AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {

    // order 
    Route::get('/order/{id_user}',[OrderController::class,'index']);
    Route::get('/order/detail/{id_order}',[OrderController::class,'order_detail']);
    Route::post('/order',[OrderController::class,'store']);
    Route::post('/order/pembayaran/{id_order}',[OrderController::class,'store_pembayaran']);
    Route::post('/order/dibatalkan/{id_order}',[OrderController::class,'store_dibatalkan']);
    Route::post('/order/waktu_habis/{id_order}',[OrderController::class,'store_waktu_habis']);
    Route::post('/order/verifikasi_gagal/{id_order}',[OrderController::class,'store_verifikasi_gagal']);
    Route::post('/order/selesai/{id_order}',[OrderController::class,'store_selesai']);

    // product 
    Route::get('/products',[ProductsController::class,'index']);
    Route::get('/products/{id}',[ProductsController::class,'product_detail']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
