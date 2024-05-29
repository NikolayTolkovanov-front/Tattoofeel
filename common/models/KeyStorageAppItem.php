<?php

namespace common\models;

/**
 * This is the model class for table "key_storage_app_item".
 *
 * @property integer $key
 * @property integer $value
 */
class KeyStorageAppItem extends KeyStorageItem
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%key_storage_app_item}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Ключ',
            'value' => 'Значение',
            'comment' => 'Описание',
        ];
    }

}
