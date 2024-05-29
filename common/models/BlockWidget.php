<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\models\query\BlockWidgetQuery;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;
use \Yii;

/**
 * This is the model class for table "blockWidget".
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
class BlockWidget extends ActiveRecord
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
        return '{{%block_widget}}';
    }

    /**
     * @return BlockWidgetQuery
     */
    public static function find()
    {
        return new BlockWidgetQuery(get_called_class());
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
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','body_short','widget_id'], 'required'],
            [['widget_id'], 'unique'],
            [['title','url','widget_id'], 'string', 'max' => 128],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['thumbnail_path'], 'string', 'max' => 128],
            [['thumbnail'], 'safe'],
            [['body'], 'string'],
            [['custom_1', 'custom_2', 'custom_3', 'custom_4'], 'string'],
            [['body_short'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'widget_id' => t_b('Ид. виджета'),
            'url' => t_b('Ссылка'),
            'title' => t_b('Название'),
            'status' => t_b('Публиковать'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
            'thumbnail' => t_b('Картинка (735x492)'),
            'body' => t_b('Описание'),
            'body_short' => t_b('Короткое описание'),
            'custom_1' => t_b('Доп. поле'),
            'custom_2' => t_b('Доп. поле'),
            'custom_3' => t_b('Доп. поле'),
            'custom_4' => t_b('Доп. поле'),
        ];
    }
}
