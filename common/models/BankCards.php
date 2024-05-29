<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

class BankCards extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bank_cards}}';
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
            [['sort', 'number', 'owner'], 'required'],
            [['number', 'owner'], 'string', 'max' => 255],
            [['text'], 'string'],
            [['sort', 'is_actual'], 'integer'],
            [['is_actual'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'sort' => t_b('Порядковый номер'),
            'number' => t_b('Номер карты'),
            'owner' => t_b('Владелец'),
            'text' => t_b('Текст'),
            'is_actual' => t_b('Актуальная'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    public function beforeSave($insert) {
        if ($this->is_actual) {
            $actualCard = BankCards::find()->andWhere(['is_actual' => 1])->one();
            if ($actualCard) {
                $actualCard->is_actual = 0;
                $actualCard->save();
            }
        }

        return parent::beforeSave($insert);
    }
}
