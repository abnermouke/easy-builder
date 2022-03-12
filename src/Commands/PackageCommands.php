<?php

namespace Abnermouke\EasyBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Easy Builder to package
 * Class PackageCommands
 * @package Abnermouke\EasyBuilder\Commands
 */
class PackageCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'builder:package {name} {--desc=} {--dictionary=} {--dp=} {--dc=} {--dcs=} {--de=} {--cd=} {--migration} {--cache} {--controller} {--fcp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Easy builder power by Abnermouke
                              {name: 表名}
                              {--desc: 表注释并以"表"字结尾，例如：用户信息表}
                              {--dictionary: 目录名称，多层级请使用 \ 分割，例如：www (www\home，多目录使用分号包裹)}
                              {--dp: 表前缀，例如：alb_}
                              {--dc: 表链接信息，database.php中定义的表链接类，默认：mysql}
                              {--dcs: 表字符集，默认：utf8mb4}
                              {--de: 表引擎，默认：innodb}
                              {--cd: 驱动引擎，默认：file}
                              {--migration: 是否生成migration}
                              {--cache: 是否生成cache缓存管理文件}
                              {--controller: 是否生成controller控制器}
                              {--fcp: 是否强制生成service、repository、model等}
                            ';

    /**
     * 初始化模版参数
     * @var array
     */
    private $tplParams = [
        '__NAME__' => '',
        '__TABLE_NAME__' => '',
        '__CASE_NAME__' => '',
        '__DATA_NAME__' => '',
        '__LOWER_CASE_NAME__' => '',
        '__AUTHOR__' => '',
        '__AUTHOR_CONTACT_EMAIL' => '',
        '__ORIGINATE__' => '',
        '__DATE__' => '',
        '__TIME__' => '',
        '__CHARSET__' => 'utf8mb4',
        '__ENGINE__' => 'innodb',
        '__DB_PREFIX__' => '',
        '__DATA_CACHE_NAME__' => '',
        '__DATA_CACHE_EXPIRE_SECOND__' => 0,
        '__DB_CONNECTION__' => '',
        '__DATA_CACHE_DRIVER__' => '',
        '__DICTIONARY__' => ''
    ];

    /**
     * Easy Builder to package Construct
     * PackageCommands constructor.
     * @param $config
     */
    public function __construct($config)
    {
        //引入父级构造
        parent::__construct();
        //初始化基本配置
        $default_params = [
            '__DATE__' => date('Y-m-d'),
            '__TIME__' => date('H:i:s'),
            '__AUTHOR__' => data_get($config, 'author', 'Abnermouke'),
            '__AUTHOR_CONTACT_EMAIL' => data_get($config, 'author_email', 'abnermouke@outlook.com'),
            '__ORIGINATE__' => data_get($config, 'original', 'Yunni Network Technology Co., Ltd. '),
//            '__DB_PREFIX__' => data_get($config, 'database_prefix', ''),
            '__DB_CONNECTION__' => data_get($config, 'database_connection', 'mysql'),
            '__DATA_CACHE_DRIVER__' => data_get($config, 'cache_driver', 'file'),
        ];
        //初始化配置
        $this->tplParams = array_merge($this->tplParams, $default_params);
    }

    /**
     * Easy Builder to package
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-07-16 11:34:24
     * @throws \Exception
     */
    public function handle()
    {
        //获取生成文件包统一名称
        $this->tplParams['__NAME__'] = $this->tplParams['__MIGRATION_NAME__'] = $name = $this->argument('name');
        //提示输入表名
        $this->tplParams['__TABLE_NAME__'] = $tableName = $this->option('desc') ?? $this->output->ask('请输入当前表注释名称并以"表"字结尾，例如：用户基本信息表');
        //提示获取目录结构
        $this->tplParams['__DICTIONARY__'] = $dictionary =  $this->option('dictionary') ?? (string)$this->output->ask('多项目部署时如需对各服务文件进行分开部署请输入目录名称，多层级请使用 \ 分割，例如：www (www\home)');
        //检测是否存在数据库表前缀
        if (empty($this->tplParams['__DB_PREFIX__'])) {
            //提示设置数据库前缀
            $this->tplParams['__DB_PREFIX__'] = $this->option('dp') ?? $dbPrefix = (string)$this->output->ask('请设置数据库表前缀，如数据库表存在统一前缀，请输入前缀，例如：'.config('builder.database_prefix', 'system_'), config('builder.database_prefix', ''));
            //初始化数据库前缀信息
            !empty($dbPrefix) && $this->tplParams['__DB_PREFIX__'] = Str::finish($dbPrefix, '_');
        }
        //整理驼峰名称
        $this->tplParams['__CASE_NAME__'] = $this->tplParams['__MIGRATION_CASE_NAME_'] = $caseName = Str::studly($name);
        //整理驼峰名称（小写）
        $this->tplParams['__LOWER_CASE_NAME__'] = Str::singular($caseName);
        //整理驼峰目录名称
        $this->tplParams['__DICTIONARY__'] = data_get($this->tplParams, '__DICTIONARY__', false) ? Str::start($this->caseDictionary($this->tplParams['__DICTIONARY__']), '\\') : '';
        //匹配数据名
        $ret = preg_match('~(.*)表~Uuis', $tableName, $matched);
        //初始化数据名
        $this->tplParams['__DATA_NAME__'] = $dataName = intval($ret) >= 1 ? $matched[1] : $tableName;
        //询问获取数据库链接信息
        $this->tplParams['__DB_CONNECTION__'] = $this->option('dc') ?? $this->choice('请选择当前数据查询时使用的表链接信息！', array_keys(config('database.connections')), config('database.default'));
        //生成model
        $this->makeModel();
        //生成数据仓库
        $this->makeRepository();
        //询问是否生成迁移文件Migration
        if ($this->option('migration') || $this->confirm('是否生成数据迁移（Migration）文件？', config('builder.default_builder.migration', true))) {
            //生成migration
            $this->makeMigration();
        }
        //生成服务容器
        $this->makeService();
        //询问是否生成数据缓存
        if ($this->option('cache') || $this->confirm('是否生成数据缓存文件？', config('builder.default_builder.data_cache', true))) {
            //设置基础缓存名
            $this->tplParams['__DATA_CACHE_NAME__'] = $this->option('cache') ? (($dictionary ? strtolower(str_replace('\\', ':', $dictionary)).':' : '').$name.'_data_cache') : $this->ask('您可以自定义当前数据缓存名，默认为：[ '. (($dictionary ? strtolower(str_replace('\\', ':', $dictionary)).':' : '').$name.'_data_cache').' ]，如需更改，请输入您要使用的缓存名！', (($dictionary && !empty($dictionary) ? strtolower(str_replace('\\', ':', $dictionary)).':' : '').$name.'_data_cache'));
            //设置缓存过期时间，随机1小时-一天
            $this->tplParams['__DATA_CACHE_EXPIRE_SECOND__'] = $this->option('cache') ? 0 : $this->ask('您可以自定义数据缓存过期时间（单位：s）,系统将默认设定为 1 小时至一天的随机时间过期，您也可以自定义，0 为永远不过期，请输入当前数据缓存的过期时间！', rand(3600, 86400));
            //生成数据缓存文件
            $this->makeDataCache();
        }
        //询问是否生成控制器
        if ($this->option('controller') || $this->confirm('是否生成控制器文件？', config('builder.default_builder.controller', true))) {
            //生成控制器文件
            $this->makeController();
        }
        //输出信息
        $this->output->write('Easy Builder Packages Create Success, Make it awesome!', true);

    }

    /**
     * 检查维护迁移文件是否存在
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-10-09 23:52:26
     * @return bool
     */
    private function checkFillingMigration()
    {
        //初始化目录
        $dir_path = database_path('migrations/fillings');
        //判断是否存在目录
        if (!File::exists($dir_path)) {
            try {
                //执行命令
                Artisan::call('make:migration', ['name' => 'create_fillings_table']);
            } catch (\Exception $exception) {
                //输出错误
                $this->output->warning($exception->getMessage());
            }
            //获取执行结果
            $output = Artisan::output();
            //匹配文件名
            $ret = preg_match('~Created Migration: (.*)\n~Uuis', $output, $matched);
            //判断是否匹配成功
            if ($ret <= 0) {
                //输出错误
                $this->output->warning('Migration出现未知错误');
                return true;
            }
            //初始化文件名
            $migrationName = $matched[1];
            //判断是否不存在目录
            !File::isDirectory($dir_path) && File::makeDirectory($dir_path, 0777, true);
            //填充迁移文件
            $this->putContent(database_path('migrations/fillings/'.$migrationName.'.php'), $this->getTplContent('migration_fillings'));
            //删除原创建迁移文件
            File::delete(database_path('migrations/'.$migrationName.'.php'));
        }
        //返回成功
        return true;
    }

    /**
     * 创建迁移文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:05:36
     * @return bool
     */
    private function makeMigration()
    {
        //检测迁移文件
        $this->checkFillingMigration();
        //判断是否存在目录信息
        if ($this->tplParams['__DICTIONARY__'] && !empty($this->tplParams['__DICTIONARY__'])) {
            //整理迁移名称
            $this->tplParams['__MIGRATION_NAME__'] = str_replace('\\', '___', strtolower(Str::after($this->tplParams['__DICTIONARY__'], '\\'))).'___'.$this->tplParams['__NAME__'];
            //判断信息
            $this->tplParams['__MIGRATION_NAME__'] = strstr($this->tplParams['__MIGRATION_NAME__'], '\\') ? Str::snake(str_replace('\\', '_', $this->tplParams['__MIGRATION_NAME__'])) : $this->tplParams['__MIGRATION_NAME__'];
            //设置迁移显示类名称
            $this->tplParams['__MIGRATION_CASE_NAME_'] = Str::studly($this->tplParams['__MIGRATION_NAME__']);
        }
        try {
            //执行命令
            Artisan::call('make:migration', ['name' => 'create_'.$this->tplParams['__MIGRATION_NAME__'].'_table']);
        } catch (\Exception $exception) {
            //输出错误
            $this->output->warning($exception->getMessage());
        }
        //获取执行结果
        $output = Artisan::output();
        //匹配文件名
        $ret = preg_match('~Created Migration: (.*)\n~Uuis', $output, $matched);
        //判断是否匹配成功
        if ($ret <= 0) {
            //输出错误
            $this->output->warning('Migration出现未知错误');
            return true;
        }
        //初始化文件名
        $migrationName = trim($matched[1]);
        //询问获取数据库配置信息
        $this->tplParams['__CHARSET__'] = $this->option('dcs') ?? $this->choice('请选择当前数据库表使用的字符集！', ['utf8', 'utf8mb4'], $this->tplParams['__CHARSET__']);
        $this->tplParams['__ENGINE__'] = $this->option('de') ?? $this->choice('请输入当前数据库表使用的储存引擎！', ['myisam', 'innodb'], $this->tplParams['__ENGINE__']);
        //获取模版内容
        $content = $this->getTplContent('migration');
        //内容存在
        if ($content && !empty($content)) {
            //整理路径
            $migrationPath = $migrationDictionaryPath = database_path('migrations/'.$migrationName.'.php');
            //判断是否存在目录信息
            if ($this->tplParams['__DICTIONARY__'] && !empty($this->tplParams['__DICTIONARY__'])) {
                //获取子目录名称
                $dictionary_name = Arr::first(explode('_', $this->tplParams['__MIGRATION_NAME__']));
                //整理目录
                $migrationDictionary = database_path('migrations/'.strtolower($dictionary_name));
                //判断目录是否存在
                if (!File::isDirectory($migrationDictionary)) {
                    //创建目录
                    File::makeDirectory($migrationDictionary, 0777, true, true);
                }
                //整理目录地址
                $migrationDictionaryPath = database_path('migrations/'.strtolower($dictionary_name).'/'.$migrationName.'.php');
            }
            //设置内容
            $this->putContent($migrationDictionaryPath, $content);
            //判断信息
            if ($migrationDictionaryPath !== $migrationPath) {
                //删除源文件
                File::delete($migrationPath);
            }
        }
        return true;
    }

    /**
     * 创建模型文件
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:06:56
     * @return bool
     */
    private function makeModel()
    {
        //整理目录
        $modelDirectory = app_path('Model'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']));
        //判断目录是否存在
        if (!File::isDirectory($modelDirectory)) {
            //创建目录
            File::makeDirectory($modelDirectory, 0777, true, true);
        }
        //整理路径
        $modelPath = app_path('Model'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']).'/'.$this->tplParams['__CASE_NAME__'].'.php');
        //判断文件地址
        if (file_exists($modelPath) && !$this->option('fcp') && !$this->confirm('数据模型 ['.$modelPath.'] 已存在，是否覆盖写入？')) {
            //直接返回
            return true;
        }
        //获取模版内容
        $content = $this->getTplContent('model');
        //内容存在
        if ($content && !empty($content)) {
            //设置内容
            $this->putContent($modelPath, $content);
        }
        return true;
    }

    /**
     * 创建服务容器
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:12:43
     * @return bool
     */
    private function makeService()
    {
        //整理目录
        $serviceDirectory = app_path('Services'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']));
        //判断目录是否存在
        if (!File::isDirectory($serviceDirectory)) {
            //创建目录
            File::makeDirectory($serviceDirectory, 0777, true, true);
        }
        //整理路径
        $servicePath = app_path('Services'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']).'/'.$this->tplParams['__LOWER_CASE_NAME__'].'Service.php');
        //判断文件地址
        if (file_exists($servicePath) && !$this->option('fcp') && !$this->confirm('服务容器 ['.$servicePath.'] 已存在，是否覆盖写入？')) {
            //直接返回
            return true;
        }
        //获取模版内容
        $content = $this->getTplContent('service');
        //内容存在
        if ($content && !empty($content)) {
            //设置内容
            $this->putContent($servicePath, $content);
        }
        return true;
    }

    /**
     * 创建控制器
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:12:43
     * @return bool
     */
    private function makeController()
    {
        //整理目录
        $controllerDirectory = app_path('Http/Controllers'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']));
        //判断目录是否存在
        if (!File::isDirectory($controllerDirectory)) {
            //创建目录
            File::makeDirectory($controllerDirectory, 0777, true, true);
        }
        //整理路径
        $controllerPath = app_path('Http/Controllers'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']).'/'.$this->tplParams['__LOWER_CASE_NAME__'].'Controller.php');
        //判断文件地址
        if (file_exists($controllerPath) && !$this->option('fcp') && !$this->confirm('控制器 ['.$controllerPath.'] 已存在，是否覆盖写入？')) {
            //直接返回
            return true;
        }
        //获取模版内容
        $content = $this->getTplContent('controller');
        //内容存在
        if ($content && !empty($content)) {
            //设置内容
            $this->putContent($controllerPath, $content);
        }
        return true;
    }

    /**
     * 创建数据缓存
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:12:43
     * @return bool
     */
    private function makeDataCache()
    {
        //询问获取缓存链接信息
        $this->tplParams['__DATA_CACHE_DRIVER__'] = $this->option('cd') ?? $this->choice('请选择当前数据缓存储存驱动！', array_keys(config('cache.stores')), config('cache.default'));
        //整理目录
        $dataCacheDirectory = app_path('Handler/Cache/Data'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']));
        //判断目录是否存在
        if (!File::isDirectory($dataCacheDirectory)) {
            //创建目录
            File::makeDirectory($dataCacheDirectory, 0777, true, true);
        }
        //整理路径
        $dataCachePath = app_path('Handler/Cache/Data'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']).'/'.$this->tplParams['__LOWER_CASE_NAME__'].'CacheHandler.php');
        //判断文件地址
        if (file_exists($dataCachePath) && !$this->option('fcp') && !$this->confirm('数据缓存 ['.$dataCachePath.'] 已存在，是否覆盖写入？')) {
            //直接返回
            return true;
        }
        //获取模版内容
        $content = $this->getTplContent('data_cache');
        //内容存在
        if ($content && !empty($content)) {
            //设置内容
            $this->putContent($dataCachePath, $content);
        }
        return true;
    }

    /**
     * 创建数据仓库
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:12:52
     * @return bool
     */
    private function makeRepository()
    {
        //整理目录
        $repositoryDirectory = app_path('Repository'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']));
        //判断目录是否存在
        if (!File::isDirectory($repositoryDirectory)) {
            //创建目录
            File::makeDirectory($repositoryDirectory, 0777, true, true);
        }
        //整理路径
        $repositoryPath = app_path('Repository'.str_replace('\\', '/', $this->tplParams['__DICTIONARY__']).'/'.$this->tplParams['__LOWER_CASE_NAME__'].'Repository.php');
        //判断文件地址
        if (file_exists($repositoryPath) && !$this->option('fcp') && !$this->confirm('数据仓库 ['.$repositoryPath.'] 已存在，是否覆盖写入？')) {
            //直接返回
            return true;
        }
        //获取模版内容
        $content = $this->getTplContent('repository');
        //内容存在
        if ($content && !empty($content)) {
            //设置内容
            $this->putContent($repositoryPath, $content);
        }
        return true;
    }

    /**
     * 设置文件内容
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:03:54
     * @param $path string 文件绝对路径
     * @param $content string 文件内容
     * @return bool
     */
    private function putContent($path, $content)
    {
        //打开文件
        $fp = fopen($path, 'w+');
        //写入文件
        fwrite($fp, $content);
        //关闭写入事件
        fclose($fp);
        //判断文件内容
        if (!file_exists($path)) {
            //抛出错误
            $this->output->warning('文件写入失败：'.$path);
        }
        //返回成功
        return true;
    }

    /**
     * 根据内容渲染模版获取详细模版内容
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Company Yunnitec.
     * @Time 2020-04-27 20:01:14
     * @param string $mode 模版类型
     * @return bool|false|mixed|string
     */
    private function getTplContent($mode = 'migration')
    {
        //获取TPL文件信息
        $tplPath = __DIR__.'/../../tpl/'.strtolower($mode).'.tpl';
        //判断模版信息是否存在
        if (!file_exists($tplPath)) {
            //抛出错误
            $this->output->warning('缺少TPL：'.$tplPath);
            //直接返回
            return false;
        }
        //获取内容
        $content = file_get_contents($tplPath);
        //循环参数
        foreach ($this->tplParams as $item => $value) {
            //替换参数
            $content = str_replace('{'.$item.'}', $value, $content);
        }
        //返回模版内容
        return $content;
    }

    /**
     * 设置多层级目录结构
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2021-08-04 11:15:59
     * @param $dictionary
     * @return string
     */
    private function caseDictionary($dictionary)
    {
        //拆分目录
        $dictionaries = explode('\\', $dictionary);
        //循环目录信息
        foreach ($dictionaries as $k => $dict) {
            //设置信息
            $dictionaries[$k] = Str::studly($dict);
        }
        //返回信息
        return implode('\\', $dictionaries);
    }

}
