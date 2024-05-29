<?php

namespace backend\modules\system\controllers;

use common\models\AdminIp;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\modules\system\models\search\AdminIpSearch;
use common\traits\FormAjaxValidationTrait;

class AdminIpController extends Controller
{
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
        $model = new AdminIp();

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        $searchModel = new AdminIpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminIp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
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
        if (is_null(Yii::$app->request->post('update')) && is_null(Yii::$app->request->post('save')))
            Url::remember(Yii::$app->request->referrer,'index');

        $model = $this->findModel($id);

        $this->performAjaxValidation($model);

        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save() &&
            is_null(Yii::$app->request->post('update'))
        ) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
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
     * @return AdminIp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminIp::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
