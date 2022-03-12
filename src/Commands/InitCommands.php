<?php

namespace Abnermouke\EasyBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Easy Builder to init
 * Class PackageCommands
 * @package Abnermouke\EasyBuilder\Commands
 */
class InitCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'builder:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Easy builder power by Abnermouke';


    /**
     * Easy Builder to init
     * @Author Abnermouke <abnermouke@outlook.com>
     * @Originate in Abnermouke's MBP
     * @Time 2022-03-12 17:03:56
     * @return bool
     */
    public function handle()
    {
        //替换文件关键词（configs/project.php）
        $project_php_tpl = str_replace(['__APP_KEY__', '__APP_SECRET__', '__AES_IV__', '__AES_ENCRYPT_KEY__'], ['ak'.date('Ymd').strtolower(Str::random(10)), strtoupper(md5(Uuid::uuid4()->toString().Str::random())), strtoupper(Str::random()), strtoupper(Str::random(8))], file_get_contents(config_path('project.php')));
        //替换内容
        file_put_contents(config_path('project.php'), $project_php_tpl);
        //替换文件关键词（configs/builder.php）
        $builder_php_tpl = str_replace('__APP_VERSION__', rand(10000, 99999), file_get_contents(config_path('builder.php')));
        //替换内容
        file_put_contents(config_path('builder.php'), $builder_php_tpl);
        //打印信息
        $this->output->success('构建器初始化完成！');
        //返回成功
        return true;
    }

}
