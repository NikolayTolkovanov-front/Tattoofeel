<?php

$this->title =t_b( 'Добавить');
$this->params['breadcrumbs'][]  = ['label' =>t_b( 'Шаблоны писем'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
