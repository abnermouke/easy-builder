<?php

namespace Abnermouke\EasyBuilder\Tools;

/**
 * 接口加密处理工具
 * Class InterfaceCryptographyTool
 * @package Abnermouke\EasyBuilder\Tools
 */
class InterfaceCryptographyTool
{

    //应用验签KEY
    private $app_key = '';
    //应用验签密钥
    private $app_secret = '';
    //本地RSA私钥（PKCS1<非JAVA适用>）
    private $private_rsa_key = '';
    //外部平台RSA公钥
    private $outside_public_rsa_key = '';
    //请求基础域名
    private $base_domain = '';
    //接口超时时长(s)
    private $timeout = 60;

    /**
     * 构造函数
     * InterfaceCryptographyImplementers constructor.
     * @param $domain
     * @param $app_key
     * @param $app_secret
     * @param $private_rsa_key
     * @param $outside_public_rsa_key
     * @param int $timeout
     */
    public function __construct($domain, $app_key, $app_secret, $private_rsa_key, $outside_public_rsa_key, $timeout = 60)
    {
        //设置参数
        $this->base_domain = trim($domain);
        $this->app_key = trim($app_key);
        $this->app_secret = trim($app_secret);
        $this->private_rsa_key = trim($private_rsa_key);
        $this->outside_public_rsa_key = trim($outside_public_rsa_key);
        $this->timeout = $timeout;
    }


    /**
     * 生成实例对象
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-08 12:57:24
     * @param $domain
     * @param $app_key
     * @param $app_secret
     * @param $private_rsa_key
     * @param $outside_public_rsa_key
     * @param int $timeout
     * @return InterfaceCryptographyTool
     */
    public static function make($domain, $app_key, $app_secret, $private_rsa_key, $outside_public_rsa_key, $timeout = 60)
    {
        //返回当前实例
        return new InterfaceCryptographyTool($domain, $app_key, $app_secret, $private_rsa_key, $outside_public_rsa_key, $timeout);
    }

    /**
     * 通用发起请求
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-09 12:41:43
     * @param $url
     * @param array $params
     * @param string $method
     * @return false|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($url, $params = [], $method = 'post')
    {
        //实例化请求
        $client = new Client();
        //尝试发起请求
        try {
            //发送请求
            $response = $client->request($method, $this->base_domain.$url, [
                'form_params' => [
                    '__encrypt__' => $this->encrypt($params)
                ],
            ]);
        } catch (\Exception $exception) {
            //返回失败
            return false;
        }
        //判断是否请求失败
        if ((int)$response->getStatusCode() !== 200) {
            //返回失败
            return false;
        }
        //获取返回结果
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 解密传输参数
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-08 13:24:59
     * @param $encrypt_content
     * @return false|mixed
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
            if (openssl_private_decrypt($chunk, $decryptData, $this->formatRsaKey($this->private_rsa_key, 'RSA PRIVATE'), OPENSSL_PKCS1_PADDING)) {
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
        //设置解密结果
        $decryptData = '';
        //分段加密
        foreach (str_split($decrypt_content, (int)$rsa_decrypt_block_size) as $chunk) {
            //外部公钥解密
            if (openssl_public_decrypt($chunk, $decrypt_string, $this->formatRsaKey($this->outside_public_rsa_key, 'PUBLIC'), OPENSSL_PKCS1_PADDING)) {
                //设置加密结果
                $decryptData .= $decrypt_string;
            }
        }
        //判断结果
        if (!$decryptData || empty($decryptData)) {
            //解密失败
            return false;
        }
        //整理信息
        $content = json_decode($decryptData, true);
        //判断接口超时时长
        if ((int)$content['__timestamp__'] + (int)$this->timeout < time()) {
            //返回失败
            return false;
        }
        //获取处理参数
        $data = $content;
        //移除系统默认参数
        unset($data['__timestamp__'], $data['__nonceStr__'], $data['__signature__']);
        //循环数组
        foreach ($data as $key => $value) {
            //判断是否为null
            if (is_null($value)) {
                //设置内容
                $data[$key] = '';
            }
        }
        //倒序排列
        krsort($data);
        //获取body签名
        $body_signature = $this->signature($data, $content['__timestamp__'], $content['__nonceStr__']);
        //判断签名
        if (trim($body_signature) !== trim($content['__signature__'])) {
            //返回失败
            return false;
        }
        //返回内容
        return $data;
    }

    /**
     * 加密传输参数
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-08 13:08:11
     * @param array $data
     * @return false|string
     */
    private function encrypt($data = [])
    {
        //判断信息
        if ($data) {
            //循环数组
            foreach ($data as $key => $value) {
                //判断是否为null
                if (is_null($value)) {
                    //设置为空
                    $data[$key] = '';
                }
            }
            //倒序排列
            krsort($data);
        }
        //整理参数
        $__timestamp__ = time();
        $__nonceStr__ = $this->getRandChar();
        //获取加密字符串
        $__signature__ = $this->signature($data, $__timestamp__, $__nonceStr__);
        //设置内容
        $data = array_merge($data, compact('__timestamp__', '__nonceStr__', '__signature__'));
        //整理加密字符串
        $signature_string = json_encode($data, JSON_NUMERIC_CHECK);;
        //检测字符串字符集
        $char_set = mb_detect_encoding($signature_string, ['UTF-8', 'GB2312', 'GBK']);
        //转换字符集
        $signature_string = mb_convert_encoding($signature_string, 'UTF-8', $char_set);
        //设置内部私钥
        if (!$private_key = openssl_pkey_get_private($this->formatRsaKey($this->private_rsa_key, 'RSA PRIVATE'))) {
            //设置私钥失败
            return false;
        }
        //初始化加密结果
        $binary_signature = '';
        //分段加密
        foreach (str_split($signature_string, 117) as $chunk) {
            //内部私钥加密
            if (openssl_private_encrypt($chunk, $binaryEncryptData, $private_key)) {
                //设置加密结果
                $binary_signature .= $binaryEncryptData;
            }
        }
        //序列化签名
        $body = bin2hex($binary_signature);
        //设置外部公钥匙
        if (!$public_key = openssl_pkey_get_public($this->formatRsaKey($this->outside_public_rsa_key, 'PUBLIC'))) {
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
     * 生成签名
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-08 13:06:16
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
     * 获取随机字符串
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-08 13:05:03
     * @param int $length
     * @return false|string
     */
    private function getRandChar($length = 6)
    {
        //设置字符串集合
        $string = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        //随机截取字符串
        return substr(str_shuffle($string),mt_rand(0,strlen($string)-($length + 1)),$length);
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
