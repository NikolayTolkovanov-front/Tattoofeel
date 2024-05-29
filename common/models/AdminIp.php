<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admin_ip".
 *
 * @property integer         $id
 * @property string          $ip_address
 * @property string          $comment
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $created_at
 * @property integer         $updated_at
 */
class AdminIp extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_ip}}';
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
            [['ip_address'], 'required'],
            [['ip_address'], 'unique'],
            [['ip_address', 'comment'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'ip_address' => t_b('IP адрес'),
            'comment' => t_b('Комментарий'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }
}
