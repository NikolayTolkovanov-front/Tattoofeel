<?php

namespace common\models;

use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

class ProductFiltersCategory extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;

    public $visible_in_menu;

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
        return '{{%product_filters_category}}';
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
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
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
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(ProductFilters::class, ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPubFilters()
    {
        return $this->hasMany(ProductFilters::class, ['category_id' => 'id'])
            ->andWhere([ProductFilters::tableName().'.status' => ProductFilters::STATUS_PUBLISHED])
            ->innerJoin('{{%product_filters_product}}',
                "{{%product_filters_product}}.product_filters_id = ". ProductFilters::tableName() .".id")
            ->innerJoin(Product::tableName(),
                Product::tableName() .".id = {{%product_filters_product}}.product_id")
            ->andWhere(Product::queryPublished());
    }

    public function getPubFiltersByCatId($cat_ms_id)
    {
        return $this->hasMany(ProductFilters::class, ['category_id' => 'id'])
            ->andWhere([ProductFilters::tableName().'.status' => ProductFilters::STATUS_PUBLISHED])
            ->innerJoin('{{%product_filters_product}}',
                "{{%product_filters_product}}.product_filters_id = ". ProductFilters::tableName() .".id")
            ->innerJoin(Product::tableName(),
                Product::tableName() .".id = {{%product_filters_product}}.product_id")
            ->andWhere([Product::tableName() .".category_ms_id" => $cat_ms_id])
            ->andWhere(Product::queryPublished())
            ->orderBy([ProductFilters::tableName().'.sort' => SORT_ASC])
            ->all();
    }
}
