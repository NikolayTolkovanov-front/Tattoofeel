<?php

namespace frontend\widgets\common\seoMetaTags;

use Yii;
use yii\base\Widget;

class SeoMetaTags extends Widget
{
    public $seoUrl = '';
    public $seoTitle = '';
    public $seoDescription = '';
    public $seoKeywords = '';
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
        $title = !empty($this->seoTitle) ? $this->seoTitle : Yii::$app->params['title'];
        $description = !empty($this->seoDescription) ? $this->seoDescription : Yii::$app->params['description'];
        $keywords = !empty($this->seoKeywords) ? $this->seoKeywords : Yii::$app->params['keywords'];

        $params = [
            'seoUrl' => $this->seoUrl,
            'seoTitle' => $this->replaceVars($title),
            'seoDescription' => $this->replaceVars($description),
            'seoKeywords' => $this->replaceVars($keywords),
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
        $seoH1 = !empty($this->seoH1) ? $this->seoH1 : '';
        if ($seoH1) {
            $string = str_replace('[h1]', $seoH1, $string);
        }
        if (!is_null($this->subdomainInfo) && !empty($string)) {
            $string = str_replace('[city1]', $this->subdomainInfo['city'], $string);
            $string = str_replace('[city2]', $this->subdomainInfo['word_form'], $string);
        }

        return $string;
    }
}
