<?php

namespace frontend\widgets\common\tlock;

use yii\base\Widget;

class CommonTlock extends Widget
{

    public $title = null;
    public $imgUrl = null;
    public $body = null;
    public $body_short = null;
    public $more_link_full_text = 'Свернуть';
    public $more_link_short_text = 'Читать полностью';

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
            'imgUrl' => $this->imgUrl,
            'body' => $this->body,
            'body_short' => $this->body_short,
            'more_link_full_text' => $this->more_link_full_text,
            'more_link_short_text' =>$this->more_link_short_text
        ]);
    }
}
