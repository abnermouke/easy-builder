<?php


namespace Abnermouke\EasyBuilder\Library\Currency;

/**
 * Easy Builder Tool Library Power By Abnermouke
 * Class ToolLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class ToolLibrary
{

    /**
     * 获取更短Md5随机唯一字符
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 12:53:49
     * @param $md5_hash_str
     * @return string|string[]
     */
    public static function shortMd5($md5_hash_str)
    {
        //初始化解析字段
        $md5_bin_str = '';
        //拆分md5 hash结果
        foreach (str_split($md5_hash_str, 2) as $byte_str) {
            //追加解析字段
            $md5_bin_str .= chr(hexdec($byte_str));
        }
        //base64处理
        $md5_b64_str = base64_encode($md5_bin_str);
        //截取指定长度
        $md5_b64_str = substr($md5_b64_str, 0, 22);
        //替换无效字符并返回
        return str_replace(['+', '/'], ['-', '_'], $md5_b64_str);
    }

    /**
     * 查询某个元素是否存在某个一维数组
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 12:56:29
     * @param $search
     * @param $arr
     * @return bool
     */
    public static function existArr($search, $arr, $bool = true)
    {
        //搜索值
        $res = array_search($search, $arr);
        //判断是否成立
        if (is_numeric($res) && $res >= 0) {
            //返回成功
            return $bool ? true : (int)$res;
        }
        return $bool ? false : -1;
    }

    /**
     * 获取某个链接所有参数信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 12:58:35
     * @param $url
     * @return array
     */
    public static function parseUrlParams($url)
    {
        //初始化链接信息
        $url_info = parse_url($url);
        //设置参数信息
        $params = [];
        //判读是否存在参数
        if ($url_info['query']) {
            //拆分信息
            foreach (explode('&', $url_info['query']) as $param) {
                //初始化信息
                $param = explode('=', $param);
                //初始化信息
                $params[$param[0]] = $param[1];
            }
        }
        //返回参数
        return $params;
    }

    /**
     * XML转为数组（解析微信通知结果可使用）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 12:59:18
     * @param $xml
     * @return mixed
     */
    public static function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    /**
     * 字符串中提取纯文字
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 13:00:33
     * @param $string
     * @param int $num
     * @return string|string[]|null
     */
    public static function stringToText($string, $num = 0)
    {
        //判断字符串
        if($string){
            //把一些预定义的 HTML 实体转换为字符
            $html_string = htmlspecialchars_decode($string);
            //将空格替换成空
            $content = str_replace(" ", "", $html_string);
            //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
            $contents = strip_tags($content);
            //返回字符串中的前$num字符串长度的字符
            return (int)$num > 0 ? (mb_strlen($contents,'utf-8') > $num ? mb_substr($contents, 0, $num, "utf-8").'....' : mb_substr($contents, 0, $num, "utf-8")) : $content;
        }
        //返回愿字符串
        return $string;
    }

    /**
     * 隐藏凭证安全字符串（电话/身份证号码/邮箱等）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 13:03:11
     * @param $number
     * @param string $replace_word
     * @return string|string[]|null
     * @throws \Exception
     */
    public static function hideAuthenticateString($number, $replace_word = '*')
    {
        //判断是否为邮箱
        if (strpos($number, '@')) {
            //拆分号码
            $email_array = explode("@", $number);
            //获取前缀
            $prefix = (strlen($email_array[0]) < 4) ? "" : substr($number, 0, 3);
            //初始化次数
            $count = 0;
            //正则匹配
            $number = preg_replace('/([\d\w+_-]{0,100})@/', ($replace_word.$replace_word.$replace_word.'@'), $number, -1, $count);
            //组合信息
            $rs = $prefix . $number;
        } else if (ValidateLibrary::bankCard($number)) {
            //直接处理
            $rs = substr($number, 0, 4) . str_repeat($replace_word, strlen($number)-5) . substr($number, -4);
        } else {
            //初始化电话验证规则
            $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
            //正则验证
            if (preg_match($pattern, $number)) {
                //处理结果
                $rs = preg_replace($pattern, '$1****$2', $number);
            } else if (strlen($number) > 0) {
                //直接处理
                $rs = substr($number, 0, 3) . str_repeat($replace_word, strlen($number)-4) . substr($number, -1);
            } else {
                //直接返回
                $rs = $number;
            }
        }
        //返回匹配结果
        return $rs;
    }

    /**
     * 过滤字符串中的表情
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 13:04:24
     * @param $str
     * @return mixed
     */
    public static function filter_emoji($str)
    {
        //整理匹配规则
        $regex = '/(\\\u[ed][0-9a-f]{3})/i';
        //过滤信息
        return json_decode(preg_replace($regex, '', json_encode($str)), true);
    }

    /**
     * 色值转为rgb
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-10-22 13:05:07
     * @param $hexColor
     * @return array
     */
    public static function hex2rgb($hexColor)
    {
        $color = str_replace('#','',$hexColor);
        if (strlen($color)> 3){
            $rgb=array(
                'r'=>hexdec(substr($color,0,2)),
                'g'=>hexdec(substr($color,2,2)),
                'b'=>hexdec(substr($color,4,2))
            );
        }else{
            $rgb=array(
                'r'=>hexdec(substr($color,0,1). substr($color,0,1)),
                'g'=>hexdec(substr($color,1,1). substr($color,1,1)),
                'b'=>hexdec(substr($color,2,1). substr($color,2,1))
            );
        }
        return $rgb;
    }

    /**
     * 计算百分比金额
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2023-02-28 13:28:21
     * @param $amount int 金额（*100处理）
     * @param $percent int 指定比例
     * @return int
     */
    public static function amountPercentCompute($amount, $percent = 50)
    {
        //计算百分比数据
        return (int)(floor($amount * $percent /100));
    }
}
