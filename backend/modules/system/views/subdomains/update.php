<?php

$this->title = t_b( 'Обновить поддомен') . ' ' . $model->subdomain;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Поддомены'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
