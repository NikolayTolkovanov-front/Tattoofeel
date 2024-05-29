<?php

namespace frontend\widgets\home\team;

use yii\base\Widget;

class HomeTeam extends Widget
{

    public $model = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        return $this->render('index',['model' => $this->model]);
    }
}
