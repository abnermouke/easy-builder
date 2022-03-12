<?php


namespace Abnermouke\EasyBuilder\Library\Cryptography;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Easy Builder Signature Library Power By Abnermouke
 * Class SignatureLibrary
 * @package Abnermouke\EasyBuilder\Library\Cryptography
 */
class SignatureLibrary
{

    private $app_key;
    private $app_secret;

    /**
     * 构造函数
     * SignatureLibrary constructor.
     * @param string $app_key 应用KEY
     * @param string $app_secret 应用SECRET
     * @throws \Exception
     */
    public function __construct($app_key = '', $app_secret = '')
    {
        //初始化APP KEY
        $this->app_key = $app_key ?? config('project.signature.app_key', auto_datetime('Ymd'));
        //初始化APP Secret
        $this->app_secret = $app_secret ?? config('project.signature.app_secret', auto_datetime('YmdHis'));
    }

    /**
     * 创建签名
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 15:12:52
     * @param $body array 加密内容
     * @return array
     * @throws \Exception
     */
    public function create($body)
    {
        //循环数组
        foreach ($body as $key => $value) {
            //判断是否为null
            if (is_null($value)) {
                //设置内容
                $body[$key] = '';
            }
        }
        //倒序排列
        krsort($body);
        //整理参数
        $__timestamp__ = time();
        $__nonceStr__ = Str::random(8);
        //获取加密字符串
        $__signature__ = $this->signature($body, $__timestamp__, $__nonceStr__);
        //设置内容
        $body = array_merge($body, compact('__timestamp__', '__nonceStr__', '__signature__'));
        //返回结果信息
        return $body;
    }

    /**
     * 生成签名
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 15:18:08
     * @param $body
     * @param $timestamp
     * @param $nonceStr
     * @return string
     */
    private function signature($body, $timestamp, $nonceStr)
    {
        //生成签名
        return  md5($this->app_key.$timestamp.json_encode($body, JSON_NUMERIC_CHECK|JSON_PRESERVE_ZERO_FRACTION).$nonceStr.$this->app_secret);
    }

    /**
     * 验证签名
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 15:19:54
     * @param $body array 解密内容
     * @return false|array
     */
    public function verify($body)
    {
        //移除系统参数
        $content = Arr::except($body, ['__timestamp__', '__nonceStr__', '__signature__']);
        //循环数组
        foreach ($content as $key => $value) {
            //判断是否为null
            if (is_null($value)) {
                //设置内容
                $content[$key] = '';
            }
        }
        //倒序排列
        krsort($content);
        //获取body签名
        $body_signature = $this->signature($content, data_get($body, '__timestamp__', 0), data_get($content, '__nonceStr__', ''));
        //判断签名
        if (trim($body_signature) !== trim(data_get($content, '__signature__', ''))) {
            //返回失败
            return false;
        }
        //返回内容
        return $body;
    }



}
