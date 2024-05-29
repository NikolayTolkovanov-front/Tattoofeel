<?php

namespace backend\modules\catalog\controllers;

use common\models\ProductGift;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use backend\modules\catalog\models\search\CouponsSearch;
use common\models\Brand;
use common\models\Coupons;
use common\models\KeyStorageItem;
use common\models\Product;
use common\models\ProductCategory;
use common\traits\FormAjaxValidationTrait;

class CouponsController extends Controller
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
            $key = "backend.history.coupons.$sp.$user->username";
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

        $searchModel = new CouponsSearch();
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

        $coupon = new Coupons();

        $this->performAjaxValidation($coupon);

        if ($coupon->load(Yii::$app->request->post()) && $coupon->save() && is_null(Yii::$app->request->post('update'))) {
            $previousUrl = Url::previous('index');
            return $this->redirect($previousUrl ? $previousUrl : ['index']);
        }

        return $this->render(is_null(Yii::$app->request->post('update')) ? 'create' : 'update', [
            'model' => $coupon,
            'brands' => [],
            'categories' => [],
            'client_groups' => [],
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
        if (is_null(Yii::$app->request->post('update')) && is_null(Yii::$app->request->post('save'))) {
            Url::remember(Yii::$app->request->referrer, 'index');
        }

        $coupon = $this->findModel($id);

        $this->performAjaxValidation($coupon);
        if ($coupon->load(Yii::$app->request->post()) && $coupon->save()) {
            $brandIds = Yii::$app->request->post('Brand')['id'];
            if (is_array($brandIds)) {
                $brands = array();
                foreach ($brandIds as $id => $val) {
                    if ($val) {
                        $brands[] = $id;
                    }
                }

                $links = $coupon->getBrands()->indexBy('id')->column();
                $create = array_diff($brands, $links);
                $delete = array_diff($links, $brands);
                //echo "<pre>";print_r($create);echo "</pre>";die('create');

                foreach ($create as $id) {
                    $brand = Brand::findOne($id);
                    if ($brand) {
                        $coupon->link('brands', $brand);
                    }
                }

                foreach ($delete as $id) {
                    $brand = Brand::findOne($id);
                    if ($brand) {
                        $coupon->unlink('brands', $brand, true);
                    }
                }
            }

            $productGifts = Yii::$app->request->post('gifts');
            ProductGift::deleteAll(['coupon_id' => $coupon->id]);
            if ($productGifts) {
                $productGifts = explode(PHP_EOL, $productGifts);
                foreach ($productGifts as $productGift) {
                    if (!empty($productGift)) {
                        $productGift = explode(':', $productGift);
                        if (isset($productGift[1]) && !empty($productGift[1])) {
                            $qty = (int)$productGift[1];
                        } else {
                            $qty = 1;
                        }
                        $productId = $productGift[0];
                        $product = Product::findOne([
                            'id' => $productId
                        ]);
                        if ($product) {
                            \Yii::$app->db->createCommand()
                                ->insert(ProductGift::tableName(), [
                                    'product_id' => $product->id,
                                    'coupon_id' => $coupon->id,
                                    'quantity' => $qty,
                                ])->execute();
                        }
                    }
                }
            }

            $categoryIds = Yii::$app->request->post('ProductCategory')['id'];
            if (is_array($categoryIds)) {
                $categories = array();
                foreach ($categoryIds as $id => $val) {
                    if ($val) {
                        $categories[] = $id;
                    }
                }

                $links = $coupon->getCategories()->indexBy('id')->column();
                $create = array_diff($categories, $links);
                $delete = array_diff($links, $categories);

                foreach ($create as $id) {
                    $category = ProductCategory::findOne($id);
                    if ($category) {
                        $coupon->link('categories', $category);
                    }
                }

                foreach ($delete as $id) {
                    $category = ProductCategory::findOne($id);
                    if ($category) {
                        $coupon->unlink('categories', $category, true);
                    }
                }
            }

            $productIds = isset(Yii::$app->request->post('Coupons')['products']) ? explode(',', Yii::$app->request->post('Coupons')['products']) : array();
            $products = array();
            foreach ($productIds as $key => $id) {
                $id = trim(intval($id));
                if ($id > 0 && !in_array($id, $products)) {
                    $products[] = $id;
                }
            }

            $links = $coupon->getProducts()->indexBy('id')->column();
            $create = array_diff($products, $links);
            $delete = array_diff($links, $products);
            //echo "<pre>";print_r($create);echo "</pre>";die('create');

            foreach ($create as $id) {
                $product = Product::findOne($id);
                if ($product) {
                    $coupon->link('products', $product);
                }
            }

            foreach ($delete as $id) {
                $product = Product::findOne($id);
                if ($product) {
                    $coupon->unlink('products', $product, true);
                }
            }

            if (is_null(Yii::$app->request->post('update'))) {
                $previousUrl = Url::previous('index');
                return $this->redirect($previousUrl ? $previousUrl : ['index']);
            }
        }

        return $this->render('update', [
            'model' => $coupon,
            'brands' => Brand::find()
                ->andWhere([Brand::tableName().'.status' => 1])
                ->orderBy([Brand::tableName() . '.title' => SORT_ASC])
                ->all(),
            'categories' => ProductCategory::find()
                ->andWhere([ProductCategory::tableName().'.status' => 1])
                ->orderBy([ProductCategory::tableName() . '.title' => SORT_ASC])
                ->all(),
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
     * @return Coupons the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Coupons::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');

    }
}
