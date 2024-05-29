<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\models\query\PageQuery;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "page".
 */
class Page extends ActiveRecord
{
    use \common\models\traits\BlameAble;
    use \common\models\traits\Img;

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    public $thumbnail;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @return PageQuery
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
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
            [['title','body'], 'required'],
            [['slug'], 'unique'],
            [['title','slug'], 'string', 'max' => 128],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['thumbnail_path'], 'string', 'max' => 128],
            [['thumbnail'], 'safe'],
            [['body','thumbnail_desc'], 'string'],
            [['body_short'], 'string', 'max' => 512],
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
            'thumbnail_desc' => t_b('Описание под картинкой'),
            'seo_title' => t_b('SEO Заголовок'),
            'seo_desc' => t_b('SEO Описание'),
            'seo_keywords' => t_b('SEO Ключевые слова'),
        ];
    }
    public function attributeHints()
    {
        return [
            'thumbnail_desc' => t_b('Блок "Описание под картинкой" ограничен по высоте в 7 строчек')
        ];
    }
}
