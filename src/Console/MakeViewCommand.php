<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * php artisan quick:make-vue quick_user --force=1 --showOnly=1
 * Class MakeViewCommand
 * @package Lazyou\Quick\Console
 */
class MakeViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:make-view {table}
        {--model= : 模型名}
        {--subDir=admin : 子目录，默认admin}
        {--showOnly=0 : 展示代码，方便复制}
        {--force=0 : 强制覆盖文件(建议只在开发Command中使用)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建--前端 blade 代码';

    // 表名
    protected $table = '';

    // 模型
    protected $model = '';

    // url 接口默认地址
    protected $url = '';
    protected $urlList = '';

    // resources/views 主子目录
    protected $subDir = 'admin';

    // 文件生成的目录
    protected $fileDir = '';

    // 文件
    protected $filePathPhp = '';
    protected $filePathJs = '';

    // 强制覆盖文件
    protected $force = false;

    // 输出内容（不生成文件）
    protected $showOnly = false;

    // 模板内容
    protected $contentPhp = '';
    protected $contentJs = '';

    // 表单内容: html
    protected $formContent = '';

    // 列表内容: html
    protected $tableContent = '';

    // 表单初始化: js
    protected $formInitContent = '';

    // 表单规则: js
    protected $formRulesContent = '';

    // 表单下拉选项, 以及调用方法
    protected $optionContent = '';
    protected $optionMethodContent = '';
    protected $optionMethodCallContent = '';

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
        $this->model = $this->option('model');
        if (empty($this->model)) {
            $this->model = Str::ucfirst(Str::camel($this->table)); // 下划线转大驼峰;
        }

        $this->subDir = $this->option('subDir');
        $this->fileDir = $this->table;

        $this->url = $this->table;
        $this->urlList = "/$this->subDir/$this->table";

        $this->force = (bool) $this->option('force');
        $this->showOnly = (bool) $this->option('showOnly');

        if ($this->checkExist() && ! $this->showOnly) {
            if (! $this->force) {
                echo("{$this->filePathPhp} 文件已存在 \n");
                return false;
            }
        }

        $this->parseModel();

        $this->makeContentPhp();
        $this->makeContentJs();

        if ($this->showOnly) {
            $this->info($this->contentPhp);
            return true;
        }

        if (file_put_contents($this->filePathPhp, $this->contentPhp)) {
            echo("{$this->filePathPhp} 写入成功 \n");
        } else {
            echo("{$this->filePathPhp} 写入失败 \n");
        }

        if (file_put_contents($this->filePathJs, $this->contentJs)) {
            echo("{$this->filePathJs} 写入成功 \n");
        } else {
            echo("{$this->filePathJs} 写入失败 \n");
        }

        return true;
    }

    protected function parseModel()
    {
        $classFull = "App\\Models\\{$this->model}";
        if (! class_exists($classFull)) {
            throw new \Exception("{$classFull} 模型不存在");
        }

        $fields = DB::select($this->getSql($this->table));
        $attributes = $classFull::ATTRIBUTES;

        foreach ($fields as $field) {
            $fieldName = $field->column_name;
            $fieldType = $field->data_type;
            $label = $attributes[$fieldName] ?? $fieldName;

            // 不做任何处理的字段
            if ($fieldName === 'deleted_at') {
                continue;
            }

            // 生成 列表字段
            $this->tableContent .= $this->tableColumn($label, $fieldName);

            // 生成 表单字段, 表单初始化, 表单验证
            switch($fieldType) {
                case 'varchar':
                    $this->formContent .= $this->formInput($fieldName, $label);
                    $this->formInitContent .= $this->formInitTemplate($fieldName);
                    $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    break;
                case 'tinytext':
                case 'text':
                case 'mediumtext':
                case 'longtext':
                    $this->formContent .= $this->formInput($fieldName, $label, 'textarea');
                    $this->formInitContent .= $this->formInitTemplate($fieldName);
                    $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    break;
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                    // 特殊字段不生成表单
                    if (! in_array($fieldName, ['id'])) {
                        // 整型字段 -- 特殊处理表单： 单选框，下拉框，否则 输入框
                        if (in_array($fieldName, ['status', 'type']) || Str::endsWith($fieldName, ['_status', '_type'])) {
                            // 单选框
                            $this->formContent .= $this->formRadio($fieldName, $label);
                        } elseif (Str::endsWith($fieldName, ['_id'])) {
                            // 下拉框
                            $this->formContent .= $this->formOption($fieldName, $label);
                        } else {
                            // 输入框
                            $this->formContent .= $this->formInput($fieldName, $label, 'number');
                        }

                        $this->formInitContent .= $this->formInitTemplate($fieldName, 0);
                        $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    }
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                    // 浮点情况暂时和整型一致的处理方案（TODO: 生成的表单可能要调整）
                    $this->formContent .= $this->formInput($fieldName, $label, 'number');
                    $this->formInitContent .= $this->formInitTemplate($fieldName, 0);
                    $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    break;
                case 'date':
                    $this->formContent .= $this->formDate($fieldName, $label);
                    $this->formInitContent .= $this->formInitTemplate($fieldName, 'null');
                    $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    break;
                case 'timestamp':
                    // 特殊字段不生成表单
                    if (! in_array($fieldName, ['created_at', 'updated_at', 'deleted_at'])) {
                        $this->formContent .= $this->formDate($fieldName, $label, 'datetime');
                        $this->formInitContent .= $this->formInitTemplate($fieldName, 'null');
                        $this->formRulesContent .= $this->formRulesTemplate($fieldName, $label);
                    }
                    break;
                default:
                    $this->error("字段--暂无处理方案: $fieldName, $label, $fieldType");
            }
        }

        // 表格最后补上操作按钮
        $this->tableContent .= $this->tableColumn('', '', 'slot-button');
    }

    /**
     * https://element.eleme.cn/#/zh-CN/component/form#zi-ding-yi-xiao-yan-gui-ze
     * 表单规则
     *
     * @param $fieldName
     * @param $label
     * @return string
     */
    protected function formRulesTemplate($fieldName, $label)
    {
        $op = '填写';

        if (
            in_array($fieldName, ['status', 'type']) ||
            Str::startsWith($fieldName, ['is_']) ||
            Str::endsWith($fieldName, ['_id', '_status', '_type', '_at'])
        ) {
            $op = '选择';
        }

        return <<<STR
                {$fieldName}: [
                    { required: true, message: '请{$op} {$label}', trigger: 'blur' }
                ],\n
STR;
    }

    // 表单默认值
    protected function formInitTemplate($fieldName, $default = '')
    {
        if ($fieldName === 'id') {
            $default = 0;
        }

        if ($default == '') {
            $default = "''";
        }

        return "                {$fieldName}: {$default},\n";
    }

    /**
     * https://element.eleme.cn/#/zh-CN/component/table
     * 生成表格常用字段
     *
     * @param $label
     * @param $prop
     * @param string $type 'normal, slot, slot-button'
     */
    protected function tableColumn($label, $prop, $type = 'normal')
    {
        // 特殊处理 id 字段作为 slot， 方便后续有其他字段调整可复制
        if ($prop === 'id') {
            $type = 'slot';
        }

        // 特殊处理，无需在表格显示的字段
        if ($prop === 'deleted_at') {
            return '';
        }

        switch ($type) {
            case 'normal':
                return <<<STR
            <el-table-column prop="{$prop}" label="{$label}"></el-table-column>\n
STR;
                break;
            case 'slot':
                return <<<STR
            <el-table-column prop="{$prop}" label="{$label}">
                <template slot-scope="{row}">
                    <span>@{{ row.{$prop} }}</span>
                </template>
            </el-table-column>\n
STR;
                break;
            case 'slot-button':
                return <<<STR
            <el-table-column label="操作">
                <template slot-scope="{row}">
                    <el-button @click="openEdit(row)" v-show="{{ p('admin.{$this->table}.edit') }}" type="primary" plain>编辑</el-button>
                    <el-button @click="rowDelete(row)" v-show="{{ p('admin.{$this->table}.delete') }}" type="warning" plain>删除</el-button>
                </template>
            </el-table-column>
STR;
                break;
            default:
                $this->error("表格字段--暂无处理方案: $prop, $label");
        }
    }

    /**
     * https://element.eleme.cn/#/zh-CN/component/input
     *
     * @param $fieldName "字段key"
     * @param $label "字段中文名"
     * @param string $type "text，textarea 和其他 原生 input 的 type 值"
     * @return string
     */
    protected function formInput($fieldName, $label, $type = 'text')
    {
        $rows = ($type === 'textarea') ? 'rows="3"' : '';

        return <<<STR
                        <el-form-item label="{$label}" prop="{$fieldName}">
                            <el-input v-model="{$this->table}_form.$fieldName" type="{$type}" placeholder="请输入 {$label}" {$rows}>
                            </el-input>
                        </el-form-item>\n
        STR;
    }

    /**
     * 表单单选框
     *
     * @param $fieldName
     * @param $label
     * @return string
     */
    protected function formRadio($fieldName, $label)
    {
        return <<<STR
                <el-form-item label="{$label}" prop="{$fieldName}">
                    <el-radio-group v-model="{$this->table}_form.{$fieldName}">
                        <el-radio :label="1">{$label}1</el-radio>
                        <el-radio :label="2">{$label}2</el-radio>
                    </el-radio-group>
                </el-form-item>\n
STR;
    }

    /**
     * 表单下拉框
     *
     * @param $fieldName
     * @param $label
     * @return string
     */
    protected function formOption($fieldName, $label)
    {
        $optionName = str_replace(['_id'], '', $fieldName);
        $optionName = "{$optionName}_options";
        $optionMethodName = Str::ucfirst(Str::camel($optionName)); // 转大驼峰

        // 下拉选项， vue 当中的属性，方法，以及方法的调用
        $this->optionContent .= "            {$optionName}: [],\n";
        $this->optionMethodContent .= $this->formOptionMethod($optionName, $optionMethodName);
        $this->optionMethodCallContent .= "        this.get{$optionMethodName}();\n";

        return <<<STR
                <el-form-item label="{$label}" prop="{$fieldName}">
                    <el-select v-model="{$this->table}_form.{$fieldName}" placeholder="请选择 {$label}">
                        <el-option v-for="item in {$optionName}" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>\n
STR;
    }

    /**
     * 生成 js 方法
     * @param $optionName "属性名"
     * @param $methodName "方法名"
     */
    protected function formOptionMethod($optionName, $methodName)
    {
        return <<<STR
        get{$methodName}() {
            axios.get('/admin/{$optionName}')
            .then((res) => {
                this.{$optionName} = res;
            })
        },\n
STR;
    }

    /**
     * https://element.eleme.cn/#/zh-CN/component/date-picker
     * https://element.eleme.cn/#/zh-CN/component/datetime-picker
     *
     * @param $fieldName
     * @param $label
     * @param string $type "date, datetime"
     */
    protected function formDate($fieldName, $label, $type = 'date')
    {
        return <<<STR
                        <el-form-item label="{$label}" prop="{$fieldName}">
                            <el-date-picker v-model="{$this->table}_form.$fieldName" type="$type" placeholder="请选择 {$label}">
                            </el-date-picker>
                        </el-form-item>\n
        STR;
    }

    protected function checkExist(): bool|int
    {
        $path = $this->fileDir;
        if ($this->subDir) {
            $path = "$this->subDir/$this->fileDir";
        }

        $dir = resource_path("views/$path");
        if (! file_exists($dir)) {
            mkdir($dir);
        }

        $this->filePathPhp = $dir . DIRECTORY_SEPARATOR . "index.blade.php";
        $this->filePathJs = $dir . DIRECTORY_SEPARATOR . "index_vue.js";

        return file_exists($this->filePathPhp);
    }

    /**
     * 生成模板
     */
    protected function makeContentPhp()
    {
        $replaces = [
            '{$table}' => $this->table,
            '{$subDir}' => $this->subDir,
            '{$fileDir}' => $this->fileDir,
            '{$formContent}' => $this->formContent,
            '{$tableContent}' => $this->tableContent,
        ];

        $this->contentPhp = $this->loadPhpStub();

        $this->contentPhp = str_replace(array_keys($replaces), array_values($replaces), $this->contentPhp);
    }

    /**
     * 生成模板 Js
     */
    protected function makeContentJs()
    {
        $replaces = [
            '{$table}' => $this->table,
            '{$subDir}' => $this->subDir,
            '{$fileDir}' => $this->fileDir,
            '{$formInitContent}' => $this->formInitContent,
            '{$formRulesContent}' => $this->formRulesContent,
            '{$optionContent}' => $this->optionContent,
            '{$optionMethodContent}' => $this->optionMethodContent,
            '{$optionMethodCallContent}' => $this->optionMethodCallContent,
            '{$urlList}' => $this->urlList,
        ];

        $this->contentJs = $this->loadJsStub();

        $this->contentJs = str_replace(array_keys($replaces), array_values($replaces), $this->contentJs);
    }

    protected function loadPhpStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/index.blade.php.stub');
    }


    protected function loadJsStub(): string
    {
        return file_get_contents(__DIR__.'/stubs/index_vue.js.stub');
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
