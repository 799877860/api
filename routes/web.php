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

Route::get('info',function(){
    phpinfo();
});

//////////////////////////  TEST  //////////////////////////////
Route::get('/test/sign2','Api\TestController@sign2');



//////////////////////////  TEST  //////////////////////////////

Route::get('test/pay','TestController@alipay');     // 去支付

Route::get('test/accii','TestController@accii');    // 加密
Route::get('test/dec','TestController@dec');    // 加密

Route::get('test/alipay/return','Alipay\PayController@aliReturn');
Route::post('test/alipay/notify','Alipay\PayController@notify');

// 接口
Route::get('api/test','Api\TestController@test');

Route::post('api/user/reg','Api\TestController@reg');      // 用户注册
Route::post('api/user/login','Api\TestController@login');      // 用户登录
Route::get('api/show/data','Api\TestController@showData');     //获取数据接口
Route::get('api/user/list','Api\TestController@userList')->middleware('filter');      // 用户列表
Route::get('/test/postman','Api\TestController@postman')->middleware('filter','check.token');      // 接口防刷

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
