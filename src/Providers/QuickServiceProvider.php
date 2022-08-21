<?php

declare(strict_types=1);

namespace Lazyou\Quick\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Lazyou\Quick\Console\InstallCommand;
use Lazyou\Quick\Console\MakeControllerCommand;
use Lazyou\Quick\Console\MakeModelCommand;
use Lazyou\Quick\Http\Middleware\QuickAuthenticate;
use Lazyou\Quick\Http\Middleware\QuickOperationLog;

/**
 * 配置在 config/app.php 的 providers 属性下.
 *
 */
class QuickServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $commands = [
        InstallCommand::class,
        MakeControllerCommand::class,
        MakeModelCommand::class,
    ];

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->addVueExtension();

        $this->commands($this->commands);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadRoutes();
        $this->logMysql();
        $this->loadConfigs();

        // php artisan vendor:publish --tag=quick
        $this->publishes([
            __DIR__ . '/../../resources/lang' => lang_path(''),
            __DIR__ . '/../../resources/vue' => public_path('quick/vue'),
            __DIR__ . '/../../config/quick.php' => config_path('quick.php'),
            __DIR__ . '/../../config/captcha.php' => config_path('captcha.php'),
        ], 'quick');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'quick');

        $this->addMiddlewareAlias('quick.auth', QuickAuthenticate::class);
        $this->addMiddlewareAlias('quick.log', QuickOperationLog::class);
    }

    // 添加路由中间件
    protected function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        // 判断 aliasMiddleware 是否在类中存在
        if (method_exists($router, 'aliasMiddleware')) {
            // aliasMiddleware 顾名思义,就是给中间件设置一个别名
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }

    protected function addVueExtension()
    {
        View::addExtension('js', 'blade');
        View::addExtension('vue', 'blade');
    }

    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../route.php');
    }

    protected function loadConfigs()
    {
        // mergeConfigFrom 仅支持一级 key 的覆盖
//        $this->mergeConfigFrom(__DIR__ . '/../../config/quick_auth.php', 'auth');
//        $this->mergeConfigFrom(__DIR__ . '/../../config/quick_logging.php', 'logging');
    }

    protected function logMysql(): bool
    {
        if (! in_array(config('app.env'), ['dev', 'local', 'develop'])) {
            return false;
        }

        DB::listen(
            function ($sql) {
                foreach ($sql->bindings as $index => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$index] = $binding->format('Y-m-d H:i:s');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$index] = "'{$binding}'";
                        }
                    }
                }

                $query = str_replace(['%', '?'], ['%%', '%s'], $sql->sql);

                $query = vsprintf($query, $sql->bindings);

                Log::info($query);
            }
        );

        return true;
    }
}
