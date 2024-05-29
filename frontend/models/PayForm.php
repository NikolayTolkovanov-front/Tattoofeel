<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PayForm extends Model
{
    public $order_id;
    public $payment_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['order_id', 'payment_type'], 'required'],
            [['order_id', 'payment_type'], 'integer'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'ID заказа',
            'payment_type' => 'Способ оплаты',
        ];
    }
}
