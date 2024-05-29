<?php
namespace backend\widgets\view\grid;

class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('profile', 'user', ['class'=>'btn btn-sm btn-info']);
        $this->initDefaultButton('view', 'eye-open', ['class'=>'btn btn-sm btn-info']);
        $this->initDefaultButton('update', 'pencil', ['class'=>'btn btn-sm btn-success']);
        $this->initDefaultButton('delete', 'trash', [
            'class' => 'btn btn-sm btn-danger',
            'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }
}
