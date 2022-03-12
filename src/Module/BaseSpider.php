<?php

namespace Abnermouke\EasyBuilder\Module;

use Abnermouke\EasyBuilder\Library\CodeLibrary;
use Abnermouke\EasyBuilder\Library\Currency\FakeUserAgentLibrary;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Easy Builder Basic Spider Module Power By Abnermouke
 * Class BaseSpider
 * @package Abnermouke\EasyBuilder\Module
 */
class BaseSpider
{

    //响应信息
    private $response;
    //处理成功结果
    protected $result;

    /**
     * 开始请求信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-31 12:09:49
     * @param $link string 请求链接
     * @param array $form_params 请求参数
     * @param string $method 请求方式
     * @param bool $decode 是否反JSON返回结果
     * @param int $timeout 超时时间（0为不限制，>0单位：s）
     * @param array|bool $headers 访问由信息
     * @param array|bool $ops 自定义请求配置
     * @return bool
     * @throws \Exception
     */
    protected function query($link, $form_params = [], $method = 'POST', $decode = true, $timeout = 1000, $headers = false, $ops = false)
    {
        //实例化请求
        $client = new Client();
        //尝试发起请求
        try {
            //处理化处理链接
            $request_link = $this->initRequestLink($link, $form_params, $method);
            //判断链接是否相同
            if ($request_link !== $link) {
                //清空参数
                $form_params = [];
                //设置请求链接
                $link = $request_link;
            }
            //整理信息
            $options = ['timeout' => (int)$timeout, 'verify' => false, 'headers' => $this->initRequestHeaders($headers)];
            //判断是否存在参数
            if ($form_params) {
                //设置参数
                $options['form_params'] = $form_params;
            }
            //判断是否存在自定义请求配置
            $ops && $options = array_merge($options, $ops);
            //发起请求
            $response = $client->request(strtoupper($method), $link, $options);
            //判断是否请求成功
            if ((int)$response->getStatusCode() !== CodeLibrary::CODE_SUCCESS) {
                //返回错误
                return $this->error(CodeLibrary::GUZZLE_HTTP_REQUEST_FAIL, $response->getStatusCode());
            }
            //获取结果集
            $result = $response->getBody()->getContents();
            //判断是否反序列化
            if ($decode) {
                //反Json序列化
                $result = json_decode($result, true);
            }
        } catch (\Exception $exception) {
            //返回错误
            return $this->error($exception->getCode(), $exception->getMessage());
        } catch (GuzzleException $exception) {
            //返回错误
            return $this->error(CodeLibrary::GUZZLE_HTTP_REQUEST_ERROR, $exception->getCode().':'.$exception->getMessage());
        }
        //返回处理结果
        return $this->success($result);
    }

    /**
     * 初始化访问头信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-09-24 14:13:30
     * @param false|array $headers 自定义头信息
     * @return array|false
     * @throws \Exception
     */
    private function initRequestHeaders($headers = false)
    {
        //初始化默认headers
        $default_headers = [
            'User-Agent' => FakeUserAgentLibrary::random(),
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Referer' => '',
        ];
        //判断数据
        if ($headers) {
            //重置信息
            $headers = array_merge($default_headers, $headers);
        } else {
            //设置头信息
            $headers = $default_headers;
        }
        //返回headers
        return $headers;
    }

    /**
     * 初始化请求链接
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-31 12:09:11
     * @param $link string 请求链接
     * @param $form_params array 请求参数
     * @param $method string 请求方式
     * @return string
     * @throws \Exception
     */
    private function initRequestLink($link, $form_params, $method)
    {
        //判断请求方式
        if (strtoupper($method) === 'GET' && !empty($form_params)) {
            //整理信息
            $params = [];
            //循环参数信息
            foreach ($form_params as $k => $param) {
                //设置信息
                $params[] = $k.'='.urlencode($param);
            }
            //设置参数信息
            $link .= (strstr($link, '?') ? '' : '?').implode('&', $params);
        }
        //返回请求链接
        return $link;
    }

    /**
     * 获取处理结果响应
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-31 11:59:06
     * @return mixed
     * @throws \Exception
     */
    public function getResponse()
    {
        //返回处理结果响应
        return $this->response;
    }

    /**
     * 返回成功
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-31 22:45:28
     * @param $result array 返回结果集
     * @param string $msg 返回提示信息
     * @return bool
     * @throws \Exception
     */
    protected function success($result, $msg = '')
    {
        //设置错误响应信息
        $this->response = ['state' => true, 'code' => CodeLibrary::CODE_SUCCESS, 'msg' => $msg, 'data' => $result];
        //设置结果集
        $this->result = $result;
        //返回成功
        return true;
    }

    /**
     * 返回错误
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-08-31 11:55:56
     * @param $error_code int 错误编码
     * @param string $error_msg 错误信息
     * @param array $extra_data 返回数据
     * @return bool
     * @throws \Exception
     */
    protected function error($error_code, $error_msg = '', $extra_data = [])
    {
        //设置错误响应信息
        $this->response = ['state' => false, 'code' => (int)$error_code, 'msg' => $error_msg, 'data' => $extra_data];
        //返回失败
        return false;
    }

}
