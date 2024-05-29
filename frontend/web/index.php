<?php
session_start();
if (!isset($_SESSION['__idClient']) && $_SERVER['REQUEST_URI'] == "/") {
    if (file_exists(dirname(__FILE__)."/../runtime/".date("Y-m-d")."-{$_SERVER["SERVER_NAME"]}-index.html")) {
        die(file_get_contents(dirname(__FILE__)."/../runtime/".date("Y-m-d")."-{$_SERVER["SERVER_NAME"]}-index.html"));
    }
    ob_start();
}
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ^ E_STRICT);

date_default_timezone_set('Europe/Moscow');

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

if (!isset($_SESSION['__idClient']) && $_SERVER['REQUEST_URI'] == "/") {
    $home_content = ob_get_contents();
    //fputs(fopen(dirname(__FILE__)."/../runtime/".date("Y-m-d")."-{$_SERVER["SERVER_NAME"]}-index.html", 'w'), $home_content);
}
