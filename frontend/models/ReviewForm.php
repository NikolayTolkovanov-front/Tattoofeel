<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ReviewForm extends Model
{
    public $product_id;
    public $rating;
    public $text;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['product_id', 'rating', 'text'], 'required'],
            [['product_id'], 'integer'],
            [['rating'], 'double', 'min' => 1.0, 'max' => 5.0],
            [['text'], 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'ID товара',
            'rating' => 'Рейтинг',
            'text' => 'Текст отзыва',
        ];
    }
}
