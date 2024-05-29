<?php

namespace backend\modules\catalog\controllers;

use common\models\Product;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\components\sync\SyncController;

use backend\modules\catalog\models\search\ProductSyncSearch;
use common\models\ProductSync;


class ProductsSyncController extends SyncController
{
    public $sync_cmd = "sync/sync-products";
    public function getSyncProvider() {
        return Product::syncProvider();
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->indexTable();
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionDisable()
    {
        return $this->indexTable();
    }

    protected function indexTable() {
        $searchModel = new ProductSyncSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->sort = ['defaultOrder' => ['date' => SORT_DESC]];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionView($id)
    {
        $sync = $this->findModel($id);

        return $this->render('view', [
            'model' => $sync,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return mixed
     * @throws
     */
    public function actionClear()
    {
        ProductSync::deleteAll();

        return $this->redirect(['index']);
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
     * @return ProductSync the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductSync::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
