<?php

$this->title = t_b( 'Обновить мета теги') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => t_b( 'SEO Мета теги'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
