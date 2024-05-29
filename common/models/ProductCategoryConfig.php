<?php

namespace common\models;

use backend\components\sync\Sync;
use common\components\BlameableBehavior;
use common\models\query\ProductCategoryConfigQuery;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "product_category".
 *
 * @property integer         $id
 * @property string          $title
 * @property string          $ms_id
 * @property string          $error
 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 * @property integer         $status
 * @property integer         $disable_sync

 * @property Product[]       $products
 */

class ProductCategoryConfig extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const SCENARIO_SYNC = 'sync';

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
        return '{{%product_category_config}}';
    }

    /**
     * @return ProductCategoryConfigQuery
     */
    public static function find()
    {
        return new ProductCategoryConfigQuery(get_called_class());
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required', 'except' => self::SCENARIO_SYNC],
            [['ms_id'], 'required'],
            [['ms_id'], 'unique'],
            [['ms_id'], 'default', 'value' => function () {
                return "ts_".time();
            }],
            [['title'], 'string', 'max' => 128],
            [['error'], 'string'],
            [['ms_id'], 'string', 'max' => 40],
            [['status','disable_sync'], 'integer'],
            [['status'], 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'title' => t_b('Название'),
            'ms_id' => t_b('ИД МС конфигупации'),
            'status' => t_b('Публиковать'),
            'error' => t_b('Ошибки синх.'),
            'disable_sync' => t_b('Отк. синх.'),
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
        return $this->hasMany(Product::class, ['config_ms_id' => 'ms_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPubProducts()
    {
        return $this->hasMany(Product::class, ['config_ms_id' => 'ms_id'])
            ->andWhere(Product::queryPublished())
            ->orderBy(new Expression("CAST(".Product::tableName().".title_short AS INT) ASC"));
    }

    public function getTitleShort() {
        return mb_substr($this->title, 0, Product::SHORT_TITLE_LENGTH).
            (mb_strlen($this->title) > Product::SHORT_TITLE_LENGTH ? '...' : '');
    }

    static function syncProvider() {
        return new Sync([
            'key_start' => 'backend.productConfig.sync.isStart',
            'key_success' => 'backend.productConfig.sync.success_last_update',
            'key_error' => 'backend.productConfig.sync.error_last_update',
            'url_code' => 'PRODUCT_CONFIGS',
            'model' => self::class,
            'model_sync_attrs' => [
                'title' => 'name'
            ],
            'model_error_attr' => 'error',
            'model_sync_prop' => 'ms_id',
            'model_ms_sync_prop' => 'id',
            'model_ms_skip' => [],
            'onError' => function ($result) {
                if (isset($result->error))
                    \Yii::getLogger()->log($result->error, 1, 'SyncProductConfig');
            }
        ]);
    }
    static function sync($uid) {
        self::syncProvider()->sync($uid);
    }
}
