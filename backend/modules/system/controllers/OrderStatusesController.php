<?php

namespace backend\modules\system\controllers;

use common\models\OrderStatuses;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\modules\system\models\search\OrderStatusesSearch;
use common\traits\FormAjaxValidationTrait;

class OrderStatusesController extends Controller
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
        $category = new OrderStatuses();

        $this->performAjaxValidation($category);

        if ($category->load(Yii::$app->request->post()) && $category->save()) {
            return $this->redirect(['index']);
        }
        $searchModel = new OrderStatusesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $ms_statuses = $this->ms_get_order_statuses();
        foreach ($ms_statuses as $ms_status) {
            if (isset($ms_status->id) && !empty($ms_status->id)) {
                $status = OrderStatuses::find()->where(['ms_status_id' => $ms_status->id])->one();
                if (!$status) {
                    $status = new OrderStatuses();
                    $status->ms_status_id = $ms_status->id;
                    $status->ms_title = $ms_status->name;
                    $status->title = '-';
                    $status->save();
                }
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $category,
            //'statuses' => $this->ms_get_order_statuses(),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderStatuses();

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
     * @return OrderStatuses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderStatuses::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function ms_get_order_statuses()
    {
        $msStatuses = $this->ms_send_sync('entity/customerorder/metadata', $method = 'GET');

        $statuses = array();
        if (count($msStatuses->responseContent->states)) {
            $statuses = $msStatuses->responseContent->states;
        }

        return $statuses;
    }

    /**
     * @param $url
     * @param null $data
     * @param string $method
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function ms_send_sync($url, $method = 'GET')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/1.2/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        $response = $request->send();
        $result->response = $response;

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;
            return $result;
        }

        try {
            if (isset($response->content)) {
                if ($response->headers['content-encoding'] == 'gzip') {
                    $responseContent = json_decode(gzdecode($response->content));
                } else {
                    $responseContent = json_decode($response->content);
                }
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || $e = json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error (' . $e . ')';
                    $result->json_error = true;
                    return $result;
                }
            }

            return $result;

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;
            return $result;
        }
    }
}
