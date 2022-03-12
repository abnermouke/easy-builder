<?php

namespace Abnermouke\EasyBuilder\Library\Currency;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\JiebaAnalyse;
use Fukuball\Jieba\Posseg;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

/**
 * Easy Builder Jieba Participle Library Power By Abnermouke
 * Class JiebaLibrary
 * @package Abnermouke\EasyBuilder\Library\Currency
 */
class JiebaLibrary
{

    /**
     * 普通分词
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-15 13:04:20
     * @param $content_raw string 处理语句
     * @param string $user_dictionary 自定义词典，提高纠错能力（一個詞佔一行；每一行分為三部分，一部分為詞語，一部分為詞頻，一部分為詞性，用空格隔開）
     * @param false $full_mode 是否全模式，默认精准模式（false）
     * @param int $memory_limit 内存调整数值（单位：M）
     * @return array
     * @throws \Exception
     */
    public static function cut($content_raw, $user_dictionary = '', $full_mode = false, $memory_limit = 1024)
    {
        //调整运行内存
        ini_set('memory_limit', $memory_limit.'M');
        //初始化Jieba
        Jieba::init();
        //初始化Finalseg
        Finalseg::init();
        //判断是否自定义词词典
        if ($user_dictionary && File::exists($user_dictionary)) {
            //添加自定义词典
            Jieba::loadUserDict($user_dictionary);
        }
        //拆分词组
        $dictionary = Arr::where(array_unique(Jieba::cut($content_raw, $full_mode)), function ($value, $key) {
            //长度大于1的元素
            return mb_strlen($value, 'utf-8') > 1;
        });
        //判断并返回信息
        return !empty($dictionary) ? array_values($dictionary) : [];
    }

    /**
     * 搜索引擎模式分词（拆分搜索关键词）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-15 15:48:23
     * @param $content_raw string 处理语句
     * @param int $memory_limit 内存调整数值（单位：M）
     * @return array
     * @throws \Exception
     */
    public static function cutKeywords($content_raw, $memory_limit = 1024)
    {
        //调整运行内存
        ini_set('memory_limit', $memory_limit.'M');
        //初始化Jieba
        Jieba::init();
        //初始化Finalseg
        Finalseg::init();
        //拆分词组
        $dictionary = Arr::where(array_unique(Jieba::cutForSearch($content_raw)), function ($value, $key) {
            //长度大于1的元素
            return mb_strlen($value, 'utf-8') > 1;
        });
        //判断并返回信息
        return !empty($dictionary) ? array_values($dictionary) : [];
    }

    /**
     * 分词获取标签（拆分标签）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-15 15:48:53
     * @param $content_raw string 处理语句
     * @param int $top_limit 获取前多少位，默认：20
     * @param string $stop_dictionary 自定义停止词典
     * @param int $memory_limit 内存调整数值（单位：M）
     * @return array
     * @throws \Exception
     */
    public static function cutTags($content_raw, $top_limit = 20, $stop_dictionary = '', $memory_limit = 2048)
    {
        //调整运行内存
        ini_set('memory_limit', $memory_limit.'M');
        //初始化Jieba
        Jieba::init();
        //初始化Finalseg
        Finalseg::init();
        //初始化JiebaAnalyse
        JiebaAnalyse::init();
        //判断是否自定义停止文本
        if ($stop_dictionary) {
            //添加自定义停止文本
            JiebaAnalyse::setStopWords($stop_dictionary);
        }
        //获取指定靠前数量标签
        return Arr::where(JiebaAnalyse::extractTags($content_raw, (int)$top_limit), function ($value, $key) {
            //长度大于1的元素
            return mb_strlen($value, 'utf-8') > 1;
        });
    }

    /**
     * 分词并分解词性
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-10-15 16:38:52
     * @param $content_raw string 处理语句
     * @param array $except_types 剔除词性
     * @param string $user_dictionary 自定义词典，提高纠错能力（一個詞佔一行；每一行分為三部分，一部分為詞語，一部分為詞頻，一部分為詞性，用空格隔開）
     * @param int $memory_limit 内存调整数值（单位：M）
     * @return array
     * @throws \Exception
     */
    public static function cutTypes($content_raw, $except_types = ['w', 'u', 'ud', 'ug', 'uj', 'ul', 'uv', 'uz', 'q', 'p', 'd', 'df', 'dg', 'x', 'z', 'zg'], $user_dictionary = '', $memory_limit = 2048)
    {
        //调整运行内存
        ini_set('memory_limit', $memory_limit.'M');
        //初始化Jieba
        Jieba::init();
        //初始化Finalseg
        Finalseg::init();
        //初始化Posseg
        Posseg::init();
        //判断是否自定义词词典
        if ($user_dictionary && File::exists($user_dictionary)) {
            //添加自定义词典
            Jieba::loadUserDict($user_dictionary);
        }
        //拆分词性
        $words = array_column(Posseg::cut($content_raw), 'tag', 'word');
        //判断是否筛选词性
        if ($except_types) {
            //处理词性
            $words = Arr::where($words, function ($type, $word) use ($except_types) {
                //判断是否不允许此词性
                return !in_array($type, $except_types, true);
            });
        }
        //返回分词结果
        return $words;
    }

    /**
     * 分词并分解词性获取权重排列数据
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-15 15:49:48
     * @param $content_raw string 处理语句
     * @param array $except_types 剔除词性
     * @param string $user_dictionary 自定义词典，提高纠错能力（一個詞佔一行；每一行分為三部分，一部分為詞語，一部分為詞頻，一部分為詞性，用空格隔開）
     * @param int $top_limit 获取前多少位，默认：20
     * @param int $memory_limit 内存调整数值（单位：M）
     * @return array
     * @throws \Exception
     */
    public static function cutSearchKeywords($content_raw, $except_types = ['w', 'u', 'ud', 'ug', 'uj', 'ul', 'uv', 'uz', 'q', 'p', 'd', 'df', 'dg', 'x', 'z', 'zg'], $user_dictionary = '', $top_limit = 20, $memory_limit = 2048)
    {
        //根据词性拆分内容
        $words = self::cutTypes($content_raw, $except_types, $user_dictionary, (int)$memory_limit);
        //返回分词结果
        return $words ? array_keys(self::cutTags(implode(' ', array_keys($words)), (int)$top_limit)) : [];
    }

    /**
     * 获取字典词性解释
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company <Macbook Pro>
     * @Time 2020-10-15 16:12:50
     * @return string[]
     * @throws \Exception
     */
    public static function dictionaryTypes()
    {
        //初始化字典词性对应意思
        return [
            'a' => '形容词', 'ad' => '副形词', 'ag' => '形容词性语素', 'an' => '名形词', 'b' => '区别词',
            'c' => '连词', 'd' => '副词', 'df' => '副词*', 'dg' => '副语素', 'e' => '叹词',
            'eng' => '外语', 'f' => '方位词', 'g' => '语素', 'h' => '前接成分', 'i' => '成语',
            'j' => '简称略语', 'k' => '后接成分', 'l' => '习用语', 'm' => '数词', 'mg' => '数语素',
            'mq' => '数词*', 'n' => '名词', 'ng' => '名语素', 'nr' => '人名', 'nrfg' => '名词*',
            'nrt' => '名词*', 'ns' => '地名', 'nt' => '机构团体', 'nz' => '其他专名', 'o' => '拟声词',
            'p' => '介词', 'q' => '量词', 'r' => '代词', 'rg' => '代词语素', 'rr' => '代词*',
            'rz' => '代词*', 's' => '处所词', 't' => '时间词', 'tg' => '时语素', 'u' => '助词',
            'ud' => '助词*', 'ug' => '助词*', 'uj' => '助词*', 'ul' => '助词*', 'uv' => '助词*',
            'uz' => '助词*', 'v' => '动词', 'vd' => '副动词', 'vg' => '动语素', 'vi' => '动词*',
            'vn' => '名动词', 'vq' => '动词*', 'w' => '标点符号', 'x' => '非语素字', 'y' => '语气词',
            'z' => '状态词', 'zg' => '状态词*'
        ];
    }
}
