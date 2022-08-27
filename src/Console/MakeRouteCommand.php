<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRouteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:make-route {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建--路由（复制用）';

    // 表名
    protected $table = '';

    // 控制器名
    protected $controller = '';

    // 模板内容
    protected $content = '';

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
        $this->table = $this->argument('table');
        $this->controller = Str::ucfirst(Str::camel($this->table));

        $this->makeContent();
        echo("{$this->content}");

        return true;
    }

    /**
     * 生成模板
     */
    protected function makeContent()
    {
        $replaces = [
            '{$table}' => $this->table,
            '{$controller}' => $this->controller,
        ];

        $this->content = $this->loadStub();

        $this->content = str_replace(array_keys($replaces), array_values($replaces), $this->content);
    }

    protected function loadStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/route.stub');
    }
}
