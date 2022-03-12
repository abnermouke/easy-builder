<?php

namespace Abnermouke\EasyBuilder;

use Abnermouke\EasyBuilder\Commands\PackageCommands;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class EasyBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //引入配置
        $this->app->singleton('command.builder.package', function ($app) {
            //返回实例
            return new PackageCommands($app['config']['builder']);
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../config/builder.php' => config_path('builder.php'),
            __DIR__.'/../config/project.php' => config_path('project.php'),
            __DIR__.'/../helpers/auth.php' => app_path('Helpers/auth.php'),
            __DIR__.'/../helpers/functions.php' => app_path('Helpers/functions.php'),
            __DIR__.'/../helpers/helpers.php' => app_path('Helpers/helpers.php'),
            __DIR__.'/../helpers/response.php' => app_path('Helpers/response.php'),
            __DIR__ . '/../src/Middlewares/EasyBuilderBaseMiddleware.php' => app_path('Http/Middleware/LaravelBuilder/LaravelBuilderBaseMiddleware.php'),
            __DIR__.'/../views/vendor/errors.blade.php' => resource_path('views/vendor/errors.blade.php'),
        ]);
        // 注册配置
        $this->commands('command.builder.package');
        //替换文件关键词（configs/project.php）
        $project_php_tpl = str_replace(['__APP_KEY__', '__APP_SECRET__', '__AES_IV__', '__AES_ENCRYPT_KEY__'], ['ak'.date('Ymd').strtolower(Str::random(10)), strtoupper(md5(Uuid::uuid4()->toString().Str::random())), strtoupper(Str::random()), strtoupper(Str::random(8))], file_get_contents(config_path('project.php')));
        //替换内容
        file_put_contents(config_path('project.php'), $project_php_tpl);
        //替换文件关键词（configs/builder.php）
        $builder_php_tpl = str_replace('__APP_VERSION__', rand(10000, 99999), file_get_contents(config_path('builder.php')));
        //替换内容
        file_put_contents(config_path('builder.php'), $builder_php_tpl);
    }
}
