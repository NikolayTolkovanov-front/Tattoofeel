<?php

namespace common\models;

use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_price_template".
 *
 * @property integer         $id
 * @property string          $title
 * @property integer             $created_by
 * @property integer             $updated_by
 * @property integer             $created_at
 * @property integer             $updated_at
 * @property integer             $product_id
 *
 * @property User            $author
 * @property User            $updater
 */
class ProductPriceTemplate extends ActiveRecord
{

    //розничная цена
    const TEMPLATE_ID_DEFAULT = 1;
    //скидка общая
    const TEMPLATE_ID_SALE = 11;

    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_price_template}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'immutable' => true,
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string'],
            [['title'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'title' => t_b('Название скидка'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено')
        ];
    }
}
