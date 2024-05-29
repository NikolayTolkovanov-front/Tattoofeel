<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;

class CustomerorderController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * @return array
     */
    public function actionCreate()
    {
        return ['order' => 'create'];
    }

    /**
     * @return array
     */
    public function actionUpdate()
    {
        return ['order' => 'update'];
    }

    /**
     * @return array
     */
    public function actionDelete()
    {
        return ['order' => 'delete'];
    }
}
