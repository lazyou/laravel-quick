<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * php artisan quick:make-curd quick_user [--force=1]
 *
 * Class MakeViewCommand
 * @package Lazyou\Quick\Console
 */
class MakeCurdCommand extends Command
{
    protected $signature = 'quick:make-curd {table}
        {--model= : 模型名}
        {--subDir=admin : 子目录，默认admin}
        {--subDirController=Admin : 子目录，默认Admin}
        {--force=0 : 强制覆盖文件(建议只在开发Command中使用)}
    ';

    protected $table = '';

    protected $model = '';

    protected $force = 0;

    public function handle()
    {
        $this->table = $this->argument('table');
        $this->model = Str::ucfirst(Str::camel($this->table));
        $this->force = (int) $this->option('force');

        $this->info("");
        Artisan::call('quick:make-route', [
            'table' => $this->table,
        ]);

        $this->info("");
        Artisan::call('quick:make-model', [
            'table' => $this->table,
            '--force' => $this->force,
        ]);

        $this->info("");
        Artisan::call('quick:make-controller', [
            'model' => $this->model,
            '--force' => $this->force,
        ]);

        $this->info("");
        Artisan::call('quick:make-request', [
            'model' => $this->model,
            '--request' => "{$this->model}Edit",
            '--force' => $this->force,
        ]);

        Artisan::call('quick:make-request', [
            'model' => $this->model,
            '--request' => "{$this->model}Delete",
            '--force' => $this->force,
        ]);

        $this->info("");
        Artisan::call('quick:make-view', [
            'table' => $this->table,
            '--force' => $this->force,
        ]);
    }
}
