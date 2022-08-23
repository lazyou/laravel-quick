<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * 模型快速生成 php artisan quick:make-model user [--showOnly=1] [ --noType=1] [--force=1]
 * Class MakeModelCommand
 * @package Lazyou\Quick\Console
 */
class MakeModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:make-model {table}
        {--noType=0 : 字段无类型}
        {--showOnly=0 : 展示代码，方便复制}
        {--force=0 : 强制覆盖文件}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建--模型';

    // 表名
    protected $table = '';

    // 模型名
    protected $model = '';

    // 模型属性内容
    protected $fieldsContent = '';

    // 模板内容
    protected $content = '';

    // 字段无类型
    protected $noType = false;

    // 强制覆盖文件
    protected $force = false;

    // 输出内容（不生成文件）
    protected $showOnly = false;

    // 生成路径
    protected $filePath;

    protected $ignoreColumns = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
        if (! Schema::hasTable($this->table)) {
            $this->error("{$this->table} 表不存在");
            return false;
        }

        $this->model = Str::ucfirst(Str::camel($this->table)); // 下划线转大驼峰;
        $this->force = (bool) $this->option('force');
        $this->noType = (bool) $this->option('noType');
        $this->showOnly = (bool) $this->option('showOnly');

        $this->makeFieldsContent();

        if ($this->checkExist() && ! $this->showOnly) {
            if (! $this->force) {
                $this->error("{$this->filePath} 文件已存在");
                return false;
            }
        }

        $this->makeContent();

        if ($this->showOnly) {
            $this->info($this->content);
            return true;
        }

        if (file_put_contents($this->filePath, $this->content)) {
            $this->info("{$this->filePath} 写入成功");
            return true;
        } else {
            $this->error("{$this->filePath} 写入失败");
            return false;
        }
    }

    protected function loadStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/model.stub');
    }

    /**
     * 生成模板
     */
    protected function makeContent()
    {
        $this->content = $this->loadStub();

        $replaces = [
            '{$model}' => $this->model,
            '{$table}' => $this->table,
            '{$fieldsContent}' => $this->fieldsContent,
        ];

        $this->content = str_replace(array_keys($replaces), array_values($replaces), $this->content);
    }

    protected function checkExist(): bool
    {
        $this->filePath = app_path('Models') . DIRECTORY_SEPARATOR . $this->model . '.php';

        return file_exists($this->filePath);
    }


    /**
     * @param $table
     * @return array
     */
    protected function fields($table): array
    {
        $fields = [];
        $columns = Schema::getColumnListing($table);

        foreach ($columns as $column) {
            if (in_array($column, $this->ignoreColumns)) {
                continue;
            }

            $fields[] = $column;
        }

        return $fields;
    }

    /**
     * 根据表结构字段和类型生成 property 注释.
     */
    protected function makeFieldsContent()
    {
        $eof = PHP_EOL;
        $fields = DB::select($this->getSql($this->table));
        $count = count($fields);
        $lastIndex = $count - 1;

        foreach ($fields as $index => $field) {
            $phpType = $this->mysqlTypeToPhpType($field->data_type);
            $phpType = $this->noType ? '' : "{$phpType} "; // 有类型注意右边留一个空格

            $this->fieldsContent = "{$this->fieldsContent}{$eof} * @property {$phpType}\${$field->column_name}";
            if (! empty($field->column_comment)) {
                $this->fieldsContent = "{$this->fieldsContent} {$field->column_comment}";
            }

            if ($index === $lastIndex) {
                $this->fieldsContent = "{$this->fieldsContent}{$eof}";
            }
        }

        dd($this->fieldsContent);
    }

    // mysql 字段类型对应 php 的类型
    protected function mysqlTypeToPhpType(string $type): ?string
    {
        switch ($type) {
            case 'varchar':
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'string';
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return 'integer';
            case 'decimal':
            case 'float':
            case 'double':
            case 'real':
                return 'float';
//                return 'decimal:2'; // 设置为 decimal，并设置对应精度
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'timestamp':
                return '\Carbon\Carbon';
            default:
                return null;
        }
    }

    // sql 语句参考子: php bin/hyperf.php gen:model --with-comments table_name
    protected function getSql($table)
    {
        $db = config('database.connections.mysql.database');

        return "select
               `column_key` as `column_key`,
               `column_name` as `column_name`,
               `data_type` as `data_type`,
               `column_comment` as `column_comment`,
               `extra` as `extra`,
               `column_type` as `column_type`
            from information_schema.columns
            where `table_schema` = '{$db}'
            and `table_name` = '{$table}'
            order by ORDINAL_POSITION
       ";
    }
}
