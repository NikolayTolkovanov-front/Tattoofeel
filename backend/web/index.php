<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set('Europe/Moscow');

ini_set('max_execution_time', 1200);
ini_set('memory_limit', '1000M');
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '1000M');

// Composer
require(__DIR__ . '/../../vendor/autoload.php');

// Environment
require(__DIR__ . '/../../common/env.php');

// Yii
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

// Bootstrap application
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');


$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/base.php'),
    require(__DIR__ . '/../../common/config/web.php'),
    require(__DIR__ . '/../config/base.php'),
    require(__DIR__ . '/../config/web.php')
);

(new yii\web\Application($config))->run();
