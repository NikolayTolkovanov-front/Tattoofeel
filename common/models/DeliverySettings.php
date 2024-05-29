<?php

namespace common\models;

use common\models\query\ArticleQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article".
 *
 * @property integer             $id
 * @property string              $label
 * @property string              $key
 * @property string              $value
 * @property string              $description
 *
 */
class DeliverySettings extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%delivery_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key', 'label'], 'unique'],
            [['label', 'key', 'value', 'description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'label' => Yii::t('common', 'Label'),
            'key' => Yii::t('common', 'Key'),
            'value' => Yii::t('common', 'Value'),
            'description' => Yii::t('common', 'Description'),
        ];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getValueByKey($key)
    {
        return self::findOne(['key' => $key])->value;
    }
}
