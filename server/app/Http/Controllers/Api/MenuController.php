<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * 返回权限配置给认证系统
     */
    public function getMenuInfo()
    {
        return config('menu.aclList');
    }
}