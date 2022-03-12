<?php


namespace Abnermouke\EasyBuilder\Tools;

use Abnermouke\EasyBuilder\Library\Currency\SensitiveFilterLibrary;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * 可搜索字段构建器处理工具
 * Class SearchableTool
 * @package Abnermouke\EasyBuilder\Builders\Tools
 */
class SearchableTool
{

    //检索表表名称
    private static $searchable_table_prefix = 'aeb_';
    private static $searchable_table_name = 'searchable_words';
    //检索表表描述
    private static $searchable_table_description = 'easy_builder关键词检索表';
    //检索表字段
    private static $searchable_table_fields = [];

    /**
     * 设置关键词（违禁词自动过滤）
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 22:42:35
     * @param $field string 类型/对象标识
     * @param $object mixed 类型/对象对应唯一编码（ID）
     * @param array $keywords 储存关键词
     * @return array|mixed
     * @throws \Exception
     */
    public static function set($field, $object, $keywords = [])
    {
        //初始化工具内容
        self::init($field);
        //过滤违禁词
        $keywords = self::filterProhibitedWords($keywords);
        //判断数据
        if ($object && $keywords) {
            //查询当前ID存在的关键词
            if ($isset_keywords = DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->whereJsonContains($field, $object)->pluck('guard_name')->toArray()) {
                //获取需要移除项
                if ($diff_keywords = array_merge(array_diff($isset_keywords, $keywords))) {
                    //循环移除项
                    foreach ($diff_keywords as $k => $keyword) {
                        //整理关键词
                        $keyword = (string)$keyword;
                        //查询信息
                        $keyword_field_content = object_2_array(DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->where(['guard_name' => $keyword])->first($field)->$field);
                        //保存信息
                        DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->where(['guard_name' => $keyword])->update([$field => object_2_array(array_merge(array_diff($keyword_field_content, [$object]))), 'updated_at' => date('Y-m-d H:i:s')]);
                        //释放内存
                        unset($diff_keywords[$k]);
                    }
                }
            }
            //获取新增项
            if ($insert_keywords = (!$isset_keywords ? $keywords : array_merge(array_diff($keywords, $isset_keywords)))) {
                //循环新增关键词
                foreach ($insert_keywords as $k => $keyword) {
                    //整理关键词
                    $keyword = (string)$keyword;
                    //整理信息
                    $keyword_data = [
                        'guard_name' => $keyword,
                        $field => [$object],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    //判断关键词是否存在
                    if ($keyword_info = DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->where(['guard_name' => $keyword])->first()) {
                        //设置信息
                        $keyword_data[$field] = json_decode($keyword_info->$field, true);
                        //添加信息
                        $keyword_data[$field][] = $object;
                        //整理信息
                        $keyword_data[$field] = json_encode(array_unique($keyword_data[$field]));
                        //更新数据
                        DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->where(['guard_name' => $keyword])->update([$field => $keyword_data[$field], 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        //整理信息
                        $keyword_data[$field] = json_encode(array_unique($keyword_data[$field]));
                        //查询字段名
                        $columns = Schema::getColumnListing(self::$searchable_table_prefix.self::$searchable_table_name);
                        //设置字段信息
                        self::$searchable_table_fields = array_merge(array_diff($columns, ['id', 'guard_name', 'created_at', 'updated_at']));
                        //循环字段名
                        foreach (self::$searchable_table_fields as $searchable_table_field) {
                            //判断是否存在
                            if (!isset($keyword_data[$searchable_table_field])) {
                                //设置默认信息
                                $keyword_data[$searchable_table_field] = json_encode([]);
                            }
                        }
                        //新增数据
                        DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->insertGetId($keyword_data);
                    }
                    //释放内存
                    unset($insert_keywords[$k]);
                }
            }
        }
        //返回成功
        return $keywords;
    }

    /**
     * 根据检索词搜索满足对象
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-12 00:52:49
     * @param $field
     * @param $keywords
     * @return array
     */
    public static function search($field, $keywords)
    {
        //初始化对象
        $objects = [];
        //初始化工具内容
        self::init($field);
        //判断关键词信息
        if (object_2_array($keywords)) {
            //查询满足条件所有信息
            if ($rows = DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->whereIn('guard_name', object_2_array($keywords))->pluck($field)->toArray()) {
                //循环信息
                foreach ($rows as $k => $row) {
                    //判断是否有效
                    if (!object_2_array($row)) {
                        //移除信息
                        unset($rows[$k]);
                    }
                }
                //判断信息
                if ($rows) {
                    //重新排序
                    sort($rows);
                    //判断长度
                    if (count($rows) > 1) {
                        //整理信息
                        $merges = array_merge(object_2_array($rows[0]), object_2_array($rows[1]));
                        //循环结果
                        foreach ($rows as $k => $row) {
                            //判断索引值
                            if ((int)$k > 1) {
                                //继续叠加
                                $merges = array_merge($merges, object_2_array($row));
                            }
                        }
                    } else {
                        //设置信息
                        $merges = object_2_array(Arr::first($rows));
                    }
                    //获取objects
                    $objects = array_unique($merges);
                }
            }
        }
        //返回满足对象
        return $objects;
    }

    /**
     * 过滤违禁词
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-12 00:17:11
     * @param $keywords
     * @return mixed
     * @throws \Exception
     */
    private static function filterProhibitedWords($keywords)
    {
        //判断关键词
        if ($keywords) {
            //循环关键词
            foreach ($keywords as $k => $keyword) {
                //判断当前关键词是否为违禁词
                if (SensitiveFilterLibrary::islegal($keyword)) {
                    //移除当前关键词
                    unset($keywords[$k]);
                }
            }
        }
        //返回关键词
        return $keywords;
    }

    /**
     * 初始化工具内容
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 22:20:18
     * @param $field
     * @return mixed
     */
    private static function init($field)
    {
        //判断表是否存在
        if (!Schema::hasTable(self::$searchable_table_prefix.self::$searchable_table_name)) {
            //创建表信息
            self::createPackage();
        }
        //判断字段是否存在
        if (!Schema::connection('mysql')->hasColumn(self::$searchable_table_prefix.self::$searchable_table_name, $field)) {
            //创建字段
            Schema::table(self::$searchable_table_prefix.self::$searchable_table_name, function (Blueprint $table) use ($field) {
                //新增字段
                $table->longText($field)->comment(strtoupper($field).' 记录列')->after('guard_name');
            });
            //设置默认数据
            DB::connection('mysql')->table(self::$searchable_table_prefix.self::$searchable_table_name)->update([$field => json_encode([])]);
        }
        //返回成功
        return $field;
    }

    /**
     * 创建搜索类包
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-11 19:13:06
     * @return bool
     */
    private static function createPackage()
    {
        //创建信息
        Artisan::call('builder:package', [
            'name' => 'searchable_words',
            '--desc' => self::$searchable_table_description, '--dictionary' => 'abnermouke\builders',
            '--dp'=> self::$searchable_table_prefix, '--dc' => 'mysql', '--dcs' => 'utf8mb4', '--de' => 'innodb',
            '--cd' => 'file',
            '--migration' => true, '--cache' => true, '--controller' => true, '--fcp' => true
        ]);
        //查询全部应用迁移
        $abnermouke_migrations_path = database_path('migrations/abnermouke');
        //获取全部文件
        foreach (File::files($abnermouke_migrations_path) as $file) {
            //判断是否为当前搜索类包
            if (strstr($file->getFilename(), 'create_abnermouke___builders___searchable_words_table')) {
                //替换内容
                $content = str_replace(["//TODO : 其他字段配置\n", "//TODO : 索引配置\n"], ["
            //其他字段配置
            ".('$table->string(\'guard_name\', 200)->nullable(false)->default(\'\')->comment(\'关键词名称\');')."
                ", "
            //索引配置
            ".('$table->unique(\'guard_name\', \'GUARD_NAME\');')."
                "], file_get_contents($file->getRealPath()));
                //替换内容
                file_put_contents($file->getRealPath(), $content);
                //获取类名
                $class_name = 'CreateAbnermoukeBuildersSearchableWordsTable';
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
