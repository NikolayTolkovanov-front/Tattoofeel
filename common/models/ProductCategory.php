<?php

namespace common\models;

use backend\components\sync\Sync;
use common\components\BlameableBehavior;
use common\models\query\ProductCategoryQuery;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_category".
 *
 * @property integer         $id
 * @property string          $slug
 * @property string          $ms_id
 * @property string          $title
 * @property string          $body_short
 * @property string          $error
 * @property integer         $status
 * @property integer         $disable_sync

 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 *
 *
 * @property Product[]       $products
 */
class ProductCategory extends ActiveRecord
{
    use \common\models\traits\BlameAble;
    use \common\models\traits\Img;
    use \common\models\traits\Icon;

    const SCENARIO_SYNC = 'sync';

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;

    const LAST_NESTED_SLUG_MARKER = 'filters';

    /**
     * @var array
     */
    public $thumbnail;
    public $icon;

    private static $cacheBySlug = [];
    private static $cacheByMsId = [];

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
        return '{{%product_category}}';
    }

    /**
     * @return ProductCategoryQuery
     */
    public static function find()
    {
        return new ProductCategoryQuery(get_called_class());
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
            [
                'class' => UploadBehavior::class,
                'attribute' => 'icon',
                'pathAttribute' => 'icon_path',
                'baseUrlAttribute' => null,
            ],
            [
                'class' => UploadBehavior::class,
                'attribute' => 'thumbnail',
                'pathAttribute' => 'thumbnail_path',
                'baseUrlAttribute' => null,
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'level'], 'integer'],
            [['title'], 'required', 'except' => self::SCENARIO_SYNC],
            [['slug'], 'unique', 'except' => self::SCENARIO_SYNC],
            [['ms_id'], 'required'],
            [['ms_id'], 'default', 'value' => function () {
                return "ts_".time();
            }],
            [['ms_id'], 'unique'],
            [['error'], 'string'],
            [['title','slug'], 'string', 'max' => 128],
            [['body_short'], 'string'],
            [['ms_id'], 'string', 'max' => 40],
            [['status','disable_sync'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['order'], 'integer'],

            [['thumbnail_path', 'icon_path'], 'string', 'max' => 128],
            [['thumbnail','icon'], 'safe'],
            [['small_amount','large_amount','avr_amount'], 'integer'],
            [['seo_title', 'seo_desc', 'seo_keywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'parent_id' => t_b('Родительская категория'),
            'slug' => t_b('Чпу'),
            'title' => t_b('Название'),
            'error' => t_b('Ошибки синх.'),
            'status' => t_b('Публиковать'),
            'disable_sync' => t_b('Отк. синх.'),
            'body_short' => t_b('Короткое описание'),
            'ms_id' => t_b('ИД МС категории'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'thumbnail' => t_b('Картинка (510x330)'),
            'icon' => t_b('Иконка'),
            'small_amount' => t_b('Мало'),
            'avr_amount' => t_b('Средне'),
            'large_amount' => t_b('Много'),
            'productFiltersCategories' => t_b('Фильтры для категории'),
            'order' => t_b('Сортировка'),
            'seo_title' => t_b('SEO Заголовок'),
            'seo_desc' => t_b('SEO Описание'),
            'seo_keywords' => t_b('SEO Ключевые слова'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'small_amount' => t_b('Для определения количества товаров'),
            'avr_amount' => t_b('Для определения количества товаров'),
            'large_amount' => t_b('Для определения количества товаров'),
            'productFiltersCategories' => t_b('Для выделения используются Ctrl и Shift, 
            значения выбираем в продукте на вкалдке фильтр'),
        ];
    }

    public function getTitleShort() {
        return mb_substr($this->title, 0, Product::SHORT_TITLE_LENGTH).
            (mb_strlen($this->title) > Product::SHORT_TITLE_LENGTH ? '...' : '');
    }

    public function getParent()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_ms_id' => 'ms_id']);
    }

    public function setProductFiltersCategories($value)
    {

        $curIds = ArrayHelper::getColumn($this->getProductFiltersCategories()->select(['id'])->asArray()->all(), 'id');

        $new = ProductFiltersCategory::find()->where(['in','id', array_diff((array) $value, (array) $curIds)])->all();
        $del = ProductFiltersCategory::find()->where(['in','id', array_diff((array) $curIds, (array) $value)])->all();

        foreach($new as $cf)
            $this->link('productFiltersCategories', $cf);

        foreach($del as $cf)
            $this->unlink('productFiltersCategories', $cf, true);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductFiltersCategories()
    {
        return $this->hasMany(ProductFiltersCategory::class, ['id' => 'product_filters_category_id'])
            ->viaTable('{{%product_filters_category_product_category}}',
                ['product_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCoupons() {
        return $this->hasMany(Coupons::class, ['id' => 'coupon_id'])
            ->viaTable('{{%coupon_category}}', ['category_id' => 'id']);
    }

    public function getFilterCatIds()
    {
        return implode(',', ArrayHelper::getColumn($this->productFiltersCategories, 'id'));
    }


    public function getProductFilters()
    {
        return ProductFilters::find()->andWhere([
                'in',
                'category_id',
                ArrayHelper::getColumn($this->productFiltersCategories, 'id')
            ])->all();
    }

    static function syncProvider() {
        return new Sync([
            'key_start' => 'backend.productCat.sync.isStart',
            'key_success' => 'backend.productCat.sync.success_last_update',
            'key_error' => 'backend.productCat.sync.error_last_update',
            'url_code' => 'PRODUCT_CATEGORY',
            'model' => self::class,
            'model_sync_attrs' => [
                'title' => 'name',
                'slug' => 'name'
            ],
            'model_error_attr' => 'error',
            'model_sync_prop' => 'ms_id',
            'model_ms_sync_prop' => 'id',
            'model_ms_skip' => [],
            'onError' => function ($result) {
                if (isset($result->error))
                    \Yii::getLogger()->log($result->error, 1, 'SyncProductCategory');
            }
        ]);
    }
    static function sync($uid) {
        self::syncProvider()->sync($uid);
    }

    public static function getPublishedBySlug(string $slug): ?ProductCategory
    {
        if (isset(self::$cacheBySlug[$slug])) {
            return self::$cacheBySlug[$slug];
        }
        self::$cacheBySlug[$slug] = null;

        $p = self::find()->published()->andWhere(['slug' => $slug])->cache(300)->one();
        if ($p instanceof ProductCategory) {
            self::$cacheBySlug[$slug] = $p;
            self::$cacheByMsId[$p->ms_id] = $p;
        }

        return self::$cacheBySlug[$slug];
    }

    public static function getPublishedByMsId($msId): ?ProductCategory
    {
        if (isset(self::$cacheByMsId[$msId])) {
            return self::$cacheByMsId[$msId];
        }
        self::$cacheByMsId[$msId] = null;

        $p = self::find()->published()->andWhere(['ms_id' => $msId])->cache(300)->one();
        if ($p instanceof ProductCategory) {
            self::$cacheByMsId[$msId] = $p;
            self::$cacheBySlug[$p->slug] = $p;
        }

        return self::$cacheByMsId[$msId];
    }
}
