<?php

$this->title = t_b( 'Обновить банковскую карту') . ' ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Банковские карты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
