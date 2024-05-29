<?php

namespace backend\modules\catalog\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\ReviewsSearch;
use common\models\Reviews;
use common\models\KeyStorageItem;
use common\traits\FormAjaxValidationTrait;

class ReviewsController extends Controller
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

    protected function getRequestParams($saveParams = [], $resetParam = null) {
        $requestParams = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $user = Yii::$app->user->getIdentity();
        $default = !empty($requestParams[$resetParam]);

        foreach($saveParams as $sp) {
            $key = "backend.history.reviews.$sp.$user->username";
            $param = KeyStorageItem::findOne(['key' => $key]);

            if (empty($param)) {
                $param = new KeyStorageItem();
                $param->key = $key;
            }

            if ($param) {
                if (!empty($requestParams[$sp]) || $default) {
                    $param->value = json_encode($requestParams[$sp]??null);
                    $param->save();
                }

                if (empty($requestParams[$sp]) && !$default)
                    $requestParams[$sp] = json_decode($param->value);

            }
        }

        return $requestParams;
    }

    /**
     * @return mixed
     */
    public function actionIndex() {
        return $this->index();
    }

    public function index()
    {
        $requestParams = $this->getRequestParams(['sh', 'per-page'], 'default');

        $searchModel = new ReviewsSearch();
        $dataProvider = $searchModel->search($requestParams);
        $dataProvider->sort = ['defaultOrder' => ['date' => 'DESC']];

        $pagination =  ['defaultPageSize' => 50];

        if (!empty($requestParams['page'])) {
            $pagination['page'] = $requestParams['page'];
        }
        if (!empty($requestParams['per-page'])) {
            $pagination['pageSize'] = $requestParams['per-page'];
        }

        $dataProvider->pagination = $pagination;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'queryParam__sh' => $requestParams['sh']??[],
            'queryParam__perPage' => $requestParams['per-page']??null,
            'qs' => $requestParams
        ]);
    }

    /**
     * @return mixed
     * @throws
     */
    public function actionCreate()
    {
        if ( is_null(Yii::$app->request->post('update')) && is_null(Yii::$app->request->post('save')) )
            Url::remember(Yii::$app->request->referrer,'index');

        $review = new Reviews();

        $this->performAjaxValidation($review);

        if ($review->load(Yii::$app->request->post()) && $review->save() && is_null(Yii::$app->request->post('update'))) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        return $this->render(is_null(Yii::$app->request->post('update')) ? 'create' : 'update', [
            'model' => $review,
            'brands' => [],
            'categories' => [],
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
     * @return Reviews the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reviews::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');

    }
}
