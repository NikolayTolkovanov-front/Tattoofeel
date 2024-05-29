<?php

namespace backend\modules\import\controllers;

use backend\modules\import\models\PickPointTerminal;
use yii\web\Controller;

class CdekController extends Controller
{

    public function actionIndex()
    {
       return $this->render('index');
    }
}
