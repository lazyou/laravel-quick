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


### 路由说明
* 视图路由必须设置 name, 用作视图渲染和权限管理
