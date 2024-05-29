<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sync_logs".
 *
 * @property integer         $id
 * @property integer         $created_at
 * @property string          $entity_type
 * @property string          $event_type
 * @property string          $ms_id
 * @property integer         $entity_id
 * @property integer         $is_success
 */
class SyncLogs extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sync_logs}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            //TimestampBehavior::class,
            //'blameable' => BlameableBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'entity_type', 'event_type'], 'required'],
            [['created_at', 'entity_id', 'is_success'], 'integer'],
            [['entity_type'], 'string', 'max' => 100],
            [['event_type', 'ms_id'], 'string', 'max' => 40],
            [['is_success'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'created_at' => t_b('Дата и время'),
            'entity_type' => t_b('Тип сущности'),
            'event_type' => t_b('Тип события'),
            'ms_id' => t_b('Ид. в МС'),
            'entity_id' => t_b('Ид. в БД'),
            'is_success' => t_b('Результат'),
        ];
    }
}
