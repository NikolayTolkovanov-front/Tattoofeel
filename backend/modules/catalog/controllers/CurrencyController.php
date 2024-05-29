<?php

namespace backend\modules\catalog\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use \backend\components\sync\SyncController;

use backend\modules\catalog\models\search\CurrencySearch;
use common\models\Currency;
use common\traits\FormAjaxValidationTrait;

class CurrencyController extends SyncController
{
    use FormAjaxValidationTrait;

    public $sync_cmd = "sync/sync-currency";
    public function getSyncProvider() {
        return Currency::syncProvider();
    }

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
        $currency = new Currency();

        $this->performAjaxValidation($currency);

        if ($currency->load(Yii::$app->request->post()) && $currency->save()) {
            return $this->redirect(['index']);
        }

        return $this->indexTable($currency);
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionDisable()
    {
        return $this->indexTable(new Currency());
    }

    protected function indexTable($currency) {
        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $currency,
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
        $currency = $this->findModel($id);

        $this->performAjaxValidation($currency);

        if ($currency->load(Yii::$app->request->post()) && $currency->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $currency
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
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
