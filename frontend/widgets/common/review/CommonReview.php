<?php

namespace frontend\widgets\common\review;

use yii\base\Widget;

class CommonReview extends Widget
{

    public $reviews = null;
    public $product_id = null;
    public $product_title = null;
    public $is_main_config = false;

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
            'reviews' => $this->reviews,
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'is_main_config' => $this->is_main_config,
        ]);
    }
}
