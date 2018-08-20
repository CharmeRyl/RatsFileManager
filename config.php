<?php
/**
 * FileManager by Charmeryl
 * Author: jqjiang
 * Time:   2018/8/2 18:21
 */

return [
    'auth' => [
        'enabled' => true,
        'users' => [
            'admin' => 'admin'
        ],
        'salt' => 'bcE9pTBzmWBqUApsvBt919EGPFrNVJPM7XVX1Hpw2csZ7MiC7cRrrqn'
    ],
    'storage' => [
        'backend' => 'local',
        'path' => '/storage',
        'local' => '',
        'tencent-cos' => [
            'credentials' => [
                'secretId' => 'AKIDQjz3ltompVjBni5LitkWHFlFpwkn9U5q',
                'secretKey' => 'BQYIM75p8x0iWVFSIgqEKwFprpRSVHlz'
            ],
            'region' => 'ap-beijing',
            'bucket' => 'bucket1-1254000000',
            'custom_url' => ''
        ]
    ],
    'system' => [
        'rewrite' => false,
        'timezone' => 'Asia/Shanghai',
        'site_name' => 'RatsFileManager'
    ],
];