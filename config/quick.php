<?php

return [
    // medium / small / mini
    'ui_size' => 'medium',

    // title 标签
    'admin_title' => 'Quick Laravel',

    // 左上角菜单
    'admin_name' => 'Quick',

    // 后台路径 自定义 （eg 'admin'）
    'admin_path' => 'admin',

    // 操作日志不记录的路由path （eg '/admin/log/index'）
    'block_path_list' => [
        '/admin/permission/menus',
        '/admin/menu/tree_menus',
        '/admin/user/roles',
        '/admin/menu/tree',
        '/admin/menu/top_options',
    ],

    // 操作日志不记录的路由别名
    'block_as_list' => [
        'admin.log.index',
    ],
];
