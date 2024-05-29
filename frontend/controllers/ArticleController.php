<?php

namespace frontend\controllers;

use common\models\ArticleN;
use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class ArticleController extends BaseController
{

    public $pageSize = 8;

    /**
     * @var $page integer
     * @return string
     */
    public function actionIndex($page = 0)
    {
        $articleNDataProvider = new ActiveDataProvider([
            'query' => ArticleN::find()->published()->orderBy([ 'published_at' => SORT_DESC ]),
            'pagination' => [ 'pageSize' => $this->pageSize, 'page' => $page ]
        ]);

        if ( Yii::$app->request->isAjax && !Yii::$app->request->isPjax )
            return $this->renderPartial('_list',[
                'isAjax' => true,
                'dataProvider' => $articleNDataProvider
            ]);

        return $this->render('index',[
            'dataProvider' => $articleNDataProvider,
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()
                    ->preparePrice()
                    ->published()->limit(16),
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
        $model = ArticleN::find()->published()->andWhere(['slug' => $slug])->one();
        if (!$model) {
            throw new NotFoundHttpException;
        }

        return $this->render('detail', [
            'model' => $model,
            'productsRecently' => new ActiveDataProvider([
                'query' => Product::find()
                    ->preparePrice()
                    ->published()->limit(16),
                'pagination' => false,
            ])
        ]);
    }
}
