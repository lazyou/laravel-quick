<?php

/**
 * 新增的配置
 */
return [
    // 日志可参考 config/logging.php
    // 记录 sql 专用日志. 实时查看: tail -n 200 -f  storage/logs/sql-xxxx-xx-xx.log
        'channels.mysql' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mysql.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
];
