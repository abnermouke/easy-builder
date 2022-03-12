<?php


namespace Abnermouke\EasyBuilder\Library\Cryptography;

use Illuminate\Support\Str;

/**
 * Easy Builder Cryptography To Encryption  Library Power By Abnermouke
 * Class EncryptionLibrary
 * @package Abnermouke\EasyBuilder\Library\Cryptography
 */
class EncryptionLibrary
{

    /**
     * 内部私钥（PKCS8 JAVA适用版 - 下载支付宝开放平台开发助手可转换私钥格式）
     * @var string
     */
    private $inside_private_key_pkcs8 = '';

    /**
     * 内部私钥（PKCS1 非JAVA适用版 - 下载支付宝开放平台开发助手可转换私钥格式）
     * @var string
     */
    private $inside_private_key_pkcs1 = '';

    /**
     * 外部共钥（外部系统提供）
     * @var string
     */
    private $outside_public_key = '';

    /**
     * 应用KEY
     * @var string
     */
    private $app_key = '';

    /**
     * 应用SECRET
     * @var string
     */
    private $app_secret = '';

    /**
     * 构造函数
     * EncryptionLibrary constructor.
     * @param false $app_key
     * @param false $app_secret
     * @param false $inside_private_key_pkcs8
     * @param false $inside_private_key_pkcs1
     * @param false $outside_public_key
     */
    public function __construct($app_key = false, $app_secret = false, $inside_private_key_pkcs8 = false, $inside_private_key_pkcs1 = false, $outside_public_key = false)
    {
        //设置内部私钥（PKCS8）
        $this->inside_private_key_pkcs8 = !$inside_private_key_pkcs8 ? config('project.rsa.default.inside_private_key_pkcs8') : trim($inside_private_key_pkcs8);
        //设置内部私钥（PKCS1）
        $this->inside_private_key_pkcs1 = !$inside_private_key_pkcs1 ? config('project.rsa.default.inside_private_key_pkcs1') : trim($inside_private_key_pkcs1);
        //设置外部公钥
        $this->outside_public_key = !$outside_public_key ? config('project.rsa.default.outside_public_key') : trim($outside_public_key);
        //设置应用KEY与SECRET
        $this->app_key = !$app_key ? config('project.rsa.default.app_key') : trim($app_key);
        $this->app_secret = !$app_key ? config('project.rsa.default.app_secret') : trim($app_secret);
    }

    /**
     * 加密
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-09-15 17:25:14
     * @param $content mixed 实际参数
     * @return false|string
     */
    public function encrypt($content)
    {
        //判断是否为数组
        $content = !is_array($content) ? compact('content') : $content;
        //排序内容
        krsort($content);
        //设置参数
        $content = (new SignatureLibrary($this->app_key, $this->app_secret))->create($content);
        //整理加密字符串
        $signature_string = json_encode($content, JSON_NUMERIC_CHECK);
        //检测字符串字符集
        $char_set = mb_detect_encoding($signature_string, ['UTF-8', 'GB2312', 'GBK']);
        //转换字符集
        $signature_string = mb_convert_encoding($signature_string, 'UTF-8', $char_set);
        //设置内部私钥
        if (!$private_key = openssl_pkey_get_private($this->formatRsaKey($this->inside_private_key_pkcs1, 'RSA PRIVATE'))) {
            //设置私钥失败
            return false;
        }
        //内部私钥加密
        openssl_private_encrypt($signature_string, $binary_signature, $private_key);
        //序列化签名
        $body = bin2hex($binary_signature);
        //设置外部公钥匙
        if (!$public_key = openssl_pkey_get_public($this->formatRsaKey($this->outside_public_key, 'PUBLIC'))) {
            //设置公钥失败
            return false;
        }
        //初始化加密结果
        $encrypt_res = '';
        //分段加密
        foreach (str_split($body, 245) as $chunk) {
            //公钥加密
            if (openssl_public_encrypt($chunk, $encryptData, $public_key)) {
                //设置加密结果
                $encrypt_res .= $encryptData;
            }
        }
        //序列化加密结果
        $encrypt_res = bin2hex($encrypt_res);
        //释放密钥
        openssl_free_key($public_key);
        openssl_free_key($private_key);
        //返回加密结果
        return $encrypt_res;
    }

    /**
     * 格式化加密密钥
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-09-15 16:48:20
     * @param $key
     * @param string $alias
     * @param int $length
     * @return string
     */
    private function formatRsaKey($key, $alias = 'RSA PRIVATE', $length = 64)
    {
        //拆分格式
        $key = chunk_split($key, (int)$length, "\n");
        //生成pem
        $pem = "-----BEGIN $alias KEY-----\n".$key."-----END $alias KEY-----";
        //返回pem
        return $pem;
    }

}
