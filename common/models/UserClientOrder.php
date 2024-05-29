<?php

namespace common\models;

use frontend\helpers\Debug as _;
use frontend\models\Roistat;
use Yii;

use common\components\TimestampBehavior;
use frontend\models\Product;
use frontend\modules\lk\models\Delivery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\httpclient\Client;

/**
 * @property int $id
 * @property \common\models\UserClient $user
 * @property array $cdekTariffList Список тарифов СДЭК
 * @property string $sumFormat Форматированная сумма
 * @property int $delivery_type Тип доставки 1-склад-склад, 2-склад-дверь, 3-самовывоз со склада
 * @property string $delivery_city Город доставки
 * @property string $delivery_service Компания доставки
 * @property string $pvz_code Код ПВЗ
 * @property string $pvz_info
 * @property float $delivery_weight Расчетный вес отправления
 * @property string $delivery_date Дата доставки
 * @property string $delivery_tariff_name Наименование тарифа доставки
 * @property string $delivery_period_max Максимальный период доставки
 * @property string $address_delivery Адрес доставки (курьер)
 * @property string $address_delivery_pvz Адрес ПВЗ
 * @property string $courier_time_interval
 * @property integer $coupon_id
 * @property double $sum_discount
 * @property double $sum_delivery_discount
 * @property string $comment
 * @property string $phone
 */
class UserClientOrder extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const SCENARIO_BUY = 'buy';
    const SCENARIO_PAY = 'pay';
    const SCENARIO_ORDER_REGISTER = 'order_register';

    const STATUS_PAY_YES = 1;
    const STATUS_PAY_NO = 0;

    const STATUS_DELIVERY_YES = 1;
    const STATUS_DELIVERY_NO = 0;

    const ORDER_STATUS_WAITING_FOR_PAYMENT = 8; // Ждем оплату
    const ORDER_STATUS_NEW_PAYED = 28; // Новый заказ (Оплачено)

    const DELIVERY_TYPE_PICKUP = 1;
    const DELIVERY_TYPE_COURIER = 2;
    const DELIVERY_TYPE_PICKUP_FROM_WAREHOUSE = 3;

    const MS_ID_ORDER_STORE_MAIN = 'a968b283-5ab8-11e5-90a2-8ecb00119d8e';

    const MS_ID_ORDER_STATE_NEW_ORDER = '3039e25a-540b-11e6-7a69-8f5500107e15';

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
    const MS_ID_ORDER_ATTR_DELIVERY_CITY = 'f616443c-5e19-11eb-0a80-0646001ba91c';
    const MS_ID_ORDER_ATTR_DELIVERY_DATE = 'ffb214fa-ac30-11e8-9107-5048000e9520';
    const MS_ID_ORDER_ATTR_DELIVERY_TIME_FROM = 'e9e3a205-2eb0-11e4-6982-002590a28eca';
    const MS_ID_ORDER_ATTR_DELIVERY_TIME_TO = 'e9e3a516-2eb0-11e4-945d-002590a28eca';

    const MS_ID_CUSTOM_ENTITY_SHIPMENT_TYPE = '75339beb-292f-11e4-50c8-002590a28eca';
    const MS_ID_CUSTOM_ENTITY_TARIFF_SDEK = '5a8594ec-9c05-11ea-0a80-016f00040ef5';
    const MS_ID_CUSTOM_ENTITY_PLACES_COUNT = '3816886d-522f-11ea-0a80-05e800051d8a';
    const MS_ID_CUSTOM_ENTITY_DELIVERY_CITY = 'cc70d1df-4522-11eb-0a80-05e50021ac9c';
    const MS_ID_CUSTOM_ENTITY_DELIVERY_TIME_FROM = 'd6411509-2eb0-11e4-e228-002590a28eca';
    const MS_ID_CUSTOM_ENTITY_DELIVERY_TIME_TO = 'e76274a0-2eb0-11e4-c6bd-002590a28eca';

    const MS_ID_VALUE_PLACES_COUNT_1 = '52ab4a31-522f-11ea-0a80-007e000565e8';
    const MS_ID_VALUE_PLACES_COUNT_2 = '57d5fdda-522f-11ea-0a80-04dc00054655';

    const MS_ID_POSITION_DELIVERY_SERVICE = '7a87ae90-6b12-11e4-90a2-8ecb0006910f';

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

    public static $order_statuses_can_change_products = [
        1 => "Новый заказ",
        2 => "Предзаказ",
        8 => "Ждем оплату",
        9 => "Ожидаев",
        10 => "Резерв",
    ];

    public $delivery_service;
    public $delivery_weight;
    public $delivery_date;
    public $delivery_tariff_name;
    public $delivery_period_max;

    /**
     * @return array statuses list
     */
    public static function statusesPay()
    {
        return [
            self::STATUS_PAY_NO => t_b('не оплачено'),
            self::STATUS_PAY_YES => t_b('оплачено')
        ];
    }

    /**
     * @return array statuses list
     */
    public static function statusesDelivery()
    {
        return [
            self::STATUS_DELIVERY_NO => t_b('не доставлено'),
            self::STATUS_DELIVERY_YES => t_b('доставлено'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client_order}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return $this->ts_behavior();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['status_pay', 'status_delivery'], 'integer'],
            [['status_pay', 'status_delivery'], 'default', 'value' => 0],
            [['isCart'], 'integer'],
            [['isCart'], 'default', 'value' => 0],
            [['order_ms_id', 'order_ms_number'], 'string'],
            [['order_ms_id'], 'unique', 'skipOnEmpty' => true],
            [['products'], 'safe'],
            [['sum_delivery'], 'integer'],
            [['payment_type'], 'integer'],
            [['sum_buy'], 'integer'],
            [['pay_id'], 'string'],
            [['address_delivery', 'address_delivery_pvz'], 'string'],
            [['comment'], 'string'],
            [
                ['address_delivery', 'address_delivery_pvz'],
                'required',
                'on' => self::SCENARIO_ORDER_REGISTER,
                'when' => function ($model) {
                    return !($model->address_delivery || $model->address_delivery_pvz);
                }
            ],
            [['sum_delivery', 'delivery_city', 'delivery_type'], 'required', 'on' => self::SCENARIO_ORDER_REGISTER],
            [['date_pay', 'pay_id'], 'required', 'on' => self::SCENARIO_PAY],
            [
                ['date', 'date_pay'],
                'filter',
                'filter' => 'strtotime',
                'skipOnEmpty' => true,
                'except' => self::SCENARIO_PAY
            ],
            [['date', 'date_pay'], 'integer', 'on' => self::SCENARIO_PAY],
            //0 не создан в мс
            //1 создан в мс
            //2 зарезервирован в мс
            [['status_ms_sync'], 'integer'],
            [['status_ms_sync'], 'default', 'value' => 0],
            [
                [
                    'delivery_city',
                    'delivery_service',
                    'pvz_code',
                    'pvz_info',
                    'delivery_date',
                    'delivery_tariff_name',
                    'delivery_period_max',
                    'track_number'
                ],
                'string'
            ],
            [['delivery_weight'], 'number'],
            [['delivery_type', 'delivery_service_id'], 'integer'],
            [['tariff_sdek'], 'integer'],
            [['places_count'], 'integer'],
            [['is_new'], 'integer'],
            [['is_new'], 'default', 'value' => 0],
            [['courier_time_interval'], 'string'],
            [['commission'], 'double'],
            [['delivery_days', 'coupon_id'], 'integer'],
            [['delivery_days'], 'default', 'value' => 0],
            [['sum_discount', 'sum_delivery_discount'], 'integer'],
            [['phone'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'order_ms_number' => t_b('№ в МС'),
            'user_id' => t_b('Клиент'),
            'order_ms_id' => t_b('Ид. в МС'),
            'status_pay' => t_b('Статус оплаты'),
            'status_delivery' => t_b('Статус доставки'),
            'date' => t_b('Дата/Время доб. в МС'),
            'isCart' => t_b('Корзина'),
            'comment' => t_b('Комментарий'),
            'address_delivery' => t_b('Адрес доставки'),
            'phone' => t_b('Контактный телефон'),
            'address_delivery_pvz' => t_b('Адрес доставки ПВЗ'),
            'sum_delivery' => t_b('Стоимость доставки'),
            '_sum_delivery' => t_b('Стоимость доставки'),
            'sum_buy' => t_b('Стоимость покупки'),
            'date_pay' => t_b('Дата/Время оплаты'),
            'pay_id' => t_b('Ид. оплаты'),
            'payment_type' => t_b('Способ оплаты'),
            'created_at' => t_b('Создан (админ)'),
            'updated_at' => t_b('Обновлен (админ)'),
            'created_by' => t_b('Создал (админ)'),
            'updated_by' => t_b('Обновил (админ)'),
            'client_created_at' => t_b('Создан (клиент)'),
            'client_updated_at' => t_b('Обновлен (клиент)'),
            'client_created_by' => t_b('Создал (клиент)'),
            'client_updated_by' => t_b('Обновил (клиент)'),
            'delivery_city' => t_b('Город получателя'),
            'delivery_type' => t_b('Тип доставки'),
            'tariff_sdek' => t_b('Тариф СДЭК'),
            'delivery_service_id' => t_b('Сервис доставки'),
            'places_count' => t_b('Количество мест'),
            'is_new' => t_b('Новый заказ'),
            'courier_time_interval' => t_b('Временной интервал для способа доставки "Курьер"'),
            'commission' => t_b('Процент комиссии'),
            'delivery_days' => t_b('Кол-во дней доставки заказа'),
            'coupon_id' => t_b('Купон'),
            'sum_discount' => t_b('Сумма скидки на товары'),
            'sum_delivery_discount' => t_b('Сумма скидки на доставку'),
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'count',
            'sumFormat',
            'couponCode',
            'sumDiscountFormat',
            'sumWithoutDiscountFormat'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(OrderStatuses::class, ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryService()
    {
        return $this->hasOne(DeliveryServices::class, ['id' => 'delivery_service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoupon()
    {
        return $this->hasOne(Coupons::class, ['id' => 'coupon_id']);
    }

    public function getCouponCode()
    {
        $coupon = Coupons::findOne($this->coupon_id);

        return $coupon ? $coupon->coupon_code : '';
    }

    /**
     * @param $value
     * @param bool $add
     * @param bool $afterLogin
     * @throws \yii\base\InvalidConfigException
     */
    public function setProducts($value, $add = false, $afterLogin = false)
    {
        $curIds = ArrayHelper::getColumn($this->getProducts()->select(['id'])->asArray()->all(), 'id');

        $newIds = array_diff(array_keys((array)$value), $curIds);
        $delIds = array_diff($curIds, array_keys((array)$value));
        if ($add) {
            $delIds = [];
        }
        $updIds = array_diff(array_diff($curIds, $newIds), $delIds);

        $new = Product::find()->preparePrice()->where(['in', Product::tableName() . '.id', $newIds])->all();
        $del = Product::find()->where(['in', 'id', $delIds])->all();
        $update = Product::find()->preparePrice()->where(['in', Product::tableName() . '.id', $updIds])->all();

        foreach ($new as $cf) {
            if (!empty($value[$cf->id])) {
                $this->link('products', $cf, [
                    'count' => !$afterLogin ? (int)$value[(int)$cf->id]['count'] : (int)$value[(int)$cf->id],
                    'price' => $cf->clientPriceValue,
                    'currency_iso_code' => '643'
                ]);
            }
        }

        if (!$add) {
            foreach ($del as $cf) {
                $this->unlink('products', $cf, true);
            }
        }

        foreach ($update as $cf) {
            $u = UserClientOrder_Product::findOne(['order_id' => $this->id, 'product_id' => $cf->id]);
            if ($u && !empty($value[$cf->id])) {
                $u->count = $add ? $u->count + (int)$value[$cf->id]['count'] : (int)$value[$cf->id]['count'];
                $u->price = $u->is_gift ? 0 : $cf->clientPriceValue;
                $u->save(false);
            }
        }
    }

    public function removeProduct($id, $coupon_code = '')
    {
        $del = Product::find()->where(['in', 'id', $id])->one();
        $this->unlink('products', $del, true);

        $coupon = null;
        if ($this->coupon_id) {
            $coupon = Coupons::findOne($this->coupon_id);
        } elseif (!empty($coupon_code)) {
            $coupon = Coupons::find()->where(['coupon_code' => $coupon_code])->one();
        }

        if ($coupon) {
            $coupon->resultApply($this);
        }

        return $this;
    }

    public function removeGifts() {
        $prods = $this->linkProducts;
        $to_del = [];
        foreach ($prods as $prod) {
            if ($prod->is_gift) {
                $to_del[] = $prod;
            }
        }
        if (!empty($to_del)) {
            foreach ($to_del as $item) {
                $item->delete();
            }
            unset($this->linkProducts);
        }
    }

    /**
     * @param int $id
     * @param $value
     * @param bool $sumCount
     * @param string $coupon_code
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function updateProduct($id, $value, $sumCount = true, $coupon_code = '', $isGift = false)
    {
        if ($upd = $this->getProducts()->preparePrice()->where([Product::tableName() . '.id' => $id])->one()) {
            $u = UserClientOrder_Product::findOne(['order_id' => $this->id, 'product_id' => $id]);
            $u->count = $sumCount ? $u->count + $value['count'] : $value['count'];
            $u->price = $isGift ? 0 : $upd->clientPriceValue;
            $u->is_gift = $isGift ? 1 : null;
            $u->save(false);

            $coupon = null;
            if ($this->coupon_id) {
                $coupon = Coupons::findOne($this->coupon_id);
            } elseif (!empty($coupon_code)) {
                $coupon = Coupons::find()->where(['coupon_code' => $coupon_code])->one();
            }

            if ($coupon) {
                $coupon->resultApply($this);
            }
        } else {
            $new = Product::find()->preparePrice()->where([Product::tableName() . '.id' => $id])->one();

            if ($new) {
                $this->link('products', $new, [
                    'count' => $value['count'],
                    'price' => $isGift ? 0 : $new->clientPriceValue,
                    'is_gift' => $isGift ? 1 : null,
                    'currency_iso_code' => '643'
                ]);
            }
        }

        return $this;
    }

    /**
     * @param $value
     * @param bool $add
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function addProducts($value, $add = true)
    {
        $this->setProducts($value, $add);
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('{{%user_client_order__product}}',
                ['order_id' => 'id']);
    }

    public function addSumDelivery($sum)
    {
        $this->sum_delivery = $sum * 100;
        $this->save(false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkProducts()
    {
        return $this->hasMany(UserClientOrder_Product::class, ['order_id' => 'id'])
            ->cache(3);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserClient::class, ['id' => 'user_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(PaymentTypes::class, ['id' => 'payment_type']);
    }

    public function clearCart()
    {
        foreach ($this->products as $cf) {
            $this->unlink('products', $cf, true);
        }
    }

    public function getCount()
    {
        $allCount = 0;
        $links = $this->getLinkProducts()->all();

        foreach ($links as $link) {
            $allCount += $link->count;
        }
        return $allCount;
    }

    public function getSum()
    {
        $sum = 0;
        $links = $this->getLinkProducts()->all();

        foreach ($links as $link) {
            if (!$link->is_gift) {
                $sum += $link->count * $link->product->clientPriceValue;
            }
        }

        return $sum;
    }

    public function getSumWithoutDiscount()
    {
        $sum = $this->getSum();

        if ($sum > 0 && $this->sum_discount) {
            $sum -= $this->sum_discount;
        }

        return $sum;
    }

    public function getCommissionPercent()
    {
        $sale_ms_id = Yii::$app->client->identity->profile->sale_ms_id;
        if ($sale_ms_id) {
            $commission = Commission::find()
                ->where(['payment_type_id' => $this->payment_type])
                ->andWhere(['like', 'discount_group', $sale_ms_id])
                ->one();
        }

        return isset($commission) ? $commission->percent : 0;
    }

    public function getCommissionSum()
    {
        $commission_sum = 0;
        if ($this->commission) {
            $commission_sum = ceil((($this->sum_buy / 100) + ($this->sum_delivery / 100)) / 100 * $this->commission) * 100;
        }

        return $commission_sum;
    }

    /**
     * @return int|string
     */
    public function getSumFormat()
    {
        $price = ProductPrice::getParsePrice(
            $this->sum,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );

        return $this->sum ? implode('', [$price->ceil_fr, ' ', $price->cur]) : 0;
    }

    /**
     * @return int|string
     */
    public function getSumWithoutDiscountFormat()
    {
        $price = ProductPrice::getParsePrice(
            $this->sumWithoutDiscount,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );

        return $this->sumWithoutDiscount ? implode('', [$price->ceil_fr, ' ', $price->cur]) : 0;
    }

    /**
     * @return int|string
     */
    public function getSumDiscountFormat()
    {
        $price = ProductPrice::getParsePrice(
            (int)$this->sum_discount,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );

        return (int)$this->sum_discount ? implode('', [$price->floor_fr, ' ', $price->cur]) : 0;
    }

    /**
     * @return int|string
     */
    public function getTotalSumDiscountFormat()
    {
        $total_sum = (int)$this->sum_discount + (int)$this->sum_delivery_discount;

        $price = ProductPrice::getParsePrice(
            $total_sum,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );

        return $total_sum ? implode('', [$price->value, ' ', $price->cur]) : 0;
    }

    /**
     * @return int|string
     */
    public function getTotalSumFormat()
    {
        $total_sum = $this->sum_buy + $this->sum_delivery + $this->commissionSum - (int)$this->sum_discount - (int)$this->sum_delivery_discount;

        $price = ProductPrice::getParsePrice(
            $total_sum,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );

        return ($total_sum) ? implode('', [$price->value, ' ', $price->cur]) : 0;
    }

    public function get_sum_delivery()
    {
        $price = ProductPrice::getParsePrice(
            $this->sum_delivery,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );
        return $this->sum_delivery ? implode('', [$price->ceil_fr, ' ', $price->cur]) : 0;
    }

    public const CREATE_ORDER_ERROR__CANT_CREATE = -2;
    public const CREATE_ORDER_ERROR__NOT_ENOUGH_STOCK = -4;
    public const CREATE_ORDER_ERROR__CANT_RESERVE = -5;

    public function msCreateOrder($skipIsCart = false)
    {
        if (!Yii::$app->user->isGuest && !$this->isCart && !$skipIsCart) {
            return null;
        }

        $result = $this->msStatusResponse();

        if ($this->status_ms_sync == 0) {
            if ($this->user->profile->client_ms_id) {
                $this->user->profile->sync();
                $agentMeta = $this->ms_get_meta(
                    "entity/counterparty/" . $this->user->profile->client_ms_id,
                    "counterparty"
                );
            } else {
                $client = $this->ms_create_client();
                if ($client->status) {
                    $agentMeta = ['meta' => $client->responseContent->meta];

                    $u = UserClientProfile::findOne($this->user->profile->id);
                    $u->client_ms_id = isset($client->responseContent->id) ? $client->responseContent->id : null;
                    $u->sale_ms_id = isset($client->responseContent->priceType) ? $client->responseContent->priceType->name : null;
                    $u->save(false);

                } else {
                    Yii::error([$client->msg, 'id profile: ' . $this->user->profile->id], 'ms_sync_order__client');
                    $result->status = false;
                    $result->statusCode = -1;
                    $result->msg = 'Не удалось создать покупателя';
                    return $result;
                }
            }

            $order = $this->ms_create_order($agentMeta);

            if ($order->status) {
                $this->order_ms_id = $order->responseContent->id;
                $this->order_ms_number = $order->responseContent->name;
                $this->status_ms_sync = 1;
                $this->status = 1; // новый заказ

                if (!$this->save(false)) {
                    Yii::error([$this->getErrors(), 'id profile: ' . $this->user->profile->id],
                        'ms_sync_order__order_upd');
                }

            } else {
                $result->status = false;
                $result->statusCode = $this::CREATE_ORDER_ERROR__CANT_CREATE;
                $result->msg = 'Не удалось создать заказ';
                return $result;
            }
        } else {
            if ($this->order_ms_id) {
                $update = $this->ms_update_order();
            }

            //если нет в ms ордера
            if ($update->response->statusCode == 404 || empty($this->order_ms_id)) {
                $this->status_ms_sync = 0;
                return $this->msCreateOrder();
            }

            if (!$update->status) {
                Yii::error([$update->msg, 'id profile: ' . $this->user->profile->id], 'ms_sync_order__order');
                $result->status = false;
                $result->statusCode = $this::CREATE_ORDER_ERROR__CANT_CREATE;
                $result->msg = 'Не удалось обновить заказ';
                return $result;
            }
        }

        $roistat = new Roistat();
        $roistat->setName($this->user->profile->getFullName());
        $roistat->setEmail($this->user->profile->getMail());
        if (!$this->phone) {
            $roistat->setPhone($this->user->profile->phone);
        } else {
            $roistat->setPhone($this->phone); // для клиентов CRM
        }

        $roistat->setFields([
            'form ' => 'Tattoofeel: новый заказ',
        ]);

        $roistat->sendProxyLead(); // Отправка проксилида в Roistat

        $stock = $this->ms_get_stock($this->order_ms_id);
        if ($stock->status) {
            $check = $this->check_stock($stock->responseContent); // проверка имеется ли достаточное кол-во товаров в МС для заказа (пустой массив = ОК)
            if (empty($check)) {
                $reserve = $this->ms_reserve();

                if (!$reserve->status) {
                    Yii::error([$reserve->msg, 'id profile: ' . $this->user->profile->id], 'reserve');
                    $result->status = false;
                    $result->statusCode = $this::CREATE_ORDER_ERROR__CANT_RESERVE;
                    $result->msg = 'Не удалось зарезервировать товары';
                    return $result;
                }

                $this->sync_product_stock($stock->responseContent);
            } else { // есть позиции товаров в заказе, где кол-во больше, имеющегося в МС
                $result->status = false;
                $result->statusCode = $this::CREATE_ORDER_ERROR__NOT_ENOUGH_STOCK;
                $result->msg = 'Данного количества товаров нет в наличии';
                $result->errors_qty = $check;
                return $result;
            }
        } else {
            Yii::error([$stock->msg, 'id profile: ' . $this->user->profile->id], 'ms_sync_order__stock');
            $result->status = false;
            $result->statusCode = -3;
            $result->msg = 'Не удалось проверить количество товаров';
            return $result;
        }

        return $result;
    }

    public function msChangeOrderProducts()
    {
        $result = $this->msStatusResponse();
        $stock = $this->ms_get_stock($this->order_ms_id);
        if ($stock->status) {
            $check = $this->check_stock($stock->responseContent); // проверка имеется ли достаточное кол-во товаров в МС для заказа (пустой массив = ОК)
            if (empty($check)) {
                $reserve = $this->ms_reserve();
                if (!$reserve->status) {
                    Yii::error([$reserve->msg, 'id profile: ' . $this->user->profile->id], 'reserve');
                    $result->status = false;
                    $result->statusCode = $this::CREATE_ORDER_ERROR__CANT_RESERVE;
                    $result->msg = 'Не удалось зарезервировать товары';
                    return $result;
                }
                $this->sync_product_stock($stock->responseContent);
            } else { // есть позиции товаров в заказе, где кол-во больше, имеющегося в МС
                $result->status = false;
                $result->statusCode = $this::CREATE_ORDER_ERROR__NOT_ENOUGH_STOCK;
                $result->msg = 'Данного количества товаров нет в наличии';
                $result->errors_qty = $check;
                return $result;
            }
        } else {
            Yii::error([$stock->msg, 'id profile: ' . $this->user->profile->id], 'ms_sync_order__stock');
            $result->status = false;
            $result->statusCode = -3;
            $result->msg = 'Не удалось проверить количество товаров';
            return $result;
        }

        return $result;
    }

    public function msChangePaymentType()
    {
        $dataForSend = [
            'description' => $this->ms_get_order_desc(),
            'positions' => $this->ms_get_positions(true)
        ];

        if (self::PAYMENT_TYPE_CASH == $this->payment_type && self::DELIVERY_SERVICE_SDEK == $this->delivery_service_id) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CASH == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_TINKOFF == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CHECKING == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CARD_2_CARD == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => false,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        }

        return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');
    }

    public function msSetOrderStatus($status_id)
    {
        $status = OrderStatuses::find()->where(['id' => $status_id])->one();
        if ($status) {
            $dataForSend = [
                'state' => $this->ms_get_meta(
                    'entity/customerorder/metadata/states/' . $status->ms_status_id,
                    'state'
                ),
            ];

            return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');
        }

        return false;
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function msEndOrder()
    {
        if (!Yii::$app->user->isGuest) {
            $this->sendNewOrderMail(); // отправка письма покупателю
        }

        $dataForSend = [
            'description' => $this->ms_get_order_desc(),
        ];

        return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');
    }

    public function ms_get_order_statuses()
    {
        $msStatuses = $this->ms_send_sync('entity/customerorder/metadata', null, $method = 'GET');

        $statuses = array();
        if (count($msStatuses->responseContent->states)) {
            $statuses = $msStatuses->responseContent->states;
        }

        return $statuses;
    }

    public function ms_get_order($ms_order_id)
    {
        $msStatuses = $this->ms_send_sync('entity/customerorder/' . $ms_order_id, null, $method = 'GET');

        $msOrder = array();
        if (isset($msStatuses->responseContent)) {
            $msOrder = $msStatuses->responseContent;
        }

        return $msOrder;
    }

    public function ms_get_pos($ms_order_id)
    {
        $msPos = $this->ms_send_sync('entity/customerorder/' . $ms_order_id . '/positions', null, $method = 'GET');

        $pos = array();
        if (isset($msPos->responseContent->rows)) {
            $pos = $msPos->responseContent->rows;
        }

        return $pos;
    }

    public function ms_get_hooks()
    {
        $msHook = $this->ms_send_sync('entity/webhook/?limit=100', null, $method = 'GET');

        $hook = array();
        if (isset($msHook->responseContent->rows)) {
            $hook = $msHook->responseContent->rows;
        }

        return $hook;
    }

    public function ms_update_hook_url($ms_hook_id, $url)
    {
        //old url: https://service.modulpos.ru/api/v1/moysklad/webhook/a9cfa490-f379-46cb-8143-4ee73ae35246
        $dataForSend = [
            //'url' => 'https://new.tattoofeel.ru/api/web/v1/hook/moysklad/product/update'
            'url' => $url
        ];

        return $this->ms_send_sync('entity/webhook/' . $ms_hook_id, $dataForSend, $method = 'PUT');
    }

    public function ms_get_entity($ms_entity_id)
    {
        $msEntity = $this->ms_send_sync('entity/customentity/' . $ms_entity_id, null, $method = 'GET');

        $entity = array();
        if (isset($msEntity->responseContent)) {
            $entity = $msEntity->responseContent;
        }

        return $entity;
    }

    public function ms_get_product($ms_product_id)
    {
        $msProduct = $this->ms_send_sync('entity/product/' . $ms_product_id, null, $method = 'GET');

        $product = array();
        if (isset($msProduct->responseContent)) {
            $product = $msProduct->responseContent;
        }

        return $product;
    }

    public function ms_get_country($ms_country_id)
    {
        $msCountry = $this->ms_send_sync('entity/country/' . $ms_country_id, null, $method = 'GET');

        $country = array();
        if (isset($msCountry->responseContent)) {
            $country = $msCountry->responseContent;
        }

        return $country;
    }

    public function checkAddDayToDelivery()
    {
        foreach ($this->linkProducts as $l) {
            if (!$l->product->is_fixed_amount) {
                continue;
            }

            $l->product->ms_send_sync_stock_new();

            if ($l->product->amount > 0) {
                return 1;
            }
        }

        return 0;
    }


    public function ms_get_client($ms_client_id)
    {
        $msClient = $this->ms_send_sync('entity/counterparty/' . $ms_client_id, null, $method = 'GET');

        $client = array();
        if (isset($msClient->responseContent)) {
            $client = $msClient->responseContent;
        }

        return $client;
    }

    public function ms_get_employee($ms_employee_id)
    {
        $msEmployee = $this->ms_send_sync('entity/employee/' . $ms_employee_id, null, $method = 'GET');

        $employee = array();
        if (isset($msEmployee->responseContent)) {
            $employee = $msEmployee->responseContent;
        }

        return $employee;
    }

    public function msStatusResponse()
    {
        return (object)[
            'status' => true,
            //1 ok,
            // -1 not create user,
            // -2 not create order,
            // -3 not get stock
            // -4 not check stock
            // -5 not reserve
            'statusCode' => 1,
            'order' => null,
            'msg' => null,
            'errors_qty' => null
        ];
    }

    public function check_stock($dataStock)
    {
        $result = [];

        $stock = [];

        if (isset($dataStock->rows[0]) && isset($dataStock->rows[0]->positions)) {
            foreach ($dataStock->rows[0]->positions as $p) {
                $arr = explode('/', $p->meta->href);
                $pid = array_pop($arr);
                $stock[$pid] = $p->quantity;
            }
        }

        foreach ($this->linkProducts as $l) {
            if ($l->product->is_fixed_amount) { // если товар с фиксированным кол-вом, то пропустить проверку на соответствие количества
                continue;
            }

            if (isset($stock[$l->product->ms_id]) && ($stock[$l->product->ms_id] < $l->count)) {
                $result[$l->product->ms_id] = $stock[$l->product->ms_id];
            }
        }

        return $result;
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function ms_reserve()
    {

        $dataForSend = [
            'description' => $this->ms_get_order_desc(),
            'positions' => $this->ms_get_positions(true)
        ];

        return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');

    }

    /**
     * @param $order_id
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function ms_get_stock($order_id)
    {
        return $this->ms_send_sync("report/stock/byoperation?operation.id=$order_id");
    }

    /**
     * @param $agentMeta
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function ms_create_order($agentMeta)
    {
        $dataForSend = [
            'name' => $this->ms_get_current_order_name(),
            'organization' => $this->ms_get_meta(
                'entity/organization/' . env('MS_ID_ORG_AT_BUY'),
                'organization'
            ),
            'agent' => $agentMeta,
            'store' => $this->ms_get_meta(
                'entity/store/' . self::MS_ID_ORDER_STORE_MAIN,
                'store'
            ),
            'state' => $this->ms_get_meta(
                'entity/customerorder/metadata/states/' . self::MS_ID_ORDER_STATE_NEW_ORDER,
                'state'
            ),
            'description' => $this->ms_get_order_desc(),
            'positions' => $this->ms_get_positions()
        ];

        $dataForSend = $this->ms_get_order_attributes($dataForSend);

        return $this->ms_send_sync('entity/customerorder', $dataForSend, $method = 'POST');
    }

    protected function ms_get_order_attributes($dataForSend)
    {
        $deliveryService = $this->delivery_service_id ? DeliveryServices::find()->where(['id' => $this->delivery_service_id])->one() : null;
        if ($deliveryService) {
            if ($deliveryService->ms_id) {
                $dataForSend['attributes'][] = json_decode('{
                    "meta": {
                        "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/782bc080-292f-11e4-d7fd-002590a28eca",
                        "type": "attributemetadata",
                        "mediaType": "application/json"
                    },
                    "type": "customentity",
                    "value": {
                        "meta": {
                            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/75339beb-292f-11e4-50c8-002590a28eca/'.$deliveryService->ms_id.'",
                            "type": "customentity",
                            "mediaType": "application/json"
                        }
                    }
                }', true);
            }
        }

        if (!$this->phone) {
            $phone = $this->user->profile->phone;
        } else {
            $phone = $this->phone; // для клиентов CRM
        }

        if ($phone) {
            if (substr($phone, 0, 3) == '007') {
                $phone = substr_replace($phone, "8", 0, 3);
            } elseif (substr($phone, 0, 2) == '+7') {
                $phone = substr_replace($phone, "8", 0, 2);
            } elseif (substr($phone, 0, 1) == '7') {
                $phone = substr_replace($phone, "8", 0, 1);
            }
            $phone = preg_replace('/[^+0-9]/', '', $phone);
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SMS_NUMBER,
                'value' => $phone,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SMS_NUMBER,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        }

        $tariffSdek = $this->tariff_sdek ? TariffSdek::find()->where(['id' => $this->tariff_sdek])->one() : null;
        if ($tariffSdek) {
            if ($tariffSdek->ms_id) {
                $dataForSend['attributes'][] = json_decode('{
                    "meta": {
                        "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/'.self::MS_ID_ORDER_ATTR_TARIFF_SDEK.'",
                        "type": "attributemetadata",
                        "mediaType": "application/json"
                    },
                    "type": "customentity",
                    "value": {
                        "meta": {
                            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/5a8594ec-9c05-11ea-0a80-016f00040ef5/'.$tariffSdek->ms_id.'",
                            "type": "customentity",
                            "mediaType": "application/json"
                        }
                    }
                }', true);
            }
        }

        // tomorrow_5150c2e4-7693-11e7-7a6c-d2a90020a3c6_bc1a4d6f-39c9-11e4-56bb-002590a28eca
        if ($this->courier_time_interval) {
            $arTime = explode('_', $this->courier_time_interval);
            if (count($arTime) == 3) {
                $date = '';
                switch ($arTime[0]) {
                    case 'today':
                        $date = date('Y-m-d 11:11:00');
                        break;
                    case 'tomorrow':
                        $date = date("Y-m-d 11:11:00", time() + 86400);
                        break;
                }

                if ($date) {
                    $dataForSend['attributes'][] = [
                        'id' => self::MS_ID_ORDER_ATTR_DELIVERY_DATE,
                        'value' => $date,
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_DELIVERY_DATE,
                            "type" => "attributemetadata",
                    "mediaType" => "application/json",
                        ],
                    ];
                }

                if ($arTime[1]) {
                    $dataForSend['attributes'][] = json_decode('{
                        "meta": {
                            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/e9e3a205-2eb0-11e4-6982-002590a28eca",
                            "type": "attributemetadata",
                            "mediaType": "application/json"
                        },
                        "type": "customentity",
                        "value": {
                            "meta": {
                                "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/d6411509-2eb0-11e4-e228-002590a28eca/'.$arTime[1].'",
                                "type": "customentity",
                                "mediaType": "application/json"
                            }
                        }
                    }', true);
                }

                if ($arTime[2]) {
                    $dataForSend['attributes'][] = json_decode('{
                        "meta": {
                            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/e9e3a516-2eb0-11e4-945d-002590a28eca",
                            "type": "attributemetadata",
                            "mediaType": "application/json"
                        },
                        "type": "customentity",
                        "value": {
                            "meta": {
                                "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/e9e3a516-2eb0-11e4-945d-002590a28eca/'.$arTime[2].'",
                                "type": "customentity",
                                "mediaType": "application/json"
                            }
                        }
                    }', true);
                }
            }
        }

        if ($this->pvz_code) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_PVZ_CODE,
                'value' => $this->pvz_code,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_PVZ_CODE,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        }

//        $vxr = fopen('/home/tfeel/web/tfeel.101bot.ru/public_html/slog.log', 'w');
//        fputs($vxr, print_r(array(
//            'datetime' => date("Y-m-d H:i:s"),
//            '$this->delivery_city' => $this->delivery_city,
//        ), true).PHP_EOL);
        if ($this->delivery_city) {
            $delivery_city = DeliveryCity::findOne(['city_full' => $this->delivery_city]);
            if ($delivery_city->ms_id) {
                $dataForSend['attributes'][] = json_decode('{
                    "meta": {
                        "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/10606014-2931-11e4-686d-002590a28eca",
                        "type": "attributemetadata",
                        "mediaType": "application/json"
                    },
                    "type": "customentity",
                    "value": {
                        "meta": {
                            "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/0f0470d8-2931-11e4-ae32-002590a28eca/'.$delivery_city->ms_id.'",
                            "type": "customentity",
                            "mediaType": "application/json"
                        }
                    }
                }', true);
            }
        }

        if (self::PAYMENT_TYPE_CASH == $this->payment_type && self::DELIVERY_SERVICE_SDEK == $this->delivery_service_id) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TRANSPORT_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CASH == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_TINKOFF == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_TINKOFF_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CHECKING == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CHECKING_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        } elseif (self::PAYMENT_TYPE_CARD_2_CARD == $this->payment_type) {
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_CASH_CARD_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
            $dataForSend['attributes'][] = [
                'id' => self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                'value' => true,
                "meta" => [
                    "href" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/".self::MS_ID_ORDER_ATTR_SET_TO_ZERO_PAYMENT,
                    "type" => "attributemetadata",
                    "mediaType" => "application/json",
                ],
            ];
        }

        if ($this->places_count == 1) {
            $places_count_id = self::MS_ID_VALUE_PLACES_COUNT_1;
        } elseif ($this->places_count == 2) {
            $places_count_id = self::MS_ID_VALUE_PLACES_COUNT_2;
        }
        if (isset($places_count_id)) {
            $dataForSend['attributes'][] = json_decode('{
                "meta": {
                    "href": "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes/3d5c75b1-522f-11ea-0a80-01ba000552e4",
                    "type": "attributemetadata",
                    "mediaType": "application/json"
                },
                "type": "customentity",
                "value": {
                    "meta": {
                        "href": "https://api.moysklad.ru/api/remap/1.2/entity/customentity/'.self::MS_ID_CUSTOM_ENTITY_PLACES_COUNT.'/'.$places_count_id.'",
                        "type": "customentity",
                        "mediaType": "application/json"
                    }
                }
            }', true);
        }

//        if ($this->payment_type == 2 && $this->status_pay == self::STATUS_PAY_YES) {
//            $dataForSend['state'] = $this->ms_get_meta(
//            //'entity/customerorder/metadata/states/e79c652d-1d21-11eb-0a80-0033002ee41d', // e79c652d-1d21-11eb-0a80-0033002ee41d - uuid Tinkoff pay
//                'entity/customerorder/metadata/states/3039e25a-540b-11e6-7a69-8f5500107e15', // 3039e25a-540b-11e6-7a69-8f5500107e15 - uuid Новый заказ
//                'state'
//            );
//        }

//        $flog = fopen(dirname(__FILE__) . '/logs/send_order_' . date("Ymd-His"), 'w');
//        fputs($flog, print_r($dataForSend, true));
//        fclose($flog);

        //return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');
        return $dataForSend;
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function ms_update_order()
    {
        $dataForSend = [
            'description' => $this->ms_get_order_desc(),
            'positions' => $this->ms_get_positions()
        ];

        $dataForSend = $this->ms_get_order_attributes($dataForSend);

        return $this->ms_send_sync('entity/customerorder/' . $this->order_ms_id, $dataForSend, $method = 'PUT');
    }

    protected function ms_get_current_order_name()
    {
        return date('my_d_') . rand(1000, 9999);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function ms_get_order_desc()
    {
        if (!Yii::$app->user->isGuest) {
            $desc = [
                "Адрес доставки: " . $this->address_delivery,
                //"Стоимость доставки: " . $this->get_sum_delivery(),
                "Стоимость доставки: " . number_format(round($this->sum_delivery / 100), 0, ',', ' ') . " руб",
                "Комиссия: " . number_format(round($this->commissionSum / 100), 0, ',', ' ') . " руб",
                "Комментарий: " . $this->comment,
                "Оплата: " . (empty($this->date_pay) ? 'не оплачен' : Yii::$app->formatter->asDatetime($this->date_pay)),
                "Ид. платежа: " . (empty($this->pay_id) ? 'не оплачен' : $this->pay_id),
            ];

            if (!empty($this->address_delivery_pvz)) {
                $desc[] = "Адрес ПВЗ: " . $this->address_delivery_pvz;
            }

            if (!empty($this->date)) {
                $desc[] = "Дата доставки: " . Yii::$app->formatter->asDatetime($this->date);
                $desc[] = "Крайняя дата доставки: " . Yii::$app->formatter->asDatetime($this->date + ($this->delivery_days * 24 * 60 * 60));
            }

            if ($this->coupon_id) {
                $coupon = Coupons::findOne($this->coupon_id);
                if ($coupon) {
                    $desc[] = "Применен промокод \"{$coupon->coupon_code}\": скидка {$coupon->coupon_value}" . ($coupon->is_percent ? '%' : ' руб');
                }
            }
        } else {
            $desc = [
                "Заказ оформлен неавторизованным пользователем через форму 'Купить в один клик'",
                "Контактный телефон: " . $this->phone,
                "Контактное лицо: " . $this->comment,
            ];
        }

        return implode(" || ", $desc);
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected function ms_create_client()
    {
        $phone = $this->user->profile->phone;
        if ($phone) {
            if (substr($phone, 0, 3) == '007') {
                $phone = substr_replace($phone, "8", 0, 3);
            } elseif (substr($phone, 0, 2) == '+7') {
                $phone = substr_replace($phone, "8", 0, 2);
            } elseif (substr($phone, 0, 1) == '7') {
                $phone = substr_replace($phone, "8", 0, 1);
            }
            $phone = preg_replace('/[^+0-9]/', '', $phone);
        }

        $fax = $this->user->profile->phone_1;
        if ($fax) {
            if (substr($fax, 0, 3) == '007') {
                $fax = substr_replace($fax, "8", 0, 3);
            } elseif (substr($fax, 0, 2) == '+7') {
                $fax = substr_replace($fax, "8", 0, 2);
            } elseif (substr($fax, 0, 1) == '7') {
                $fax = substr_replace($fax, "8", 0, 1);
            }
            $fax = preg_replace('/[^+0-9]/', '', $fax);
        }

        return $this->ms_send_sync(
            'entity/counterparty',
            [
                "name" => $this->user->profile->full_name ?: '',
                "description" => "Регистрация через сайт",
                "actualAddress" => $this->user->profile->address_delivery ?: '',
                "email" => $this->user->email ?: '',
                "phone" => $phone ?: '',
                "fax" => $fax ?: '',
                "attributes" => [
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_FIO'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "ФИО на сайте",
                        "type" => "string",
                        "value" => $this->user->profile->full_name
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_VCONTACT'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Vkontakte",
                        "type" => "link",
                        "value" => $this->user->profile->link_vk
                    ],
                    [
                        "id" => env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                        "meta" => [
                            "href" => "https://api.moysklad.ru/api/remap/1.2/entity/counterparty/metadata/attributes/".env('MS_ID_CUSTOM_COUNTERPARTY_INSTA'),
                            "type" => "attributemetadata",
                            "mediaType" => "application/json"
                        ],
                        "name" => "Instagram",
                        "type" => "link",
                        "value" => $this->user->profile->link_inst
                    ],
                ]
            ],
            'POST'
        );
    }

    /**
     * @param $url
     * @param null $data
     * @param string $method
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    protected function ms_send_sync($url, $data = null, $method = 'GET')
    {
        _::step('ms', __FUNCTION__, '=====');
        _::value('ms', __FUNCTION__, 'url', $method .' '. $url);

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

        if ($data) {
            $request->setData($data);
        }

        _::step('ms', __FUNCTION__, 'send');

        $response = $request->send();
        $result->response = $response;

        _::step('ms', __FUNCTION__, 'response');
        _::value('ms', __FUNCTION__, 'isOk', $response->isOk);

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;

            _::value('ms', __FUNCTION__, 'send data', $data);
            _::value('ms', __FUNCTION__, 'response', $result->msg);
            _::step('ms', __FUNCTION__, 'fail');

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

                    _::value('ms', __FUNCTION__, 'send data', $data);
                    _::value('ms', __FUNCTION__, 'response', $responseContent);
                    _::value('ms', __FUNCTION__, 'error', $result->msg);
                    _::step('ms', __FUNCTION__, 'fail');

                    return $result;
                }
            }

            _::step('ms', __FUNCTION__, 'success');

            return $result;

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;

            _::step('ms', __FUNCTION__, 'fail');

            return $result;
        }
    }

    protected function ms_get_positions($reserve = false)
    {
        $positions = [];

        // Сортировка по артикулу (по возрастанию)
        $arr = $this->linkProducts;
        usort($arr, function ($a, $b) {
            if ($a->product->article == $b->product->article) {
                return 0;
            }
            return ($a->product->article < $b->product->article) ? -1 : 1;
        });

        $percent = 0;
        $discount_products = array();
        $discount_product_ids = array();
        if ($reserve) {
            $coupon = null;

            if ($this->coupon_id) {
                $coupon = Coupons::findOne($this->coupon_id);

                if ($coupon) {
                    $result = $coupon->resultApply($this);

                    if ($result['status']) {
                        $percent = $result['percent'];
                        $discount_products = $result['discountProducts'];
                        $discount_product_ids = array_keys($result['discountProducts']);
                    }
                }
            }// elseif ()
        }

        foreach ($arr as $l) {
            if ($reserve) {
                $item = [
                    "reserve" => $l->count,
                    "price" => $l->price,
                    "discount" => $l->crm_percent_discount, // обработка прилетевших позиций из CRM
                ];
            }

            if (in_array($l->product_id, $discount_product_ids)) {
                //$item['discount'] = $percent;
                $item['discount'] = $discount_products[$l->product_id]['percentByPos'];
            }

            $item["assortment"] = $this->ms_get_meta(
                "entity/product/{$l->product->ms_id}",
                "product"
            );
            $item['quantity'] = $l->count;

            $positions[] = $item;
        }

        // Позиция с доставкой
        if ((int)$this->sum_delivery > 0) {
            if ($this->commission) {
                $item = [
                    "price" => (int)$this->sum_delivery + (int)$this->commissionSum,
                ];
            } else {
                $item = [
                    "price" => (int)$this->sum_delivery,
                ];
            }

            if ($percent) {
                $item['discount'] = $percent;
            }

            $item["assortment"] = $this->ms_get_meta(
                "entity/service/" . self::MS_ID_POSITION_DELIVERY_SERVICE,
                "service"
            );
            $item['quantity'] = 1;

            $positions[] = $item;
        }

        return $positions;
    }

    public function ms_get_meta($hrefUrl, $type)
    {
        return [
            "meta" => [
                "href" => "https://api.moysklad.ru/api/remap/1.2/$hrefUrl",
                "type" => $type,
                "mediaType" => "application/json",
            ]
        ];
    }

    public function sync_product_stock($dataStock)
    {
        $stock = [];

        if (isset($dataStock->rows[0]) && isset($dataStock->rows[0]->positions)) {
            foreach ($dataStock->rows[0]->positions as $p) {
                $arr = explode('/', $p->meta->href);
                $pid = array_pop($arr);
                $stock[$pid] = array(
                    'stock' => $p->stock ?: 0,
                    'inTransit' => $p->inTransit ?: 0,
                    'reserve' => $p->reserve ?: 0,
                    'quantity' => $p->quantity ?: 0,
                );
            }
        }

        foreach ($this->linkProducts as $l) {
            if (isset($stock[$l->product->ms_id])) {
                $pm = Product::find()->where(['ms_id' => $l->product->ms_id])->one();
                if ($pm->is_fixed_amount) {
                    $pm->amount = 100;
                } else {
                    $pm->amount = (int)$stock[$l->product->ms_id]['stock'] - (int)$stock[$l->product->ms_id]['reserve']; // остаток товара в МС минус резерв товара в МС
                }
                $pm->save(false);
            }
        }

    }

    /**
     * Собираем всю инфу о доставке в кучу.(Временно. Пока клиент не определит как что и куда писать/отправлять)
     * @return string
     */
    public function setDeliveryInfo()
    {
        $delivery = new Delivery();

        if (@$_COOKIE['debug_log']) {
            //$flog = fopen(dirname(__FILE__) . '/logs/delivery_' . date("Ymd-His"), 'w');
            $arDelivery = array(
                'delivery_type' => $this->delivery_type,
                'delivery_type_name' => $this->getDeliveryTypes()[$this->delivery_type],
                'delivery_service' => $this->delivery_service,
                'delivery_service_name' => $delivery->getDeliveryServicesName()[$this->delivery_service],
                'delivery_city' => $this->delivery_city,
                'delivery_tariff_name' => $this->delivery_tariff_name,
                'pvz_code' => $this->pvz_code,
                'address_delivery_pvz' => $this->address_delivery_pvz,
                'pvz_info' => $this->pvz_info,
                'total_weight' => $delivery->getTotalWeight(),
                'delivery_period_max' => $this->delivery_period_max,
                'sum_delivery' => $this->sum_delivery,
                'payment_type' => $this->payment_type,
                'courier_time_interval' => $this->courier_time_interval,
            );
//            fputs($flog, "arrOrder: ".print_r($arDelivery, true));
//            fclose($flog);
        }

        $this->sum_delivery = !empty($this->sum_delivery) ? $this->sum_delivery : null;
        $this->delivery_type = $this->delivery_type ? $this->delivery_type : null;
        $this->pvz_code = !empty($this->pvz_code) ? $this->pvz_code : null;
        $this->pvz_info = !empty($this->pvz_info) ? $this->pvz_info : null;

        if ('iml' == $this->delivery_service) {
            if (1 == $this->delivery_type) {
                $this->delivery_service = 'iml_pickup';
            } elseif (2 == $this->delivery_type) {
                $this->delivery_service = 'iml_courier';
            }
        }

        $deliveryService = DeliveryServices::find()
            ->cache(7200000)
            ->where(['code' => $this->delivery_service])
            ->one();
        $this->delivery_service_id = $deliveryService ? $deliveryService->id : null;
        $tariffSdek = TariffSdek::find()
            ->cache(7200)
            ->where(['title' => $this->delivery_tariff_name, 'delivery_type' => $this->delivery_type])
            ->one();
        $this->tariff_sdek = $tariffSdek ? $tariffSdek->id : null;
        $this->delivery_city = !empty($this->delivery_city) ? $this->delivery_city : null;
        $this->courier_time_interval = !empty($this->courier_time_interval) ? $this->courier_time_interval : null;
        $this->delivery_days = isset($this->delivery_period_max) ? intval($this->delivery_period_max) : 0;
    }

    private function getDeliveryTypes()
    {
        return [
            self::DELIVERY_TYPE_PICKUP => 'Самовывоз',
            self::DELIVERY_TYPE_COURIER => 'Курьер',
            self::DELIVERY_TYPE_PICKUP_FROM_WAREHOUSE => 'Самовывоз со склада',
        ];
    }

    /**
     * Получает общий вес товаров
     * @return float
     */
    public function getTotalWeight()
    {
        $total_weight = 0; //В граммах
        $goods = $this->getGoods();
        if ($goods) {
            foreach ($goods as $good) {
                $total_weight += ($good['weight'] * 1000);
            }

            $total_weight = round((float)$total_weight / 1000, 3);
        }

        return $total_weight;
    }

    /**
     * Возвращает список товаров
     * @return array
     */
    private function getGoods()
    {
        $goods = [];
        $i = 0;
        /** @var UserClientOrder_Product $l */
        foreach ($this->linkProducts as $l) {
            $length = $l->product->length;
            $width = $l->product->width;
            $height = $l->product->height;

            $goods[$i] = [
                'weight' => (float)!empty($l->product->weight) ? round($l->product->weight, 3) : 0.1,
                'length' => (int)!empty($l->product->length) ? $length : 10,
                'width' => (int)!empty($l->product->width) ? $width : 10,
                'height' => (int)!empty($l->product->height) ? $height : 10
            ];
            $goods[$i]['volume'] = round($length * $width * $height, 3);
            $goods[$i]['volume_weight'] = round(($length * $width * $height) / 5000, 3); //Объемный вес
            $i++;
        }

        return $goods;
    }

    public function sendNewOrderMail()
    {
        $user_email = $this->user->email;

        $product_list = '';
        foreach ($this->linkProducts as $lp) {
            $product_list .= '<tr><td><img style="width: 50px" width="50" src="' . Url::to($lp->product->getImgUrl()) . '"></td><td>' . $lp->product->title . '</td><td>' . $lp->count . '</td><td>' . number_format(($lp->is_gift ? 0 : $lp->product->clientPriceValue) / 100, 0, '.', ' ') . ' руб.</td></tr>';
        }

        $product_list = '<table>' . $product_list . '</table>';

        $total_sum = (int)$this->sum_buy + (int)$this->sum_delivery + (int)$this->commissionSum - (int)$this->sum_discount - (int)$this->sum_delivery_discount;

        $body = EmailTemplate::render(EmailTemplate::NEW_ORDER_TEMPLATE, [
            'order_number' => $this->order_ms_number,
            'order_date' => Yii::$app->formatter->asDatetime($this->date ? $this->date : ($this->created_at ? $this->created_at : $this->client_created_at), 'php:d.m.Y H:i'),
            'sum_delivery' => number_format($this->sum_delivery / 100, 2, '.', ' '),
            'sum_buy' => number_format($this->sum_buy / 100, 2, '.', ' '),
            'commission' => $this->commission,
            'total_sum' => number_format(($total_sum) / 100, 2, '.', ' '),
            'sum_commission' => number_format($this->commissionSum / 100, 2, '.', ' '),
            'delivery_service' => $this->deliveryService->title,
            'payment_type' => $this->payment->title,
            'product_list' => $product_list,
        ]);

        return Yii::$app->mailer->compose()
            ->setTo($user_email)
            //->setTo('medvedgreez@yandex.ru')
            ->setFrom(env('ROBOT_EMAIL'))
            //->setReplyTo([$this->email => $this->name])
            ->setSubject('Tattoofeel: новый заказ')
            //->setTextBody($body)
            ->setHtmlBody($body)
            ->send();
    }

    public static function getCartInfoMessage($cart) {
        if (Yii::$app->user->isGuest) {
            return '';
        }
        if (!$cart) {
            $cart = Yii::$app->client->identity->getCart();
        }
        /** @var $cart UserClientOrder */
        $lps = $cart->linkProducts;
        /** @var $lps UserClientOrder_Product[] */
        $deficitProducts = $affectedProducts = [];
        foreach ($lps as $key => $lp) {
            if ($lp->getCount() > $lp->product->amount) {
                $deficitProducts[] = sprintf('%s -> %d шт.',
                    $lp->product->title,
                    $lp->count - $lp->product->amount
                );
                $lp->count = $lp->product->amount;
                if ($lp->count > 0) {
                    $lp->save();
                }
                $affectedProducts[] = sprintf('%s -> %d шт.', $lp->product->title, $lp->count);
            }
            if ($lp->count <= 0) {
                $lp->delete();
            }
        }
        $cartInfoMessage = '';
        if ($affectedProducts) {
            $cart->comment = sprintf(
                "Мне не хватило следующих позиций:\n%s",
                implode("\n", $deficitProducts)
            );
            $cart->save(true, ['comment']);
            $cartInfoMessage = implode("<br/>", $affectedProducts);
        }
        return $cartInfoMessage;
    }
}
