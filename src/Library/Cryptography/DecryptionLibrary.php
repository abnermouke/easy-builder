<?php


namespace Abnermouke\EasyBuilder\Library\Cryptography;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Easy Builder Cryptography To Decryption  Library Power By Abnermouke
 * Class DecryptionLibrary
 * @package Abnermouke\EasyBuilder\Library\Cryptography
 */
class DecryptionLibrary
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
     * DecryptionLibrary constructor.
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
     * 解密
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-09-15 18:01:45
     * @param $encrypt_content
     * @return array|false
     */
    public function decrypt($encrypt_content)
    {
        //转换二进制
        $content = pack('H*', $encrypt_content);
        //设置解密长度
        $rsa_decrypt_block_size = 256;
        //设置私钥解密结果
        $decrypt_res = '';
        //分段解密
        foreach (str_split($content, (int)$rsa_decrypt_block_size) as $chunk) {
            //私钥解密
            if (openssl_private_decrypt($chunk, $decryptData, $this->formatRsaKey($this->inside_private_key_pkcs1, 'RSA PRIVATE'), OPENSSL_PKCS1_PADDING)) {
                //设置私钥解密结果
                $decrypt_res .= $decryptData;
            }
        }
        //判断结果
        if (!$decrypt_res || empty($decrypt_res)) {
            //解密失败
            return false;
        }
        //转换二进制
        $decrypt_content = pack('H*', $decrypt_res);
        //公钥解密
        openssl_public_decrypt($decrypt_content, $decryptData, $this->formatRsaKey($this->outside_public_key, 'PUBLIC'), OPENSSL_PKCS1_PADDING);
        //判断结果
        if (!$decryptData || empty($decryptData)) {
            //解密失败
            return false;
        }
        //整理信息
        $content = json_decode($decryptData, true);
        //验证签名
        if (!$body = (new SignatureLibrary($this->app_key, $this->app_secret))->verify($content)) {
            //返回失败
            return false;
        }
        //返回结果
        return $body;
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
