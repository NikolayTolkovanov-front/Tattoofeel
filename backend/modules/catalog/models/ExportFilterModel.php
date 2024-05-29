<?php

namespace backend\modules\catalog\models;

use yii\base\Model;

class ExportFilterModel extends Model
{
    public $article;
    public $filter;
    public $value;

    public function rules()
    {
        return [
            [['article','filter','value'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'article' => 'Артикул',
            'filter' => 'Фильтр',
            'value' => 'Значение',
        ];
    }

}
