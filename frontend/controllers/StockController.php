<?php

namespace frontend\controllers;

use common\models\Stock;
use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class StockController extends BaseController
{

    public $pageSize = 8;

    /**
     * @var $page integer
     * @return string
     */
    public function actionIndex($page = 0)
    {
        $stockDataProvider = new ActiveDataProvider([
            'query' => Stock::find()->published()->orderBy([ 'published_at' => SORT_DESC ]),
            'pagination' => [ 'pageSize' => $this->pageSize, 'page' => $page ]
        ]);

        if ( Yii::$app->request->isAjax && !Yii::$app->request->isPjax )
            return $this->renderPartial('_list',[
                'isAjax' => true,
                'dataProvider' => $stockDataProvider
            ]);

        return $this->render('index',[
            'dataProvider' => $stockDataProvider,
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()->popular()->limit(16),
                'pagination' => false,
            ])
        ]);
    }

    /**
     * @param $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($slug)
    {
        $model = Stock::find()->published()->andWhere(['slug' => $slug])->one();
        if (!$model) {
            throw new NotFoundHttpException;
        }

        return $this->render('detail', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()->popular()->limit(16),
                'pagination' => false,
            ])
        ]);
    }
}
