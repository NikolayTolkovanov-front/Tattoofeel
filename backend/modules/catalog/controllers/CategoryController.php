<?php

namespace backend\modules\catalog\controllers;

use backend\components\sync\SyncController;
use common\models\ProductCategory;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\ProductCategorySearch;
use common\traits\FormAjaxValidationTrait;

class CategoryController extends SyncController
{
    public $sync_cmd = "sync/sync-product-category";

    public function getSyncProvider() {
        return ProductCategory::syncProvider();
    }

    use FormAjaxValidationTrait;

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $category = new ProductCategory();

        $this->performAjaxValidation($category);

        if ($category->load(Yii::$app->request->post()) && $category->save()) {
            $this->updateLevel($category);
            $category->setProductFiltersCategories(
                Yii::$app->request->post('ProductCategory')['productFiltersCategories']
            );

            return $this->redirect(['index']);
        }

        $searchModel = new ProductCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $categories = ProductCategory::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $category,
            'categories' => $categories,
        ]);
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionDisable()
    {
        $category = new ProductCategory();

        $searchModel = new ProductCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $categories = ProductCategory::find()->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $category,
            'categories' => $categories,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionUpdate($id)
    {
        if ( is_null(Yii::$app->request->post('update')) && is_null(Yii::$app->request->post('save')) )
            Url::remember(Yii::$app->request->referrer,'index');

        $category = $this->findModel($id);

        $this->performAjaxValidation($category);

        if ($category->load(Yii::$app->request->post()) && $category->save() && is_null(Yii::$app->request->post('update'))) {
            $this->updateLevel($category);
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        $categories = ProductCategory::find()->andWhere(['not', ['id' => $id]])->all();
        $categories = ArrayHelper::map($categories, 'id', 'title');

        return $this->render('update', [
            'model' => $category,
            'categories' => $categories,
        ]);
    }

    public function updateLevel($category)
    {
        if ($category->parent_id) {
            $parentCategory = $this->findModel($category->parent_id);
            $category->level = $parentCategory->level + 1;
            $category->save(false);
        }
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     *
     * @return ProductCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductCategory::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
