<?php

use Illuminate\Http\Request;

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
Route::group(['namespace' => 'Api'], function()
{
    Route::get('menu/getMenuInfo', 'MenuController@getMenuInfo');//获取菜单配置
    Route::get('api/GetAllUserAccount', 'ApiController@GetAllUserAccount');
});
