<?php

$this->title = t_b( 'Обновить Клиента') . ' ' . $model->username ."($model->email)";
$this->params['breadcrumbs'][] = ['label' => t_b( 'Клиенты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
