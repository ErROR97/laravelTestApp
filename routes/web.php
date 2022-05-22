<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('verify-TM', [\App\Http\Controllers\TMStoreController::class, 'verifyTMPackage'])->name('payment-verify-tm');
Route::get('payNikTM', [\App\Http\Controllers\Api\v1\TMStoreController::class,'buyPackage']);
