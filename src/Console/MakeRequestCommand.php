<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:make-request {model}
        {--request : 类名}
        {--subDir=Admin : 子目录，默认Admin}
        {--force=0 : 强制覆盖文件(建议只在开发Command中使用)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建--表单验证';

    // 模型
    protected $model = '';

    // 名字
    protected $request = '';

    // 验证器在 app/Http/Requests 下的目录
    protected $subDir = '';

    // 模板内容
    protected $content = '';

    // 强制覆盖文件
    protected $force = false;

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
        $this->model = $this->argument('model');

        $this->subDir = $this->option('subDir');
        $this->force = (bool) $this->option('force');

        $this->request = $this->option('request');
        if (empty($this->request)) {
            $this->request = $this->model;
        }

        if ($this->checkExist()) {
            if (! $this->force) {
                echo("{$this->filePath} 文件已存在 \n");
                return false;
            }
        }

        $this->makeContent();

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
        $path = 'Http/Requests';
        if ($this->subDir) {
            $path = "$path/$this->subDir";
        }

        $dir = app_path($path);
        $this->filePath = $dir . DIRECTORY_SEPARATOR . "{$this->request}Request.php";
        if (! file_exists($dir)) {
            mkdir($dir);
        }

        return file_exists($this->filePath);
    }

    protected function loadStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/request.stub');
    }

    protected function makeContent()
    {
        $this->content = $this->loadStub();

        $replaces = [
            '{$model}' => $this->model,
            '{$request}' => $this->request,
            '{$subDir}' => $this->subDir,
        ];

        $this->content = str_replace(array_keys($replaces), array_values($replaces), $this->content);
    }
}
