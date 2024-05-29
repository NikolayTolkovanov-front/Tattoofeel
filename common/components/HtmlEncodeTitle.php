<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\HtmlPurifier;
use HTMLPurifier_Config;

class HtmlEncodeTitle extends Widget
{
    public $value = '';

    public function __construct($value, $config = [])
    {
        parent::__construct($config);

        $this->value = static::process($value);
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->value;
    }

    static function process($value) {

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'br');

        return HtmlPurifier::process($value, $config);
    }
}
