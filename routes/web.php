<?php

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
Route::group(['middleware' => ['login']], function () {

Route::get('login/index','admin\LoginController@index');
Route::post('login/dong','admin\LoginController@dong');
Route::get('login/zuce','admin\LoginController@zuce');
Route::post('login/zuceTo','admin\LoginController@zuceTo');
Route::get('index/ding','index\IndexController@ding');

Route::get('goods/add','admin\GoodsController@add');
Route::post('goods/addTo','admin\GoodsController@addTo');
Route::get('goods/index','admin\GoodsController@index');
Route::get('goods/update','admin\GoodsController@update');
Route::get('goods/delete','admin\GoodsController@delete');
Route::post('goods/updateTo','admin\GoodsController@updateTo');
Route::get('index/index','index\IndexController@index');
Route::get('index/goods','index\IndexController@liebiao');


});