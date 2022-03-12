<?php

namespace Abnermouke\EasyBuilder;

use Abnermouke\EasyBuilder\Commands\InitCommands;
use Abnermouke\EasyBuilder\Commands\PackageCommands;
use Illuminate\Support\ServiceProvider;

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
        //引入配置
        $this->app->singleton('command.builder.init', function ($app) {
            //返回实例
            return new InitCommands();
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
        $this->commands('command.builder.init');
    }
}
