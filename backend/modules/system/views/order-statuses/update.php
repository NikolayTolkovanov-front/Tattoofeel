<?php

$this->title = t_b( 'Обновить статус заказа');
$this->params['breadcrumbs'][] = ['label' => t_b( 'Статусы заказов'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
