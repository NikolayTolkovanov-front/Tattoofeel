<?php

namespace frontend\helpers;

use DateInterval;
use DateTime;
use Yii;

class Debug {
    static function logModel($m, $p="") {
        Yii::info("logModel ($p):". str_replace(["\r\n", "\r", "\n"], ' ',  var_export($m,true)));
    }

    static function step($category, $context, $name) {
        Yii::info("$context :: $name", $category);
    }

    static function value($category, $context, $name, $value) {
        $value = var_export($value, true);
        Yii::info("$context > $name($value)", $category);
    }
}
