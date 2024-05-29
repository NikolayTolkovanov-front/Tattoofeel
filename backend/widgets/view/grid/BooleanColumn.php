<?php
namespace backend\widgets\view\grid;

use yii\grid\DataColumn;
use yii\helpers\Html;

class BooleanColumn extends DataColumn
{

    /**
     * {@inheritdoc}
     */
    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        return Html::activeInput('hidden',$model, $this->attribute);
    }
}
