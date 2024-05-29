<?php

namespace backend\modules\catalog\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\SeoMetaTagsSearch;
use common\models\SeoMetaTags;
use common\models\KeyStorageItem;
use common\traits\FormAjaxValidationTrait;

class SeoMetaTagsController extends Controller
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
            $key = "backend.history.seo_meta_tags.$sp.$user->username";
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
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $model = new SeoMetaTags();

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        $searchModel = new SeoMetaTagsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
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

        $seo_meta_tags = new SeoMetaTags();

        $this->performAjaxValidation($seo_meta_tags);

        if ($seo_meta_tags->load(Yii::$app->request->post()) && $seo_meta_tags->save() && is_null(Yii::$app->request->post('update'))) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        return $this->render(is_null(Yii::$app->request->post('update')) ? 'create' : 'update', [
            'model' => $seo_meta_tags,
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
     * @return SeoMetaTags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SeoMetaTags::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');

    }
}
