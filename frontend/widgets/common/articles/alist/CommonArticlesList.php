<?php

namespace frontend\widgets\common\articles\alist;

use yii\base\Widget;

class CommonArticlesList extends Widget
{
    public $dataProvider = null;
    public $isAjax = false;
    public $linkLoadMore = false;

    public function init() {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {

        $params = [
            'id' => $this->id,
            'linkLoadMore' => $this->linkLoadMore,
            'dataProvider' => $this->dataProvider,
        ];

        if ( $this->isAjax )
            return $this->render( '_list-items', $params);

        return $this->render( 'index', $params);
    }
}
