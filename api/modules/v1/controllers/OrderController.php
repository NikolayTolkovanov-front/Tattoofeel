<?php

namespace api\modules\v1\controllers;

use api\errors\ErrorMsg;
use common\models\OrderStatuses;
use common\models\PaymentTypes;
use common\models\Product;
use common\models\TariffSdek;
use common\models\UserClientOrder;
use common\models\UserClientOrder_Product;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\rest\ActiveController;
use yii\web\HttpException;

class OrderController extends ActiveController
{
    const PAYMENT_TYPE_CARD = 2;

    const REDIRECT_SUCCESS_PAY_URL = 'https://studio.tattoo-manager.com/payment/successful';
    const REDIRECT_FAIL_PAY_URL = 'https://studio.tattoo-manager.com/payment/failed';

    public $modelClass = 'common\models\UserClientOrder';

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
            'create' => ['POST'],
            'get-pay-link' => ['POST'],
            'get-payment-types' => ['GET'],
            'update-payment-type' => ['PUT', 'PATCH', 'POST'],
            //'delete' => ['DELETE'],
            'index' => ['GET'],
        ];
    }

    public function actionIndex($id)
    {
        if (empty($id)) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.order_ms_id' => $id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

            $data = $this->orderInfo();
        } else {
            throw new HttpException(404);
        }
        
        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Возникла непредвиденная ошибка при получении информации о заказе");
        }

        return $data;
    }

    public function actionGetPaymentTypes($id)
    {
        $data = array();

        if (!$id) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.order_ms_id' => $id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

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
        } else {
            throw new HttpException(404);
        }

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Возникла непредвиденная ошибка при получении доступных способов оплаты");
        }

        return $data;
    }

    public function actionGetPayLink()
    {
        $data = array();

        $order_id = \Yii::$app->request->post('order_id', '');
        if (empty($order_id)) {
            throw new HttpException(404);
        }

        $success_url = \Yii::$app->request->post('success_url', self::REDIRECT_SUCCESS_PAY_URL);
        $fail_url = \Yii::$app->request->post('fail_url', self::REDIRECT_FAIL_PAY_URL);

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.order_ms_id' => $order_id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

            if (isset($this->order->status_pay) && $this->order->status_pay) {
                return ErrorMsg::customErrorMsg(400, "Заказ уже оплачен");
            }

            if (isset($this->order->delivery_service_id) && !$this->order->delivery_service_id) {
                return ErrorMsg::customErrorMsg(400, "Не сформирован способ доставки");
            }

            if ($this->order->delivery_service_id == 1 && !$this->order->delivery_type) {
                return ErrorMsg::customErrorMsg(400, "Не установлен тип доставки для СДЭК");
            }

            if (isset($this->order->payment_type) && $this->order->payment_type !== self::PAYMENT_TYPE_CARD) {
                return ErrorMsg::customErrorMsg(400, "Платежная ссылка не доступна для установленного у заказа способа оплаты");
            }

            $params = array(
                "TerminalKey" => env("TINKOFF_TERMINAL_KEY"),
                "Amount" => (int)$this->order->sum_buy + (int)$this->order->sum_delivery + (int)$this->order->commissionSum - (int)$this->order->sum_discount - (int)$this->order->sum_delivery_discount,
                "OrderId" => $this->order->id,
                "PayType" => 'O',
            );

            if (!empty($success_url)) {
                $params['SuccessURL'] = $success_url;
            }

            if (!empty($fail_url)) {
                $params['FailURL'] = $fail_url;
            }

            $result = $this->tinkoff_send('Init', $params, 'POST');
            //echo '<pre>';print_r($result);echo '</pre>';die();
            if ($result->status) {
                if (isset($result->responseContent['Success']) && $result->responseContent['Success']) {
                    $data = array(
                        "Success" => $result->responseContent['Success'],
                        "ErrorCode" => $result->responseContent['ErrorCode'],
                        "Status" => $result->responseContent['Status'],
                        "PaymentId" => $result->responseContent['PaymentId'],
                        "OrderId" => $this->order->order_ms_id,
                        "Amount" => $result->responseContent['Amount'],
                        "PaymentURL" => $result->responseContent['PaymentURL'],
                    );
                }
            } else {
                return ErrorMsg::customErrorMsg(400, $result->msg);
            }
        } else {
            throw new HttpException(404);
        }

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Возникла непредвиденная ошибка при формировании платежной ссылки");
        }

        return $data;
    }

    public function actionCreate()
    {
        $data = array();

        $cart_id = \Yii::$app->request->post('cart_id', 0);
        if (!$cart_id || (int) $cart_id < 1) {
            throw new HttpException(404);
        }

        $payment_type_id = \Yii::$app->request->post('payment_type_id', 0);
        $payment_type = PaymentTypes::findOne($payment_type_id);
        if (!$payment_type) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.id' => $cart_id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

            if (isset($this->order->order_ms_id) && !is_null($this->order->order_ms_id)) {
                return ErrorMsg::customErrorMsg(400, "Заказ уже сформирован");
            }

            if (isset($this->order->delivery_service_id) && !$this->order->delivery_service_id) {
                return ErrorMsg::customErrorMsg(400, "Не сформирован способ доставки");
            }

            if ($this->order->delivery_service_id == 1 && !$this->order->delivery_type) {
                return ErrorMsg::customErrorMsg(400, "Не установлен тип доставки для СДЭК");
            }

            $this->order->places_count = 1;
            foreach ($this->order->linkProducts as $product) {
                if ($product->product->is_oversized) {
                    $this->order->places_count = 2;
                    break;
                }
            }

            $this->order->payment_type = $payment_type->id;
            //$this->order->date_pay = time();

            $result = $this->msCreateOrder();
            if ($result->status) {
                $this->order->date = time();

                if ($this->order->save(false)) {
                    $data = $this->orderInfo();
                }
            } else {
                $this->order->order_ms_id = null; // если не удалось зарезервировать товары, то следует "обнулить" заказ, иначе CRM'щики ничего не смогут с ним сделать
                $this->order->save(false);

                return ErrorMsg::customErrorMsg(400, $result->msg);
            }
        } else {
            throw new HttpException(404);
        }

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Возникла непредвиденная ошибка при формировании заказа");
        }

        return $data;
    }

    public function actionUpdatePaymentType($id) {
        $data = array();

        if (!$id) {
            throw new HttpException(404);
        }

        $payment_type_id = \Yii::$app->request->post('payment_type_id', 0);
        $payment_type = PaymentTypes::findOne($payment_type_id);
        if (!$payment_type) {
            throw new HttpException(404);
        }

        $identity = \Yii::$app->user->getIdentity();
        if (isset($identity->id) && (int)$identity->id > 0) {
            $this->profile = $identity->getProfile();
            if (!$this->profile) {
                throw new HttpException(404);
            }

            $this->order = UserClientOrder::find()
                ->andWhere([UserClientOrder::tableName() . '.order_ms_id' => $id])
                ->andWhere([UserClientOrder::tableName() . '.user_id' => $identity->id])
                ->one();

            if (!$this->order) {
                throw new HttpException(404);
            }

            if (isset($this->order->status_pay) && $this->order->status_pay) {
                return ErrorMsg::customErrorMsg(400, "Заказ уже оплачен");
            }

            if (isset($this->order->delivery_service_id) && !$this->order->delivery_service_id) {
                return ErrorMsg::customErrorMsg(400, "Не сформирован способ доставки");
            }

            if ($payment_type_id == $this->order->payment_type) {
                $data = $this->orderInfo();
            } else {
                $payment_types = PaymentTypes::find()->all();

                $available = array();
                foreach ($payment_types as $type) {
                    if (4 == $type->id) { // не показывать способ оплаты "перевод с карты на карту"
                        continue;
                    }

                    if ((1 == $type->id && $this->profile->hide_cash) || (2 == $type->id && $this->profile->hide_card)) {
                        continue;
                    }

                    $available[] = $type->id;
                }

                if (!in_array($payment_type_id, $available)) {
                    return ErrorMsg::customErrorMsg(400, "Данный способ оплаты недоступен");
                }

                $this->order->payment_type = $payment_type_id;
                //$this->order->date_pay = time();

                if ($this->order->save(false)) {
                    $this->order->msChangePaymentType();
                    $data = $this->orderInfo();
                }
            }
        } else {
            throw new HttpException(404);
        }

        if (!count($data)) {
            return ErrorMsg::customErrorMsg(400, "Возникла непредвиденная ошибка при смене способа оплаты");
        }

        return $data;
    }

    protected function msCreateOrder()
    {
        if (!$this->order) {
            return null;
        }

        $result = $this->order->msStatusResponse();

        if ($this->profile->client_ms_id) {
            $agentMeta = $this->order->ms_get_meta(
                "entity/counterparty/" . $this->profile->client_ms_id,
                "counterparty"
            );
        } else {
            $result->status = false;
            $result->statusCode = -10;
            $result->msg = 'У данного пользователя отсутствует идентификатор МС';
            return $result;
        }

        $order = $this->order->ms_create_order($agentMeta);

        if ($order->status) {
            $this->order->order_ms_id = $order->responseContent->id;
            $this->order->order_ms_number = $order->responseContent->name;
            $this->order->status_ms_sync = 1;
            $this->order->status = 1; // новый заказ

            if (!$this->order->save(false)) {
                $result->status = false;
                $result->statusCode = -11;
                $result->msg = 'Не удалось сохранить заказ в БД';

                return $result;
            }
        } else {
            $result->status = false;
            $result->statusCode = -2;
            $result->msg = 'Не удалось создать заказ в системе МС';

            return $result;
        }

        $stock = $this->order->ms_get_stock($this->order->order_ms_id);
        if ($stock->status) {
            $check = $this->order->check_stock($stock->responseContent); // проверка имеется ли достаточное кол-во товаров в МС для заказа (пустой массив = ОК)

            if (empty($check)) {
                $reserve = $this->order->ms_reserve();
                if (!$reserve->status) {
                    $result->status = false;
                    $result->statusCode = -5;
                    $result->msg = 'Не удалось зарезервировать товары в системе МС';

                    return $result;
                }

                $this->order->sync_product_stock($stock->responseContent);
            } else { // есть позиции товаров в заказе, где кол-во больше, имеющегося в МС
                $result->status = false;
                $result->statusCode = -4;
                $result->msg = 'Такого количества товаров нет в наличии в системе МС';
                //if (is_array($check)) {
                //    $result->msg .= '. ID этих товаров: ' . implode(', ', array_keys($check));
                //}
                //$result->errors_qty = $check;

                return $result;
            }
        } else {
            $result->status = false;
            $result->statusCode = -3;
            $result->msg = 'Не удалось проверить количество товаров в системе МС';

            return $result;
        }

        return $result;
    }

    protected function orderInfo()
    {
        $data = array();
        if (!is_null($this->order) && !is_null($this->profile)) {
            // Основное
            $data['id'] = $this->order->order_ms_id;
            $data['number'] = $this->order->order_ms_number;
            $data['user_id'] = $this->profile->client_ms_id;

            $statuses = ArrayHelper::map(OrderStatuses::find()->asArray()->all(), 'id', 'title');

            //$data['status_id'] = $this->order->status;
            $data['status'] = $this->order->status ? ($statuses[$this->order->status] ? ($statuses[$this->order->status] != '-' ? $statuses[$this->order->status] : 'Обрабатывается') : '') : null;
            $data['date'] = date('d.m.Y H:i', $this->order->date);

            // Корзина
            $positions = UserClientOrder_Product::find()
                ->where([UserClientOrder_Product::tableName() . '.order_id' => $this->order->id])
                ->all();

            if (!empty($positions)) {
                $data['cart'] = array(
                    'id' => $this->order->id,
                );
                foreach ($positions as $key => $position) {
                    $data['cart']['positions'][$key]['id'] = $position->id;
                    $data['cart']['positions'][$key]['product_id'] = $position->product ? $position->product->ms_id : null;
                    $data['cart']['positions'][$key]['count'] = $position->count;
                    $data['cart']['positions'][$key]['price'] = $position->price;
                    $data['cart']['positions'][$key]['percent_discount'] = $position->crm_percent_discount;
                }
            }

            $delivery_types = array(
                1 => 'Самовывоз из пунктов выдачи СДЭК',
                2 => 'Курьер CДЭК',
                3 => 'Самовывоз со склада',
                4 => 'Наш курьер',
            );

            $tariff_sdek = ArrayHelper::map(TariffSdek::find()->all(), 'id', 'title');
            $tariff_courier = array(
                'today_' . \Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . \Yii::$app->keyStorageApp->get('courier_to_time_id_3') => 'Сегодня срочно ' . \Yii::$app->keyStorageApp->get('courier_time_interval_3'),
                'tomorrow_' . \Yii::$app->keyStorageApp->get('courier_from_time_id_1') . '_' . \Yii::$app->keyStorageApp->get('courier_to_time_id_1') => 'Завтра ' . \Yii::$app->keyStorageApp->get('courier_time_interval_1'),
                'tomorrow_' . \Yii::$app->keyStorageApp->get('courier_from_time_id_2') . '_' . \Yii::$app->keyStorageApp->get('courier_to_time_id_2') => 'Завтра ' . \Yii::$app->keyStorageApp->get('courier_time_interval_2'),
                'tomorrow_' . \Yii::$app->keyStorageApp->get('courier_from_time_id_3') . '_' . \Yii::$app->keyStorageApp->get('courier_to_time_id_3') => 'Завтра ' . \Yii::$app->keyStorageApp->get('courier_time_interval_3'),
            );

            if ($this->order->delivery_service_id == 1 && !$this->order->delivery_type) {
                return ErrorMsg::customErrorMsg(400, "Не установлен тип доставки для СДЭК");
            }

            // Доставка
            $data['delivery'] = array(
                'status' => $this->order->status_delivery,
                //'type_id' => $this->order->delivery_service_id ? ($this->order->delivery_service_id == 1 ? $this->order->delivery_type : $this->order->delivery_service_id) : null,
                'type' => $this->order->delivery_service_id ? ($this->order->delivery_service_id == 1 ? $delivery_types[$this->order->delivery_type] : $delivery_types[$this->order->delivery_service_id]) : null,
                'city' => $this->order->delivery_city,
                //'tariff_id' => $this->order->tariff_sdek ?: ($this->order->courier_time_interval ?: null),
                'tariff' => $this->order->tariff_sdek ? $tariff_sdek[$this->order->tariff_sdek] : ($this->order->courier_time_interval ? $tariff_courier[$this->order->courier_time_interval] : null),
                'pvz' => $this->order->pvz_code ? array(
                    'code' => $this->order->pvz_code,
                    'address' => $this->order->address_delivery_pvz,
                    'info' => $this->order->pvz_info,
                ) : null,
                'address' => $this->order->address_delivery,
                'phone' => $this->order->phone,
                'track_number' => $this->order->track_number,
                'days' => $this->order->delivery_days,
                'comment' => $this->order->comment,
            );

            // Оплата
            $payment_types = ArrayHelper::map(PaymentTypes::find()->asArray()->all(), 'id', 'title');

            $data['payment'] = array(
                //'id' => $this->order->payment_type,
                'status' => $this->order->status_pay,
                'name' => $payment_types[$this->order->payment_type] ?: null,
                'date_pay' => $this->order->status_pay == 1 ? date('d.m.Y H:i', $this->order->date_pay) : null,
            );

            $data['sum_buy'] = $this->order->sum_buy ?: 0;
            $data['sum_delivery'] = $this->order->sum_delivery ?: 0;
            $data['sum_discount'] = $this->order->sum_discount ?: 0;
            $data['sum_total'] = $this->order->sum_buy + $this->order->sum_delivery - $this->order->sum_discount;
        }

        return $data;
    }

    protected function tinkoff_send($url, $data = null, $method = 'GET')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://securepay.tinkoff.ru/v2/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($data) {
            $request->setData($data);
        }

        $response = $request->send();
        $result->response = $response;

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;

            return $result;
        }

        try {
            if (isset($response->content)) {
                $responseContent = json_decode($response->content, true);
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error (' . json_last_error() . ')';
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
