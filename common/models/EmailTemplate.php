<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "email_template".
 *
 * @property integer         $id
 * @property string          $name
 * @property string          $html
 * @property string          $comment
 * @property string          $desc
 * @property integer         $created_at
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $updated_at
 */
class EmailTemplate extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const BUY_ONE_CLICK_TEMPLATE = 1;
    const ACCOUNT_ACTIVATION_TEMPLATE = 2;
    const RESET_PASSWORD_TEMPLATE = 3;
    const REVIEWS_TEMPLATE = 4;
    const NOT_FOUND_SEARCH_TEMPLATE = 5;
    const CONTACT_FORM_TEMPLATE = 6;
    const NEW_ORDER_TEMPLATE = 7;
    const DELETE_USER_TEMPLATE = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%email_template}}';
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
            [['html', 'name'], 'required'],
            [['html', 'name', 'comment', 'desc'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'name' => t_b('Название'),
            'html' => t_b('HTML-шаблон'),
            'comment' => t_b('Комментарий'),
            'desc' => t_b('Описание тегов'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    public static function render($template_id, $params)
    {
        $template = EmailTemplate::find()->where(['id' => $template_id])->one();
        if ($template && is_array($params)) {
            $result = $template->html;
            foreach ($params as $key => $value) {
                $result = str_replace('{{'.$key.'}}', $value, $result);
            }
        }

        return isset($result) ? $result : '';
    }
}
