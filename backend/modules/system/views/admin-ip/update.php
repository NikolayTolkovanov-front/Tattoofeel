<?php

$this->title = t_b( 'Обновить IP адрес') . ' ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Разрешенные IP адреса'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
