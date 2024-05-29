<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "seo_meta_tags".
 *
 * @property integer         $id
 * @property string          $url
 * @property string          $h1
 * @property string          $seo_title
 * @property string          $seo_desc
 * @property string          $seo_keywords
 * @property string          $seo_text
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $created_at
 * @property integer         $updated_at
 */
class SeoMetaTags extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo_meta_tags}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url', 'h1', 'seo_title', 'seo_desc', 'seo_keywords', 'seo_text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'url' => t_b('Адрес страницы'),
            'h1' => t_b('Заголовок H1'),
            'seo_title' => t_b('SEO Заголовок'),
            'seo_desc' => t_b('SEO Описание'),
            'seo_keywords' => t_b('SEO Ключевые слова'),
            'seo_text' => t_b('SEO Текст'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }
}
