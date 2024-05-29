<?php

namespace frontend\modules\api\controllers;

use common\models\Coupons;
use common\models\DeliveryCity;
use common\models\PaymentTypes;
use common\models\UserClientOrder;
use frontend\modules\lk\models\Delivery;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;

class CartController extends _Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
            ]
        ];

        return $behaviors;
    }

    /**
     * @SWG\Get(
     *     path="/cart/checkout-init/",
     *     tags={"Корзина"},
     *     summary="Получить данные заказа, варианты доставки и оплаты.",
     *     @SWG\Response(
     *         response=200,
     *         description="Общий JSON ответ с кучей полей.",
     *         @SWG\Schema(ref="#/definitions/ResponseWithData"),
     *     )
     * )
     */
    public function actionCheckoutInit()
    {
        if (!$cart = \Yii::$app->client->identity->getCart()) {
            throw new NotFoundHttpException('Заказ не найден');
        }

        if (isset($cart->order_ms_id)) {
            $cart->order_ms_id = null;
            $cart->date = 'now'; // omg it actually has filter applying strtotime to this sh*t
            $cart->save();
        }

        $delivery = new Delivery();
        $delivery_city = null;
        $delivery_city_id = null;
        $city_codes = [];

        // get client order having delivery city.
        $order = UserClientOrder::find()
            ->where(['not', ['delivery_city' => null]])
            ->andWhere(['user_id' => \Yii::$app->client->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($order) {
            // find city record by name.
            $deliveryCity = DeliveryCity::find()
                ->where(['city_full' => $order->delivery_city])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if ($deliveryCity) {
                // use sdek city id.
                $delivery_city = $order->delivery_city;
                $delivery_city_id = $deliveryCity->sdek_id;
                $cdek_city_id = [
                    'status' => true,
                    'data' => $delivery_city_id
                ];
            } else { // get sdek city id.
                $cdek_city_id = $delivery->getCdekCityCodeByCityName($order->delivery_city);
            }

            $city_codes = [
                Delivery::DELIVERY_CDEK => $cdek_city_id,
            ];
        }

        return $this->success(true, null, [
            'order' => $cart, // todo hide unused fields
            'coupon' => Coupons::findOne($cart->coupon_id), // todo hide unused fields
            'info_message' => UserClientOrder::getCartInfoMessage($cart),
            'delivery_city' => $delivery_city,
            'delivery_city_id' => $delivery_city_id,
            'city_codes' => $city_codes, // todo is used ???
            // todo separate call ???
            'delivery_address' => \Yii::$app->client->identity->profile->address_delivery,
            'delivery_services' => $delivery->getDeliveryServicesName(), // getDSList() ???
            'payment_types' => PaymentTypes::find()->where(['active' => true])->all(),
        ]);
    }

    public function actionCheckoutProcess()
    {

    }
}
