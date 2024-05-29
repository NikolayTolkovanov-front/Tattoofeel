<?php

namespace api\modules\v1;

use common\models\DeliveryServices;
use common\models\SyncLogs;
use common\models\TariffSdek;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\httpclient\Client;
use yii\web\Response;
use common\models\UserClientProfile;
use common\models\UserClientOrder;
use common\models\OrderStatuses;
use common\models\Product;
use common\models\Currency;
use common\models\UserClientOrder_Product;

class Module extends \yii\base\Module
{
    const MS_API_URL = 'https://api.moysklad.ru/api/remap/1.2/';
    const MS_ID_CUSTOM_COUNTERPARTY_BONUS = '0903dcf9-27da-11eb-0a80-064800224ef1';

    const ORDER_STATUS_COMPLETED = 22;
    const ORDER_STATUS_COMPLETED_PASS = 23;

    const MS_ID_ORDER_ATTR_SHIPMENT_TYPE = '782bc080-292f-11e4-d7fd-002590a28eca';
    const MS_ID_ORDER_ATTR_TARIFF_SDEK = '69c1142b-9c0f-11ea-0a80-031300052092';
    const MS_ID_ORDER_ATTR_PVZ_CODE = 'db367f45-2cf8-11e4-2537-002590a28eca';
    const MS_ID_ORDER_ATTR_TRACK_NUMBER = 'a58e4266-29c2-11e4-cdfe-002590a28eca';
    const MS_ID_ORDER_ATTR_SMS_NUMBER = '4e64b1de-3d56-11e4-5c8b-002590a28eca';
    const MS_ID_ORDER_ATTR_TINKOFF_PAYMENT = '76abd301-2500-11eb-0a80-0140000fa4c9';
    const MS_ID_ORDER_ATTR_CHECKING_PAYMENT = '1247ff84-c876-11ea-0a80-06df001d34a9';
    const MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT = '1247e8f4-c876-11ea-0a80-06df001d34a7';
    const MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT = '124804db-c876-11ea-0a80-06df001d34aa';
    const MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT = '05c0a268-d2cc-11e8-9ff4-3150001f9bfb';
    const MS_ID_ORDER_ATTR_PLACES_COUNT = '3d5c75b1-522f-11ea-0a80-01ba000552e4';

    const MS_ID_CUSTOM_ENTITY_SHIPMENT_TYPE = '75339beb-292f-11e4-50c8-002590a28eca';
    const MS_ID_CUSTOM_ENTITY_TARIFF_SDEK = '5a8594ec-9c05-11ea-0a80-016f00040ef5';
    const MS_ID_CUSTOM_ENTITY_PLACES_COUNT = '3816886d-522f-11ea-0a80-05e800051d8a';

    const MS_ID_CUSTOM_COUNTERPARTY_HIDE_CASH = 'bdb545a0-7cd8-11eb-0a80-0317000ad507';
    const MS_ID_CUSTOM_COUNTERPARTY_HIDE_CARD = 'bdb547e5-7cd8-11eb-0a80-0317000ad508';

    const MS_ID_EMPLOYEE_ATTR_VK = 'f160f001-69f4-11eb-0a80-06d4002a8426';
    const MS_ID_EMPLOYEE_ATTR_WHATS_APP = 'ffcaf3b7-69f4-11eb-0a80-05f4002b8c45';
    const MS_ID_EMPLOYEE_ATTR_NAME_AT_SITE = '956f0cdc-69f5-11eb-0a80-0749002b60b9';

    const MS_ID_VALUE_PLACES_COUNT_1 = '52ab4a31-522f-11ea-0a80-007e000565e8';

    const DELIVERY_TYPE_PICKUP = 1;
    const DELIVERY_TYPE_COURIER = 2;
    const DELIVERY_TYPE_PICKUP_FROM_WAREHOUSE = 3;

    const DELIVERY_SERVICE_SDEK = 1;
    const DELIVERY_SERVICE_PICK_POINT = 2;
    const DELIVERY_SERVICE_BRATISLAVSKAYA = 3;
    const DELIVERY_SERVICE_OUR_COURIER = 4;
    const DELIVERY_SERVICE_IML_COURIER = 5;
    const DELIVERY_SERVICE_IML_COURIER_PAY = 6;
    const DELIVERY_SERVICE_IML_PICKUP = 7;
    const DELIVERY_SERVICE_IML_PICKUP_PAY = 8;

    const PAYMENT_TYPE_CASH = 1;
    const PAYMENT_TYPE_TINKOFF = 2;
    const PAYMENT_TYPE_CHECKING = 3;
    const PAYMENT_TYPE_CARD_2_CARD = 4;

    /** @var string */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!file_exists(dirname(__FILE__) . '/logs')) {
            mkdir(dirname(__FILE__) . '/logs', 0775, true);
        }

        $rq_uri = explode("?", $_SERVER['REQUEST_URI']);
        $rq_uri = $rq_uri[0];
        $rq_uri = trim($rq_uri, '/');
        $rq_uri = '/'.$rq_uri;

        switch ($rq_uri) {
            case '/api/web/v1/hook/moysklad/product/create':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events) && is_array($json->events)) {
                        foreach ($json->events as $event) {
                            if (isset($event->meta->href)) {
                                $product_ms_id = str_replace(self::MS_API_URL . 'entity/product/', '', $event->meta->href);
                                $product = new Product(['ms_id' => $product_ms_id]);

                                $result = 0;
                                $log = new SyncLogs([
                                    'created_at' => time(),
                                    'entity_type' => 'product',
                                    'event_type' => 'create',
                                    'ms_id' => $product_ms_id,
                                ]);

                                if ($product) {
                                    $result = $product->syncCreateProductHookHandler();

                                    if ($result) {
                                        $log->entity_id = $product->id;
                                    }
                                }

                                $log->is_success = $result;
                                if (!$log->save()) {
                                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                                    fputs($flog, print_r($log->errors, true));
                                    fclose($flog);
                                }
                            }
                        }

                        usleep(100000);
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;
            case '/api/web/v1/hook/moysklad/product/delete':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events) && is_array($json->events)) {
                        foreach ($json->events as $event) {
                            if (isset($event->meta->href)) {
                                $product_ms_id = str_replace(self::MS_API_URL . 'entity/product/', '', $event->meta->href);
                                $product = Product::find()
                                    ->where(['ms_id' => $product_ms_id])
                                    ->one();

                                $result = 0;
                                $log = new SyncLogs([
                                    'created_at' => time(),
                                    'entity_type' => 'product',
                                    'event_type' => 'delete',
                                    'ms_id' => $product_ms_id,
                                ]);

                                if ($product) {
                                    $result = $product->syncDeleteProductHookHandler();
                                    $log->entity_id = $product->id;
                                }

                                $log->is_success = $result;
                                if (!$log->save()) {
                                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                                    fputs($flog, print_r($log->errors, true));
                                    fclose($flog);
                                }
                            }
                        }

                        usleep(100000);
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;
            case '/api/web/v1/hook/moysklad/product/update':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events) && is_array($json->events)) {
                        foreach ($json->events as $event) {
                            if (isset($event->meta->href)) {
                                $product_ms_id = str_replace(self::MS_API_URL . 'entity/product/', '', $event->meta->href);
                                $product = Product::find()
                                    ->where(['ms_id' => $product_ms_id])
                                    ->one();

                                $log = new SyncLogs([
                                    'created_at' => time(),
                                    'entity_type' => 'product',
                                    'event_type' => $product ? 'update' : 'update (create)',
                                    'ms_id' => $product_ms_id,
                                ]);

                                if ($product) {
                                    $result = $product->syncUpdateProductHookHandler();
                                    $log->entity_id = $product->id;
                                } else {
                                    $product = new Product(['ms_id' => $product_ms_id]);

                                    $result = $product->syncCreateProductHookHandler();

                                    if ($result) {
                                        $log->entity_id = $product->id;
                                    }
                                }

                                $log->is_success = $result;
                                if (!$log->save()) {
                                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                                    fputs($flog, print_r($log->errors, true));
                                    fclose($flog);
                                }
                            }

                            usleep(100000);
                        }
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;
            case '/api/web/v1/hook/moysklad/customerorder/create':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events[0]->meta->href)) {
                        $order_ms_id = str_replace(self::MS_API_URL . 'entity/customerorder/', '', $json->events[0]->meta->href);
                        $clientOrder = UserClientOrder::find()
                            ->where(['order_ms_id' => $order_ms_id])
                            ->one();

                        if (is_null($clientOrder)) { // такого заказа не найдено в бд
                            $msOrder = self::ms_send_req($json->events[0]->meta->href, null, $method = 'GET');
                            if (isset($msOrder->responseContent->agent->meta->href)) {
                                $cms_id = explode('/', $msOrder->responseContent->agent->meta->href);
                                $client_ms_id = end($cms_id);
                                $client = UserClientProfile::find()
                                    ->where(['client_ms_id' => $client_ms_id])
                                    ->one();

                                if ($client) {
                                    $arOrder = $this->getOrder($json->events[0]->meta->href);

                                    if (is_array($arOrder)) {
                                        $clientOrder = new UserClientOrder();
                                        $clientOrder->user_id = $client->user_id;
                                        $clientOrder->order_ms_id = $order_ms_id;
                                        $clientOrder->date = time();
                                        $clientOrder->status_delivery = 0;
                                        $clientOrder->isCart = 0;
                                        $clientOrder->address_delivery = $client->address_delivery;
                                        $clientOrder->status_ms_sync = 1;
                                        $clientOrder->order_ms_number = $arOrder['order_ms_number'];
                                        $clientOrder->sum_discount = $arOrder['sum_discount'];
                                        $clientOrder->sum_delivery = $arOrder['sum_delivery'];
                                        $clientOrder->sum_delivery_discount = $arOrder['sum_delivery_discount'];
                                        $clientOrder->status = $arOrder['status'];
                                        $clientOrder->delivery_service_id = $arOrder['delivery_service_id'];
                                        $clientOrder->delivery_type = $arOrder['delivery_type'];
                                        $clientOrder->tariff_sdek = $arOrder['tariff_sdek'];
                                        $clientOrder->pvz_code = $arOrder['pvz_code'];
                                        $clientOrder->track_number = $arOrder['track_number'];
                                        $clientOrder->places_count = $arOrder['places_count'];
                                        $clientOrder->payment_type = $arOrder['payment_type'];

                                        if ($arOrder['status'] && ((int)$arOrder['status'] === self::ORDER_STATUS_COMPLETED || (int)$arOrder['status'] === self::ORDER_STATUS_COMPLETED_PASS)) {
                                            $clientOrder->status_pay = 1;
                                        } else {
                                            $clientOrder->status_pay = 0;
                                        }

                                        $log = new SyncLogs([
                                            'created_at' => time(),
                                            'entity_type' => 'customerorder',
                                            'event_type' => 'create',
                                            'ms_id' => $order_ms_id,
                                        ]);

                                        //$result = $clientOrder->save(false);
                                        $result = $clientOrder->save();
                                        $log->entity_id = $clientOrder->id;
                                        $log->is_success = $result ? 1 : 0;

                                        if (!$log->save()) {
                                            $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                                            fputs($flog, print_r($log->errors, true));
                                            fclose($flog);
                                        }

                                        // добавление позиций
                                        foreach ($arOrder['positions'] as $pos) {
                                            if (isset($pos['product_id'])) {
                                                $insertPosition = new UserClientOrder_Product();
                                                $insertPosition->order_id = $clientOrder->id;
                                                $insertPosition->product_id = $pos['product_id'];
                                                $insertPosition->price = $pos['price'];
                                                $insertPosition->count = $pos['count'];
                                                $insertPosition->currency_iso_code = Currency::DEFAULT_CART_PRICE_CUR_ISO;
                                                $insertPosition->save(false);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;
            case '/api/web/v1/hook/moysklad/customerorder/update':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events[0]->meta->href)) {
                        $order_ms_id = str_replace(self::MS_API_URL . 'entity/customerorder/', '', $json->events[0]->meta->href);
                        $clientOrder = UserClientOrder::find()
                            ->where(['order_ms_id' => $order_ms_id])
                            ->one();

                        if ($clientOrder) {
                            $arOrder = $this->getOrder($json->events[0]->meta->href);
                            if (is_array($arOrder)) {
                                $clientOrder->order_ms_number = $arOrder['order_ms_number'];
                                $clientOrder->sum_buy = $arOrder['sum_buy'];
                                $clientOrder->sum_discount = $arOrder['sum_discount'];
                                $clientOrder->sum_delivery = $arOrder['sum_delivery'];
                                $clientOrder->sum_delivery_discount = $arOrder['sum_delivery_discount'];
                                $clientOrder->status = $arOrder['status'];
                                $clientOrder->delivery_service_id = $arOrder['delivery_service_id'];
                                $clientOrder->delivery_type = $arOrder['delivery_type'];
                                $clientOrder->tariff_sdek = $arOrder['tariff_sdek'];
                                $clientOrder->pvz_code = $arOrder['pvz_code'];
                                $clientOrder->track_number = $arOrder['track_number'];
                                $clientOrder->places_count = $arOrder['places_count'];
                                $clientOrder->payment_type = $arOrder['payment_type'];

                                if ($arOrder['status'] && ((int)$arOrder['status'] === self::ORDER_STATUS_COMPLETED || (int)$arOrder['status'] === self::ORDER_STATUS_COMPLETED_PASS)) {
                                    $clientOrder->status_pay = 1;
                                } else {
                                    $clientOrder->status_pay = 0;
                                }

                                $log = new SyncLogs([
                                    'created_at' => time(),
                                    'entity_type' => 'customerorder',
                                    'event_type' => 'update',
                                    'ms_id' => $order_ms_id,
                                ]);

                                $result = $clientOrder->save(false);
                                $log->entity_id = $clientOrder->id;
                                $log->is_success = $result ? 1 : 0;

                                if (!$log->save()) {
                                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                                    fputs($flog, print_r($log->errors, true));
                                    fclose($flog);
                                }

                                // удаление позиций
                                $allPositions = UserClientOrder_Product::find()
                                    ->where(['order_id' => $clientOrder->id])
                                    ->asArray()
                                    ->all();

                                $arDelete = array();
                                foreach ($allPositions as $item) {
                                    $ms_exist = false;
                                    foreach ($arOrder['positions'] as $pos) {
                                        if (isset($pos['product_id']) && $item['product_id'] == $pos['product_id']) {
                                            $ms_exist = true;
                                            break;
                                        }
                                    }

                                    if (!$ms_exist) {
                                        $arDelete[] = $item['id'];
                                    }
                                }

                                if (count($arDelete)) {
                                    UserClientOrder_Product::deleteAll(['id' => $arDelete]);
                                }

                                // изменение/добавление позиций
                                foreach ($arOrder['positions'] as $pos) {
                                    if (isset($pos['product_id'])) {
                                        $position = UserClientOrder_Product::find()
                                            ->where(['order_id' => $clientOrder->id, 'product_id' => $pos['product_id']])
                                            ->one();
                                        if ($position) {
                                            $position->price = $pos['price'];
                                            $position->count = $pos['count'];
                                            $position->save(false);
                                        } else {
                                            $insertPosition = new UserClientOrder_Product();
                                            $insertPosition->order_id = $clientOrder->id;
                                            $insertPosition->product_id = $pos['product_id'];
                                            $insertPosition->price = $pos['price'];
                                            $insertPosition->count = $pos['count'];
                                            $insertPosition->currency_iso_code = Currency::DEFAULT_CART_PRICE_CUR_ISO;
                                            $insertPosition->save(false);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;

            case '/api/web/v1/hook/moysklad/counterparty/update':
                try {
                    $json = json_decode(@file_get_contents('php://input'));
                    if (!empty($json) && isset($json->events[0]->meta->href)) {
                        $client_ms_id = str_replace(self::MS_API_URL . 'entity/counterparty/', '', $json->events[0]->meta->href);
                        $clientProfile = UserClientProfile::find()
                            ->where(['client_ms_id' => $client_ms_id])
                            ->one();

                        if ($clientProfile) {
//                        $flog = fopen(dirname(__FILE__).'/logs/user_'.date("Ymd-His"), 'w');
//                        fputs($flog, "hook response: ". @file_get_contents('php://input'));
                            $arrClient = $this->searchClient($client_ms_id);

//                        fputs($flog, "response array: ".print_r($arrClient, true));
//                        fputs($flog, "UserClientProfile: ".print_r($clientProfile, true));

//                        $flog = fopen(dirname(__FILE__).'/logs/client_'.date("Ymd-His"), 'w');
//                        fputs($flog, "arrClient: ".print_r($arrClient, true));
//                        fclose($flog);

                            if (is_array($arrClient)) {
                                $isChanged = false;
                                foreach ($arrClient as $key => $value) {
                                    switch ($key) {
                                        case 'email':
                                            //fputs($flog, "clientProfile[key]: email value: {$value}\r\n");
                                            if (!is_null($value) && $clientProfile->user->email != $value) {
                                                $user = $clientProfile->user;
                                                $user->email = $value;
                                                $user->save(false);
                                            }
                                            break;
                                        //case 'full_name':
                                        case 'address_delivery':
                                        case 'phone':
                                        case 'phone_1':
                                        case 'link_vk':
                                        case 'link_inst':
                                        case 'ms_owner':
                                        case 'ms_owner_vk':
                                        case 'ms_owner_whatsapp':
                                        case 'ms_owner_name_at_site':
                                        case 'ms_bonus':
                                        case 'sale_ms_id':
                                        case 'sale_brands':
                                        case 'hide_cash':
                                        case 'hide_card':
                                            //fputs($flog, "clientProfile[key]: {$clientProfile[$key]} value: {$value}\r\n");
                                            if ($clientProfile[$key] != $value) {
                                                $clientProfile[$key] = $value;
                                                $isChanged = true;
                                            }
                                            break;
                                    }
                                }

                                if ($isChanged) {
                                    $clientProfile->save(false);
                                }
                            }
//                        fclose($flog);
                        }
                    }
                } catch (\Exception $e) {
                    $flog = fopen(dirname(__FILE__) . '/logs/error_' . date("Ymd-His"), 'w');
                    fputs($flog, print_r($e, true));
                    fclose($flog);
                }

                die('stop script');
                break;
        }

        parent::init();
        Yii::$app->user->identityClass = 'api\modules\v1\models\ApiUserIdentity';
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
        ];

        return $behaviors;
    }

    protected function getOrder($href)
    {
        $msOrder = self::ms_send_req($href, null, $method = 'GET');

        $name = null;
        $status = null;
        $sum = 0;
        $sum_buy = 0;
        $sum_discount = 0;
        $sum_delivery = 0;
        $sum_delivery_discount = null;
        $delivery_service_id = null;
        $delivery_type = null;
        $tariff_sdek = null;
        $pvz_code = null;
        $track_number = null;
        $places_count = null;
        $payment_type = null;
        $arPos = array();
        if (isset($msOrder->responseContent)) {
            $name = isset($msOrder->responseContent->name) ? $msOrder->responseContent->name : null;
            $sum = isset($msOrder->responseContent->sum) ? $msOrder->responseContent->sum : null;
            if (isset($msOrder->responseContent->state->meta->href)) {
                $state_ms_id = str_replace(self::MS_API_URL . 'entity/customerorder/metadata/states/', '', $msOrder->responseContent->state->meta->href);
                $status = OrderStatuses::find()
                    ->where(['ms_status_id' => $state_ms_id])
                    ->one();
            }

            if (isset($msOrder->responseContent->positions->meta->href)) {
                $msPos = self::ms_send_req($msOrder->responseContent->positions->meta->href, null, $method = 'GET');
                if (isset($msPos->responseContent->rows) && is_array($msPos->responseContent->rows)) {
                    foreach ($msPos->responseContent->rows as $key => $prod) {
                        if (isset($prod->assortment->meta->type) && 'product' == $prod->assortment->meta->type) {
                            if (isset($prod->quantity)) {
                                $arPos[$key]['count'] = $prod->quantity;
                            }

                            if (isset($prod->price)) {
                                $arPos[$key]['price'] = $prod->price;
                                $sum_buy += $prod->price * $prod->quantity;
                                if (isset($prod->discount) && $prod->discount) {
                                    $sum_discount += ($prod->price * $prod->quantity) / 100 * $prod->discount;
                                }
                            }

                            if (isset($prod->assortment->meta->href)) {
                                $prod_ms_id = str_replace(self::MS_API_URL . 'entity/product/', '', $prod->assortment->meta->href);
                                $arPos[$key]['ms_id'] = $prod_ms_id;
                                $product = Product::find()
                                    ->where(['ms_id' => $prod_ms_id])
                                    ->one();
                                if ($product) {
                                    $arPos[$key]['product_id'] = $product->id;
                                }
                            }
                        }

                        if (isset($prod->assortment->meta->type) && 'service' == $prod->assortment->meta->type) {
                            $sum_delivery = $prod->price;
                            if (isset($prod->discount) && $prod->discount) {
                                $sum_delivery_discount = $sum_delivery / 100 * $prod->discount;
                            }
                        }
                    }
                }
            }

            $arPayment = array();
            if (isset($msOrder->responseContent->attributes) && is_array($msOrder->responseContent->attributes)) {
                foreach ($msOrder->responseContent->attributes as $attr) {
                    // Тип отгрузки
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_SHIPMENT_TYPE) {
                        $service_ms_id = str_replace(self::MS_API_URL . 'entity/customentity/'.self::MS_ID_CUSTOM_ENTITY_SHIPMENT_TYPE.'/', '', $attr->value->meta->href);
                        $service = DeliveryServices::find()
                            ->where(['ms_id' => $service_ms_id])
                            ->one();
                        if ($service) {
                            $delivery_service_id = $service->id;
                            continue;
                        }
                    }
                    // Тариф СДЭК
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_TARIFF_SDEK) {
                        $tariff_ms_id = str_replace(self::MS_API_URL . 'entity/customentity/'.self::MS_ID_CUSTOM_ENTITY_TARIFF_SDEK.'/', '', $attr->value->meta->href);
                        $tariff = TariffSdek::find()
                            ->where(['ms_id' => $tariff_ms_id])
                            ->one();
                        if ($tariff) {
                            $tariff_sdek = $tariff->id;
                            $delivery_type = $tariff->delivery_type ? $tariff->delivery_type : null;
                            continue;
                        }
                    }
                    // Количество мест
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_PLACES_COUNT) {
                        if (isset($attr->value->name)) {
                            $places_count = $attr->value->name ? $attr->value->name : null;
                            continue;
                        }
                    }
                    // Код ПВЗ
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_PVZ_CODE) {
                        $pvz_code = $attr->value;
                        continue;
                    }
                    // Трэк-номер
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_TRACK_NUMBER) {
                        $track_number = $attr->value;
                        continue;
                    }
                    // Оплата ТИНЬК_БЕЗНАЛ
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT) {
                        if ($attr->value == 1) {
                            $arPayment[] = 'tinkoff';
                        }
                        continue;
                    }
                    // Оплата на РАСЧ_СЧ
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT) {
                        if ($attr->value == 1) {
                            $arPayment[] = 'checking';
                        }
                        continue;
                    }
                    // Оплата в ТРАНСПОРТНУЮ
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT) {
                        if ($attr->value == 1) {
                            $arPayment[] = 'transport';
                        }
                        continue;
                    }
                    // Оплата НАЛ_НАШ_КАРТА
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT) {
                        if ($attr->value == 1) {
                            $arPayment[] = 'cash';
                        }
                        continue;
                    }
                    // Обнулить (оплата)
                    if (isset($attr->id) && $attr->id == self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT) {
                        if ($attr->value == 1) {
                            $arPayment[] = 'zero';
                        }
                        continue;
                    }
                }
            }

            if ($delivery_service_id == self::DELIVERY_SERVICE_IML_COURIER || $delivery_service_id == self::DELIVERY_SERVICE_IML_COURIER_PAY) {
                $delivery_type = self::DELIVERY_TYPE_COURIER;
            } elseif ($delivery_service_id == self::DELIVERY_SERVICE_IML_PICKUP || $delivery_service_id == self::DELIVERY_SERVICE_IML_PICKUP_PAY) {
                $delivery_type = self::DELIVERY_TYPE_PICKUP;
            }

            if (!empty($arPayment)) {
                if (in_array('cash', $arPayment) && in_array('zero', $arPayment)) {
                    $payment_type = self::PAYMENT_TYPE_CARD_2_CARD;
                } elseif (in_array('cash', $arPayment) || in_array('transport', $arPayment)) {
                    $payment_type = self::PAYMENT_TYPE_CASH;
                } elseif (in_array('tinkoff', $arPayment)) {
                    $payment_type = self::PAYMENT_TYPE_TINKOFF;
                } elseif (in_array('checking', $arPayment)) {
                    $payment_type = self::PAYMENT_TYPE_CHECKING;
                }
            }
        }

        return array(
            'order_ms_number' => $name,
            //'sum_buy' => $sum - $sum_delivery,
            'sum_buy' => $sum_buy,
            'sum_discount' => $sum_discount ? $sum_discount : null,
            'sum_delivery' => $sum_delivery,
            'sum_delivery_discount' => $sum_delivery_discount,
            'status' => isset($status->id) ? $status->id : null,
            'positions' => $arPos,
            'delivery_service_id' => $delivery_service_id,
            'delivery_type' => $delivery_type,
            'tariff_sdek' => $tariff_sdek,
            'pvz_code' => $pvz_code,
            'track_number' => $track_number,
            'places_count' => $places_count,
            'payment_type' => $payment_type,
        );
    }

    protected function searchClient($search_ms_id) {
        if (empty($search_ms_id))
            return null;

        $client = new Client([
            'baseUrl' => self::MS_API_URL.'entity/'
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic ".base64_encode(env('MS_LOGIN').':'.env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->setData(['limit' => 1])
            ->addOptions([
                'timeout' => 15
            ]);

        $request->setUrl("counterparty?filter=id=$search_ms_id");
        $response = $request->send();

        if (!$response->isOk)
            return false;

        try {
            if ($response->headers['content-encoding'] == 'gzip') {
                $responseContent = json_decode(gzdecode($response->content));
            } else {
                $responseContent = json_decode($response->content);
            }

            if (is_null($responseContent) || json_last_error())
                return false;

            $responseContent = $responseContent->rows;

            if (empty($responseContent) || !isset($responseContent[0])) {
                return null;
            }

            $attrs = array(
                'link_vk' => null,
                'link_inst' => null,
                'bonus' => null,
                'owner_vk' => null,
                'owner_whatsapp' => null,
                'owner_name_at_site' => null,
                'hide_cash' => 0,
                'hide_card' => 0,
            );
            if (isset($responseContent[0]->attributes) && is_array($responseContent[0]->attributes)) {
                foreach ($responseContent[0]->attributes as $item) {
                    switch ($item->id) {
                        case env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'):
                            $attrs['link_vk'] = $item->value;
                            break;
                        case env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'):
                            $attrs['link_inst'] = $item->value;
                            break;
                        case self::MS_ID_CUSTOM_COUNTERPARTY_BONUS:
                            $attrs['bonus'] = $item->value;
                            break;
                        case self::MS_ID_CUSTOM_COUNTERPARTY_HIDE_CASH:
                            $attrs['hide_cash'] = $item->value ? 1 : 0;
                            break;
                        case self::MS_ID_CUSTOM_COUNTERPARTY_HIDE_CARD:
                            $attrs['hide_card'] = $item->value ? 1 : 0;
                            break;
                    }
                }
            }

//            $flog = fopen(dirname(__FILE__).'/logs/attr1_'.date("Ymd-His"), 'w');
//            fputs($flog, "attrs: ".print_r($attrs, true));
//            fclose($flog);

            $owner = null;
            if (isset($responseContent[0]->owner->meta->href)) {
                $ms_owner = self::ms_send_req($responseContent[0]->owner->meta->href, null, $method = 'GET');
                $owner = isset($ms_owner->responseContent->name) ? $ms_owner->responseContent->name : null;

                if (isset($ms_owner->responseContent->attributes) && is_array($ms_owner->responseContent->attributes)) {
                    foreach ($ms_owner->responseContent->attributes as $item) {
                        switch ($item->id) {
                            case self::MS_ID_EMPLOYEE_ATTR_VK:
                                $attrs['owner_vk'] = $item->value;
                                break;
                            case self::MS_ID_EMPLOYEE_ATTR_WHATS_APP:
                                $attrs['owner_whatsapp'] = $item->value;
                                break;
                            case self::MS_ID_EMPLOYEE_ATTR_NAME_AT_SITE:
                                $attrs['owner_name_at_site'] = $item->value;
                                break;
                        }
                    }
                }
            }

//            $flog = fopen(dirname(__FILE__).'/logs/attr2_'.date("Ymd-His"), 'w');
//            fputs($flog, "attr: ".print_r($attrs, true));
//            fclose($flog);

            $sale_brands = null;
            if (isset($responseContent[0]->attributes) && is_array($responseContent[0]->attributes)) {
                $result = array();
                foreach($responseContent[0]->attributes as $attr) {
                    if (mb_strpos($attr->name, 'Скидка') === 0)
                        $result[$attr->name] = $attr->value;
                }

                $sale_brands = count($result) ? json_encode($result) : null;
            }

//            $flog = fopen(dirname(__FILE__).'/logs/search_user_'.date("Ymd-His"), 'w');
//            fputs($flog, "responseContent: ".print_r($responseContent, true));
//            fclose($flog);

            $result = array(
                //'full_name' => isset($responseContent[0]->name) ? $responseContent[0]->name : null,
                'address_delivery' => isset($responseContent[0]->actualAddress) ? $responseContent[0]->actualAddress : null,
                'email' => isset($responseContent[0]->email) ? $responseContent[0]->email : null,
                'phone' => isset($responseContent[0]->phone) ? $responseContent[0]->phone : null,
                'phone_1' => isset($responseContent[0]->fax) ? $responseContent[0]->fax : null,

                'link_vk' => $attrs['link_vk'],
                'link_inst' => $attrs['link_inst'],
                'ms_owner' => $owner,
                'ms_owner_vk' => $attrs['owner_vk'],
                'ms_owner_whatsapp' => $attrs['owner_whatsapp'],
                'ms_owner_name_at_site' => $attrs['owner_name_at_site'],
                'ms_bonus' => $attrs['bonus'],
                'hide_cash' => $attrs['hide_cash'],
                'hide_card' => $attrs['hide_card'],

                'sale_ms_id' => isset($responseContent[0]->priceType) ? $responseContent[0]->priceType->name : null,
                'sale_brands' => $sale_brands,
            );

//            $flog = fopen(dirname(__FILE__).'/logs/array_'.date("Ymd-His"), 'w');
//            fputs($flog, "array: ".print_r($result, true));
//            fclose($flog);

            return $result;
        } catch (\Exception $e) {
            \Yii::error([$e, 'sync client: '.$search_ms_id], 'client_sync__except');
            return false;
        }
    }

    /**
     * @param $url
     * @param null $data
     * @param string $method
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected static function ms_send_req($url, $data = null, $method = 'GET')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => self::MS_API_URL,
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
