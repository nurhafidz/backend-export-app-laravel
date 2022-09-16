<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::group(['prefix' => 'category', 'controller' => CategoryController::class], function () {
    Route::get('/getall', 'index');
    Route::get('/searchbyid', 'searchById');
    Route::get('/searchbytitle', 'searchBySimilarTitle');
    Route::get('/searchbytype', 'searchByType');
    Route::middleware('auth:api')->group(function () {
        Route::post('/createsingle', 'storeSingleData');
        Route::post('/update/{id}', 'update');
        Route::post('/deletemany', 'destroy');
    });
});

Route::group(['prefix' => 'media', 'controller' => MediaController::class], function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/createsingle', 'storeSingleData');
        Route::post('/update/{id}', 'updateSingleData');
    });
});