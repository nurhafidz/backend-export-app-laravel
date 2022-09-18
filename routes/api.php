<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ChildProductController;
use App\Http\Controllers\AuthAdminController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user', 'controller' => AuthUserController::class], function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::group(['prefix' => 'admin', 'controller' => AuthAdminController::class], function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::group(['prefix' => 'category', 'controller' => CategoryController::class], function () {
    Route::get('/getall', 'index');
    Route::post('/searchbyid', 'searchById');
    Route::get('/searchbytitle', 'searchBySimilarTitle');
    Route::get('/searchbytype', 'searchByType');
    Route::middleware(['assign.guard:admin', 'jwt.auth'])->group(function () {
        Route::post('/createsingle', 'storeSingleData');
        Route::patch('/update/{id}', 'update');
        Route::delete('/deletemany', 'destroy');
    });
});

Route::group(['prefix' => 'media', 'controller' => MediaController::class], function () {
    Route::get('/getall', 'getAll');
    Route::get('/getbystatus', 'getByStatus');
    Route::get('/getbytype', 'getByType');
    Route::post('/findbyid', 'findById');
    Route::get('/findbytitle', 'findBytitle');
    Route::get('/findbylink', 'findByLink');
    Route::get('/findbyoriginalname', 'findByOriginalName');
    Route::middleware('auth:api')->group(function () {
        Route::post('/createsingle', 'storeSingleData');
        Route::patch('/update/{id}', 'updateSingleData');
        Route::delete('/deletesingle', 'destroySingleData');
    });
});
Route::group(['prefix' => 'product', 'controller' => ProductController::class], function () {

    Route::get('/getall', 'getAll');
    Route::get('/getproductlist', 'getProductList');
    // Route::get('/filterproduct', 'filterProduct');

    Route::middleware('auth:api')->group(function () {
        Route::post('/createsingle', 'createSingleProduct');
        Route::patch('/updatesingle/{id}', 'updateSingleProduct');
        Route::delete('/deletesingle', 'destroySingleProduct');
    });
});

Route::group(['prefix' => 'childproduct', 'controller' => ChildProductController::class], function () {
    Route::post('/createsingle', 'storeSingleData');
    // Route::get('/getall', 'getAll');
    // Route::get('/filterproduct', 'filterProduct');

    // Route::middleware('auth:api')->group(function () {
    //     Route::post('/createsingle', 'storeSingleData');
    //     Route::post('/update/{id}', 'updateSingleData');
    //     Route::post('/deletesingle', 'destroySingleData');
    // });
});
