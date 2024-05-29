<?php

namespace frontend\widgets\common;

use yii\base\Widget;
use yii\helpers\Html;

class Icon extends Widget
{
    public $name;

    public $class = 'icon';
    public $options = [];
    public $width = null;
    public $height = null;

    public $sprite = '/img/svg/icons.svg';

    public function init() {
        $this->options = array_merge(
            ['class' => $this->class],
            $this->options
        );

        if (!is_null($this->width))
            $this->options = array_merge(
                $this->options,
                ['width' => $this->width]
            );

        if (!is_null($this->height))
            $this->options = array_merge(
                $this->options,
                ['height' => $this->height]
            );

        return parent::init();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {

        if ($this->name == 'loader')
            return Html::tag('svg',
                '<circle style="animation: loader-dash 1.4s ease-in-out infinite;stroke-dasharray: 80px, 200px;stroke-dashoffset: 0;"
                cx="44" cy="44" r="20.2" fill="none" stroke-width="4" />',
                array_merge(
                    [
                        "viewBox" => "22 22 44 44",
                        "xmlns" => "http://www.w3.org/2000/svg",
                        "style" => "animation: loader-rotate 1.4s linear infinite;",
                    ],
                    $this->options
                )
            );

        return Html::tag('svg',
            "<use xlink:href='{$this->sprite}#{$this->name}'></use>",
            $this->options
        );
    }
}
