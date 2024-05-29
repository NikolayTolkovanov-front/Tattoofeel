<?php

namespace backend\modules\catalog\controllers;

use Yii;
use yii\web\Controller;

use backend\modules\catalog\models\search\ProductPriceTemplateSearch;
use common\models\ProductPriceTemplate;

class PriceTemplateController extends Controller
{
    public function actionIndex()
    {
        $priceTemplate = new ProductPriceTemplate();

        $searchModel = new ProductPriceTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $priceTemplate,
        ]);
    }

}
