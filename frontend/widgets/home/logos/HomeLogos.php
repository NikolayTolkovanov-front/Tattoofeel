<?php

namespace frontend\widgets\home\logos;

use yii\base\Widget;

class HomeLogos extends Widget
{

    public $dataProvider = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        return $this->render('index',[
            'dataProvider' => $this->dataProvider
        ]);
    }
}
