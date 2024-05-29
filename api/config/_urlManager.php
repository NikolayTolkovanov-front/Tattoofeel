<?php

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article', 'only' => ['index', 'view', 'options']],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/product', 'only' => ['index', 'view', 'options']],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'api/v1/cart',
            'tokens' => [
                '{id}' => '<id:[\d]+>',
            ],
            'extraPatterns' => [
                'POST' => 'create',
                'PUT {id}' => 'update',
                'PATCH {id}' => 'update',
                'GET {id}' => 'index',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'api/v1/city',
            'tokens' => [
                '{sdek_id}' => '<sdek_id:[\d]+>',
            ],
            'extraPatterns' => [
                'POST get-by-name' => 'get-by-name',
                'GET get-sdek-pvz {sdek_id}' => 'get-sdek-pvz',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'api/v1/delivery',
            'extraPatterns' => [
                'POST get-tariffs' => 'get-tariffs',
                'POST set-tariff' => 'set-tariff',
            ],
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'api/v1/order',
            'tokens' => [
                '{id}' => '<id:\\w+>',
            ],
            'extraPatterns' => [
                'POST' => 'create',
                'POST get-pay-link' => 'get-pay-link',
                'GET {id}' => 'index',
                'GET get-payment-types {id}' => 'get-payment-types',
                'PUT update-payment-type {id}' => 'update-payment-type',
                'PATCH update-payment-type {id}' => 'update-payment-type',
            ],
        ],

        //Мой склад
        ['pattern' => 'api/v1/moy-sklad/<action>', 'route' => 'api/v1/moy-sklad/<action>'],
    ]
];
