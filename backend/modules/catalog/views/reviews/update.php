<?php

$this->title = t_b( 'Обновить отзыв') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Отзывы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
