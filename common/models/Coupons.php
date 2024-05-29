<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use frontend\models\Product;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "coupons".
 *
 * @property integer         $id
 * @property string          $coupon_code
 * @property integer         $active_until
 * @property integer         $uses_count
 * @property integer         $used_count
 * @property integer         $is_percent
 * @property integer         $is_one_user
 * @property integer         $is_one_product
 * @property double          $coupon_value
 * @property double          $order_sum_min
 * @property string          $client_groups
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $created_at
 * @property integer         $updated_at
 */
class Coupons extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%coupons}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coupon_code'], 'required'],
            [['coupon_code'], 'unique'],
            [['coupon_code'], 'string', 'max' => 191],
            //[['active_until'], 'date','format'=>'php:U'],
            //[['active_until'], 'filter','filter'=>'strtotime'],
            [['active_until'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['is_percent', 'is_one_user', 'is_one_product'], 'integer'],
            [['uses_count', 'used_count'], 'integer', 'min' => 0],
            [['coupon_value', 'order_sum_min'], 'double', 'min' => 0],
            [['client_groups'], 'string', 'max' => 255],
            [['uses_count', 'used_count', 'is_percent', 'is_one_user', 'is_one_product', 'order_sum_min'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'coupon_code' => t_b('Код купона'),
            'active_until' => t_b('Действителен до'),
            'uses_count' => t_b('Кол-во применений, раз'),
            'used_count' => t_b('Применен, раз'),
            'is_percent' => t_b('Скидка в процентах'),
            'is_one_user' => t_b('Скидка для каждого пользователя действует только 1 раз'),
            'is_one_product' => t_b('Скидка для каждого товара действует только 1 раз'),
            'coupon_value' => t_b('Значение скидки'),
            'order_sum_min' => t_b('Минимальная сумма заказа, руб.'),
            'client_groups' => t_b('Группы клиентов'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(UserClientOrder::class,
            ['coupon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getBrands() {
        return $this->hasMany(Brand::class, ['id' => 'brand_id'])
            ->viaTable('{{%coupon_brand}}', ['coupon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCategories() {
        return $this->hasMany(ProductCategory::class, ['id' => 'category_id'])
            ->viaTable('{{%coupon_category}}', ['coupon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProducts() {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('{{%coupon_product}}', ['coupon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProductsGifts() {
        return $this->hasMany(ProductGift::class, ['coupon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserClients() {
        return $this->hasMany(UserClient::class, ['id' => 'user_client_id'])
            ->viaTable('{{%coupon_user}}', ['coupon_id' => 'id']);
    }

    private function saveSumDiscount(&$cart, $sum_discount = null, $sum_delivery_discount = null)
    {
        if ($cart) {
            $productsGifts = ProductGift::find()->where([
                'coupon_id' => $this->id,
            ])->all();
            $cart->sum_discount = $sum_discount;
            $cart->sum_delivery_discount = $sum_delivery_discount;
            $cart->coupon_id = !($sum_discount || count($productsGifts)) ? null : $this->id;
            $cart->save(false);
        }
    }

    public function resultApply(&$cart)
    {
        $cp = $cart->linkProducts;
        if (empty($cp)) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Корзина с товарами пуста',
            ];
        }

        if ($this->active_until < time()) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Время действия промокода истекло',
            ];
        }

        if ($this->is_one_user) {
            $user = $this->getUserClients()->andWhere(['id' => Yii::$app->getUser()->getId()])->one();
            if (!is_null($user)) {
                return [
                    'status' => false,
                    'msg' => 'Данный купон можно применить только один раз',
                ];
            }
        }

        if ($this->uses_count <= $this->used_count) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Лимит применений промокода исчерпан',
            ];
        }

        $profile = Yii::$app->user->identity->userProfile;
        if (!$profile) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Отсутствует профиль для текущего аккаунта',
            ];
        }

        if ($profile->sale_ms_id === 'Скидка 4' || $profile->sale_ms_id === 'Скидка 5' || $profile->sale_ms_id === 'Скидка 6') {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Промокод не применим для текущего аккаунта',
            ];
        }

        $not_apply_brands = array();
        if ($profile->sale_brands) {
            foreach (json_decode($profile->sale_brands, true) as $key => $item) {
                if ($key === 'Скидка 4' || $key === 'Скидка 5' || $key === 'Скидка 6') {
                    $not_apply_brands[] = $item;
                }
            }

            if (!empty($not_apply_brands)) {
                $not_apply_brands = Brand::find()->where(['in', 'slug', $not_apply_brands])->indexBy('id')->column();
            }
        }

        $apply_ids_gifts = [];
        $productsGifts = ProductGift::find()->where([
            'coupon_id' => $this->id,
        ])->all();
        if (count($productsGifts)) {
            foreach ($productsGifts as $productsGift) {
                $insertPosition  = new UserClientOrder_Product();
                $insertPosition->order_id = $cart->id;
                $insertPosition->product_id = $productsGift->product_id;
                $insertPosition->price = 0;
                $insertPosition->is_gift = 1;
                $insertPosition->count = $productsGift->quantity;
                $insertPosition->currency_iso_code = Currency::DEFAULT_CART_PRICE_CUR_ISO;
                $insertPosition->save(false);
                $apply_ids_gifts[] = $productsGift->product_id;
            }
        }

        unset($cart->linkProducts);
        $cp = $cart->linkProducts;

        $cart_products = array();
        foreach ($cp as $item) {
            $cart_products[$item->product_id] = array(
                'product_id' => $item->product_id,
                'count' => $item->count,
                'price' => $item->price,
            );
        }

        $apply_categories = $this->getCategories()->indexBy('id')->column();
        $apply_brands = $this->getBrands()->indexBy('id')->column();
        $apply_products = $this->getProducts()->indexBy('id')->column();

        $apply_ids = Product::find()
            ->leftJoin(ProductCategory::tableName(), ProductCategory::tableName().'.ms_id = '. Product::tableName().'.category_ms_id')
            ->leftJoin(Brand::tableName(), Brand::tableName().'.slug = '. Product::tableName().'.brand_id')
            ->andWhere(['or',
                ['in', ProductCategory::tableName().'.id', $apply_categories],
                ['in', Brand::tableName().'.id', $apply_brands],
                ['in', Product::tableName().'.id', $apply_products],
            ])
            ->andWhere(['not in', Brand::tableName().'.id', $not_apply_brands])
            ->andWhere([Product::tableName().'.is_discount' => 0])
            ->andWhere([Product::tableName().'.is_super_price' => 0])
            //->all();
            ->indexBy(Product::tableName().'.id')->column();
        if (isset($apply_ids_gifts) && !empty($apply_ids_gifts)) {
            foreach ($apply_ids_gifts as $apply_ids_gift) {
                $apply_ids[] = $apply_ids_gift;
            }
        }
        //echo '<pre>';print_r($apply_ids);echo '</pre>';die('test');

        $sum_cart = 0;
        $discount_products = array();
        $discount_products_sum_cart = 0; // общая стоимость всех позиций товаров, попадающих под условие скидки
        foreach ($cart_products as $id => $product) {
            $sum = $product['price'] * $product['count'];
            $sum_cart += $sum;

            /*
            *  Сейчас процент скидки выставляется только позициям, попадающим под условия скидки, а не всем позициям в корзине. это правильно или нет? если нет,
            *  то надо в массив $discount_products "запихнуть" все id из корзины, а не только, попавшие в массив $apply_ids. Как вариант добавить в
            *  модель еще одно поле типа да/нет (галка в админке) и раскомментировать условие ниже + заменить apply_discount_all_pos на название поля
            */

            //if ($this->apply_discount_all_pos) {
            //    $discount_products[$id] = $product;
            //    $discount_products[$id]['sum'] = $sum;
            //    $discount_products_sum_cart += $sum;
            //} else {
            if (in_array($id, $apply_ids)) { // позиция товара в корзине подходит под условия действия купона (категория, бренд или id)
                $discount_products[$id] = $product;
                $discount_products[$id]['sum'] = $sum;
                $discount_products_sum_cart += $sum;
            }
            //}
        }

        //echo '<pre>';print_r($sum_cart);echo '</pre>';die('test');

        if (empty($discount_products) || !$sum_cart || !($discount_products_sum_cart || count($apply_ids_gifts))) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Промокод не применим к текущему составу товаров в корзине',
            ];
        }

        if ($this->order_sum_min * 100 > $sum_cart) {
            $this->saveSumDiscount($cart);
            return [
                'status' => false,
                'msg' => 'Стоимость товаров в корзине, попадающих под действие промокода, меньше '.$this->order_sum_min.' руб.',
            ];
        }

        //Все условия применения купона соблюдены
        $sum_delivery = $cart->sum_delivery ? $cart->sum_delivery : 0;
        if ($sum_delivery > 0) {
            if ($cart->commission) {
                $sum_delivery += (int)$cart->commissionSum;
            }
        }

        //$percent = $this->is_percent ? $this->coupon_value : ($this->coupon_value * 100) / ($sum_cart + $sum_delivery) * 100;
        $percent = $this->is_percent ? $this->coupon_value : ($this->coupon_value * 100) / ($discount_products_sum_cart + $sum_delivery) * 100;

        //$sum_discount = $this->is_percent ? 0 : $this->coupon_value * 100;
        $sum_discount = 0;
        //$arrTest = array();
        foreach ($discount_products as $id => $product) {
            if ($this->is_one_product) { // скидка действует только на 1 единицу товара, а не на все его кол-во
                if ($product['count'] > 1) {
                    $posSumDiscount = $product['price'] / 100 * $percent; // стоимость скидки
                    $posSum = $product['price'] * ($product['count'] - 1) + $posSumDiscount; // стоимость этой позиции со скидкой
                    $discount_products[$id]['percentByPos'] = 100 - ($posSum * 100 / $product['sum']); // процент по этой позиции для МС
                    $sum_discount += $posSumDiscount;
                } else {
                    $discount_products[$id]['percentByPos'] = $percent; // процент по этой позиции для МС
                    $sum_discount += $product['sum'] / 100 * $percent;
                }
            } else {
                $discount_products[$id]['percentByPos'] = $percent; // процент по этой позиции для МС
                $sum_discount += $product['sum'] / 100 * $percent;

                //$arrTest[$id] = array(
                //    'sum' => $product['sum'],
                //    'sum_discount' => $product['sum'] / 100 * $percent,
                //);
            }
        }
        $sum_delivery_discount = $sum_delivery / 100 * $percent;

        if ($sum_delivery_discount) {
            $this->saveSumDiscount($cart, round($sum_discount), round($sum_delivery_discount));
        } else {
            $this->saveSumDiscount($cart, round($sum_discount));
        }

        return [
            'status' => true,
            'sumWithoutDiscountFormat' => $cart->sumWithoutDiscountFormat,
            'msg' => (int)$cart->sumDiscountFormat ? ('Скидка составит ' . $cart->sumDiscountFormat) : 'Промокод применен',
            'percent' => $percent, // процент
            //'percentByPos' => $percent, // процент по позиции
            //'sum_cart' => $sum_cart, // общая стоимость товаров в корзине
            //'sum_delivery' => $sum_delivery, // стоимость доставки
            //'sum_discount' => $sum_discount, // сумма скидки
            //'test' => $arrTest,
            'discountProducts' => $discount_products, // массив товаров, к которым применяется скидка
        ];
    }

    public function getClientGroupsArray() {
        if (!$this->client_groups) {
            return [];
        }
        return explode(',', $this->client_groups);
    }
}
