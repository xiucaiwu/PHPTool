<?php

/**
 * Curl 封装
 */
namespace PHPTool;

class Curl {

    /**
     * 
     * @param string $method 请求方式
     * @param string $url 地址
     * @param mixed $fields 附带参数，可以是数组，也可以是字符串
     * @param string $userAgent 浏览器UA
     * @param array $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean|array
     */
    public static function execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
        $ch = self::create();
        if (false === $ch) {
            return false;
        }
        if (is_string($url) && strlen($url)) {
            $ret = curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            return false;
        }
        //是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($username != '') {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }
        
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        
        $method = strtolower($method);
        if ('post' == $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields AS $key => $val) {
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&', $sets);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        } else if ('put' == $method) {
            curl_setopt($ch, CURLOPT_PUT, true);
        }
        //curl_setopt($ch, CURLOPT_PROGRESS, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_MUTE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置curl超时秒数
        if (strlen($userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if (is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = array(curl_error($ch), curl_errno($ch));
			curl_close($ch);
			return $error;
        } else {
            curl_close($ch);
            if (!is_string($ret) || !strlen($ret)) {
                return false;
            }
            return $ret;
        }
    }

    /**
     * 发送POST请求
     * @param string $url 地址
     * @param mixed $fields 附带参数，可以是数组，也可以是字符串
     * @param string $userAgent 浏览器UA
     * @param array $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean
     */
    public static function post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
        $ret = self::execute('POST', $url, $fields, $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * GET
     * @param string $url 地址
     * @param string $userAgent 浏览器UA
     * @param array $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean
     */
    public static function get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
        $ret = self::execute('GET', $url, "", $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * curl支持 检测
     * @return bool|null|resource
     */
    private static function create() {
        $ch = null;
        if (!function_exists('curl_init')) {
            return false;
        }
        $ch = curl_init();
        if (!is_resource($ch)) {
            return false;
        }
        return $ch;
    }

}

