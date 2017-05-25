<?php

namespace App\Http\Middleware;

use Closure;
use App\McLibs\Classes\McAuth;
use Log;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 开发环境
        /*if(env('APP_ENV', 'production') == 'local') {
            return $next($request);
        }*/

      
        //检查用户是否登录
        if($request->session()->get('isLogin') === true) {
            return $next($request);
        } else {
            return returnData([], 400, '请先登录');
        }


        $actions = $request->route()->getAction();
        $actionName = isset($actions['controller']) ? $actions['controller'] : '';
        $actionArr = explode('@', $actionName);
        $controllerArr = explode('\\', $actionArr[0]);
        $controller = array_pop($controllerArr);
        $controller = lcfirst(str_replace('Controller', '', $controller));
        $method = $actionArr[1];
        //是否登录
        if(!McAuth::getInstance()->alreadyLogin()) {
            return returnError('未登录', 400, 400);
        }

        //是否需要验证权限
        //获取需要权限的list，获取权限list，判断controller method是否有权限
        if( $this->checkAuth($controller, $method) ) {
            return $next($request);
        } else {
            return returnError('未授权', 401, 401);
        }
    }

    private function isNeedCheck($controller, $method)
    {
        $menuList = config('menu.aclList');
        $menuList = current($menuList);
        if(isset($menuList['modules'][$controller]['actions'][$method])) {
            Log::info('nedd');
            return true;
        } else {
            return false;
        }
    }

    private function checkAuth($controller, $method)
    {
        if($this->isNeedCheck($controller, $method)) {
            $aclList = json_decode(session( config('systemauth.ACL_LIST') ), true);
            $aclList = current($aclList);
            if(isset($aclList[$controller]) && is_array($aclList[$controller])) {
                return in_array($method, $aclList[$controller]);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
