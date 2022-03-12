<?php


namespace Abnermouke\EasyBuilder\Library\Cryptography;

/**
 * Easy Builder AES Decryption Library Power By Abnermouke
 * Class AesLibrary
 * @package Abnermouke\EasyBuilder\Library\Cryptography
 */
class AesLibrary
{
    /**
     * 解密表单数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 14:43:20
     * @param $data array 前端传输数据
     * @param string $key 加密字段
     * @param false|string $aes_encrypt_key 加密KEY
     * @param false|string $aes_iv 加密IV
     * @return false|mixed
     * @throws \Exception
     */
    public static function decryptFormData($data, $key = '__encrypt__', $aes_encrypt_key = false, $aes_iv = false)
    {
        //整理信息
        $aes_encrypt_key = !$aes_encrypt_key ? (auto_datetime('Ymd').config('project.aes.encrypt_key_suffix')) : $aes_encrypt_key;
        $aes_iv = !$aes_iv ? config('project.aes.iv') : $aes_iv;
        //判断信息
        if (data_get($data, $key, false)) {
            //获取加密字符串
            $encrypt_string = data_get($data, $key, '');
            //解密信息
            $encrypt_data = openssl_decrypt($encrypt_string, 'AES-128-CBC', $aes_encrypt_key, OPENSSL_ZERO_PADDING , $aes_iv);
            //整理信息
            $json_str = rtrim($encrypt_data, "\0");
            //获取表单数据
            return json_decode($json_str, true);
        }
        //返回失败
        return false;
    }

}
