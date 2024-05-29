<?php

$this->title = t_b( 'Обновить шаблон письма') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Шаблоны писем'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
