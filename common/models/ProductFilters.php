<?php

namespace common\models;

use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

class ProductFilters extends ActiveRecord
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
        return '{{%product_filters}}';
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
            [['title'], 'required'],
            [['slug'], 'unique'],
            [['slug'], 'unique'],
            [['category_id', 'status', 'sort'], 'integer'],
            [['title','slug'], 'string', 'max' => 128],
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
            'status' => t_b('Публиковать на фронте'),
            'category_id' => t_b('Категория'),
            'sort' => t_b('Сортировка'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        //return $this->hasMany(Product::class, ['type_eq' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProductFiltersCategory::class, ['id' => 'category_id']);
    }

}
