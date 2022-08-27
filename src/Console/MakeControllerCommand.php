<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * 控制器快速生成 php artisan quick:make-controller User [--deleteRequest=1] [--showOnly=1] [--force=1]
 * Class MakeControllerCommand
 * @package Lazyou\Quick\Console
 */
class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:make-controller {model}
        {--subDir=Admin : 子目录，默认Admin}
        {--deleteRequest=0 : 移除请求验证}
        {--showOnly=0 : 展示代码，方便复制}
        {--force=0 : 强制覆盖文件(建议只在开发Command中使用)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建--控制器';

    // 模型名
    protected $model = '';

    // 模型名 转小驼峰
    protected $modelCamel = '';

    // 控制器名
    protected $controller = '';

    // 控制器在 app/Http/Controllers 下的目录
    protected $subDir = '';

    // 模板内容
    protected $content = '';

    // 是否移除请求验证
    protected $deleteRequest = false;

    // 强制覆盖文件
    protected $force = false;

    // 输出内容（不生成文件）
    protected $showOnly = false;

    // 生成路径
    protected $filePath;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->subDir = $this->option('subDir');

        $this->model = $this->argument('model');
        $this->modelCamel = Str::camel($this->model);

        $this->controller = $this->model;

        $this->deleteRequest = (bool) $this->option('deleteRequest');
        $this->force = (bool) $this->option('force');
        $this->showOnly = (bool) $this->option('showOnly');

        if ($this->checkExist() && ! $this->showOnly) {
            if (! $this->force) {
                echo("{$this->filePath} 文件已存在 \n");
                return false;
            }
        }

        $this->makeContent();

        if ($this->showOnly) {
            $this->info($this->content);
            return true;
        }

        if (file_put_contents($this->filePath, $this->content)) {
            echo("{$this->filePath} 写入成功 \n");
            return true;
        } else {
            echo("{$this->filePath} 写入失败 \n");
            return false;
        }
    }

    protected function checkExist(): bool|int
    {
        $path = 'Http/Controllers';
        if ($this->subDir) {
            $path = "$path/$this->subDir";
        }

        $this->filePath = app_path($path) . DIRECTORY_SEPARATOR . "{$this->model}Controller.php";

        return file_exists($this->filePath);
    }

    protected function loadStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/controller.stub');
    }

    protected function makeContent()
    {
        $this->content = $this->loadStub();

        if ($this->deleteRequest) {
            $replaces = [
                'use App\Http\Requests\{$subDir}\{$model}EditRequest;' . "\n" => '',
                'use App\Http\Requests\{$subDir}\{$model}DeleteRequest;' . "\n" => '',
                '{$model}EditRequest' => 'Request',
                '{$model}DeleteRequest' => 'Request',
            ];

            $this->content = str_replace(array_keys($replaces), array_values($replaces), $this->content);
        }

        $replaces = [
            '{$controller}' => $this->controller,
            '{$model}' => $this->model,
            '{$modelCamel}' => $this->modelCamel,
            '{$subDir}' => $this->subDir,
        ];

        $this->content = str_replace(array_keys($replaces), array_values($replaces), $this->content);
    }
}
