<?php

/**
 * 新增的配置
 */
return [
    // 用户登录认证部分可参考 config/auth.php
    'guards' => [
        'quick_web' => [
            'driver' => 'session',
            'provider' => 'quick_user',
        ],

//        'quick_api' => [
//            'driver' => 'passport',
//            'provider' => 'quick_wechat_user', // 底下 providers 需要增加相应的 quick_wechat_user 配置
//            'hash' => false,
//        ],
    ],

    'providers' => [
        'quick_user' => [
            'driver' => 'eloquent',
            'model' => \Lazyou\Quick\Models\QuickUser::class,
        ],

//        'quick_wechat_user' => [
//            'driver' => 'eloquent',
//            'model' => \Lazyou\Quick\Models\QuickWechatUser::class,
//        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],
];
