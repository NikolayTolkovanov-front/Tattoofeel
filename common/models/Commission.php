<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use common\models\PaymentTypes;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "commission".
 *
 * @property integer         $id
 * @property integer         $payment_type_id
 * @property double          $percent
 * @property string          $discount_group
 * @property string          $text
 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 */
class Commission extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%commission}}';
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
            [['payment_type_id', 'percent', 'discount_group'], 'required'],
            [['payment_type_id'], 'integer'],
            [['percent'], 'double'],
            [['discount_group'], 'string', 'max' => 255],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'payment_type_id' => t_b('Способ оплаты'),
            'percent' => t_b('Комиссия'),
            'discount_group' => t_b('Для кого'),
            'text' => t_b('Текст'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    public function getPaymentType()
    {
        return $this->hasOne(PaymentTypes::class,
            ['id' => 'payment_type_id']);
    }
}
