<?php

$this->title = t_b( 'Обновить Профиль Клиента') . ' ' .
    $model->user->username .
    "({$model->user->email})";

$this->params['breadcrumbs'][] = ['label' => t_b( 'Клиенты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form-profile', [
        'model' => $model
    ]) ?>

</div>
