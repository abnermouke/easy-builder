<?php

namespace Abnermouke\EasyBuilder\Tools;

use Abnermouke\EasyBuilder\Library\CodeLibrary;
use App\Handler\Cache\Data\Abnermouke\Builders\SentenceCacheHandler;
use App\Repository\Abnermouke\Builders\SentenceRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * 语录/句子构建处理工具
 * Class SentenceTool
 * @package Abnermouke\EasyBuilder\Tools
 */
class SentenceTool
{

    //爬取种子链接
    private static $seed_link = 'https://sentence.iciba.com/index.php?c=dailysentence&m=getdetail&title={DATE}';

    //检索表表名称
    private static $sentence_table_prefix = 'aeb_';
    private static $sentence_table_name = 'sentences';
    //检索表表描述
    private static $sentence_table_description = 'easy_builder语录句子表';
    //检索表字段
    private static $sentence_table_fields = [];

    /**
     * 执行抓取操作
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-23 17:07:03
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function run()
    {
        //初始化信息
        self::init();
        //查询已爬取句子最大日期
        if (!$max_date = (new SentenceRepository())->max('date')) {
            //设置为当月第一天
            $max_date = auto_datetime('Y-m-01');
        }
        //开始爬取每日句子
        self::crawlDailySentence($max_date);
        //刷新缓存
        (new SentenceCacheHandler())->refresh();
        //返回成功
        return true;
    }

    /**
     * 抓取每日一句
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-23 17:05:39
     * @param $date
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private static function crawlDailySentence($date)
    {
        //判断日期是否大于今日
        if (($strtotime = strtotime($date)) < time()) {
            //获取日期
            $date = auto_datetime('Y-m-d', $strtotime);
            //尝试发起请求
            try {
                //发起请求
                $response = (new Client())->get(str_replace('{DATE}', $date, self::$seed_link), [
                    'verify' => false,
                ]);
            } catch (\Exception $exception) {
                //返回失败
                return false;
            }
            //获取状态
            if ((int)$response->getStatusCode() !== CodeLibrary::CODE_SUCCESS) {
                //返回失败
                return false;
            }
            //获取结果集
            if (($result = json_decode($response->getBody()->getContents(), true)) && data_get($result, 'errmsg', '') == 'success') {
                //整理信息
                $sentence = [
                    'date' => $date,
                    'sentence_cn' => data_get($result, 'note', ''),
                    'sentence_en' => data_get($result, 'content', ''),
                    'created_at' => auto_datetime(),
                    'updated_at' => auto_datetime()
                ];
                //更新或新增数据
                (new SentenceRepository())->updateOrInsert(['date' => $sentence['date']], $sentence);
            }
            //抓取下一天
            return self::crawlDailySentence(auto_datetime('Y-m-d', strtotime($date) + 86400));
        }
        //返回成功
        return true;
    }

    /**
     * 初始化表信息
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-23 02:12:13
     * @return bool
     */
    private static function init()
    {
        //判断表是否存在
        if (!Schema::hasTable(self::$sentence_table_prefix.self::$sentence_table_name)) {
            //创建表信息
            self::createPackage();
        }
        //返回成功
        return true;
    }

    /**
     * 创建句子类包
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-07-23 02:10:49
     * @return bool
     */
    private static function createPackage()
    {
        //创建信息
        Artisan::call('builder:package', [
            'name' => 'sentences',
            '--desc' => self::$sentence_table_description, '--dictionary' => 'abnermouke\builders',
            '--dp'=> self::$sentence_table_prefix, '--dc' => 'mysql', '--dcs' => 'utf8mb4', '--de' => 'innodb',
            '--cd' => 'file',
            '--migration' => true, '--cache' => true, '--controller' => true, '--fcp' => true
        ]);
        //查询全部应用迁移
        $abnermouke_migrations_path = database_path('migrations/abnermouke');
        //获取全部文件
        foreach (File::files($abnermouke_migrations_path) as $file) {
            //判断是否为当前搜索类包
            if (strstr($file->getFilename(), 'create_abnermouke___builders___sentences_table')) {
                //替换内容
                $content = str_replace(["//TODO : 其他字段配置\n", "//TODO : 索引配置\n"], ["
            //其他字段配置
            "
                .('$table->date(\'date\')->nullable(false)->comment(\'日期\');')."\r\n".
                ('$table->text(\'sentence_cn\')->nullable()->comment(\'中文句子\');')."\r\n".
                ('$table->text(\'sentence_en\')->nullable()->comment(\'英文句子\');')."\r\n".
            "
                ", "
            //索引配置
            ".('$table->unique(\'date\', \'DATE\');')."
                "], file_get_contents($file->getRealPath()));
                //替换内容
                file_put_contents($file->getRealPath(), $content);
                //获取类名
                $class_name = 'CreateAbnermoukeBuildersSentencesTable';
                //引入文件
                require_once $file->getRealPath();
                //判断是否存在类信息
                if (class_exists($class_name)) {
                    //引入class
                    $class = new $class_name;
                    //迁移信息
                    $class->up();
                }
            }
        }
        //返回成功
        return true;
    }
}
