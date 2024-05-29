<?php

namespace common\models;

use Yii;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "key_storage_item".
 *
 * @property integer $key
 * @property integer $value
 */
class KeyStorageItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%key_storage_item}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (empty($this->value))
            $this->value = 0;

        return parent::beforeSave($insert);

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key'], 'string', 'max' => 128],
            [['value', 'comment'], 'safe'],
            [['key'], 'unique'],
            [['value'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => Yii::t('common', 'Key'),
            'value' => Yii::t('common', 'Value'),
            'comment' => Yii::t('common', 'Comment'),
        ];
    }

    static function getKey($key) {
        $model = self::find()->cache(7200)->where(['key' => $key])->one();

        if (!$model) {
            $model = Yii::createObject(self::className());
            $model->key = $key;
            $model->save(false);
        }

        return $model;
    }
}
