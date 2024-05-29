<?php
$config = [
    'components' => [
        'assetManager' => [
            'class' => yii\web\AssetManager::class,
            'linkAssets' => env('LINK_ASSETS'),
            'appendTimestamp' => true,
            'forceCopy' => true /// TODO
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['test'],
                    'logFile' => '@app/runtime/logs/test.log'
                ],[
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['_error'],
                    'logFile' => '@app/runtime/logs/_error.log'
                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'info'],
                    'categories' => ['cart'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/cart.log',
                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'info'],
                    'categories' => ['ms'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/ms.log',
                ]
            ],
        ],
    ],
    'as locale' => [
        'class' => common\behaviors\LocaleBehavior::class,
        'enablePreferredLanguage' => true
    ]
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['*'],
    ];
}

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'allowedIPs' => ['*'],
    ];
}


return $config;
