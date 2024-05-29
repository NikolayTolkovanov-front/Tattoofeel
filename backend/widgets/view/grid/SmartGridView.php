<?php

namespace backend\widgets\view\grid;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Inflector;
use yii\web\JsExpression;

class SmartGridView extends \yii\grid\GridView
{
    protected $showColName = 'sh';
    protected $initConfig;
    protected $beforeInitConfig;

    public $topButtons = [];

    public $columns_main;
    public $columns_alt;
    public $columns_actions;

    public $show_columns = [];
    public $pageSize;
    public $scrollLeft;

    public $layout = "{topButtons}\n{items}\n{summary}\n{pager}";

    public $form;

    public function __construct($config = [])
    {
        if ( !isset($config['options']) ) $config['options'] = [];
        $config['options']['class'] = 'grid-view grid-view_smart';

        if ( empty($config['show_columns']) )
            $config['show_columns'] = array_column($config['columns_main'], 'attribute');

        $config['columns'] =
            $this->filterVisibilityColumns(
                $this->configForColumns($config),
                $config['show_columns']
            );

        $this->initConfig = $config;

        parent::__construct($config);
    }

    /**
     * Initializes the grid view.
     * This method will initialize required property values and instantiate [[columns]] objects.
     */
    public function init()
    {
        parent::init();
        $this->pager['firstPageLabel'] = t_b('Первая');
        $this->pager['lastPageLabel'] = t_b('Последняя');
        $this->registerThisAssets();
    }

    protected function configForColumns($config) {
        return $this->prepareConfigForColumnsOptions(array_merge(
            $config['columns_actions']??[],
            $config['columns_main']??[],
            $config['columns_alt']??[]
        ));
    }

    protected function prepareConfigForColumnsOptions($columns) {

        foreach($columns as &$col)
            if ( isset($col['options']) ) {
                $col['contentOptions'] = $col['options'];
                $col['filterOptions'] = $col['options'];
                $col['headerOptions'] = $col['options'];
                unset($col['options']);
            }

        return $columns;
    }

    protected function filterVisibilityColumns($columns, $show_columns) {
        return !empty($show_columns) && !empty($columns) ?
            array_map(
                function($v) use ($show_columns) {
                    $v['visible'] =
                        isset($v['attribute']) &&
                        in_array($v['attribute'], $show_columns) || !isset($v['attribute']);
                    return $v;
                },
                $columns
            ) : $columns;
    }


    protected function getShowColumnsValues() {
        return !empty($this->show_columns) ?
            $this->show_columns :
            array_keys($this->getColumnsDataList());
    }

    protected function getColLabel($col)
    {
        $provider = $this->dataProvider;
        $attr = $col->attribute;

        if (empty($col)) return '';

        if ($col->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $modelClass Model */
                $modelClass = $provider->query->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($attr);
            } elseif ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                /* @var $modelClass Model */
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($attr);
            } elseif ($this->filterModel !== null && $this->filterModel instanceof Model) {
                $label = $this->filterModel->getAttributeLabel($attr);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($attr);
                } else {
                    $label = Inflector::camel2words($attr);
                }
            }
        } else
            $label = $col->label;

        return $label;
    }

    protected function getColumnsDataList() {
        $res = [];

        foreach($this->initConfig['columns'] as $col) {
            $col = (object) $col;

            if (!isset($col->label))
                $col->label = null;

            if (!empty($col->attribute))
                $res[$col->attribute] = (object)[
                    'label' => $this->getColLabel($col),
                    'value' => $col->attribute
                ];
        }

        return $res;
    }


    protected function renderShowColumns() {

        $icon = Html::tag('span','', ['class' => 'glyphicon glyphicon-cog']);

        $link = Html::a($icon, '#',[
            'class' => 'btn btn-primary',
            'data-toggle' => 'modal',
            'data-target' => "#$this->id-modal-columns",
            ]);

        $this->renderModal();

        return $link;
    }

    protected function renderModal() {

        $pageSizeParamName = $this->dataProvider->pagination->pageSizeParam;
        $pageSize = $this->pageSize;

        if (empty($pageSize))
            $pageSize = $this->dataProvider->pagination->defaultPageSize;

        $this->dataProvider->pagination = ['pageSize' => $pageSize];

        Modal::begin([
            'header' => 'Параметры',
            'id' => "$this->id-modal-columns",
            'options' => [
                'class' => 'grid-view_smart__modal'
            ]
        ]);
                    echo Html::tag('p', t_b('Количество записей на страницы'));
                    echo Html::input('text', $pageSizeParamName, $pageSize, [
                        'min' => 1, 'step' => 10,
                        'current-val' => $pageSize,
                        'data-type' => 'number'
                        ]);

                echo Html::tag('br');
                echo Html::tag('hr');

                echo "<div class='ch-2-col'>";
                foreach($this->getColumnsDataList() as $col) {
                    echo Html::label(
                        Html::input(
                            'checkbox',
                            "$this->showColName[]",
                            $col->value,
                            [
                                'checked' => in_array($col->value, $this->getShowColumnsValues()),
                                'current-val' => in_array($col->value, $this->getShowColumnsValues())
                            ]
                        ).
                        " $col->label"
                    );
                }
                echo "</div>";

                echo Html::tag('hr');

                $defaultSetButton = Html::a(
                    t_b('По умолчанию'), '',
                    [
                        'class' => 'btn btn-default',
                        'data-dismiss' => 'modal',
                        'data-target' => "$this->id-modal-columns",
                        'onclick' => new JsExpression("
                            $(this).closest('form').find('[name=\"default\"]').val(1)
                            $(this).closest('form').find('[type=\"checkbox\"]').attr('disabled', 'disabled')
                            $(this).closest('form').find('[data-type=\"number\"]').attr('disabled', 'disabled')
                            $(this).closest(\"form\").submit()
                        ")
                    ]
                );

                $cancelButton = Html::a(
                    t_b('Отменить'), '',
                    [
                        'class' => 'btn btn-default',
                        'data-dismiss' => 'modal',
                        'data-target' => "$this->id-modal-columns",
                        'onclick' => new JsExpression("
                            $(this).closest('form').find('[type=\"checkbox\"]').each(function(){
                                $(this).removeAttr('checked');
                                $(this)[0].checked = false;
                            });
                            $(this).closest('form').find('[current-val]').each(function(){
                                if ( $(this).is('[type=\"checkbox\"][current-val]') ) {
                                    $(this).attr('checked', 'checked');
                                    $(this)[0].checked = true;
                                } else
                                    $(this).val($(this).attr('current-val'))
                            })
                        ")
                    ]
                );
                $apply_button = Html::a(
                    t_b('Применить'), '',
                    [
                        'class' => 'btn btn-success',
                        'data-dismiss' => 'modal',
                        'data-target' => "$this->id-modal-columns",
                        'onclick' => new JsExpression('$(this).closest("form").submit()')
                    ]
                );

                echo Html::tag(
                    'div',
                    "$defaultSetButton&nbsp; $cancelButton&nbsp; $apply_button",
                    ['class' => 'text-right']
                );

                echo Html::input('hidden','default');
                echo Html::input('hidden','scrollLeft');

        Modal::end();
    }

    protected function renderTopButtons() {
        $topButtons = join("&nbsp; ", array_merge(
            $this->topButtons,
            [$this->renderShowColumns()]
        ));
        return Html::tag('div', $topButtons, ['class' => 'grid-view_smart__topButtons text-right']);
    }

    protected function registerThisAssets() {
        $this->view->registerCss("
            .grid-view_smart__table-container {
                min-height: 400px;
                overflow-x: auto;
                margin-bottom: 20px;
            }
            .grid-view_smart__table-container > .table {
                width: auto;
                max-width: initial;
                min-width: 100%;
                table-layout: auto;
                margin-bottom: 0;
            }
            .grid-view_smart__table-container > .table>*>*>td,
            .grid-view_smart__table-container > .table>*>*>th,
            .grid-view_smart__table-container > .table>*>td,
            .grid-view_smart__table-container > .table>*>th
            {
                min-width: 100px;
            }
            .grid-view_smart__modal select,
            .grid-view_smart__modal input
            {
                outline: none;
                border: 1px solid #f5f5f5;
            }
            .grid-view_smart__modal input {
                padding: 3px 10px;
            }
            .grid-view_smart__modal .ch-2-col {
                column-count: 2;
            }
            .grid-view_smart__modal label {
                display: block;
                font-weight: 400;
            }
            .grid-view_smart__topButtons {
                padding-bottom: 10px;
            }
        ");
        $this->view->registerJs("
            $('#{$this->id}').each(function(){
                var t = $(this);
                var form = t.closest('form');

                //scrollLeft
                t.find('.grid-view_smart__table-container').bind('scroll', function(){
                    $('#$this->id-modal-columns').find('[name=\"scrollLeft\"]').val($(this).scrollLeft());
                });
                t.find('.grid-view_smart__table-container').scrollLeft({$this->scrollLeft});

                //sort, pagination send post
                t.find('th a, .pagination a').click(function(e){
                    e.preventDefault();
                    var url = $(this).attr('href');

                    form.attr('prev-action', form.attr('action'))
                    form.attr('action', url)
                    form.find('[type=\"submit\"]').click();
                })
            });
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function renderItems() {
        return Html::tag('div', parent::renderItems(), ['class' => 'grid-view_smart__table-container']);
    }

    /**
     * {@inheritdoc}
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{topButtons}':
                return $this->renderTopButtons();
            case '{items}':
                return $this->renderItems();
            default:
                return parent::renderSection($name);
        }
    }
}
