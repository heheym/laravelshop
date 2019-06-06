<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
//    $router->resource('uploads', UploadController::class);  //upload
//    $router->get('upload','UploadController@store1');  //获取七牛云上传token

    $router->resource('songs', SongController::class);  //歌曲上传
    $router->get('song','SongController@getToken');  //获取七牛云上传token

    $router->resource('config',ConfigController::class);  //系统配置
    $router->resource('user',UserController::class);  //用户
});

