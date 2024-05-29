<?php

namespace common\models;

use common\models\traits\BigImg;
use common\models\traits\Path;
use Yii;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%product_attachment}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $path
 * @property string $url
 * @property string $name
 * @property string $type
 * @property string $size
 * @property integer $order
 *
 * @property Product $product
 */
class ProductAttachment extends ActiveRecord
{
    use Path;
    use BigImg;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'path'], 'required'],
            [['product_id'], 'integer'],
            [['order'], 'min' => 0, 'max' => 255],
            [['size']],
            [['type', 'name'], 'string', 'max' => 32],
            [['path'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'product_id' => Yii::t('common', 'Product ID'),
            'base_url' => Yii::t('common', 'Base Url'),
            'path' => Yii::t('common', 'Path'),
            'size' => Yii::t('common', 'Size'),
            'order' => Yii::t('common', 'Order'),
            'type' => Yii::t('common', 'Type'),
            'name' => Yii::t('common', 'Name')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

}
