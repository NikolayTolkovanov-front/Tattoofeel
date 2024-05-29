<?php

namespace frontend\widgets\common\seoH1;

use Yii;
use yii\base\Widget;

class SeoH1 extends Widget
{
    public $seoH1 = '';
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
            'seoH1' => $this->replaceVars($this->seoH1),
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
