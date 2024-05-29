<?php

namespace common\models;

use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

class TariffSdek extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tariff_sdek}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
            'sluggable' =>
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'immutable' => true,
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ms_id', 'title'], 'required'],
            [['ms_id', 'title', 'ms_title'], 'string', 'max' => 255],
            [['sdek_id', 'delivery_type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'ms_id' => t_b('Ид. в МС'),
            'title' => t_b('Название'),
            'sdek_id' => t_b('Ид. в СДЭК'),
            'ms_title' => t_b('Название в МС'),
            'delivery_type' => t_b('Тип доставки'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }
}
