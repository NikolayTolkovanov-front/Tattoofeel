<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

class OrderStatuses extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_statuses}}';
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
            [['ms_status_id', 'title'], 'required'],
            [['ms_status_id', 'title', 'ms_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'ms_status_id' => t_b('Ид. в МС'),
            'title' => t_b('Название на сайте'),
            'ms_title' => t_b('Название в МС'),
            'desc' => t_b('Описание'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(UserClientOrder::class, ['payment_type' => 'id']);
    }

}
