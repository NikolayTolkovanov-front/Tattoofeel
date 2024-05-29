<?php

namespace common\models;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subdomains".
 *
 * @property integer         $id
 * @property string          $subdomain
 * @property string          $city
 * @property string          $word_form
 * @property string          $address
 * @property string          $phone
 * @property string          $work_time
 * @property string          $work_hours_showroom
 * @property integer         $created_by
 * @property integer         $updated_by
 * @property integer         $created_at
 * @property integer         $updated_at
 */
class Subdomains extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subdomains}}';
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
            [['subdomain'], 'required'],
            [['subdomain'], 'unique'],
            [['subdomain', 'city', 'word_form', 'phone', 'work_time', 'work_hours_showroom'], 'string', 'max' => 255],
            [['address'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b('Ид.'),
            'subdomain' => t_b('Поддомен'),
            'city' => t_b('Город'),
            'word_form' => t_b('Словоформа'),
            'address' => t_b('Адрес'),
            'phone' => t_b('Телефон'),
            'work_time' => t_b('График работы интернет-магазина'),
            'work_hours_showroom' => t_b('График работы Шоурума'),
            'created_by' => t_b('Создал'),
            'updated_by' => t_b('Обновил'),
            'created_at' => t_b('Создано'),
            'updated_at' => t_b('Обновлено'),
        ];
    }

    public static function subdomainInfo($subdomain)
    {
        return static::find()
            //->cache(7200)
            ->andWhere(['subdomain' => $subdomain])
            ->asArray()
            ->one();
    }
}
