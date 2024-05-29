<?php

$this->title =t_b( 'Добавить');
$this->params['breadcrumbs'][]  = ['label' =>t_b( 'Разрешенные IP адреса'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
