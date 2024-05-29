<?php

namespace frontend\widgets\products\card;

use yii\base\Widget;

class ProductCard extends Widget
{

    public $title = '';
    public $model = null;
    public $reviews = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        return $this->render('index', [
            'title' => $this->title,
            'model' => $this->model,
            'reviews' => $this->reviews,
        ]);
    }
}
