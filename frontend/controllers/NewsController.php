<?php

namespace frontend\controllers;

use common\models\News;
use frontend\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class NewsController extends BaseController
{

    public $pageSize = 8;

    /**
     * @var $page integer
     * @return string
     */
    public function actionIndex($page = 0)
    {
        $newsDataProvider = new ActiveDataProvider([
            'query' => News::find()->published()->orderBy([ 'published_at' => SORT_DESC ]),
            'pagination' => [ 'pageSize' => $this->pageSize, 'page' => $page ]
        ]);

        if ( Yii::$app->request->isAjax && !Yii::$app->request->isPjax )
            return $this->renderPartial('_list',[
                'isAjax' => true,
                'dataProvider' => $newsDataProvider
            ]);

        return $this->render('index',[
            'dataProvider' => $newsDataProvider,
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
        $model = News::find()->published()->andWhere(['slug' => $slug])->one();
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
