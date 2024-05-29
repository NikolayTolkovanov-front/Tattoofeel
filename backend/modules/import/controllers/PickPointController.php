<?php

namespace backend\modules\import\controllers;

use backend\modules\import\models\PickPointTerminal;
use yii\web\Controller;

class PickPointController extends Controller
{


    public function actionIndex()
    {
//        $model = new PickPoint();
//        $model->getPostamatListForCity(713);

        return $this->render('index');
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionUpdateTerminalInfo()
    {
        $ppt = new PickPointTerminal();

        $result = $ppt->importTerminals();

        echo json_encode($result);
    }

}
