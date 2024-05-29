<?php

namespace api\modules\v1\controllers;

use api\errors\ErrorMsg;
use common\models\Product;
use common\models\ProductPrice;
use common\models\UserClientOrder;
use common\models\UserClientOrder_Product;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\HttpException;

class CartController extends ActiveController
{
    const PRICE_TEMPLATE_DISCOUNT_5_ID = 6;

    public $modelClass = 'common\models\UserClientOrder_Product';

    public $order = null;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                //HttpBasicAuth::class,
                HttpBearerAuth::class,
                //HttpHeaderAuth::class,
                //QueryParamAuth::class
            ]
        ];

        return $behaviors;
    }

    /**
     * Declare actions supported by APIs
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);

        return $actions;
    }

    /**
     * Declare methods supported by APIs
     */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH', 'POST'],
            //'delete' => ['DELETE'],
            //'view' => ['GET'],
            'index' => ['GET'],
        ];
    }

    public function actionIndex($id)
    {
        return $this->cartInfo($id);
    }

    protected function cartInfo($id)
    {
        if (!$id || (int) $id < 1) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $profile = $identity->getProfile();
            if (!$profile) {
                throw new HttpException(404);
            }

            $order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.id' => $id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$order) {
                throw new HttpException(404);
            }

            $data['id'] = $order->id;
            $data['user_id'] = $profile->client_ms_id;

            $positions = UserClientOrder_Product::find()
                ->where([UserClientOrder_Product::tableName() . '.order_id' => $order->id])
                ->all();

            if (!empty($positions)) {
                //$data['positions'] = $positions;
                foreach ($positions as $key => $position) {
                    $data['positions'][$key]['id'] = $position->id;
                    //$data['positions'][$key]['product_ms_id'] = $position->product_id;
                    $data['positions'][$key]['product_id'] = $position->product ? $position->product->ms_id : null;
                    $data['positions'][$key]['count'] = $position->count;
                    $data['positions'][$key]['price'] = $position->price;
                    $data['positions'][$key]['percent_discount'] = $position->crm_percent_discount;
                }
            }

            $data['sum_buy'] = $order->sum_buy ?: 0;
            $data['sum_discount'] = $order->sum_discount ?: 0;
            $data['sum_total'] = $order->sum_buy - $order->sum_discount;
        } else {
            throw new HttpException(404);
        }

        return $data;
    }

    public function actionCreate()
    {
        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $user_ms_id = $identity->profile ? $identity->profile->client_ms_id : null;

            if (!$user_ms_id) {
                throw new HttpException(404);
            }
        } else {
            throw new HttpException(404);
        }

        $positions = \Yii::$app->request->post('positions');
        $arPosition = $this->findProductIds($positions);
        $sum_buy = 0;
        $sum_discount = 0;
        foreach ($arPosition as $position) {
            $sum_buy += $position['count'] * $position['price'];
            $sum_discount += (($position['count'] * $position['price']) * $position['percent_discount']) / 100; // сумма скидки на позицию
        }

        $this->order = new UserClientOrder();
        $this->order->user_id = $identity->id;
        $this->order->isCart = 0;
        $this->order->date = 0;
        $this->order->sum_buy = $sum_buy;
        $this->order->sum_discount = round($sum_discount);

        if ($this->order->save()) {
            $this->setProducts($arPosition, true);
        } else {
            //echo "<pre>";print_r($this->order->errors);echo "</pre>";die();
            return ErrorMsg::customErrorMsg(400, "Не удалось создать корзину для пользователя {$user_ms_id}");
        }

        return $this->cartInfo($this->order->id);

        //echo '<pre>';print_r($positions);echo '</pre>';die();
    }

    public function actionUpdate($id)
    {
        if (intval($id) < 1) {
            return ErrorMsg::customErrorMsg(400, "Некорректный идентификатор корзины");
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $user_ms_id = $identity->profile ? $identity->profile->client_ms_id : null;

            if (!$user_ms_id) {
                throw new HttpException(404);
            }
        } else {
            throw new HttpException(404);
        }

        $this->order = UserClientOrder::find()
            ->andWhere([UserClientOrder::tableName().'.id' => $id])
            ->andWhere([UserClientOrder::tableName().'.user_id' => (int)$identity->id])
            ->one();

        if (!$this->order) {
            throw new HttpException(404);
        } elseif ($this->order->order_ms_id) {
            return ErrorMsg::customErrorMsg(400, "Корзина принадлежит уже сформированному заказу");
        }

        $positions = \Yii::$app->request->post('positions');
        $arPosition = $this->findProductIds($positions);
        $sum_buy = 0;
        $sum_discount = 0;
        foreach ($arPosition as $position) {
            $sum_buy += $position['count'] * $position['price'];
            $sum_discount += (($position['count'] * $position['price']) * $position['percent_discount']) / 100; // сумма скидки на позицию
        }

        $this->order->date = time();
        $this->order->sum_buy = $sum_buy;
        $this->order->sum_discount = round($sum_discount);

        if ($this->order->save()) {
            $this->setProducts($arPosition);
        } else {
            return ErrorMsg::customErrorMsg(400, "Не удалось обновить корзину для пользователя {$user_ms_id}");
        }

        return $this->cartInfo($this->order->id);
    }

    protected function findProductIds($positions)
    {
        if (is_array($positions) && !empty($positions)) {
            $ms_ids = array();
            foreach ($positions as $position) {
                if (isset($position['product_id']) && $position['product_id']) {
                    $ms_ids[] = $position['product_id'];
                }
            }

            if (count($ms_ids) && count($positions) === count($ms_ids)) {
                $arProduct = Product::find()
                    ->where(['in', Product::tableName().'.ms_id', $ms_ids])
                    ->all();

                $arExists = array();
                foreach ($arProduct as $product) {
                    $arExists[$product->ms_id] = $product->id;
                }

                $arPosition = array();
                foreach ($positions as $position) {
                    if (in_array($position['product_id'], array_keys($arExists))) {
                        $product_id = $arExists[$position['product_id']];
                        $arPosition[$product_id]['id'] = $product_id;
                        //$arPosition[$product_id]['ms_id'] = $position['product_id'];

                        if (isset($position['count']) && intval($position['count']) > 0) {
                            $arPosition[$product_id]['count'] = intval($position['count']);
                        } else {
                            throw new HttpException(400, "Указано некорректное количество товара с идентификатором {$position['product_id']}");
                        }

                        if (isset($position['price']) && intval($position['price']) > 0) {
                            $min_price = ProductPrice::find()
                                ->select('price')
                                ->andWhere(['template_id' => self::PRICE_TEMPLATE_DISCOUNT_5_ID])
                                ->andWhere(['product_id' => $arPosition[$product_id]['id']])
                                ->one();
                            if (isset($min_price->price) && $min_price->price <= intval($position['price'])) {
                                $arPosition[$product_id]['price'] = intval($position['price']);
                            } else {
                                $min_str = ($min_price->price / 100) . ' руб.';
                                throw new HttpException(400, "Указана слишком низкая цена товара с идентификатором {$position['product_id']}. Мин. цена этого товара {$min_str}");
                            }

                            $arPosition[$product_id]['percent_discount'] = isset($position['percent_discount']) ? floatval($position['percent_discount']) : 0;
                        } else {
                            throw new HttpException(400, "Указана некорректная цена товара с идентификатором {$position['product_id']}");
                        }

//                        if (isset($position['discount']) && intval($position['discount']) > 0) {
//                            $arPosition[$product_id]['discount'] = intval($position['discount']);
//                        }
                    } else {
                        throw new HttpException(400, "Идентификатор товара {$position['product_id']} не найден");
                    }
                }

                return $arPosition;
            } else {
                throw new HttpException(400, 'Некорректные идентификаторы товаров');
            }
        } else {
            throw new HttpException(400, 'Отсутствуют позиции товаров корзины');
        }
    }

    public function setProducts($value, $create = false)
    {
        if ($create) {
            $curIds = [];
        } else {
            $curIds = ArrayHelper::getColumn($this->order->getProducts()->select(['id'])->asArray()->all(), 'id');
        }

        $newIds = array_diff(array_keys((array)$value), (array)$curIds);
        if ($create) {
            $delIds = [];
        } else {
            $delIds = array_diff((array)$curIds, array_keys((array)$value));
        }
        $updIds = array_diff(array_diff((array)$curIds, $newIds), $delIds);

        $new = Product::find()->where(['in', Product::tableName() . '.id', $newIds])->all();
        $del = Product::find()->where(['in', 'id', $delIds])->all();
        $update = Product::find()->where(['in', Product::tableName() . '.id', $updIds])->all();

        foreach ($new as $cf) {
            if (!empty($value[$cf->id])) {
                $this->order->link('products', $cf, [
                    'count' => (int)$value[(int)$cf->id]['count'],
                    'price' => (int)$value[(int)$cf->id]['price'],
                    'currency_iso_code' => '643',
                    'crm_percent_discount' => floatval($value[(int)$cf->id]['percent_discount']),
                ]);
            }
        }

        if (!$create) {
            foreach ($del as $cf) {
                $this->order->unlink('products', $cf, true);
            }
        }

        foreach ($update as $cf) {
            $u = UserClientOrder_Product::findOne(['order_id' => $this->order->id, 'product_id' => $cf->id]);
            if ($u && !empty($value[$cf->id])) {
                $u->count = $create ? $u->count + (int)$value[$cf->id]['count'] : (int)$value[$cf->id]['count'];
                $u->price = (int)$value[(int)$cf->id]['price'];
                $u->crm_percent_discount = floatval($value[(int)$cf->id]['percent_discount']);
                $u->save(false);
            }
        }
    }
}
