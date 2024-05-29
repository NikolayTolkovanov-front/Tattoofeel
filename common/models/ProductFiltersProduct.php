<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_filters_product".
 *
 * @property integer         $id
 * @property integer         $product_id
 * @property integer         $product_filters_id
 */
class ProductFiltersProduct extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_filters_product}}';
    }
}
