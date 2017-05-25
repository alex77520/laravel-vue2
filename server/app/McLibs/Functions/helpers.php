<?php
/**
 * 常用的返回提示信息
 * @param  string  $msg
 * @param  integer $ret
 * @param  integer $status
 * @return json
 */
function returnMsg( $msg = 'success', $ret = 0, $status = 200 )
{
   return buildResponse($ret, $msg, $status); 
}

/**
 * 常用的返回提示信息
 * @param  string  $msg
 * @param  integer $ret
 * @param  integer $status
 * @return json
 */
function returnError( $msg = 'error', $ret = 1, $status = 200 )
{
   return buildResponse($ret, $msg, $status); 
}

/**
 * 常用的返回数据
 * @param  array  $data
 * @param  integer $ret
 * @param  string  $msg
 * @return json
 */
function returnData( $data, $ret = 0, $msg = 'success' )
{
    return buildResponse($ret, $msg, 200, $data);
}
 
function buildResponse($ret = 0, $msg = '', $status = 200, $data = array())
{    
    $info = array(
        'ret' => $ret,
        'msg' => $msg
    );
    if($data) {
        $info['data'] = $data;
    }
    return response(json_encode($info), $status)->header('Content-Type', 'application/json'); 
}

/**
 * 获取客户IP地址
 */
function getRealIP() {
    $ip = false;
    if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
        $ip = $_SERVER ["HTTP_CLIENT_IP"];
    }
    if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
        $ips = explode ( ", ", $_SERVER ['HTTP_X_FORWARDED_FOR'] );
        if ($ip) {
            array_unshift ( $ips, $ip );
            $ip = FALSE;
        }
        for($i = 0; $i < count ( $ips ); $i ++) {
            if (! eregi ( "^(10|172.16|192.168).", $ips [$i] )) {
                $ip = $ips [$i];
                break;
            }
        }
    }
    @$ip = $ip ? $ip : $_SERVER ['REMOTE_ADDR'];
    if (preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip) === 0) {
    	$ip = "192.168.0.1";
    }
    return $ip;
}

/**
 * curl方式 get数据
 *
 * @param mix $data
 * @param string $url
 *          全路径,如: http://127.0.0.1:8000/test
 */
function curlGet($url, $timeOut = 60) {
    if (! trim ( $url )) {
        return false;
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeOut );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    return $result;
}

/**
 * curl方式 post数据
 *
 * @param mix $data
 * @param string $url
 *          全路径,如: http://127.0.0.1:8000/test
 */
function curlPost($url, $params, $timeOut = 60) {
    if (! trim ( $url )) {
        return false;
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeOut );
    $result = curl_exec ( $ch );
    curl_close ( $ch );
    return $result;
}