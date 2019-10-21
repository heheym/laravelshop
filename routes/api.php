<?php

use Illuminate\Http\Request;
use App\Article;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('/song', 'Api\SongController@index')->name('song');  //用户获取歌曲数据接口
    Route::get('/song/download', 'Api\SongController@download');  //用户获得歌曲下载地址接口

    Route::get('/wechat','Api\PayController@wechat'); //微信支付

    Route::get('/song/downloadReturn','Api\SongController@downloadReturn'); //用户成功下载歌曲接口

    Route::get('/song/add_songs', 'Api\SongController@add_songs');  //添加/更新补歌表
    Route::get('/song/get_add_songs', 'Api\SongController@get_add_songs');  //获取补歌表

    Route::get('/song/get_users_songs', 'Api\SongController@get_users_songs');  //获取用户已下载歌曲列表
    Route::get('/song/get_user_warehouse', 'Api\SongController@get_user_warehouse');  //获取用户已入库歌曲列表
    
    Route::get('/song/delete_danger', 'Api\SongController@delete_danger');  //删除高危歌曲
    Route::get('/song/delete_ban', 'Api\SongController@delete_ban');  //删除禁播歌曲
});

//url，需要加一个api，如：192.168.10.227:81/api/login
Route::get('/config', 'Api\ConfigController@index')->name('config');  //获取版本号
Route::get('/alipay','Api\PayController@alipay'); //支付宝支付
Route::get('/sms','Api\SmsController@index'); //获取短信验证码接口
Route::get('register', 'Auth\RegisterController@register');  //api 注册 by ma
Route::get('login', 'Auth\LoginController@login');  //api 登录 by ma
Route::post('/song/upload', 'Api\SongController@upload');  //前台上传歌曲文件更新数据库
Route::post('/song/ban_songs', 'Api\SongController@ban_songs');  //添加/更新禁播
Route::post('/song/danger_songs', 'Api\SongController@danger_songs');  //添加/更新高危
Route::get('/song/get_ban_songs', 'Api\SongController@get_ban_songs');  //添加/更新高危
Route::get('/song/get_danger_songs', 'Api\SongController@get_danger_songs');  //添加/更新高危




