<?php

use yii\rbac\DbManager;

//try {
//    $log = new \common\loggers\CustomLogger();
//    $log->createLogs();
//} catch (Exception $e) {
//    //
//}

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'components' => [
        'authManager' => [
            'class' => DbManager::class,
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'sync' => [
            'class' => console\controllers\SyncController::class
        ],
        'command-bus' => [
            'class' => trntv\bus\console\BackgroundBusController::class,
        ],
        'message' => [
            'class' => console\controllers\ExtendedMessageController::class
        ],
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => '@common/migrations/db',
            'migrationTable' => '{{%system_db_migration}}'
        ],
        'rbac-migrate' => [
            'class' => console\controllers\RbacMigrateController::class,
            'migrationPath' => '@common/migrations/rbac/',
            'migrationTable' => '{{%system_rbac_migration}}',
            'templateFile' => '@common/rbac/views/migration.php'
        ],
    ],
];
