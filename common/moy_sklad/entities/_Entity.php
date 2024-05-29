<?php

namespace common\moy_sklad\entities;

/**
 * Общий класс для entities.
 */
abstract class _Entity implements _EntityIface
{
    const URL_PATH = '/entity';

    public static function path($endPath = null) {
        return self::URL_PATH . ($endPath ?? '/' . static::NAME);
    }

    public static function url($endPath = null) {
        return \Yii::$app->params['moy_sklad']['url'] . self::path($endPath);
    }
}