<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 *@property int $count
 *@property int $price
 *@property double $crm_percent_discount
 *
 *@property Product $product
 */

class UserClientOrder_Product extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client_order__product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','product_id','count','price'], 'required'],
            [['order_id','product_id','count','price'], 'integer'],
            [['currency_iso_code'], 'string'],
            [['crm_percent_discount'], 'double'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->preparePrice();
    }

    /**
     * кол-во продуктов в корзине
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->count;
    }

}
