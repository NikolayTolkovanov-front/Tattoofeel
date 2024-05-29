<?php

namespace backend\modules\import\controllers;

use backend\modules\import\models\ImlCity;
use yii\web\Controller;

class ImlController extends Controller
{

    /**
     * @return string
     */
    public function actionIndex()
    {
       return $this->render('index');
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionUpdateCities()
    {

       $iml_city = new ImlCity();
       $result =  $iml_city->importCity();

        return json_encode($result);
    }
}
