<?php

$config = [
    'name' => 'Tattoo Feel',
    'vendorPath' => __DIR__ . '/../../vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'sourceLanguage' => 'ru-RU',
    'language' => 'ru-RU',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'authManager' => [
            'class' => yii\rbac\DbManager::class,
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],
        'dbCache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'cache' => [
            'class' => yii\caching\MemCache::class,
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 1000,
                ],
            ],
            'useMemcached' => true,
        ],
        'commandBus' => [
            'class' => trntv\bus\CommandBus::class,
            'middlewares' => [
                [
                    'class' => trntv\bus\middlewares\BackgroundCommandMiddleware::class,
                    'backgroundHandlerPath' => '@console/yii',
                    'backgroundHandlerRoute' => 'command-bus/handle',
                ]
            ]
        ],
        'consoleRunner' => [
            'class' => 'vova07\console\ConsoleRunner',
            'file' => '@console/yii' // or an absolute path to console file
        ],
        'formatter' => [
            'class' => yii\i18n\Formatter::class
        ],

        'glide' => [
            'class' => trntv\glide\components\Glide::class,
            'sourcePath' => '@storage/web/source',
            'cachePath' => '@storage/cache',
            'urlManager' => 'urlManagerStorage',
            'maxImageSize' => env('GLIDE_MAX_IMAGE_SIZE'),
            'signKey' => env('GLIDE_SIGN_KEY')
        ],

        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('SMTP_HOST'),
                'username' => env('ROBOT_EMAIL'),
                'password' => env('ROBOT_EMAIL_SMTP_PASS'),
                'port' => '465',
                'encryption' => 'ssl'
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => env('ROBOT_EMAIL'),
            ]
        ],

        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => env('DB_DSN'),
            //'attributes' => getenv('MYSQL_ATTR_SSL_CA') ? [PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),] : [],
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => env('DB_CHARSET', 'utf8'),
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 600000,
            'schemaCache' => 'dbCache',
            'enableQueryCache' => true,
            'queryCache' => 'dbCache',
            'attributes' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
  //TODO              PDO::ATTR_PERSISTENT => true
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'db' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => [ 'error', 'warning'],
                    'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*', 'yii\web\Session:*',],
                    'prefix' => function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars' => [],
                    'logTable' => '{{%system_log}}'
                ],
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => [],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/app.log',
                ]
            ],
        ],

        'i18n' => [
            'translations' => [
                /*
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php',
                        'frontend' => 'frontend.php',
                    ],
                    'on missingTranslation' => [backend\modules\translation\Module::class, 'missingTranslation']
                ],
                */
                '*' => [
                    'class' => yii\i18n\DbMessageSource::class,
                    'sourceMessageTable' => '{{%i18n_source_message}}',
                    'messageTable' => '{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    'on missingTranslation' => [backend\modules\translation\Module::class, 'missingTranslation']
                ],
            ],
        ],

        'fileStorageLL' => [
            'class' => trntv\filekit\Storage::class,
            'baseUrl' => '@storageUrl',
            'filesystem' => [
//                'class' => common\components\filesystem\LocalFlysystemBuilder::class,
                'class' => common\components\AwsS3v3FlyFilesystemBuilder::class,
                'region' => 'sa-east-1',
                'endpoint' => 'http://minio:9000',
                'use_path_style_endpoint' => true,
                'key' => 'minioadmin',
                'secret' => 'minioadmin',
                'bucket' => 'storage',
//                'path' => '@storage/web/source'
            ],
            'as log' => [
                'class' => common\behaviors\FileStorageLogBehavior::class,
                'component' => 'fileStorage'
            ]
        ],

        'fileStorage' => [
            'class' => trntv\filekit\Storage::class,
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => common\components\filesystem\LocalFlysystemBuilder::class,
                'path' => '@storage/web/source'
            ],
            'as log' => [
                'class' => common\behaviors\FileStorageLogBehavior::class,
                'component' => 'fileStorage'
            ]
        ],
        'keyStorage' => [
            'class' => common\components\keyStorage\KeyStorage::class
        ],
        'keyStorageApp' => [
            'class' => common\components\keyStorage\KeyStorage::class,
            'modelClass' => '\common\models\KeyStorageAppItem',
            'cachePrefix' => '_keyStorageApp',
        ],

        'subdomains' => [
            'class' => common\components\subdomains\Subdomains::class
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('BACKEND_HOST_INFO'),
                'baseUrl' => env('BACKEND_BASE_URL'),
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('FRONTEND_HOST_INFO'),
                'baseUrl' => env('FRONTEND_BASE_URL'),
            ],
            require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('STORAGE_HOST_INFO'),
                'baseUrl' => env('STORAGE_BASE_URL'),
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),

        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@common/runtime/queue',
        ],
    ],
    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'robotEmail' => env('ROBOT_EMAIL'),
        'availableLocales' => [
            'en-US' => 'English (US)',
            'ru-RU' => 'Русский (РФ)',
            /*
            'uk-UA' => 'Українська (Україна)',
            'es' => 'Español',
            'fr' => 'Français',
            'vi' => 'Tiếng Việt',
            'zh-CN' => '简体中文',
            'pl-PL' => 'Polski (PL)',
            */
        ],
        'moy_sklad' => [
            'url' => env('MOY_SKLAD_API_URL')
        ],
        'origins' => [
            'http://localhost:5173',
            'http://tattoofeel_prod.loc',
            'http://tattoofeel.ru',
            'http://tattoofeel2.ru',
            'http://pre-prod-ttf.ru',
        ]
    ],
];

if (YII_ENV_PROD) {
    $config['components']['log']['targets']['email'] = [
        'class' => yii\log\EmailTarget::class,
        'except' => ['yii\web\HttpException:*', 'yii\web\Session:*'],
        //'levels' => ['error', 'warning'],
        'levels' => ['error'],
        'message' => ['from' => env('ROBOT_EMAIL'), 'to' => env('ADMIN_EMAIL')]
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class
    ];

    $config['components']['cache'] = [
        'class' => yii\caching\DummyCache::class,
    ];
    $config['components']['mailer'] = [
        'useFileTransport' => true
    ];
}

return $config;
