<?php

namespace common\models;

use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property integer             $id
 * @property integer             $author
 * @property string              $products
 * @property string              $error
 * @property integer             $date
 * @property integer             $status
 */
class ProductSync extends ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR   = 0;
    const STATUS_PARTIALLY = 2;

    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_sync}}';
    }

    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_SUCCESS => t_b('Успешно'),
            self::STATUS_ERROR => t_b('Не успешно'),
            self::STATUS_PARTIALLY => t_b('Успешно частично'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => 'date'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['products','error'], 'string'],
            [['status','date','author'], 'integer'],
            [['date'], 'default', 'value' => function () {
                return date(DATE_ISO8601);
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'author' => t_b('Кто запустил'),
            'date' => t_b('Дата/время'),
            'products' => t_b('Список продуктов'),
            'error' => t_b('Ошибка'),
            'status' => t_b('Успешно если взведено'),
        ];
    }

}
