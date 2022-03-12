<?php

namespace Abnermouke\EasyBuilder\Library\Currency;

use Abnermouke\EasyBuilder\Library\CodeLibrary;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Easy Builder Amap Location Library Power By Abnermouke
 * Class AmapLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class AmapLibrary
{

    //IP定位种子链接
    private $ip_location_seed_link = 'https://restapi.amap.com/v5/ip';

    /**
     * 根据IP获取行政地区编码（省级）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-12-10 11:41:50
     * @param $ip
     * @param false $key
     * @param int $ip_type
     * @param int[] $default_provinces
     * @return array|int|mixed
     */
    public function ip($ip, $key = false, $ip_type = 4, $default_provinces = [110000, 120000, 310000, 500000])
    {
        //整理参数
        $params = [
            'key' => $key ? $key : config('project.amap_web_server_api_key', ''),
            'type' => (int)$ip_type,
            'ip' => $ip,
        ];
        //设置默认地区
        $default_province = Arr::random($default_provinces);
        //整理链接
        $seed_link = $this->ip_location_seed_link.'?'.http_build_query($params);
        //尝试发起请求
        try {
            //生成请求实例
            $client = new Client();
            //发起请求
            $response = $client->get($seed_link, ['verify' => false]);
        } catch (\Exception $exception) {
            //设置默认地区
            $default_province = Arr::random($default_provinces);
        }
        //判断请求方式
        if ((int)$response->getStatusCode() === CodeLibrary::CODE_SUCCESS) {
            //获取请求结果
            $result = $response->getBody()->getContents();
            //解析结果
            $result = object_2_array($result);
            //判断结果
            if ((int)data_get($result, 'status', 0) === 1) {
                //获取省份
                $province = data_get($result, 'adcode', []);
                //判断省份信息
                if ($province && !empty($province)) {
                    //设置默认地区code
                    $default_province = (int)$province;
                }
            }
        }
        //返回地区信息
        return $default_province;
    }

}
