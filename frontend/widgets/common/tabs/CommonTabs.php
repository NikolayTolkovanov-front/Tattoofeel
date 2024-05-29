<?php

namespace frontend\widgets\common\tabs;

use yii\base\Widget;

class CommonTabs extends Widget
{

    public $tabs = null;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        return $this->render('index', ['tabs'=>$this->tabs]);
    }
}
