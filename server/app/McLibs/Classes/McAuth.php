<?php
namespace App\McLibs\Classes;

use MingChao\MOAuthApiV1;
use Cookie;
use Log;

class McAuth {

    private static $_instance;

    /**
     * 单用例入口
     *
     * @return McAuth
     */
    public static function getInstance()
    {
        if (! self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function checkLogin( $userName, $password )
    {
        if (env('APP_ENV', 'production') == 'local' && 
            $userName == config('systemauth.ROOT_USERNAME') ) {
            if( $password == md5(config('systemauth.ROOT_PASSWORD')) ) {
                return $this->rootLogin($userName);
            } else {
                return array(
                    'ret' => 1,
                    'msg' => '登录失败，密码错误'
                );
            }
        }

        $accounts = config('systemauth.USERS');
    
        if(isset($accounts[$userName]) && $password === md5($accounts[$userName])) {
            return [
                'ret'   => 0,
                'msg'   => '登录成功'
            ];
        } else {
            return [
                'ret'   => 1,
                'msg'   => '登录失败，账号或密码错误'
            ];
        }
        exit;
        $oauth = new MOAuthApiV1(config('systemauth.MC_AUTH.APPID'),config('systemauth.MC_AUTH.APPKEY'));
        $oauth->setServerUrl(config('systemauth.MC_AUTH.MC_OAUTH_URL'));
        $rs = $oauth->login( $userName, $password );
        if ($rs['ret'] == 1) {
            $userAclList = $rs['data'];
            return $this->setAuth($userName, $userAclList ['aclList']);
        } else {
            return array(
                'ret' => 1,
                'msg' => $rs['msg']
            );
        }
    }

    private function rootLogin( $userName )
    {       
        $aclList = $this->getAllAclList();
        return $this->setAuth($userName, json_encode($aclList));
    }

    public function getAllAclList()
    {
        $aclPageConfig = config('menu.aclList');
        foreach ($aclPageConfig as $app => $aclArr) {
            if(isset($aclArr['modules']) && is_array($aclArr['modules'])) {
                foreach ($aclArr['modules'] as $module => $aclModule) {
                    if(isset($aclModule['actions']) && is_array($aclModule['actions'])) {
                        foreach ($aclModule['actions'] as $action => $aclaction) {
                            $aclList[$app][$module][] = $action;
                        }
                    }
                }
            }
        }
        return $aclList;
    }

    private function setAuth($userName, $aclList)
    {
        $session = [
            config('systemauth.ACL_LIST') => $aclList,
            config('systemauth.USERNAME') => $userName
        ];
        session($session);
        return array(
            'ret' => 0,
            'msg' => '登录成功'
        );
    }

    /**
     * 判断当前后台是否有登录
     */   
    public function alreadyLogin()
    {
        $userName = Cookie::get(config('systemauth.TICKET_NAME'));
        if( empty($userName) ) {
            return false;
        } else if( $userName == session( config('systemauth.USERNAME')) ) {
            return true;
        } else {
            return false;
        }
    }
}