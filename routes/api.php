<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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



Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signup']);



Route::middleware('auth:api')->group(function () {
    Route::post('/products/create',[ProductController::class, 'store']);
    Route::get('/products',[ProductController::class, 'list']);
    Route::get('/items/{product_id}', [ProductController::class, 'list_items']);
    
    Route::post('/update-product/{id}',[ProductController::class, 'update']);
    Route::delete('/delete-product/{id}',[ProductController::class, 'destroy']);

    Route::post('/add-item/{id}',[ProductController::class, 'addItem']);
    Route::post('/update-item/{id}',[ProductController::class, 'updateItem']);
    Route::delete('/delete-item/{id}',[ProductController::class, 'deleteItem']);
    Route::post('/sold-item/{id}',[ProductController::class, 'updateSoldStatus']);

    Route::post('logout', [AuthController::class, 'logout']);
    
});
