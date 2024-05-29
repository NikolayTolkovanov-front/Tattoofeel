<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 */

$cache = [
    'class' => yii\caching\MemCache::class,
    'servers' => [
        [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 1000,
        ],
    ],
    'useMemcached' => true,
];

if (YII_ENV_DEV) {
    $cache = [
        'class' => yii\caching\DummyCache::class
    ];
}

return $cache;
