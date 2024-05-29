<?php

namespace frontend\helpers;

use DateInterval;
use DateTime;
use Yii;

class All {
    static function rn($v) {
        return str_replace(["\r\n", "\r", "\n"], ' ',  $v);
    }
    static function esc($v) {
        $r = str_replace("\\","\\\\",  $v);
        $r = str_replace("'","\'",  $r);

        return $r;
    }

    static function asWarranty($value, $implodeString = ', ', $negativeSign = '-')
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateInterval) {
            $isNegative = $value->invert;
            $interval = $value;
        } elseif (is_numeric($value)) {
            $isNegative = $value < 0;
            $zeroDateTime = (new DateTime())->setTimestamp(0);
            $valueDateTime = (new DateTime())->setTimestamp(abs($value));
            $interval = $valueDateTime->diff($zeroDateTime);
        } elseif (strncmp($value, 'P-', 2) === 0) {
            $interval = new DateInterval('P' . substr($value, 2));
            $isNegative = true;
        } else {
            $interval = new DateInterval($value);
            $isNegative = $interval->invert;
        }

        $parts = [];
        if ($interval->y > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 year} other{# years}}', ['delta' => $interval->y], Yii::$app->language);
        }
        if ($interval->m > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 month} other{# months}}', ['delta' => $interval->m], Yii::$app->language);
        }

        return empty($parts) ? null : (($isNegative ? $negativeSign : '') . implode($implodeString, $parts));
    }
}
