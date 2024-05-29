<?php

$this->title =t_b( 'Добавить заказы');
$this->params['breadcrumbs'][]  = ['label' =>t_b( 'Заказы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
