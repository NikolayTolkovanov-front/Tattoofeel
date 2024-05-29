<?php

namespace frontend\controllers;

use frontend\models\Product;
use Yii;
use common\models\Brand;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class BrandsController extends BaseController
{
    public $pageSize = 24;
    public $productPageSize = 24;

    /**
     * @param $page
     * @return string
     */
    public function actionIndex($page = 0)
    {

        $brandDataProvider = new ActiveDataProvider([
            'query' => Brand::find()->published()->orderBy([ 'title' => SORT_ASC ]),
            'pagination' => [ 'pageSize' => $this->pageSize, 'page' => $page ]
        ]);

        if ( Yii::$app->request->isAjax && !Yii::$app->request->isPjax )
            return $this->renderPartial('_list',[
                'isAjax' => true,
                'brandDataProvider' => $brandDataProvider
            ]);

        return $this->render('index',[
            'mainModel' => Brand::find()->isMain()->one(),
            'brandDataProvider' => $brandDataProvider,
            'filterBrands' => Brand::find()->select(['title','slug'])
                ->published()->orderBy([ 'title' => SORT_ASC ])->all(),
        ]);

    }

    /**
     * @param  $slug
     * @param  $page
     * @param  $sort
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($slug = null, $page = 0)
    {
        $pagePost = Yii::$app->request->post('page');
        if ($pagePost) {
            $page = $pagePost;
        }
        if (!$slug)
            throw new NotFoundHttpException();

        $mainModel = Brand::find()->where(['slug' => $slug])->one();

        if (!$mainModel)
            throw new NotFoundHttpException();

        $productDataProvider = new ActiveDataProvider([
            'query' => Product::find()
                ->published()
                ->preparePrice()
                ->prepareConfig()
                ->andWhere([Product::tableName().'.brand_id' => $mainModel->slug]),
            'pagination' => [ 'pageSize' => $this->productPageSize, 'page' => (int)$page ]
        ]);

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            return $this->renderPartial('//catalog/_product-list', [
                'isAjax' => true,
                'productDataProvider' => $productDataProvider,
                'brandPage' => true,
            ]);
        }

        return $this->render('detail', [
            'mainModel' => $mainModel,
            'filterBrands' => Brand::find()->select(['title','slug'])
                ->published()->orderBy([ 'title' => SORT_ASC ])->all(),
            'productDataProvider' => $productDataProvider
        ]);
    }
}
