<?php

namespace frontend\widgets\categories\tile;

use yii\base\Widget;

class CategoriesTile extends Widget
{

    public $isAjax = false;
    public $head = '';
    public $headCenter = false;
    public $headUrl = null;
    public $linkShowMore = false;
    public $linkShowAll = false;
    public $linkLoadMore = false;
    public $outerTitle = false;
    //isSlider
    public $simpleList = false;
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

        $params = [
            'id' => $this->id,
            'head' => $this->head,
            'headUrl' => $this->headUrl,
            'headCenter' => $this->headCenter,
            'linkShowMore' => $this->linkShowMore,
            'linkShowAll' => $this->linkShowAll,
            'linkLoadMore' => $this->linkLoadMore,
            'outerTitle' => $this->outerTitle,
            'simpleList' => $this->simpleList,
            'dataProvider' => $this->dataProvider,
        ];

        if ( $this->isAjax )
            return $this->render( '_list-items', $params);

        return $this->render( 'index', $params);
    }
}
