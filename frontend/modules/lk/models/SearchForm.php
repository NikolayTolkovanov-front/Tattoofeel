<?php

namespace frontend\modules\lk\models;

use yii\base\Model;

/**
 * Login form
 */
class SearchForm extends Model
{
    public $full_name;
    public $phone;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['full_name', 'phone', 'email'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => 'ФИО',
            'phone' => 'Телефон',
            'email' => 'E-mail',
        ];
    }
}
