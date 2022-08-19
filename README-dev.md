### 用户认证 config/auth.php
* `'model' => App\Models\User::class,`  改成 `'model' => \Lazyou\Quick\Models\QuickUser::class,`


### 路由说明
* 视图路由必须设置 name, 用作视图渲染和权限管理


### 发布资源
* `php artisan vendor:publish --tag=quick`
    * 开发过程中可用 `--force` 选项覆盖

* `php artisan vendor:publish --provider="Lazyou\Quick\Providers\QuickServiceProvider"`
    * 本质上 执行 `QuickServiceProvider` 内的各种 `$this->publishes` 方法？


### 数据库迁移
* `php artisan queue:table` 操作日志记录有用到

* `php artisan quick:install` 初始化数据
    * 包含 migrate 和 seeder， 但数据库中已存在记录不覆盖
    
* `php artisan migrate --step`


### 【常用】 config, env 配置操作
* `php artisan key:generate`

* `config/app.php` => `'locale' => 'zh'`

* `CACHE_DRIVER=redis` -- 缓存放 redis，查看也方便

* `LOG_CHANNEL=daily` -- 错误日志按天记录到 log 文件

* `APP_TIMEZONE=Asia/Shanghai`


### 开发扩展过程中使用到了第三方包怎么办？
1. 在 laravel 项目的 composer.json 添加对应的包 require
    ```json
    "mews/captcha": "^3.2",
    "qiniu/php-sdk": "^7.4"
    ```

2. 在 `packages/lazyou/quick/composer.json` 也补上同样的配置

3. 执行 `composer install` （先删除 `composer.lock`）

4. 原理： 
    第一步 为了保证开发扩展包时有所需的第三方依赖可用； 
    第二步是补充依赖说明，别人引用我们包执行 `composer install` 时才会安装对应依赖。


### 【废弃】app/Http/Kernel.php 中间件
* `$middlewareGroups` 新增 `'auth.quick' => \Lazyou\Quick\Http\Middleware\QuickAuthenticate::class,`
