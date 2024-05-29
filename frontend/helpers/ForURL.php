<?php

namespace frontend\helpers;

class ForURL {
    static function isActive($controllerID, $actionID = null, $altClass = null, $nameClass = "-active") {

        $current = \Yii::$app->controller->id === $controllerID;

        if ($current && $actionID)
            $current = \Yii::$app->controller->action->id == $actionID ||
                (isset(\Yii::$app->controller->actionParams['slug']) && \Yii::$app->controller->actionParams['slug'] == $actionID);

        $attrClass = (string) $altClass . ( $current ? " $nameClass" : '' );

        return $attrClass ? " class=\"$attrClass\" " : '';
    }
    static function isActiveSubCat($controllerID, $slug = null, $altClass = null, $nameClass = "-active") {

        $current = \Yii::$app->controller->id === $controllerID;

        if ( isset(\Yii::$app->controller->actionParams['slug']) ) {
            if ($current && $slug)
                $current = \Yii::$app->controller->actionParams['slug'] == $slug;
        } else $current = false;

        $attrClass = (string) $altClass . ( $current ? " $nameClass" : '' );

        return $attrClass ? " class=\"$attrClass\" " : '';
    }
}
