### 安装使用
```shell
composer require lazyou/laravel-quick

php artisan vendor:publish --tag=quick [--force]
php artisan queue:table
php artisan quick:install
```


### 用户认证 config/auth.php
* `'model' => App\Models\User::class,`  改成 `'model' => \Lazyou\Quick\Models\QuickUser::class,`


### 【常用】 config, env 配置操作
* `php artisan key:generate`

* `config/app.php` => `'locale' => 'zh'`

* `CACHE_DRIVER=redis` -- 缓存放 redis，查看也方便

* `LOG_CHANNEL=daily` -- 错误日志按天记录到 log 文件

* `APP_TIMEZONE=Asia/Shanghai`


### 后台开发
* `app/Providers/RouteServiceProvider.php` 的 `boot` 方法添加如下
    ```php
    $adminPath = config('quick.admin_path', 'admin');
    Route::middleware(['web', 'quick.auth', 'quick.log'])
        ->prefix("/$adminPath")
        ->group(base_path('routes/quick_admin.php'));
    ```

* `touch routes/quick_admin.php`
    ```php
    Route::get('/example', [ExampleController::class, 'index'])->name('admin.example.index');
    ```


* 视图路由必须设置 name, 用作视图渲染和权限管理
