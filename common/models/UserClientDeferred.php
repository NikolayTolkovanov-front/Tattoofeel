<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class UserClientDeferred extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const STATUS_STOCK_YES  = 1;
    const STATUS_STOCK_NO   = 0;

    /**
     * @return array statuses list
     */
    public static function statusesStock()
    {
        return [
            self::STATUS_STOCK_NO => t_b('нет в наличии'),
            self::STATUS_STOCK_YES => t_b('в наличии'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client_product_deferred}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return $this->ts_behavior();
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','product_id'], 'required'],
            [['user_id','product_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'user_id' => t_b('Ид. клиента'),
            'product_id' => t_b('Ид. продукт'),
            'created_at' => t_b( 'Создан (админ)'),
            'updated_at' => t_b( 'Обновлен (админ)'),
            'created_by' => t_b('Создал (админ)'),
            'updated_by' => t_b('Обновил (админ)'),
            'client_created_at' => t_b( 'Создан (клиент)'),
            'client_updated_at' => t_b( 'Обновлен (клиент)'),
            'client_created_by' => t_b('Создал (клиент)'),
            'client_updated_by' => t_b('Обновил (клиент)'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserClient::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getStatusStock() {
        return $this->getStatusStock()[
            (integer)(bool) Product::findOne($this->product_id)->amount
        ];
    }
}
