<?php

namespace common\models;

use yii\db\ActiveRecord;

/*
 * @var $product_id
 * @var $quantity
 * @var $coupon_id
 */

class ProductGift extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_gift}}';
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'product' => Yii::t('common', 'Товар'),
            'coupon' => Yii::t('common', 'Купон'),
            'quantity' => Yii::t('common', 'Количество'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'coupon_id', 'quantity'], 'integer'],
        ];
    }

    public function getProduct() {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getCoupon() {
        return $this->hasOne(Coupons::class, ['id' => 'coupon_id']);
    }
}
