<?php
/**
 * Power by abnermouke/laravel-builder.
 * User: Abnermouke <abnermouke>
 * Originate in YunniTec.
 */

return [

    /*
   |--------------------------------------------------------------------------
   | Database default setting and Basic setting
   |--------------------------------------------------------------------------
   |
   | The default database settings
   |
   */

    'database_prefix' => env('DB_PREFIX', ''),          // 默认数据库表前缀 (为方便辨识，请加上分隔符'_')
    'database_charset' => 'utf8mb4',                                // Default database charset (proposal：utf8mb4)
    'database_engine' => 'innodb',                                  // Default database engine (proposal：innodb)
    'database_connection' => 'mysql',                               // 默认数据库链接 (默认：mysql < 如需指定链接，请前往 config/database.php 添加 >)

    // 默认缓存驱动 (默认：file  < 可选：file， redis， mongodb等，redis、mongodb需单独安装/配置>)
    'cache_driver' => 'file',

    //默认应用（项目）版本号
    'app_version' => env('APP_VERSION', '__APP_VERSION__'),

    // Default builder packages
    'default_builder' => [
        'migration' => true,           //default build migration
        'data_cache' => true,           //default build data cache handler
        'controller' => true,          //default build controller
    ],


    /*
   |--------------------------------------------------------------------------
   | Author config
   |--------------------------------------------------------------------------
   |
   | The base config for author
   |
   */

    'author' => 'Abnermouke',
    'author_email' => 'abnermouke@outlook.com',
    'original' => 'Yunni Technology Co Ltd.',


];
