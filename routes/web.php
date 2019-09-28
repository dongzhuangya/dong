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
Route::get('order/index','index\OrderController@index');
Route::get('login/zuce','admin\LoginController@zuce');
Route::post('login/zuceTo','admin\LoginController@zuceTo');
Route::get('index/ding','index\IndexController@ding');
Route::get('index/dan','index\IndexController@dan');
Route::get('goods/add','admin\GoodsController@add');
Route::post('goods/addTo','admin\GoodsController@addTo');
Route::get('goods/index','admin\GoodsController@index');
Route::get('goods/update','admin\GoodsController@update');
Route::get('goods/delete','admin\GoodsController@delete');
Route::post('goods/updateTo','admin\GoodsController@updateTo');
Route::get('index/index','index\IndexController@index');
Route::get('index/goods','index\IndexController@liebiao');
Route::get('horse/add','admin\HorseController@add');
Route::post('horse/addTo','admin\HorseController@addTo');
Route::get('horse/index','admin\HorseController@index');
Route::get('horse/aa','admin\HorseController@aa');
Route::post('horse/aaTo','admin\HorseController@aaTo');
Route::get('horse/list','admin\HorseController@list');
Route::get('horse/aT','admin\HorseController@aT');
Route::post('horse/qq','admin\HorseController@qq');
Route::get('hhoo/add','admin\HhooController@add');
Route::post('hhoo/addTo','admin\HhooController@addTo');
Route::get('hhoo/list','admin\HhooController@list');
Route::get('hhoo/app','admin\HhooController@app');
Route::get('hhoo/apps','admin\HhooController@apps');
Route::get('hhoo/appTo','admin\HhooController@appTo');
Route::get('hhoo/ass','admin\HhooController@ass');
Route::get('hhoo/assTo','admin\HhooController@assTo');
Route::get('hhoo/ccc','admin\HhooController@ccc');
});
Route::group(['middleware' => ['apps']], function () {

});
Route::get('aaa/index','admin\AaaController@index');
Route::post('aaa/list','admin\AaaController@list');
Route::get('aaa/listaaa','admin\AaaController@listaaa');
Route::get('aaa/add','admin\AaaController@add');
Route::post('aaa/addo','admin\AaaController@addo');
Route::get('aaa/delect','admin\AaaController@delect');
Route::get('aaa/adc','admin\AaaController@adc');

Route::get('weixin/index','admin\WeixinController@index');
Route::get('weixin/list','admin\WeixinController@list');
Route::get('weixin/code','admin\WeixinController@code');
Route::get('weixin/login','admin\WeixinController@login');
Route::get('weixin/adc','admin\WeixinController@adc');
Route::get('weixin/del_dd','admin\WeixinController@del_dd');
Route::get('weixin/add','admin\WeixinController@add');
Route::post('weixin/en_do','admin\WeixinController@en_do');
Route::get('weixin/en','admin\WeixinController@en');
Route::get('weixin/fen','admin\WeixinController@fen');
Route::post('weixin/event','admin\WeixinController@event');
Route::get('weixin/establish','admin\WeixinController@establish');
Route::get('weixin/asd','admin\WeixinController@asd');
////////////////////////////////////////
Route::get('wecha/access_token','admin\WechaController@access_token');
Route::get('wecha/user_list','admin\WechaController@user_list');
Route::get('wecha/ddd','admin\WechaController@ddd');
Route::get('wecha/code','admin\WechaController@code');
Route::get('wecha/noss','admin\WechaController@noss');
Route::post('wecha/do_noss','admin\WechaController@do_noss');
Route::get('wecha/wechat_source','admin\WechaController@wechat_source');
Route::get('wecha/upload','admin\WechaController@upload');
Route::get('wecha/guzzle_upload','admin\WechaController@guzzle_upload');
Route::get('wecha/curl_post','admin\WechaController@curl_post');
Route::get('song/index','admin\SongController@index');
Route::get('song/add','admin\SongController@add');
Route::post('song/do_add','admin\SongController@do_add');
Route::post('wecha/event','admin\WechaController@event');
Route::get('song/ddd','admin\SongController@ddd');
Route::get('song/code','admin\SongController@code');
Route::get('song/sess','admin\SongController@sess');
Route::get('song/yan','admin\SongController@yan');
Route::get('song/mu','admin\SongController@mu');
Route::get('song/add_list','admin\SongController@add_list');
Route::get('song/mm','admin\SongController@mm');
Route::get('wecha/menu','admin\WechaController@menu');
//////////////////////
Route::get('wei/caidan','admin\WeiController@caidan');
Route::get('wei/curl_post','admin\WeiController@curl_post');
Route::get('wei/access_token','admin\WeiController@access_token');
Route::any('wei/event','admin\WeiController@event');






