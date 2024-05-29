<?php

use common\models\PaymentTypes;

$this->title = t_b( 'Обновить комиссию') . ' ID: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => t_b( 'Комиссия'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=> $this->title ];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'payment_types' => PaymentTypes::find()->all(),
    ]) ?>

</div>
