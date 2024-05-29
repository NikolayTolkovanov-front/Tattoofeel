<?php

namespace frontend\modules\api\traits;

trait Helpers
{
    protected function module($module)
    {
        return \Yii::$app->getModule($module);
    }

    protected function request($key)
    {
        return \Yii::$app->request->get($key);
    }

    protected function session()
    {
        return \Yii::$app->session;
    }
}