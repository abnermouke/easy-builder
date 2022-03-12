<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('current_auth')) {
    /**
     * 获取当前session授权信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-06-29 11:47:17
     * @param bool $item
     * @param string $prefix
     * @return array|bool|mixed
     * @throws \Exception
     */
    function current_auth($item = false, $prefix = 'your-project-alias:account')
    {
        //获取session信息
        if (!$auth = Session::get(auth_name($prefix), false)) {
            //返回失败
            return false;
        }
        //整理信息
        return $item && !empty($item) ? data_get($auth, $item, false) : $auth;
    }
}

if (!function_exists('set_current_auth')) {
    /**
     * 设置当前session认证信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-03-23 23:41:50
     * @param $auth
     * @param string $prefix
     * @throws Exception
     */
    function set_current_auth($auth, $prefix = 'your-project-alias:account')
    {
        //设置session信息
        return Session::put(auth_name($prefix), $auth);
    }
}

if (!function_exists('auth_name')) {
    /**
     * 认证session名称
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-06-29 11:45:09
     * @param string $prefix
     * @return string
     * @throws \Exception
     */
    function auth_name($prefix = 'your-project-alias:account')
    {
        //获取session授权名称
        return ($prefix && !empty($prefix) ? $prefix : 'your-project-alias:account').'_auth_info';
    }
}

if (!function_exists('auth_remove')) {
    /**
     * 删除session认证信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-06-29 11:48:50
     * @param string $prefix
     * @param bool $clear_all
     * @return bool
     * @throws \Exception
     */
    function auth_remove($prefix = 'your-project-alias:account', $clear_all = false)
    {
        //删除当前缓存
        Session::forget(auth_name($prefix));
        //判断是否删除全部
        Session::flush();
        //返回成功
        return true;
    }
}
