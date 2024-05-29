<?php

namespace backend\modules\client\controllers;

use backend\modules\client\models\search\UserClientOrderSearch;
use common\models\Product;
use common\models\UserClientOrder;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserClientOrderController implements the CRUD actions for UserClientOrder model.
 */
class OrdersController extends Controller
{
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
     * Lists all UserClientOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserClientOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new UserClientOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserClientOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing UserClientOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionSyncAdmin($id)
    {
        $model = $this->findModel($id);

        $model->comment = '(Создан администратором) '.$model->comment;

        $ms_order = $model->msCreateOrder(true);

        if ($ms_order->status) {
            $model->date = time();
            $model->sum_buy = $model->getSum();
            $model->save();
        } else {
            if ($ms_order->statusCode < 0 ) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body' => $ms_order->msg,
                    'options' => ['class' => 'alert-error']
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing UserClientOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserClientOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserClientOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserClientOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    //todo global
    public function actionSearch($term = '')
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax || Yii::$app->request->isPjax)
            throw new NotFoundHttpException();

        $preResult = Product::find()
            ->published()
            ->search($term)
            ->limit(20)
            ->all();

        $result = [];

        foreach($preResult as $model)
            $result[] = [
                'id' => $model->id,
                'label' => $model->title,
                'imgUrl' => $model->getImgUrl(),
                'url' => Url::to($model->getRoute()),
                'price' => $model->clientPrice->getCartPrice(),
                'term' => $term
            ];

        return $result;
    }
}
