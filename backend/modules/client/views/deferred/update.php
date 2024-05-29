<?php

$this->title = t_b( 'Обновить Отложенный товар') . ' ' . $model->product->title . "({$model->user->username})";
$this->params['breadcrumbs'][] = ['label' => t_b( 'Отложенные товары'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
