<?php

namespace common\models;

use common\models\traits\BlameAble;
use common\models\traits\Img;
use Yii;
use backend\components\sync\Sync;
use common\models\query\ProductQuery;
use trntv\filekit\behaviors\UploadBehavior;
use common\components\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\httpclient\Client;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $slug
 * @property string $title
 * @property string $body
 * @property string $view
 * @property string $thumbnail_path
 * @property array $attachments
 * @property integer $status
 * @property integer $is_ms_deleted
 * @property integer $published_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $order
 * @property integer $order_config
 * @property integer $disable_sync_prop
 * @property integer $disable_sync_prop__prepend
 *
 * @property integer $revised
 * @property integer $is_main_in_config
 * @property string $ms_id
 * @property string $config_ms_id
 * @property integer $category_ms_id
 * @property string $manufacturer
 * @property float $price
 * @property float $warranty
 * @property float $weight
 * @property integer $amount
 * @property integer $min_amount
 * @property integer $is_fixed_amount
 * @property boolean $disable_sync
 * @property integer $is_oversized
 * @property double $length
 * @property double $width
 * @property double $height
 * @property string $seo_title
 * @property string $seo_desc
 * @property string $seo_keywords
 * @property string $similar
 * @property integer $is_discount
 * @property integer $is_super_price
 *
 * @property User $author
 * @property User $updater
 * @property ProductCategory $category
 * @property ProductAttachment[] $productAttachments
 * @property string $error
 */
class Product extends ActiveRecord
{
    use BlameAble;
    use Img;

    const SCENARIO_SYNC = 'sync';

    const SHORT_TITLE_LENGTH = 54;
    const SHORT_SHORT_TITLE_LENGTH = 10;
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    const MS_DELETED = 1;
    const MS_NOT_DELETED = 0;

    const REVISED__YES = 1;
    const REVISED__NO = 0;

    const IS_MAIN_IN_CONFIG__TRUE = 1;
    const IS_MAIN_IN_CONFIG__FALSE = 0;

    const SMALL_AMOUNT = 10;
    const AVR_AMOUNT = 20;
    const LARGE_AMOUNT = 30;

    const PRICE_TEMPLATE_RETAIL_PRICE = 1;

    /**
     * @var array
     */
    public $attachments;
    public $disable_sync_prop__prepend;
    public $count_in_order = 1;

    /**
     * @var array
     */
    public $thumbnail;
    public $prices;
    public $clientSalePercent;
    public $clientPriceValue;
    public $clientOldPriceValue;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @return ProductQuery
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public static function queryPublished()
    {
        return ['and',
            [Product::tableName() . '.status' => Product::STATUS_PUBLISHED],
            [Product::tableName() . '.revised' => Product::REVISED__YES],
            [Product::tableName() . '.is_ms_deleted' => Product::MS_NOT_DELETED],
            ['or',
                ['<', Product::tableName() . '.published_at', time()],
                [Product::tableName() . '.published_at' => null]
            ]
        ];
    }

    public static function queryOrder()
    {
        return [
            Product::tableName() . '.brand_id' => SORT_DESC,
            Product::tableName() . '.title' => SORT_ASC,
        ];
    }

    /**
     * @return array revised_value list
     */
    public static function revised_value()
    {
        return [
            self::REVISED__NO => t_b('Требует обработки'),
            self::REVISED__YES => t_b('Обработанно'),
        ];
    }

    /**
     * @return array is_main_in_config_value list
     */
    public static function is_main_in_config_value()
    {
        return [
            self::IS_MAIN_IN_CONFIG__TRUE => t_b('Основная в конфигурации'),
            self::IS_MAIN_IN_CONFIG__FALSE => t_b('Дочерняя в конфигурации'),
        ];
    }

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
                'attribute' => 'attachments',
                'multiple' => true,
                'uploadRelation' => 'productAttachments',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => null,
                'orderAttribute' => 'order',
                'typeAttribute' => 'type',
                'sizeAttribute' => 'size',
                'nameAttribute' => 'name',
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
            [['title'], 'required', 'except' => self::SCENARIO_SYNC],
            [['slug'], 'unique', 'except' => self::SCENARIO_SYNC],
            [['ms_id'], 'unique'],
            [['ms_id'], 'default', 'value' => function () {
                return "ts_" . time();
            }],
            [['body', 'body_short'], 'string'],
            [['title', 'slug'], 'string', 'max' => 256],
            [['title_short'], 'string', 'max' => 54, 'except' => self::SCENARIO_SYNC],
            [['published_at', 'is_new_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            [['status', 'disable_sync', 'revised', 'is_new'], 'integer'],
            ['is_main_in_config', 'filter', 'filter' => function ($value) {
                if (empty($value)) return 0;
                return 1;
            }],

            [['thumbnail_path'], 'string', 'max' => 128],
            [['article'], 'string', 'max' => 40],
            [['attachments', 'thumbnail', 'price'], 'safe'],

            [['manufacturer'], 'string', 'max' => 128],
            [['video_code'], 'string', 'max' => 1024],
            [['weight', 'length', 'width', 'height'], 'double'],
            [['warranty'], 'double'],
            [['amount', 'min_amount'], 'integer', 'min' => -65535, 'max' => 65535],

            [['order'], 'integer'],
            [['order'], 'default', 'value' => 0],

            [['order_config'], 'integer'],
            [['order_config'], 'default', 'value' => 0],

            [['ms_id', 'config_ms_id', 'category_ms_id'], 'string', 'max' => 40],
            [['error'], 'string'],
            [['display_currency'], 'integer'],
            [['brand_id'], 'string'],
            [['disable_sync_prop'], 'string'],
            [['disable_sync_prop__prepend'], 'safe'],
            [['type_eq'], 'integer'],
            [['productFilters', 'productRelated', 'questions'], 'safe'],
            [['view_count'], 'integer'],
            [['count_in_order'], 'safe'],
            [['config_decrypt'], 'string', 'max' => 1024],
            [['config_name'], 'string', 'max' => 255],
            [['alt_desc'], 'string'],
            [['seo_title', 'seo_desc', 'seo_keywords'], 'string'],
            [['similar'], 'string'],

            [['is_fixed_amount'], 'integer'],
            [['is_fixed_amount'], 'default', 'value' => 0],
            [['is_oversized'], 'integer'],
            [['is_oversized'], 'default', 'value' => 0],
            [['is_ms_deleted'], 'integer'],
            [['is_ms_deleted'], 'default', 'value' => 0],
            [['is_discount'], 'integer'],
            [['is_discount'], 'default', 'value' => 0],
            [['is_super_price'], 'integer'],
            [['is_super_price'], 'default', 'value' => 0],
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
            'title_short' => t_b('Короткое название'),
            'body' => t_b('Описание'),
            'body_short' => t_b('Короткое описание'),
            'thumbnail' => t_b('Превью (396x350)'),
            'attachments' => t_b('Изображения (396x350)'),
            'category_ms_id' => t_b('Категория'),
            'status' => t_b('Публиковать'),
            'published_at' => t_b('Опубликовать на'),
            'is_new_at' => t_b('В категории новый до дате'),
            'is_new' => t_b('В категории новый'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'revised' => t_b('Обработан'),
            'disable_sync' => t_b('Отк. синх.'),
            'disable_sync_prop' => t_b('Отк. синх. полей'),
            'disable_sync_prop__prepend' => t_b('Отк. синх. полей'),
            'is_main_in_config' => t_b('Основной в конфигурации'),
            'config_ms_id' => t_b('Конфигурации'),
            'ms_id' => t_b('Ид в системк MC'),
            'manufacturer' => t_b('Страна производитель'),
            'prices' => t_b('Цены'),
            'weight' => t_b('Вес'),
            'length' => t_b('Длина'),
            'width' => t_b('Ширина'),
            'height' => t_b('Высота'),
            'amount' => t_b('Количество'),
            'min_amount' => t_b('Неснижаемый остаток'),
            'order' => t_b('Сортировка'),
            'order_config' => t_b('Сортировка в конфигурации'),
            'error' => t_b('Ошибки синх.'),
            'display_currency' => t_b('Выводить в валюте'),
            'sale' => t_b('Скидка'),
            'brand_id' => t_b('Бренд'),
            'type_eq' => t_b('Тип оборудования'),
            'productFilters' => t_b('Фильтры'),
            'view_count' => t_b('Просмотров продукта'),
            'video_code' => t_b('Код для видео'),
            'questions' => t_b('Вопрос-ответ'),
            'article' => t_b('Артикул'),
            'productRelated' => t_b('С этим тов. покупают'),
            'config_decrypt' => t_b('Расшифровка конфигурации'),
            'config_name' => t_b('Название конфигурации'),
            'alt_desc' => t_b('Дополнительное описание в плитке'),
            'warranty' => t_b('Гарантия (лет)'),
            'seo_title' => t_b('SEO Заголовок'),
            'seo_desc' => t_b('SEO Описание'),
            'seo_keywords' => t_b('SEO Ключевые слова'),
            'similar' => t_b('Артикулы аналогов'),
            'is_fixed_amount' => t_b('Фиксированный остаток'),
            'is_oversized' => t_b('Крупногабарит'),
            'is_ms_deleted' => t_b('Товар удален в МС'),
            'is_discount' => t_b('Товар со скидкой'),
            'is_super_price' => t_b('Товар с суперценой'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'slug' => t_b('Генерируется автоматически, используется в Url на сайте'),
            'body_short' => t_b('Если товар - конфигурация, короткое описание звполняется только в основном товаре в конфигупации'),
            'title_short' => t_b('Для списка в плитке'),
            'published_at' => t_b('Можно выбрать дату и время для автоматической публикации на сайте'),
            'is_main_in_config' => t_b('Основной продукт из конфигурации, публикуется в основных разделах'),
            'config_ms_id' => t_b('Код для конфигурации продукта'),
            'ms_id' => t_b('Уникальный внутренний id, используется для связки с МС'),
            'order' => t_b('Возможны отрицательные значения'),
            'order_config' => t_b('Номер конфигурации, возможны отрицательные значения'),
            'disable_sync' => t_b('Отключает синхронизацию продукта'),
            'sale' => t_b('Скидка в процентах'),
            'disable_sync_prop__prepend' => t_b('Удерживайте Ctr или Shift для выдиления'),
            'productFilters' => t_b('Удерживайте Ctr или Shift для выдиления, фильтры привязываются в категории продукта'),
            'config_name' => t_b('Цвет, обьем и т.д., задается в основном товаре в конфигурации'),
            'warranty' => t_b('Дробная часть через точку'),
        ];
    }

    public function incViewCount()
    {
        $this->view_count = $this->view_count + 1;
        $this->save(false, ['view_count']);
    }

    public function afterFind()
    {
        $this->disable_sync_prop__prepend = json_decode($this->disable_sync_prop);
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        $this->disable_sync_prop = json_encode($this->disable_sync_prop__prepend, JSON_UNESCAPED_UNICODE);
        $this->is_main_in_config = (bool)$this->is_main_in_config;
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (count($changedAttributes) == 1 && array_key_exists('view_count', $changedAttributes)) {
            return;
        }
        $prices = $this->prices;
        if (!empty($prices) && is_array($prices))
            foreach ($prices as $price) {
                $template = ProductPriceTemplate::find()->where(['title' => $price->priceType->name])->one();

                if (empty($template)) {
                    $template = new ProductPriceTemplate(['title' => $price->priceType->name]);
                    $template->detachBehavior('blameable');
                    $template->save(false);
                }

                $productPrice =
                    ProductPrice::find()
                        ->where(['product_id' => $this->id, 'template_id' => $template->id])->one();

                if (empty($productPrice)) {
                    $productPrice = new ProductPrice([
                        'template_id' => $template->id,
                        'product_id' => $this->id,
                    ]);
                }

                $productPrice->formatPrice = ProductPrice::formatPrice($price->value);
                $productPrice->currency_isoCode = '643';
                $productPrice->detachBehavior('blameable');
                $productPrice->save(false);
            }
        $this->ms_send_sync_stock_new();
        parent::afterSave($insert, $changedAttributes);
    }

    public function ms_send_sync_stock_new()
    {
        if (!($this->is_fixed_amount || $this->disable_sync)) {
            $msQtyt = $this->ms_send_sync('report/stock/bystore?filter=product=https://api.moysklad.ru/api/remap/1.2/entity/product/' . $this->ms_id . ';store=https://api.moysklad.ru/api/remap/1.2/entity/store/' . UserClientOrder::MS_ID_ORDER_STORE_MAIN, null, $method = 'GET', '1.2');

            if (@$msQtyt->responseContent->rows[0]->stockByStore[0]->stock - @$msQtyt->responseContent->rows[0]->stockByStore[0]->reserve > 0) {
                if ($this->amount != $msQtyt->responseContent->rows[0]->stockByStore[0]->stock - $msQtyt->responseContent->rows[0]->stockByStore[0]->reserve) {
                    $this->amount = $msQtyt->responseContent->rows[0]->stockByStore[0]->stock - $msQtyt->responseContent->rows[0]->stockByStore[0]->reserve;
                    $this->save(false);
                }
            } else {
                if ($this->amount != 0) {
                    $this->amount = 0;
                    $this->save(false);
                }
            }

            return $this->amount;
        } else {
            $msQtyt = $this->ms_send_sync('report/stock/bystore?filter=product=https://api.moysklad.ru/api/remap/1.2/entity/product/' . $this->ms_id . ';store=https://api.moysklad.ru/api/remap/1.2/entity/store/' . UserClientOrder::MS_ID_ORDER_STORE_MAIN, null, $method = 'GET', '1.2');

            return @$msQtyt->responseContent->rows[0]->stockByStore[0]->stock - @$msQtyt->responseContent->rows[0]->stockByStore[0]->reserve;
        }
    }

    public function beforeDelete()
    {
        $this->unlinkAll('prices', true);
        return parent::beforeDelete();
    }

    public function setQuestions($value)
    {

        $curIds = ArrayHelper::getColumn(
            $this->getQuestions()->select(['id'])->asArray()->all(),
            'id'
        );

        $new = Question::find()->where(['in', 'id', array_diff((array)$value, (array)$curIds)])->all();
        $del = Question::find()->where(['in', 'id', array_diff((array)$curIds, (array)$value)])->all();

        foreach ($new as $cf)
            $this->link('questions', $cf);

        foreach ($del as $cf)
            $this->unlink('questions', $cf, true);

    }

    public function getQuestions()
    {
        return $this->hasMany(Question::class, ['id' => 'question_id'])
            ->viaTable('{{%question_product}}',
                ['product_id' => 'id'])->andWhere([Question::tableName() . '.status' => 1]);
    }

    public function getPubQuestions()
    {
        return $this->hasMany(Question::class, ['id' => 'question_id'])
            ->viaTable('{{%question_product}}',
                ['product_id' => 'id'])->andWhere([Question::tableName() . '.status' => 1]);
    }

    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['product_id' => 'id']);
    }

    public function getPubReviews()
    {
        return $this->hasMany(Reviews::class, ['product_id' => 'id'])
            ->andWhere([Reviews::tableName() . '.is_published' => 1])
            ->orderBy([Reviews::tableName() . '.date' => SORT_DESC]);
    }

    public function setProductFilters($value)
    {

        $curIds = ArrayHelper::getColumn($this->getProductFilters()->select(['id'])->asArray()->all(), 'id');

        $new = ProductFilters::find()->where(['in', 'id', array_diff((array)$value, (array)$curIds)])->all();
        $del = ProductFilters::find()->where(['in', 'id', array_diff((array)$curIds, (array)$value)])->all();

        foreach ($new as $cf)
            $this->link('productFilters', $cf);

        foreach ($del as $cf)
            $this->unlink('productFilters', $cf, true);

    }

    public function getProductFilters()
    {
        return $this->hasMany(ProductFilters::class, ['id' => 'product_filters_id'])
            ->viaTable('{{%product_filters_product}}',
                ['product_id' => 'id']);
    }

    public function getProductFiltersIds()
    {
        return implode(',', ArrayHelper::getColumn($this->productFilters, 'id'));
    }

    public function setProductRelated($value)
    {

        $curIds = ArrayHelper::getColumn($this->getProductRelated()->select(['id'])->asArray()->all(), 'id');

        $new = Product::find()->where(['in', 'id', array_diff((array)$value, (array)$curIds)])->all();
        $del = Product::find()->where(['in', 'id', array_diff((array)$curIds, (array)$value)])->all();

        foreach ($new as $cf)
            $this->link('productRelated', $cf);

        foreach ($del as $cf)
            $this->unlink('productRelated', $cf, true);

    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCoupons()
    {
        return $this->hasMany(Coupons::class, ['id' => 'coupon_id'])
            ->viaTable('{{%coupon_product}}', ['product_id' => 'id']);
    }

    public function getProductRelated()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('{{%product_related}}',
                ['related_id' => 'id']);
    }

    public function getTitleShort()
    {
        return !empty($this->title_short) ?
            $this->title_short :
            mb_substr($this->title, 0, self::SHORT_TITLE_LENGTH) .
            (mb_strlen($this->title) > self::SHORT_TITLE_LENGTH ? '...' : '');
    }

    public function getTitleShortShort()
    {
        return !empty($this->title_short) ?
            $this->title_short :
            mb_substr($this->title, 0, self::SHORT_SHORT_TITLE_LENGTH) .
            (mb_strlen($this->title) > self::SHORT_SHORT_TITLE_LENGTH ? '...' : '');
    }

    public function getAltDesc()
    {
        return mb_substr($this->alt_desc, 0, 30) .
            (mb_strlen($this->alt_desc) > 30 ? '...' : '');
    }

    public function getSubTitle()
    {
        if (!empty($this->altDesc))
            return $this->altDesc;

        return '';

        if (!empty($this->article))
            return "Арт. $this->article";

        if (isset($this->category->title))
            return $this->category->title;

        if (isset($this->brand->title))
            return $this->brand->title;

        if (isset($this->typeEq->title))
            return $this->typeEq->title;

        return '';
    }

    public function getRoute()
    {
        $category = ProductCategory::getPublishedByMsId($this->category_ms_id);
        if (!$category || !$category->slug) {
            $slug = 'detail';
        } else {
            $slug = $category->slug;

        }
        return join('', [
            '/catalog/',
            $slug,
            '/',
            $this->slug,
            '/'
        ]);
    }

    /* config */
    public function getCategoryConfig()
    {
        return $this->hasOne(ProductCategoryConfig::class,
            ['ms_id' => 'config_ms_id']);
    }

    public function getIsMainConfig()
    {
        return !empty($this->config_ms_id) and !empty($this->is_main_in_config);
    }

    public function getIsConfig()
    {
        return !empty($this->config_ms_id);
    }

    public function getMainConfig()
    {
        return $this->hasOne(Product::class,
            [Product::tableName() . '.config_ms_id' => 'config_ms_id'])
            ->andWhere(Product::queryPublished())
            ->andWhere(['IS NOT', Product::tableName() . '.config_ms_id', null])
            ->andWhere([Product::tableName() . '.is_main_in_config' => self::IS_MAIN_IN_CONFIG__TRUE]);
    }

    public function getConfigs()
    {
        return Product::find()
            ->cache(7200)
            ->preparePrice()
            ->andWhere([Product::tableName() . '.config_ms_id' => $this->config_ms_id])
            ->andWhere(Product::queryPublished())
            ->orderBy(new Expression(Product::tableName() . ".order_config"))->all();
    }

    public function getIsConfigInStock()
    {
        return Product::find()
            ->preparePrice()
            ->andWhere([Product::tableName() . '.config_ms_id' => $this->config_ms_id])
            ->andWhere(Product::queryPublished())
            ->andWhere(['>', Product::tableName() . '.amount', 0])
            ->count();
    }

    /**
     * @param array $config_ms_ids
     * @return array
     */
    public static function getIsConfigInStockByMsIds(array $config_ms_ids)
    {
        if (!$config_ms_ids) {
            return [];
        }
        $cs = Product::find()
            ->preparePrice()
            ->select(Product::tableName() . '.config_ms_id, count(*) as cnt')
            ->andWhere([Product::tableName() . '.config_ms_id' => $config_ms_ids])
            ->andWhere(Product::queryPublished())
            ->andWhere(['>', Product::tableName() . '.amount', 0])
            ->groupBy(Product::tableName() . '.config_ms_id')
            ->asArray()
            ->all();

        $result = [];
        foreach ($cs as $c) {
            $result[$c['config_ms_id']] = $c['cnt'];
        }

        return $result;
    }

    public function getManufacturer_()
    {
        return empty(strip_tags($this->manufacturer)) && $this->mainConfig ?
            $this->mainConfig->manufacturer : $this->manufacturer;
    }

    public function getBigImages_()
    {
        return empty($this->bigImages) && $this->mainConfig ?
            $this->mainConfig->bigImages : $this->bigImages;
    }

    public function getBigImage_1st_()
    {
        return empty($this->bigImages_1st) && $this->mainConfig ?
            $this->mainConfig->bigImages_1st : $this->bigImages_1st;
    }

    public function getBrand_()
    {
        return empty($this->brand) && $this->mainConfig ?
            $this->mainConfig->brand : $this->brand;
    }

    public function getVideo_code_()
    {
        return empty($this->video_code) && $this->mainConfig ?
            $this->mainConfig->video_code : $this->video_code;
    }

    public function getPubQuestions_()
    {
        return empty($this->pubQuestions) && $this->mainConfig ?
            $this->mainConfig->pubQuestions : $this->pubQuestions;

    }

    public function getPubReviews_()
    {
        return empty($this->pubReviews) && $this->mainConfig ?
            $this->mainConfig->pubReviews : $this->pubReviews;

    }

    public function getBodyShort_()
    {
        return $this->mainConfig ?
            $this->mainConfig->body_short : $this->body_short;

    }

    public function getMinPriceValue()
    {
        return min(array_merge(
            [$this->clientPriceValue], ArrayHelper::getColumn($this->configs, 'clientPriceValue')
        ));
    }

    public function getFrontendMinPriceValueWithoutOutOfStock()
    {
        $arInStock = $arOutOfStock = array();
        $minPriceValue = 0;
        $prefix = '';

        if (count($this->configs) <= 1 || count(array_unique(ArrayHelper::getColumn($this->configs, 'clientPriceValue'))) == 1) {
            $minPriceValue = min(array_merge(
                [$this->clientPriceValue], ArrayHelper::getColumn($this->configs, 'clientPriceValue')
            ));
        } else {
            foreach ($this->configs as $config) {
                if ($config->amount > 0) {
                    $arInStock[] = $config->clientPriceValue;
                } else {
                    $arOutOfStock[] = $config->clientPriceValue;
                }
            }

            if (count($arInStock) == 1) {
                $minPriceValue = min(array_merge([$this->clientPriceValue], $arInStock));
            } elseif (count($arInStock) > 1) {
                $prefix = 'от ';
                $minPriceValue = min(array_merge([$this->clientPriceValue], $arInStock));
            } elseif (count($arOutOfStock) == 1) {
                $minPriceValue = min(array_merge([$this->clientPriceValue], $arOutOfStock));
            } elseif (count($arOutOfStock) > 1) {
                $prefix = 'от ';
                $minPriceValue = min(array_merge([$this->clientPriceValue], $arOutOfStock));
            }
        }

        $price = ProductPrice::getParsePrice(
            $minPriceValue,
            $this->displayCurrencyIsoCode
        );

        return $prefix . implode('', [$price->ceil_fr, ' ', $price->cur]);
    }
    /* end config */

    /* binding */
    /**
     * @return yii\db\ActiveQuery
     */
    public function getTypeEq()
    {
        return $this->hasOne(TypeEq::class, ['type_eq' => 'id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ProductCategory::class, ['ms_id' => 'category_ms_id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['slug' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductAttachments()
    {
        return $this->hasMany(ProductAttachment::class,
            ['product_id' => 'id']);
    }

    public function getBigImages()
    {
        return $this->hasMany(ProductAttachment::class,
            ['product_id' => 'id'])->orderBy([ProductAttachment::tableName() . '.order' => SORT_ASC]);
    }

    public function getBigImage_1st()
    {
        return $this->hasOne(ProductAttachment::class,
            ['product_id' => 'id'])->orderBy([ProductAttachment::tableName() . '.order' => SORT_ASC]);
    }
    /* end binding */

    /* amount */
    public function getAmountTitle()
    {
        switch ($this->getAmountIndex()) {
            case 0:
                return 'Нет';
            case 1:
                return 'Очень мало';
            case 2:
                return 'Мало';
            case 3:
                return 'Много';
        }
    }

    public function getAmountIndex()
    {
        $category = ProductCategory::getPublishedByMsId($this->category_ms_id);

        $small_amount = $category && !is_null($category->small_amount) ?
            $category->small_amount : self::SMALL_AMOUNT;

        $large_amount = $category && !is_null($category->large_amount) ?
            $category->large_amount : self::LARGE_AMOUNT;

        if ($this->amount <= 0)
            return 0;

        if ($this->amount <= $small_amount)
            return 1;

        if ($this->amount <= $large_amount)
            return 2;

        if ($this->amount > $large_amount)
            return 3;

    }
    /* end amount */

    /* prices */
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPrice()
    {

        //цена для пользователя
        if (isset(Yii::$app->client) && !Yii::$app->user->isGuest) {
            $template_id = Yii::$app->client->identity->userProfile->saleTemplateId;

            foreach (Yii::$app->client->identity->userProfile->salesBrandsArr as $name => $brands)
                if (in_array($this->brand->slug, explode(',', $brands)))
                    if ($t = ProductPriceTemplate::findOne(['title' => $name]))
                        $template_id = $t->id;

        } else
            $template_id = ProductPriceTemplate::TEMPLATE_ID_DEFAULT;

        return $this->hasOne(ProductPrice::class,
            ['product_id' => 'id'])
            ->andWhere([ProductPrice::tableName() . '.template_id' => $template_id]);
    }

    public function getPrices()
    {
        return $this->hasMany(ProductPrice::class,
            ['product_id' => 'id']);
    }

    public function getRetailPrice()
    {
        return $this->hasOne(ProductPrice::class,
            ['product_id' => 'id'])->andWhere([ProductPrice::tableName() . '.template_id' => self::PRICE_TEMPLATE_RETAIL_PRICE]);
    }

    public function getPricesIndexID()
    {
        return $this->hasMany(ProductPrice::class,
            ['product_id' => 'id'])
            ->indexBy('id');
    }

    public function getDisplayCurrencyIsoCode()
    {
        if (!$this->display_currency) {
            return Currency::DEFAULT_FRONTEND_DISPLAY_PRICE_CUR_ISO;
        }
        $cur = Currency::findOne($this->display_currency);

        return $cur ? $cur->code_iso :
            Currency::DEFAULT_FRONTEND_DISPLAY_PRICE_CUR_ISO;
    }

    public function getFrontendCurrentPrice()
    {
        $price = ProductPrice::getParsePrice(
            $this->clientPriceValue,
            $this->displayCurrencyIsoCode
        );
        return implode('', [$price->ceil_fr, ' ', $price->cur]);
    }

    public function getFrontendMinPrice()
    {
        $price = ProductPrice::getParsePrice(
            $this->minPriceValue,
            $this->displayCurrencyIsoCode
        );
        return implode('', [$price->ceil_fr, ' ', $price->cur]);
    }

    public function getFrontendOldPrice()
    {
        $oldPrice = $this->retailPrice->price;

        $price = ProductPrice::getParsePrice(
            $oldPrice,
            $this->displayCurrencyIsoCode
        );

        return implode('', [$price->ceil_fr, ' ', $price->cur]);
    }

    /* end prices */

    /* sync */
    static function syncProviderParams()
    {
        return [
            'key_start' => 'backend.products.sync.isStart',
            'key_success' => 'backend.products.sync.success_last_update',
            'key_error' => 'backend.products.sync.error_last_update',
            'url_code' => 'PRODUCTS',
            'model' => self::class,
            'model_error_attr' => 'error',
            'model_sync_attrs' => self::getSyncProp(),
            'model_sync_prop' => 'ms_id',
            'model_ms_sync_prop' => 'id',
            //игнорировать синхронизацию полей
            'model_skip_props__prop_name' => 'disable_sync_prop__prepend',
            'skip_by_attr' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_DISABLE_SYNC'), 'value'],
        ];
    }

    static function syncProviderOne()
    {
        return new Sync(self::syncProviderParams());
    }

    static function syncProvider()
    {
        return new Sync(
            array_merge(
                [
                    'onError' => function ($result) {
                        $model = new ProductSync();
                        $model->status = $result->partially ? ProductSync::STATUS_PARTIALLY : ProductSync::STATUS_ERROR;
                        $model->author = isset($result->uid) ? $result->uid : null;
                        $model->error = json_encode($result, JSON_UNESCAPED_UNICODE);
                        $model->save(false);
                    },
                    'onSuccess' => function ($result) {
                        $model = new ProductSync();
                        $model->status = ProductSync::STATUS_SUCCESS;
                        $model->error = json_encode($result, JSON_UNESCAPED_UNICODE);
                        $model->author = isset($result->uid) ? $result->uid : null;
                        $model->save(false);
                    }
                ],
                self::syncProviderParams()
            )
        );
    }

    static function sync($uid, $ms_id = null)
    {
        if (is_null($ms_id))
            self::syncProvider()->sync($uid);
        else
            self::syncProviderOne()->sync($uid, $ms_id);
    }

    static function getSyncProp()
    {
        return [
            'title' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_TITLE'), 'value'],
            'slug' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_TITLE'), 'value'],
            'weight' => 'weight',
            'prices' => 'salePrices',
            'manufacturer' => ['country', 'name'],
            'config_ms_id' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_PRODUCT_CONFIG'), 'value', 'id'],
            'category_ms_id' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_PRODUCT_CATEGORY'), 'value', 'id'],
            'is_main_in_config' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_IS_MAIN_IN_CONFIG'), 'value'],
            'brand_id' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_BRAND_ID'), 'value'],
            'body_short' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_BODY_SHORT'), 'value'],
            'amount' => 'quantity',
            'article' => 'code',
            'title_short' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_TITLE_SHORT'), 'value'],
            'config_decrypt' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_CONFIG_DECRYPT'), 'value'],
            'config_name' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_CONFIG_NAME'), 'value'],
            'order_config' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_CONFIG_ORDER'), 'value'],
            'alt_desc' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_PRODUCT_ALT_DESC'), 'value'],
            'warranty' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_WARRANTY'), 'value'],
            'length' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_LENGTH'), 'value'],
            'width' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_WIDTH'), 'value'],
            'height' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_HEIGHT'), 'value'],
            'similar' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_SIMILAR'), 'value'],
            'is_oversized' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_IS_OVERSIZED'), 'value'],
            'is_discount' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_IS_DISCOUNT'), 'value'],
            'is_super_price' => ['attributes', 'id' => env('MS_ID_ATTRIBUTE_IS_SUPER_PRICE'), 'value'],
        ];
    }

    static function getSyncPropAllWithLabel()
    {
        $result = [];
        $props = array_keys(self::getSyncProp());
        $model = new self;

        foreach ($props as $prop)
            if ($prop != 'slug')
                $result[$prop] = $model->getAttributeLabel($prop);

        return $result;
    }

    public function ms_get_product_by_store()
    {
        $this->ms_send_sync_stock_new();

        return $this->amount;
    }

    /**
     * @param $url
     * @param null $data
     * @param string $method
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    private function ms_send_sync($url, $data = null, $method = 'GET', $api_version = '1.2')
    {
        $result = (object)[
            'status' => true,
            'response' => null,
            'responseContent' => null,
            'json_error' => false,
            'msg' => null
        ];

        $client = new Client([
            'baseUrl' => 'https://api.moysklad.ru/api/remap/' . $api_version . '/',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Basic " . base64_encode(env('MS_LOGIN') . ':' . env('MS_PASSWORD')),
                'Accept-Encoding' => 'gzip, deflate, br',
            ])
            ->addOptions([
                'timeout' => 15
            ]);

        if ($data) {
            $request->setData($data);
        }

        $response = $request->send();
        $result->response = $response;

        if (!$response->isOk) {
            $result->status = false;
            $result->msg = $response->content;
            return $result;
        }

        try {
            if (isset($response->content)) {
                if ($response->headers['content-encoding'] == 'gzip') {
                    $responseContent = json_decode(gzdecode($response->content));
                } else {
                    $responseContent = json_decode($response->content);
                }
                $result->responseContent = $responseContent;
                if (is_null($responseContent) || $e = json_last_error()) {
                    $result->status = false;
                    $result->msg = 'JSON parse error (' . $e . ')';
                    $result->json_error = true;
                    return $result;
                }
            }

            return $result;

        } catch (\Exception $e) {
            $result->status = false;
            $result->msg = (string)$e;
            return $result;
        }
    }

    /**
     * Обработка хука МС на удаление товара.
     */
    public function syncDeleteProductHookHandler()
    {
        $result = 0;
        $this->status = self::STATUS_DRAFT;
        $this->is_ms_deleted = self::MS_DELETED;
        if ($this->save()) {
            $result = 1;
        }

        return $result;
    }

    /**
     * Обработка хука МС на обновление товара.
     */
    public function syncUpdateProductHookHandler()
    {
        $result = 0;

        if (!$this->disable_sync && $this->setProductAttributes()) {
            $this->revised = self::REVISED__YES;
            $this->updated_by = -1;
            $this->updated_at = time();
            if ($this->save()) {
                $result = 1;
            }
        }

        return $result;
    }

    /**
     * Обработка хука МС на создание товара.
     */
    public function syncCreateProductHookHandler()
    {
        $result = 0;

        if (!$this->disable_sync && $this->setProductAttributes()) {
            $this->status = self::STATUS_DRAFT;
            $this->revised = self::REVISED__YES;
            $this->created_by = -1;
            $this->created_at = time();
            $this->updated_by = -1;
            $this->updated_at = time();
            if ($this->save()) {
                $result = 1;
            }
        }

        return $result;
    }

    private function setProductAttributes()
    {
        $success = false;

        $response = $this->ms_send_sync('entity/product/' . $this->ms_id);
        try {

            if (isset($response->responseContent)) {
                $ms_product = $response->responseContent;

                // цены
                if (isset($ms_product->salePrices) && is_array($ms_product->salePrices)) {
                    $this->prices = $ms_product->salePrices;
                }

                // Если в названии товара в МС есть "(*)", то устанавливаем amount=100
                if (isset($ms_product->name) && false !== strpos($ms_product->name, '(*)')) {
                    $this->is_fixed_amount = 1;
                    $this->amount = 100;
                } else {
                    $this->is_fixed_amount = 0;
                }

                if (isset($ms_product->country->meta->href)) {
                    $tmp_arr = array_reverse(explode('/', $ms_product->country->meta->href));
                    if ($tmp_arr[0]) {
                        $msCountry = $this->ms_send_sync('entity/country/' . $tmp_arr[0]);
                        if (isset($msCountry->responseContent->name)) {
                            $this->manufacturer = $msCountry->responseContent->name; // страна производитель
                        }
                    }
                }

                if (isset($ms_product->code)) {
                    $this->article = $ms_product->code; // артикул
                }

                if (isset($ms_product->weight)) {
                    $this->weight = $ms_product->weight; // вес
                }

                if (isset($ms_product->attributes)) {
                    foreach ($ms_product->attributes as $attr) {
                        if (isset($attr->id)) {
                            switch ($attr->id) {
                                case env('MS_ID_ATTRIBUTE_DISABLE_SYNC'): // !!!НЕ СИНХРОНИЗИРОВАТЬ!!!
                                    if (isset($attr->value)) {
                                        $this->disable_sync = $attr->value ? 1 : 0;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_TITLE'): // название товара и слаг
                                    if (isset($attr->value)) {
                                        $this->title = $attr->value;

                                        if (empty($this->slug)) {
                                            $this->slug = Inflector::slug($attr->value);
                                        }
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_PRODUCT_CONFIG'): // конфигурация
                                    if (isset($attr->value->meta->href)) {
                                        $tmp_arr = array_reverse(explode('/', $attr->value->meta->href));
                                        if ($tmp_arr[0]) {
                                            $this->config_ms_id = $tmp_arr[0];

                                            $config = ProductCategoryConfig::find()->where(['ms_id' => $tmp_arr[0]])->one();
                                            if ($config) { // проверить на изменение названия
                                                if (isset($attr->value->name) && !empty($attr->value->name) && $config->title != $attr->value->name) {
                                                    $config->title = $attr->value->name;
                                                    $config->updated_by = -1;
                                                    $config->updated_at = time();
                                                    if (!$config->save()) {
//                                                    $flog = fopen(dirname(__FILE__) . '/error_' . date("Ymd-His"), 'w');
//                                                    fputs($flog, print_r($config->errors, true));
//                                                    fclose($flog);
                                                    }
                                                }
                                            } else { // иначе создать новую
                                                if (isset($attr->value->name) && !empty($attr->value->name)) {
                                                    $config = new ProductCategoryConfig([
                                                        'title' => $attr->value->name,
                                                        'status' => self::STATUS_PUBLISHED,
                                                        'ms_id' => $tmp_arr[0],
                                                        'created_by' => -1,
                                                        'updated_by' => -1,
                                                        'created_at' => time(),
                                                        'updated_at' => time(),
                                                        'disable_sync' => 0,
                                                    ]);
                                                    if (!$config->save()) {
//                                                    $flog = fopen(dirname(__FILE__) . '/error_' . date("Ymd-His"), 'w');
//                                                    fputs($flog, print_r($config->errors, true));
//                                                    fclose($flog);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_PRODUCT_CATEGORY'): // категория
                                    if (isset($attr->value->meta->href)) {
                                        $tmp_arr = array_reverse(explode('/', $attr->value->meta->href));
                                        if (!empty($tmp_arr[0])) {
                                            $this->category_ms_id = $tmp_arr[0];

                                            $category = ProductCategory::find()->where(['ms_id' => $tmp_arr[0]])->one();
                                            if ($category) { // проверить на изменение названия
                                                if (isset($attr->value->name) && !empty($attr->value->name) && $category->title != $attr->value->name) {
                                                    $category->title = $attr->value->name;
                                                    $category->updated_by = -1;
                                                    $category->updated_at = time();
                                                    if (!$category->save()) {
//                                                    $flog = fopen(dirname(__FILE__) . '/error_' . date("Ymd-His"), 'w');
//                                                    fputs($flog, print_r($category->errors, true));
//                                                    fclose($flog);
                                                    }
                                                }
                                            } else { // иначе создать новую
                                                if (isset($attr->value->name) && !empty($attr->value->name)) {
                                                    $category = new ProductCategory([
                                                        'slug' => Inflector::slug($attr->value->name),
                                                        'title' => $attr->value->name,
                                                        'status' => self::STATUS_PUBLISHED,
                                                        'ms_id' => $tmp_arr[0],
                                                        'created_by' => -1,
                                                        'updated_by' => -1,
                                                        'created_at' => time(),
                                                        'updated_at' => time(),
                                                        'disable_sync' => 0,
                                                    ]);
                                                    if (!$category->save()) {
//                                                    $flog = fopen(dirname(__FILE__) . '/error_' . date("Ymd-His"), 'w');
//                                                    fputs($flog, print_r($category->errors, true));
//                                                    fclose($flog);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_IS_MAIN_IN_CONFIG'): // основной в конфигурации
                                    if (isset($attr->value)) {
                                        $this->is_main_in_config = $attr->value ? 1 : 0;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_BRAND_ID'): // бренд
                                    if (isset($attr->value) && !empty($attr->value)) {
                                        $this->brand_id = $attr->value;

                                        $brand = Brand::find()->where(['slug' => $attr->value])->one();
                                        if (is_null($brand)) { // если нет, создать новый
                                            $title = str_replace('-', ' ', $attr->value);
                                            $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8"); // каждое слово с заглавной буквы

                                            $brand = new ProductCategory([
                                                'slug' => $attr->value,
                                                'title' => $title,
                                                'status' => self::STATUS_PUBLISHED,
                                                'created_by' => -1,
                                                'updated_by' => -1,
                                                'created_at' => time(),
                                                'updated_at' => time(),
                                            ]);
                                            if (!$brand->save()) {
//                                            $flog = fopen(dirname(__FILE__) . '/error_' . date("Ymd-His"), 'w');
//                                            fputs($flog, print_r($brand->errors, true));
//                                            fclose($flog);
                                            }
                                        }
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_BODY_SHORT'): // короткое описание
                                    if (isset($attr->value)) {
                                        $this->body_short = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_TITLE_SHORT'): // короткое название
                                    if (isset($attr->value)) {
                                        $this->title_short = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_CONFIG_DECRYPT'): // расшифровка конфигурации
                                    if (isset($attr->value)) {
                                        $this->config_decrypt = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_CONFIG_NAME'): // название конфигурации
                                    if (isset($attr->value)) {
                                        $this->config_name = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_CONFIG_ORDER'): // сортировка в конфигурации
                                    if (isset($attr->value)) {
                                        $this->order_config = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_PRODUCT_ALT_DESC'): // доп. описание в плитке
                                    if (isset($attr->value)) {
                                        $this->alt_desc = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_WARRANTY'): // гарантия (лет)
                                    if (isset($attr->value)) {
                                        $this->warranty = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_LENGTH'): // длина
                                    if (isset($attr->value)) {
                                        $this->length = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_WIDTH'): // ширина
                                    if (isset($attr->value)) {
                                        $this->width = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_HEIGHT'): // высота
                                    if (isset($attr->value)) {
                                        $this->height = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_SIMILAR'): // аналоги
                                    if (isset($attr->value)) {
                                        $this->similar = $attr->value;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_IS_OVERSIZED'): // крупногабарит
                                    if (isset($attr->value)) {
                                        $this->is_oversized = $attr->value ? 1 : 0;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_IS_DISCOUNT'): // товар со скидкой (категория скидка)
                                    if (isset($attr->value)) {
                                        $this->is_discount = $attr->value ? 1 : 0;
                                    }
                                    break;
                                case env('MS_ID_ATTRIBUTE_IS_SUPER_PRICE'): // товар с суперценой
                                    if (isset($attr->value)) {
                                        $this->is_super_price = $attr->value ? 1 : 0;
                                    }
                                    break;
                            }
                        }
                    }
                }

                $success = true;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $success;
    }
}
