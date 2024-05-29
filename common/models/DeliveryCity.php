<?php

namespace common\models;

use yii\db\ActiveRecord;

class DeliveryCity extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%delivery_city}}';
    }
}
