<?php

namespace api\modules\v1\controllers;

use api\errors\ErrorMsg;
use common\models\DeliveryCity;
use common\models\DeliveryTypes;
use common\models\PaymentTypes;
use common\models\SdekPvz;
use common\models\TariffSdek;
use common\models\UserClientOrder;
use frontend\modules\lk\models\Delivery;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\HttpException;

class DeliveryController extends ActiveController
{
    public $modelClass = 'common\models\DeliveryTypes';

    public $strict = 1;
    public $maxLimit = 100;
    public $defaultLimit = 25;

    public $cart_id = 0;
    public $city_id = 0;
    public $order = null;
    public $profile = null;

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
            //'create' => ['POST'],
            //'update' => ['PUT', 'PATCH', 'POST'],
            //'delete' => ['DELETE'],
            //'view' => ['GET'],
            //'index' => ['GET'],
            'get-tariffs' => ['POST'],
            'set-tariff' => ['POST'],
        ];
    }

    public function actionGetTariffs()
    {
        $data = $this->tariffs();

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Нет доставки в этот населенный пункт");
        }

        return $data;
    }

    public function actionSetTariff()
    {
        $tariffs = $this->tariffs();

        if (isset($this->order->order_ms_id) && !is_null($this->order->order_ms_id)) {
            return ErrorMsg::customErrorMsg(400, "Изменение тарифа для сформированного заказа невозможно");
        }

        if (!count($tariffs)) {
            return ErrorMsg::customErrorMsg(400, "Нет доставки в этот населенный пункт");
        }

        $delivery_type_id = \Yii::$app->request->post('delivery_type_id', 0);
        $tariff_id = \Yii::$app->request->post('tariff_id', 0);
        $address = \Yii::$app->request->post('address', '');
        $phone = \Yii::$app->request->post('phone', '');
        $pvz_code = \Yii::$app->request->post('pvz_code', '');
        $comment = \Yii::$app->request->post('comment', '');
        $delivery_type = array();
        $tariff = array();
        $pvz = null;
        $pvz_info = array();

        if (!$delivery_type_id || (int) $delivery_type_id < Delivery::CDEK_SD_DELIVERY_TYPE || $delivery_type_id > Delivery::OUR_COURIER) {
            throw new HttpException(404);
        }

        if ($delivery_type_id != Delivery::PICKUP_FROM_WAREHOUSE) {
            if (!$tariff_id) {
                throw new HttpException(404);
            }
        }

        $courier_native_city_id = \Yii::$app->keyStorageApp->get('courier_native_city_id');
        if ($this->city_id == $courier_native_city_id && $delivery_type_id == Delivery::CDEK_SD_DELIVERY_TYPE) {
            return ErrorMsg::customErrorMsg(400, "Указан некорректный тип доставки");
        } elseif ($this->city_id != $courier_native_city_id && ($delivery_type_id == Delivery::OUR_COURIER || $delivery_type_id == Delivery::PICKUP_FROM_WAREHOUSE)) {
            return ErrorMsg::customErrorMsg(400, "Указан некорректный тип доставки");
        }

        if (empty($phone)) {
            return ErrorMsg::customErrorMsg(400, "Не указан контактный телефон");
        }

        foreach ($tariffs['delivery_types'] as $type) {
            if ($type['id'] == $delivery_type_id) {
                $delivery_type = $type;
                if ($delivery_type_id != Delivery::PICKUP_FROM_WAREHOUSE && is_array($type['tariffs'])) {
                    foreach ($type['tariffs'] as $item) {
                        if ($item['id'] == $tariff_id) {
                            $tariff = $item;
                        }
                    }

                    if (empty($tariff)) {
                        return ErrorMsg::customErrorMsg(400, "Указан некорректный тариф");
                    }
                }
            }
        }

        if ($delivery_type_id == Delivery::CDEK_SD_DELIVERY_TYPE || $delivery_type_id == Delivery::OUR_COURIER) {
            if (empty($address)) {
                return ErrorMsg::customErrorMsg(400, "Не указан адрес доставки");
            }
        } elseif ($delivery_type_id == Delivery::CDEK_SS_DELIVERY_TYPE) {
            if (empty($pvz_code)) {
                return ErrorMsg::customErrorMsg(400, "Не указан код ПВЗ");
            }

            $pvz = SdekPvz::find()->where(['pvz_code' => $pvz_code])->one();

            if (!$pvz || $pvz->sdek_id != $this->city_id) {
                return ErrorMsg::customErrorMsg(400, "Некорректный код ПВЗ");
            }

            $arr = unserialize($pvz->xml);
            if (isset($arr['@attributes'])) {
                $pvz_info = $arr['@attributes'];
            }
        }

        // Заказ
        if ($delivery_type_id == Delivery::CDEK_SD_DELIVERY_TYPE || $delivery_type_id == Delivery::CDEK_SS_DELIVERY_TYPE) {
            $this->order->delivery_service_id = 1;
        } elseif ($delivery_type_id == Delivery::PICKUP_FROM_WAREHOUSE) {
            $this->order->delivery_service_id = 3;
        } elseif ($delivery_type_id == Delivery::OUR_COURIER) {
            $this->order->delivery_service_id = 4;
        }

        if ($delivery_type_id != Delivery::OUR_COURIER) {
            // Перепутаны ID типов доставки для СДЭК в БД и коде
            switch ($delivery_type_id) {
                case 1:
                    $this->order->delivery_type = 2;
                    break;
                case 2:
                    $this->order->delivery_type = 1;
                    break;
                default:
                    $this->order->delivery_type = (int) $delivery_type_id;
            }
        } else {
            $this->order->delivery_type = null;
        }

        if ($delivery_type_id == Delivery::CDEK_SD_DELIVERY_TYPE || $delivery_type_id == Delivery::CDEK_SS_DELIVERY_TYPE) {
            $tariff_sdek = TariffSdek::find()
                ->where(['sdek_id' => $tariff_id])
                ->one();

            $this->order->tariff_sdek = isset($tariff_sdek->id) ? $tariff_sdek->id : null;
        } else {
            $this->order->tariff_sdek = null;
        }

        $city = DeliveryCity::find()
            ->where(['sdek_id' => $this->city_id])
            ->one();

        if ($city) {
            $this->order->delivery_city = $city->city_full;
        } else {
            $this->order->delivery_city = null;
        }

        $this->order->address_delivery = $address ?: null;
        $this->order->phone = $phone ?: null;
        $this->order->sum_delivery = isset($tariff['price']) && $tariff['price'] > 0 ? $tariff['price'] : 0;
        $this->order->pvz_code = isset($pvz->pvz_code) ? $pvz->pvz_code : null;

        $info = '';
        if (isset($pvz_info['Phone'])) {
            $info = 'Тел.: ' . $pvz_info['Phone'];
        }

        if (isset($pvz_info['WorkTime'])) {
            if (!empty($info)) {
                $info .= ', Режим работы: ' . $pvz_info['WorkTime'];
            }
        }

        $this->order->pvz_info = $info ?: null;
        $this->order->address_delivery_pvz = isset($pvz_info['Address']) && !empty($pvz_info['Address']) ? $pvz_info['RegionName'] . ', ' . $pvz_info['Address'] : null;
        $this->order->comment = $comment;

        if ($delivery_type_id == Delivery::OUR_COURIER) {
            $this->order->courier_time_interval = $tariff_id;
        } else {
            $this->order->courier_time_interval = null;
        }

        if ($delivery_type_id != Delivery::PICKUP_FROM_WAREHOUSE) {
            $this->order->delivery_days = isset($tariff['period_max']) && $tariff['period_max'] > 0 ? $tariff['period_max'] : 0;
        } else {
            $this->order->delivery_days = 0;
        }

        $data = array();
        if ($this->order->save()) {
            $data['cart_id'] = $this->cart_id;
            $data['city_id'] = $this->city_id;
            $data['city_name'] = $this->order->delivery_city;

            if (count($delivery_type)) {
                $data['delivery_type']['id'] = $delivery_type['id'];
                $data['delivery_type']['name'] = $delivery_type['name'];
            } else {
                $data['delivery_type'] = null;
            }

            if (count($tariff)) {
                $data['tariff'] = $tariff;
            } else {
                $data['tariff'] = null;
            }

            $data['address'] = $this->order->address_delivery;
            $data['phone'] = $this->order->phone;

            if ($pvz) {
                $data['pvz']['code'] = $this->order->pvz_code;
                if (count($pvz_info)) {
                    $data['pvz']['address'] = $this->order->address_delivery_pvz;
                    $data['pvz']['info'] = $this->order->pvz_info;
                }
            } else {
                $data['pvz'] = null;
            }

            $data['comment'] = $this->order->comment;
            $data['sum_delivery'] = $this->order->sum_delivery;

            $payment_types = PaymentTypes::find()->all();

            foreach ($payment_types as $type) {
                if (4 == $type->id) { // не показывать способ оплаты "перевод с карты на карту"
                    continue;
                }

                if ((1 == $type->id && $this->profile->hide_cash) || (2 == $type->id && $this->profile->hide_card)) {
                    continue;
                }

                $data['payments_type'][] = array(
                    'id' => $type->id,
                    'name' => $type->title,
                );
            }
        }

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Не удалось обработать данные о способе доставки");
        }

        return $data;
    }

    protected function tariffs()
    {
        $this->cart_id = \Yii::$app->request->post('cart_id', 0);

        if (!$this->cart_id || (int) $this->cart_id < 1) {
            throw new HttpException(404);
        }

        $this->city_id = \Yii::$app->request->post('city_sdek_id', 0);

        if (!$this->city_id || (int) $this->city_id < 1) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.id' => $this->cart_id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

            $data = array();
            $delivery = new Delivery();

            // Самовывоз СДЭК
            $result = $delivery->getSdekSumAndPeriod($this->city_id, Delivery::CDEK_SS_DELIVERY_TYPE, $this->cart_id);
            if (isset($result['tariffs']) && is_array($result['tariffs'])) {
                $pvz = array(
                    'id' => Delivery::CDEK_SS_DELIVERY_TYPE,
                    'name' => 'Самовывоз из пунктов выдачи СДЭК',
                    'desc' => 'Из партнерских пунктов выдачи и постматов',
                );

                foreach ($result['tariffs'] as $tariff) {
                    $pvz['tariffs'][] = array(
                        'id' => $tariff['tariffId'],
                        'name' => $tariff['tariffName'],
                        'price' => $tariff['priceByCurrency'] * 100,
                        'period_min' => $tariff['deliveryPeriodMin'],
                        'period_max' => $tariff['deliveryPeriodMax'],
                    );
                }

                if (isset($pvz['tariffs'])) {
                    $data['delivery_types'][] = $pvz;
                }
            }

            if ($this->city_id != \Yii::$app->keyStorageApp->get('courier_native_city_id')) {
                // Курьер СДЭК
                $result = $delivery->getSdekSumAndPeriod($this->city_id, Delivery::CDEK_SD_DELIVERY_TYPE, $this->cart_id);
                if (isset($result['tariffs']) && is_array($result['tariffs'])) {
                    $courier = array(
                        'id' => Delivery::CDEK_SD_DELIVERY_TYPE,
                        'name' => 'Курьер СДЭК',
                        'desc' => 'Доставит до Ваших дверей',
                    );

                    foreach ($result['tariffs'] as $tariff) {
                        $courier['tariffs'][] = array(
                            'id' => $tariff['tariffId'],
                            'name' => $tariff['tariffName'],
                            'price' => $tariff['priceByCurrency'] * 100,
                            'period_min' => $tariff['deliveryPeriodMin'],
                            'period_max' => $tariff['deliveryPeriodMax'],
                        );
                    }

                    if (isset($courier['tariffs'])) {
                        $data['delivery_types'][] = $courier;
                    }
                }
            } else {
                // Наш курьер
                $courier = array(
                    'id' => Delivery::OUR_COURIER,
                    'name' => 'Наш курьер',
                    'desc' => 'Доставит до Ваших дверей',
                );

                $hide_hours = (int)Yii::$app->keyStorageApp->get('courier_hours_hide_today');
                if (intval(date("H")) < $hide_hours) { // после 15:00 интервал становится недоступен
                    $timeInterval = 'today_' . Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_3');
                    $courier['tariffs'][] = array(
                        'id' => $timeInterval,
                        'name' => 'Сегодня срочно ' . Yii::$app->keyStorageApp->get('courier_time_interval_3'),
                        'price' => $delivery->getCourierClientPrice($timeInterval, $this->cart_id),
                        'period_min' => 0,
                        'period_max' => 0,
                    );
                }

                $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_1') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_1');
                $courier['tariffs'][] = array(
                    'id' => $timeInterval,
                    'name' => 'Завтра ' . Yii::$app->keyStorageApp->get('courier_time_interval_1'),
                    'price' => $delivery->getCourierClientPrice($timeInterval, $this->cart_id),
                    'period_min' => 0,
                    'period_max' => 1,
                );

                $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_2') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_2');
                $courier['tariffs'][] = array(
                    'id' => $timeInterval,
                    'name' => 'Завтра ' . Yii::$app->keyStorageApp->get('courier_time_interval_2'),
                    'price' => $delivery->getCourierClientPrice($timeInterval, $this->cart_id),
                    'period_min' => 0,
                    'period_max' => 1,
                );

                $timeInterval = 'tomorrow_' . Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . Yii::$app->keyStorageApp->get('courier_to_time_id_3');
                $courier['tariffs'][] = array(
                    'id' => $timeInterval,
                    'name' => 'Завтра ' . Yii::$app->keyStorageApp->get('courier_time_interval_3'),
                    'price' => $delivery->getCourierClientPrice($timeInterval, $this->cart_id),
                    'period_min' => 0,
                    'period_max' => 1,
                );

                $data['delivery_types'][] = $courier;

                // Самовывоз со склада
                $data['delivery_types'][] = array(
                    'id' => Delivery::PICKUP_FROM_WAREHOUSE,
                    'name' => 'Самовывоз со склада',
                    'desc' => "Самовывоз из магазина TATTOOFEEL.RU\nЗабор возможен завтра, после 12:00\nЭлектродная ул., дом 2, стр. 33\nв тату-студии Barn house tattoo\nВремя работы: 11:00 - 19:00",
                );
            }
            
            return $data;
        } else {
            throw new HttpException(404);
        }
    }
}
