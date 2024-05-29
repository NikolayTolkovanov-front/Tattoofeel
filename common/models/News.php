<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\models\query\NewsQuery;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer         $id
 * @property string          $slug
 * @property string          $title
 * @property integer         $status

 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 * @property string              $thumbnail_path
 * @property string              $body
 * @property string              $body_short
 *
 *
 */
class News extends ActiveRecord
{
    use \common\models\traits\BlameAble;
    use \common\models\traits\Img;

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;

    public $thumbnail;

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
        return '{{%news}}';
    }

    /**
     * @return NewsQuery
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
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
            [['title','body_short'], 'required'],
            [['slug'], 'unique'],
            [['title','slug'], 'string', 'max' => 128],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['thumbnail_path'], 'string', 'max' => 128],
            [['thumbnail'], 'safe'],
            [['body'], 'string'],
            [['body_short'], 'string', 'max' => 512],
            [['published_at'], 'default', 'value' => function () {
                return date(DATE_ISO8601);
            }],
            [['published_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
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
            'slug' => t_b('Чпу'),
            'title' => t_b('Название'),
            'status' => t_b('Публиковать'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'thumbnail' => t_b('Картинка (735x492)'),
            'body' => t_b('Описание'),
            'body_short' => t_b('Короткое описание'),
            'published_at' => t_b('Дата публикации'),
            'seo_title' => t_b('SEO Заголовок'),
            'seo_desc' => t_b('SEO Описание'),
            'seo_keywords' => t_b('SEO Ключевые слова'),
        ];
    }
}
