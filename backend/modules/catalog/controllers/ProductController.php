<?php

namespace backend\modules\catalog\controllers;

use backend\components\sync\SyncController;
use common\models\KeyStorageItem;
use common\models\ProductAttachment;
use common\models\ProductPrice;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\ProductSearch;
use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductCategoryConfig;
use common\traits\FormAjaxValidationTrait;

class ProductController extends SyncController
{
    public $sync_cmd = null;
    public function getSyncProvider() {
        return Product::syncProvider();
    }
    public function actionDisable($ms_id = null){

        if (!empty($ms_id)) {
            $m = Product::findOne(['ms_id' => $ms_id]);
            if ($m) return $this->update($m->id);
        }

        return $this->index();
    }
    public function actionSync(){
        return null;
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

    protected function getRequestParams($saveParams = [], $resetParam = null) {
        $requestParams = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $user = Yii::$app->user->getIdentity();
        $default = !empty($requestParams[$resetParam]);

        foreach($saveParams as $sp) {

            $key = "backend.history.product.$sp.$user->username";
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

//        if (!empty($requestParams['Product'])) {
//            //echo "<pre>";print_r($requestParams['Product']);echo "</pre>";
//            //die('test');
//
//            $products = Product::find()
//                ->where(['in', 'id', array_keys($requestParams['Product'])])
//                ->indexBy('id')->all();
//
//            foreach ($products as $model) {
//                $model->load($requestParams['Product'][$model->id], '');
//                $model->save(false);
//            }
//        }

        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($requestParams);

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

        $product = new Product();

        $this->performAjaxValidation($product);

        if ($product->load(Yii::$app->request->post()) && $product->save() && is_null(Yii::$app->request->post('update'))) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        return $this->render(is_null(Yii::$app->request->post('update')) ? 'create' : 'update', [
            'model' => $product,
            'categories' => ProductCategory::find()->all(),
            'categoriesConfig' => ProductCategoryConfig::find()->all(),
            'prices' => [],
            'pricesError' => false,
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

        $notPricesError = true;

        $product = $this->findModel($id);
        $prices = $product->pricesIndexID;

        $this->performAjaxValidationMultiple($product, $prices, ProductPrice::class);

        if (
            ProductPrice::loadMultiple($prices, Yii::$app->request->post()) &&
            $notPricesError = ProductPrice::validateMultiple($prices)
        ) {
            foreach ($prices as $price)
                $price->save();
        }

        if (
            $notPricesError &&
            $product->load(Yii::$app->request->post()) &&
            $product->save() &&
            is_null(Yii::$app->request->post('update'))
        ) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        return $this->render('update', [
            'model' => $product,
            'prices' => $prices,
            'categories' => ProductCategory::find()->all(),
            'categoriesConfig' => ProductCategoryConfig::find()->all(),
            'pricesError' => !$notPricesError
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
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');

    }

    public function actionSaveImages() {

        $saveStatus = true;

        $path = '../../storage/web/source/2f/';

        $json = json_decode(file_get_contents("{$path}import.json", "r"));

        foreach($json as $row) {
            $images = explode(',', $row->{'PICT'});
            $firstImg = array_shift($images);
            $p = Product::findOne(['article' => $row->{'CODE'}]);

            if ( $firstImg && $p && empty($p->thumbnail_path) ) {
                $arr = explode('/', $firstImg);
                $p->thumbnail_path = '\2f\\'.array_pop($arr);
                $p->save(false);
            }

            if (empty($p->attachments) && !empty($images)) {
                foreach ($images as $image) {
                    $arr = explode('/',$image);
                    $a = new ProductAttachment();
                    $a->product_id = $p->id;
                    $a->path = '\2f\\'.array_pop($arr);
                    $a->save(false);
                }
            }
        }

        if ($saveStatus) {
            Yii::$app->getSession()->setFlash('alert', ['options' => ['class' => 'alert-success'],
                'body' => "Изображения загрузились успешно!"]);
        } else {
            Yii::$app->getSession()->setFlash('alert', ['options' => ['class' => 'alert-danger'],
                'body' => "Изображения не загрузились!"]);
        }

        return $this->redirect(['index']);
    }
}
