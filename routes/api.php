<?php

use App\Http\Controllers\Api\v1\CityController;
use App\Http\Controllers\Api\v1\LevelController;
use App\Http\Controllers\Api\v1\OperatorController;
use App\Http\Controllers\Api\v1\SchoolController;
use App\Http\Controllers\Api\v1\SearchController;
use App\Http\Controllers\Api\v1\SoldierController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function () {


    Route::post('InsertSoldierData', [SoldierController::class, 'InsertSoldierData']);
    Route::post('getListSoldier', [SoldierController::class, 'getListSoldier']);
    Route::post('getSoldier', [SoldierController::class, 'getSoldier']);
    Route::post('searchSoldier', [SoldierController::class, 'searchSoldier']);
    Route::post('addCommentSoldier', [SoldierController::class, 'addCommentSoldier']);
    Route::post('deleteCommentSoldier', [SoldierController::class, 'deleteCommentSoldier']);


    Route::middleware('auth:api')->group(function () {

        // Route::post('show', [CityController::class, 'show']);

    });
});
