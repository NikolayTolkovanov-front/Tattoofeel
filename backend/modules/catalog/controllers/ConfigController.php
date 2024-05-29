<?php

namespace backend\modules\catalog\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use backend\components\sync\SyncController;
use backend\modules\catalog\models\search\ProductCategoryConfigSearch;
use common\models\ProductCategoryConfig;
use common\traits\FormAjaxValidationTrait;

class ConfigController extends SyncController
{
    public $sync_cmd = "sync/sync-product-config";
    public function getSyncProvider() {
        return ProductCategoryConfig::syncProvider();
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
        $categoryConfig = new ProductCategoryConfig();

        $this->performAjaxValidation($categoryConfig);

        if ($categoryConfig->load(Yii::$app->request->post()) && $categoryConfig->save()) {
            return $this->redirect(['index']);
        }

        return $this->indexTable($categoryConfig);
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionDisable()
    {
        return $this->indexTable(new ProductCategoryConfig());
    }

    protected function indexTable($categoryConfig) {
        $searchModel = new ProductCategoryConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $categoryConfig
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
        $categoryConfig = $this->findModel($id);

        $this->performAjaxValidation($categoryConfig);

        if ($categoryConfig->load(Yii::$app->request->post()) && $categoryConfig->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $categoryConfig
        ]);
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
     * @return ProductCategoryConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductCategoryConfig::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
