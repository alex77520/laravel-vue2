<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\McLibs\Classes\McAuth;
use MingChao\Rsa\RsaClass;
use Gregwar\Captcha\CaptchaBuilder;
use Cookie;
use Log;

class AdminController extends Controller
{
    public function index()
    {
        header( 'Location: /web' );
    }

    public function isLogin()
    {
        if(McAuth::getInstance()->alreadyLogin()) {
            $data['username'] = session( config('systemauth.USERNAME'));
            return returnData([], 0, "已登录");
        } else {
            $errorCount = session()->get('errcount');
            $data['errcount'] = $errorCount;
            return returnData($data, 1, "未登录");
        }
    }

    /**
     * 验证码
     */
    public function captach()
    {
        $builder = new CaptchaBuilder;
        $builder->setBackgroundColor(255,255,255);
        $builder->build(150,40);
        session(['captach' => $builder->getPhrase()]);
        return response($builder->output())->header('Content-type','image/jpeg');
    }

    public function login(Request $request)
    {
        $captach = $request->captach;
        $errorCount = session()->get('errcount');
        $errorCount = $errorCount ? $errorCount : 0;
        if($errorCount > 3 ) {
            if(session()->get('captach') != $captach) {
                $data['errcount'] = $errorCount;
                return returnData($data,1,'验证码错误');
            }   
        }

        $userName = $request->username;
        $password = $this->getPassword($request->password);
        $auth = new McAuth();
        $res = $auth->checkLogin($userName, $password);
    
        if($res['ret'] == 0) {
            $cookie = Cookie::make(config('systemauth.TICKET_NAME'), $userName, 120);
            $request->session()->put('isLogin', true, 3600);
            $data['username'] = $userName;
            $request->session()->forget('errcount');
            return returnData($data)->withCookie($cookie);
        } else {
            $errorCount ++;
            $request->session()->put('errcount', $errorCount);
            $data['errcount'] = $errorCount;
            return returnData($data,1,$res['msg']);
        }
    }

    /**
     * rsa解密
     */
    private function getPassword($password)
    {
        $passwordTmp = base64_encode(pack("H*", $password));
        $file = base_path('/ssl/private_key.pem');
        $password = RsaClass::getInstance()->privatekeyDecodeing($passwordTmp, $file, TRUE);
        return trim($password);
    }

    public function logout(Request $request)
    {
        $userName = session( config('systemauth.USERNAME'));
        Cookie::forget(config('systemauth.TICKET_NAME'));
        $request->session()->flush();
        return returnData([], 0, "登出成功");
    }

    private function getMenu()
    {
        return config('menu.menuList');
    }

    private function getAclMenu()
    {
        $menus = $this->getMenu();
        foreach ($menus as $key => $controller) {
            if( !isset($this->aclList['mdbConf'][$controller['auth']]) ) {
                unset($menus[$key]);
            }
        }
        return $menus;
    }

    private function getPermissionList()
    {
        $data = array();
        if(is_array($this->aclList)) {
            foreach ($this->aclList as $app => $appModules) {
                foreach ($appModules as $module => $modules) {
                    foreach ($modules as $key => $method) {
                        $data[$module][] = $method;
                    }
                }
            }
        }
        return $data;
    }

    public function getAuth()
    {
        $this->aclList = json_decode(session( config('systemauth.ACL_LIST') ), true);
        // $info['userName'] = session( config('systemauth.USERNAME') );
        // $info['appMenus'] = $this->getAclMenu();
        // $info['permissionList'] = $this->getPermissionList();
        // $info['userName'] = 'admin';
        $info['appMenus'] = config('menu.menuList');
        return returnData($info);
    }
}