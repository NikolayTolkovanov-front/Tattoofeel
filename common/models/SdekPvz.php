<?php

namespace common\models;

use yii\db\ActiveRecord;

class SdekPvz extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sdek_pvz}}';
    }
}
