<?php

namespace frontend\widgets\common\seoText;

use Yii;
use yii\base\Widget;

class SeoText extends Widget
{
    public $seoText = '';
    public $subdomainInfo = null;

    public function init()
    {
        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $params = [
            'seoText' => $this->replaceVars($this->seoText),
        ];

        return $this->render('index', $params);
    }

    /**
     * Replace vars.
     *
     * @param string $string
     * @return string
     */
    protected function replaceVars($string)
    {
        if (!is_null($this->subdomainInfo) && !empty($string)) {
            $string = str_replace('[city1]', $this->subdomainInfo['city'], $string);
            $string = str_replace('[city2]', $this->subdomainInfo['word_form'], $string);
        }

        return $string;
    }
}
