<?php

namespace console\controllers;

use common\rbac\Migration;
use yii\console\controllers\MigrateController;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class RbacMigrateController extends MigrateController
{
    /**
     * Creates a new migration instance.
     * @param string $class the migration class name
     * @return Migration the migration instance
     */
    protected function createMigration($class)
    {
        $file = $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
        require_once($file);

        return new $class();
    }
}
