<?php

$this->title = t_b( 'Обновить заказ') . ' ' . $model->id . "({$model->user->username})";
$this->params['breadcrumbs'][] = ['label' => t_b( 'Заказы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
