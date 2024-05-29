<?php

namespace frontend\widgets\common\question;

use yii\base\Widget;

class CommonQuestion extends Widget
{

    public $questions = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        return $this->render('index', ['questions' => $this->questions]);
    }
}
