<?php

namespace common\models;

use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

class Question extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;


    /**
     * @return array statuses list
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => t_b('нет'),
            self::STATUS_PUBLISHED => t_b('да'),
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question}}';
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
            [['slug'], 'unique'],
            [['title','slug'], 'string', 'max' => 512],
            [['answer'], 'string'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'slug' => t_b('Чпу'),
            'title' => t_b('Название'),
            'status' => t_b('Публиковать'),
            'answer' => t_b('Короткое описание'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

}
