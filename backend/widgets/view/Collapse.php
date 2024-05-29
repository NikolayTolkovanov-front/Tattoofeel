<?php
namespace backend\widgets\view;

use yii\imperavi\Widget;

class Collapse extends Widget
{
    public $title = 'Название вкладки';
    public $open = false;

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();
        $classOpen = !$this->open ? 'collapsed-box' : '';

        $wrap = <<<WRAP
<div class="box box-success {$classOpen}">
    <div class="box-header with-border">
        <h3 class="box-title">{$this->title}</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">{$content}</div>
</div>
WRAP;

        return $wrap;
    }
}
