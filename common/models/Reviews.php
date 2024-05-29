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
 * @property integer         $user_client_id
 * @property integer         $product_id
 * @property integer         $is_published
 * @property double          $rating
 * @property integer         $date
 * @property string          $text
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $created_at
 * @property integer         $updated_at
 */
class Reviews extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reviews}}';
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
            [['user_client_id', 'product_id', 'rating', 'text'], 'required'],
            [['user_client_id', 'product_id', 'is_published'], 'integer'],
            [['is_published'], 'default', 'value' => 0],
            [['date'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['rating'], 'double', 'min' => 1.0, 'max' => 5.0],
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
            'user_client_id' => t_b('Пользователь'),
            'product_id' => t_b('Товар'),
            'is_published' => t_b('Публиковать'),
            'rating' => t_b('Рейтинг'),
            'date' => t_b('Дата'),
            'text' => t_b('Текст отзыва'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserClient()
    {
        return $this->hasOne(UserClient::class,
            ['id' => 'user_client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class,
            ['id' => 'product_id']);
    }
}
