<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\models\query\SliderMainQuery;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

/**
 * This is the model class for table "sliderMain".
 *
 * @property integer         $id
 * @property string          $slug
 * @property string          $title
 * @property integer         $status

 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 * @property string          $thumbnail_path
 * @property string          $thumbnail_path_2
 * @property string          $body
 * @property string          $body_short
 *
 *
 */
class SliderMain extends ActiveRecord
{
    use \common\models\traits\BlameAble;
    use \common\models\traits\Img;

    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT     = 0;

    public $thumbnail;
    public $thumbnail_2;

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
        return '{{%slider_main}}';
    }

    /**
     * @return SliderMainQuery
     */
    public static function find()
    {
        return new SliderMainQuery(get_called_class());
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
            [
                'class' => UploadBehavior::class,
                'attribute' => 'thumbnail',
                'pathAttribute' => 'thumbnail_path',
                'baseUrlAttribute' => null,
            ],
            [
                'class' => UploadBehavior::class,
                'attribute' => 'thumbnail_2',
                'pathAttribute' => 'thumbnail_path_2',
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
            [['title','url'], 'string', 'max' => 128],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['thumbnail_path'], 'string', 'max' => 128],
            [['thumbnail'], 'safe'],
            [['thumbnail_path_2'], 'string', 'max' => 128],
            [['thumbnail_2'], 'safe'],
            [['body_short'], 'string', 'max' => 512],
            [['published_at'], 'default', 'value' => function () {
                return date(DATE_ISO8601);
            }],
            [['published_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'url' => t_b('Ссылка'),
            'title' => t_b('Название'),
            'status' => t_b('Публиковать'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'thumbnail' => t_b('Картинка (1920x576)'),
            'thumbnail_2' => t_b('Картинка для мобильной версии (1020x555)'),
            'body_short' => t_b('Короткое описание'),
            'published_at' => t_b('Дата публикации'),
        ];
    }
}
